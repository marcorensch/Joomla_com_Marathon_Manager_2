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

use Joomla\CMS\Form\FormField;

class AcyMailingResendConfirmationButtonField extends FormField
{
	public $type = 'AcyMailingResendConfirmationButton';
	protected function getInput()
	{
		return '<button type="button" class="btn btn-large btn-primary w-100">Send Confirmation Mail</button>';
	}


}