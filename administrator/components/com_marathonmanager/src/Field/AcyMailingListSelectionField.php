<?php
/**
 * @package      Joomla.Administrator
 *              Joomla.Site
 * @subpackage   com_marathonmanager
 * @author       Marco Rensch
 * @since        1.0.0
 *
 * @license      GNU General Public License version 2 or later; see LICENSE.txt
 * @copyright    Copyright (C) 2022 nx-designs NXD
 *
 */

namespace NXD\Component\MarathonManager\Administrator\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormHelper;
use Joomla\Database\DatabaseInterface;

defined('_JEXEC') or die;

FormHelper::loadFieldClass('list');

class AcyMailingListSelectionField extends ListField
{

	protected $type = 'AcyMailingListSelection';

	protected function getOptions(): array
	{
		$options   = [];
		$options[] = HTMLHelper::_('select.option', '', Text::_('COM_MARATHONMANAGER_FIELD_DEFAULT_SELECT_ACYMAILING_LIST'));

//		Is currently not working, because the ListClass is not available idk.
//        $listClass = new ListClass;
//        $allLists = $listClass->getAll();
		$allLists = $this->getAcymLists(true);

		if (!empty($allLists))
		{
			foreach ($allLists as $option)
			{
				$options[] = HTMLHelper::_('select.option', $option->id, $option->name);
			}
		}

		return array_merge(parent::getOptions(), $options);

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