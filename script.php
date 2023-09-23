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
use Joomla\CMS\Filesystem\Folder;
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
        echo Text::_('COM_MARATHONMANAGER_INSTALLERSCRIPT_INSTALL');
        $this->installCategory('Uncategorised');
        $this->createMediaDirectories();
        return true;
    }

    public function update($parent): bool
    {
        echo Text::_('COM_MARATHONMANAGER_INSTALLERSCRIPT_UPDATE');
        $this->createMediaDirectories();
        return true;
    }

    public function uninstall($parent): bool
    {
        echo Text::_('COM_MARATHONMANAGER_INSTALLERSCRIPT_UNINSTALL');
        return true;
    }

    public function preflight($type, $parent): bool
    {
        if ($type !== 'uninstall') {
            // Check minimum PHP Version
            if (!empty($this->minimumPhpVersion) && version_compare(PHP_VERSION, $this->minimumPhpVersion, '<')) {
                Log::add(Text::sprintf('JLIB_INSTALLER_MINIMUM_PHP', $this->minimumPhpVersion), Log::WARNING, 'jerror');
                return false;
            }
        }
        echo Text::_('COM_MARATHONMANAGER_INSTALLERSCRIPT_PREFLIGHT_' . strtoupper($type));
        return true;
    }

    public function postflight($type, $parent): bool
    {
        echo Text::_('COM_MARATHONMANAGER_INSTALLERSCRIPT_POSTFLIGHT_' . strtoupper($type));
        return true;
    }

    /**
     * @throws Exception
     */
    private function installCategory($categoryTitle)
    {
        if (!$categoryTitle) return false;
        $alias = ApplicationHelper::stringURLSafe($categoryTitle);
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);

        $category = Table::getInstance('Category');

        $data = [
            'extension' => 'com_marathonmanager',
            'title' => $categoryTitle,
            'alias' => $alias,
            'published' => 1,
            'access' => 1,
            'params' => '{"category_layout":"","image":""}',
            'metadescription' => '',
            'metakey' => '',
            'metadata' => '{"page_title":"","author":"","robots":""}',
            'language' => '*',
            'created_user_id' => $this->getAdminId(),
            'created_time' => Factory::getDate()->toSql(),
            'parent_id' => 1,
            'rules' => [],
        ];

        $category->setLocation(1, 'last-child');

        // Bind data to the table
        if (!$category->bind($data)) {
            Factory::getApplication()->enqueueMessage($category->getError(), 'error');
            return false;
        }

        // Check the data.
        if (!$category->check()) {
            Factory::getApplication()->enqueueMessage($category->getError(), 'error');
            return false;
        }

        // Store the data.
        if (!$category->store(true)) {
            Factory::getApplication()->enqueueMessage($category->getError(), 'error');
            return false;
        }

        return true;

    }

    private function createMediaDirectories(): void
    {
        $foldernames = array('events', 'participants', 'results','registrations');
        $path = JPATH_ROOT . '/media/com_marathonmanager';
        foreach ($foldernames as $foldername) {
            if (!Folder::exists($path . '/' . $foldername)) {
                try {
                    Folder::create($path . '/' . $foldername);
                    Factory::getApplication()->enqueueMessage(Text::sprintf('COM_MARATHONMANAGER_FOLDER_CREATED_TRUE', $foldername), 'notice');
                } catch (Exception $e) {
                    echo $e->getMessage();
                    Log::add(Text::sprintf('COM_MARATHONMANAGER_FOLDER_CREATED_FALSE', $foldername), Log::WARNING, 'jerror');
                }
            }
        }
    }

    private function getAdminId(): int
    {
        $app = Factory::getApplication();
        $user = $app->getIdentity();
        return $user->id;
    }
}