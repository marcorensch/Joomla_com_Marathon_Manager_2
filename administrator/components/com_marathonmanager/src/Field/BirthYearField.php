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

use Joomla\CMS\Form\Field\NumberField;


class BirthYearField extends NumberField
{

	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.0.0
	 */
	protected $type = 'birthyear';
	protected $max = null;
	protected $min = null;
	protected $step = 1;
	protected string $placeholder = 'YYYY';
	protected int $minAge = 0;


	public function setup(\SimpleXMLElement $element, $value, $group = null): bool
	{
		$return = parent::setup($element, $value, $group);

		if ($return)
		{
			$yearNow = date('Y');
			// It is better not to force any default limits if none is specified
			$this->max  = isset($this->element['min-age']) ? (int) $yearNow - intval($this->element['min-age']) : $yearNow - $this->minAge;
			$this->min  = isset($this->element['min']) ? (float) $this->element['min'] : null;
			$this->step = isset($this->element['step']) ? (float) $this->element['step'] : $this->step;
		}

		return $return;
	}

}