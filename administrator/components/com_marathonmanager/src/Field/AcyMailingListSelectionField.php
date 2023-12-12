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

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormHelper;

use AcyMailing\Classes\ListClass;

FormHelper::loadFieldClass('list');

defined('_JEXEC') or die;

class AcyMailingListSelectionField extends ListField
{

    protected $type = 'AcyMailingListSelection';

    protected function getOptions(): array
    {
        $options = [];
        $options[] = HTMLHelper::_('select.option', '', Text::_('COM_MARATHONMANAGER_FIELD_DEFAULT_SELECT_ACYMAILING_LIST'));

        $listClass = new ListClass;
        $allLists = $listClass->getAll();

        if (!empty($allLists)) {
            foreach ($allLists as $option) {
                $options[] = HTMLHelper::_('select.option', $option->id, $option->name);
            }
        }

        return array_merge(parent::getOptions(), $options);

    }

}