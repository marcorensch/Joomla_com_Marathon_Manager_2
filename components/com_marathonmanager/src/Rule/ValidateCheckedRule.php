<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\Rule;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormRule;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

class ValidateCheckedRule extends FormRule
{
    public function test(\SimpleXMLElement $element, $value, $group = null, Registry $input = null, Form $form = null): bool
    {
        if($value == "0"){
            $elementLabel = Text::_($element['label'] ?? $element['name']);
            Factory::getApplication()->enqueueMessage(Text::sprintf("COM_MARATHONMANAGER_MSG_FIELD_NEEDS_TO_BE_CHECKED", $elementLabel), 'error');
        }
        return $value != "0";
    }
}