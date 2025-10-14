<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Factory;

class Com_MarathonmanagerInstallerScript
{
	private $minimumJoomlaVersion = '4.0';

	private $minimumPhpVersion = JOOMLA_MINIMUM_PHP;

	/**
	 * @throws Exception
	 */
	public function install($parent): bool
	{
		$this->createMediaDirectories();
		return true;
	}

	public function update($parent): bool
	{
        $this->createMediaDirectories();
		return true;
	}

	public function uninstall($parent): bool
	{
		return true;
	}

	public function preflight($type, $parent): bool
	{
		if ($type !== 'uninstall')
		{
			// Check minimum PHP Version
			if (!empty($this->minimumPhpVersion) && version_compare(PHP_VERSION, $this->minimumPhpVersion, '<'))
			{
				Log::add(Text::sprintf('JLIB_INSTALLER_MINIMUM_PHP', $this->minimumPhpVersion), Log::WARNING, 'jerror');

				return false;
			}
		}

//        echo Text::_('COM_MARATHONMANAGER_INSTALLERSCRIPT_PREFLIGHT_' . strtoupper($type));
		return true;
	}

	/**
	 * @throws Exception
	 * @since 1.1.0
	 */
	public function postflight($type, $parent): bool
	{

		if ($type === 'install' || $type === 'update')
		{
			if (!$this->checkIfCategoryExists('Uncategorised')) $this->installCategory('Uncategorised');
		}

		return true;
	}

	/**
	 * @throws Exception
	 * @since 1.1.0
	 */
	private function installCategory(string $categoryTitle): void
	{
		if (!$categoryTitle)
		{
			return;
		}

		$alias = ApplicationHelper::stringURLSafe($categoryTitle);
		$db = Factory::getContainer()->get(DatabaseInterface::class);

		try
		{
			// Hole die höchste rgt-Nummer aus der Categories-Tabelle
			$query = $db->getQuery(true);
			$query->select('MAX(rgt)')
				->from('#__categories');
			$db->setQuery($query);
			$maxRgt = (int) $db->loadResult();

			// Die neue Kategorie bekommt lft = maxRgt + 1 und rgt = maxRgt + 2
			$newLft = $maxRgt + 1;
			$newRgt = $maxRgt + 2;

			$now = Factory::getDate()->toSql();

			// Erstelle die Kategorie direkt in der Datenbank
			$columns = [
				'parent_id',
				'lft',
				'rgt',
				'level',
				'path',
				'extension',
				'title',
				'alias',
				'published',
				'access',
				'language',
				'params',
				'created_user_id',
				'created_time',
				'modified_time'
			];

			$values = [
				1, // Root parent
				$newLft,
				$newRgt,
				1,
				$db->quote($alias),
				$db->quote('com_marathonmanager'),
				$db->quote($categoryTitle),
				$db->quote($alias),
				1,
				1,
				$db->quote('*'),
				$db->quote('{}'),
				$this->getAdminId(),
				$db->quote($now),
				$db->quote($now)
			];

			$query = $db->getQuery(true);
			$query->insert('#__categories')
				->columns($columns)
				->values(implode(',', $values));

			$db->setQuery($query);
			$db->execute();

			Factory::getApplication()->enqueueMessage(
				Text::sprintf('COM_MARATHONMANAGER_INSTALLERSCRIPT_INSTALL_CATEGORY_TRUE', $categoryTitle),
				'notice'
			);
		}
		catch (\Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			Factory::getApplication()->enqueueMessage(
				Text::sprintf('COM_MARATHONMANAGER_INSTALLERSCRIPT_INSTALL_CATEGORY_FALSE', $categoryTitle),
				'error'
			);
		}
	}

	private function checkIfCategoryExists(string $categoryTitle): bool
	{
		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from('#__categories');
		$query->where('title = ' . $db->quote($categoryTitle));
		$query->where('extension = ' . $db->quote('com_marathonmanager'));
		$db->setQuery($query);
		$result = $db->loadResult();
		if ($result) return true;

		return false;
	}

	private function createMediaDirectories(): void
	{
		$foldernames = array('events', 'participants', 'results', 'registrations');
		$path        = JPATH_ROOT . '/media/com_marathonmanager';
		foreach ($foldernames as $foldername)
		{
			if (!Folder::exists($path . '/' . $foldername))
			{
				try
				{
					Folder::create($path . '/' . $foldername);
					Factory::getApplication()->enqueueMessage(Text::sprintf('COM_MARATHONMANAGER_FOLDER_CREATED_TRUE', $foldername), 'notice');
				}
				catch (Exception $e)
				{
					echo $e->getMessage();
					Log::add(Text::sprintf('COM_MARATHONMANAGER_FOLDER_CREATED_FALSE', $foldername), Log::WARNING, 'jerror');
				}
			}
		}
	}

	private function getAdminId(): int
	{
		$app  = Factory::getApplication();
		$user = $app->getIdentity();

		return $user->id;
	}
}