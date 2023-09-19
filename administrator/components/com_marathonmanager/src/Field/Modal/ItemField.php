<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @source      https://blog.astrid-guenther.de/en/die-daten-der-datenbank-im-frontend-nutzen/
 */

namespace NXD\Component\Hello\Administrator\Field\Modal;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Database\DatabaseInterface;

class ItemField extends FormField
{
	protected $type = 'Modal_Item';

	protected function getInput()
	{
		$allowClear = ((string) $this->element['clear'] != 'false');
		$allowSelect = ((string) $this->element['select'] != 'false');

		$allowNew = ((string) $this->element['new'] != 'false');
		$allowEdit = ((string) $this->element['edit'] != 'false');

		// The active event id field
		$value = (int) $this->value ? (int) $this->value : '';

		$modalId = 'Item_' . $this->id;

		$wa = Factory::getApplication()->getDocument()->getWebAssetManager();

		$wa->useScript('field.modal-fields');

		if($allowSelect)
		{
			static $scriptSelect = null;
			if(is_null($scriptSelect))
			{
				$scriptSelect = [];
			}

			if(!isset($scriptSelect[$this->id]))
			{
				$wa->addInlineScript("
					window.jSelectItem_" . $this->id . " = function(id, title, object) {
						window.processModalSelect('Event', '". $this->id . "', id, title, '', object);
					}",
					[],
					['type' => 'module']
				);
				$scriptSelect[$this->id] = true;
			}
		}

		// Setup variables for display
		$linkItems = 'index.php?option=com_marathonmanager&amp;view=events&amp;layout=modal&amp;tmpl=component&amp;' . Session::getFormToken() . '=1';
		$linkItem = 'index.php?option=com_marathonmanager&amp;view=event&amp;layout=modal&amp;tmpl=component&amp;' . Session::getFormToken() . '=1';
		$modalTitle = Text::_('COM_MARATHONMANAGER_CHANGE_ITEM');

		$urlSelect = $linkItems . '&amp;function=jSelectItem_=' . $this->id;

		if($value){
			$db = Factory::getContainer()->get(DatabaseInterface::class);
			$query = $db->getQuery(true)
				->select($db->quoteName('title'))
				->from($db->quoteName('#__hello_items'))
				->where($db->quoteName('id') . ' = ' . $db->quote($value));
			$db->setQuery($query);

			try {
				$title = $db->loadResult();
			} catch (\Exception $e) {
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
			}
		}

		$title = empty($title) ? Text::_('COM_MARATHONMANAGER_SELECT_AN_ITEM') : htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

		// the current event display field
		$html = '';

		if($allowSelect || $allowNew || $allowEdit || $allowClear)
		{
			$html .= '<span class="input-group">';
		}

		$html .= '<input class="form-control" id="'. $this->id . '_name" type="text" value="' . $title . '" readonly size="35" />';

		// Select event button
		if($allowSelect)
		{
			$html .= '<button'
				. ' class="btn btn-primary hasTooltip' . ($value ? ' hidden' : '') . '"'
				. ' id="' . $this->id . '_select"'
				. ' data-bs-toggle="modal"'
				. ' data-bs-target="#ModalSelect' . $modalId . '"'
				. ' type="button"'
				. ' title="' . HTMLHelper::tooltipText('COM_MARATHONMANAGER_CHANGE_ITEM') . '"' . '>'
					. '<span class="icon-file" aria-hidden="true"></span> ' . Text::_('JSELECT')
				. '</button>';
		}

		if($allowClear) {
			$html .= '<button'
				. ' class="btn btn-secondary hasTooltip' . (!$value ? ' hidden' : '') . '"'
				. ' id="' . $this->id . '_clear"'
				. ' type="button"'
				. ' onclick="window.processModalParent(\'' . $this->id . '\'); return false;">'
					. '<span class="icon-remove" aria-hidden="true"></span> ' . Text::_('JCLEAR')
				. '</button>';
		}

		if($allowSelect || $allowNew || $allowEdit || $allowClear)
		{
			$html .= '</span>';
		}

		if($allowSelect){
			$html .= HTMLHelper::_(
				'bootstrap.renderModal',
				'ModalSelect' . $modalId,
				[
					'url' => $urlSelect,
					'title' => $modalTitle,
					'height' => '400px',
					'width' => '800px',
					'footer' => '<button class="btn btn-secondary" data-bs-dismiss="modal">' . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>'
				]
			);
		}

		$class = $this->required ? ' class="required modal-value"' : '';

		$html .= '<input type="hidden" id="' . $this->id . '_id" data-required="'.(int) $this->required.'"  name="' . $this->name . '" data-text="'.htmlspecialchars(Text::_('COM_MARATHONMANAGER_SELECT_AN_ITEM', true), ENT_COMPAT, 'UTF-8').'" value="' . $value . '"' . $class . ' />';

		return $html;
	}

	protected function getLabel()
	{
		return str_replace($this->id, $this->id . '_name', parent::getLabel());
	}
}