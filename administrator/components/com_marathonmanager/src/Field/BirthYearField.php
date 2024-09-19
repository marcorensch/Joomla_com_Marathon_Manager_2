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
use Joomla\CMS\Form\Field\NumberField;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;


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
	protected string $event_date;
	protected int $min_age = 0;


	public function setup(\SimpleXMLElement $element, $value, $group = null): bool
	{
		$return = parent::setup($element, $value, $group);

		$input = Factory::getApplication()->input;
		$eventId  = $input->get('event_id', 0, 'integer');

		if($eventId){
			$db = Factory::getContainer()->get(DatabaseInterface::class);
			$query = $db->getQuery(true);
			$query->select('event_date');
			$query->from('#__com_marathonmanager_events');
			$query->where('id = ' . $db->quote($eventId));
			$db->setQuery($query);
			$this->event_date = $db->loadResult();
		}

		if($this->event_date){
			$year = date('Y', strtotime($this->event_date));
		}else{
			$year = date('Y');
		}

		if ($return)
		{
			// It is better not to force any default limits if none is specified
			$this->max  = isset($this->element['data-min-age']) ? (int) $year - intval($this->element['data-min-age']) : $year - $this->min_age;
			$this->min  = isset($this->element['min']) ? (float) $this->element['min'] : null;
			$this->step = isset($this->element['step']) ? (float) $this->element['step'] : $this->step;
		}

		return $return;
	}

}