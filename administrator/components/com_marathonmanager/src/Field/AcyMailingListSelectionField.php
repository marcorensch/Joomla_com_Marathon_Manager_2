<?php
/**
 * @package     Joomla.Administrator
 *              Joomla.Site
 * @subpackage  com_marathonmanager
 * @author      Marco Rensch
 * @since        1.0.0
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @copyright   Copyright (C) 2022 nx-designs NXD
 *
 */

namespace NXD\Component\MarathonManager\Administrator\Field;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

class AcyMailingListSelectionField extends ListField
{

    protected $type = 'AcyMailingListSelection';

	protected function getOptions(): array
	{
		$options = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('COM_MARATHONMANAGER_FIELD_DEFAULT_SELECT_ACYMAILING_LIST'));

		// Check if AcyMailing is installed
		if (!$this->isAcyMailingInstalled()) {
			Factory::getApplication()->enqueueMessage(
				Text::_('COM_MARATHONMANMANAGER_FIELD_ACYM_NOT_FOUND_ERROR'),
				'warning'
			);
			return array_merge(parent::getOptions(), $options);
		}

		// Load the class dynamically
		$listClass = new \AcyMailing\Classes\ListClass();
		$allLists = $listClass->getAll();

		if (!empty($allLists)) {
			foreach ($allLists as $option) {
				$options[] = HTMLHelper::_('select.option', $option->id, $option->name);
			}
		}

		return array_merge(parent::getOptions(), $options);
	}

	/**
	 * Checks whether AcyMailing is installed and available
	 *
	 * @return bool
	 * @since 1.1.0
	 */
	private function isAcyMailingInstalled(): bool
	{
		$acyHelper = JPATH_ADMINISTRATOR . '/components/com_acym/helpers/helper.php';

		if (!file_exists($acyHelper)) {
			return false;
		}

		include_once($acyHelper);

		return class_exists('AcyMailing\Classes\ListClass');
	}

	private function getAcymLists($onlyActive = false): array
	{
		$db    = Factory::getContainer()->get(DatabaseInterface::class);
		$query = $db->getQuery(true)
			->select('id, name')
			->from('#__acym_list');

		if ($onlyActive)
		{
			$query->where('active = 1');
		}

		$db->setQuery($query);
		try
		{
			$lists = $db->loadObjectList();
		}
		catch (\Exception $e)
		{
			error_log('MarathonManager 2 AcyMailing Integration Error from Selection Field: ' . $e->getMessage());
			Factory::getApplication()->enqueueMessage(Text::_('COM_MARATHONMANMANAGER_FIELD_ACYM_NOT_FOUND_ERROR'), 'warning');
			$lists = [];
		}

		return $lists;

	}

}