<?php
/**
 * @package     Joomla.Administrator
 *              Joomla.Site
 * @subpackage  com_footballmanager
 * @author      Marco Rensch
 * @since 	    1.0.0
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @copyright   Copyright (C) 2022 nx-designs NXD
 *
 */

namespace NXD\Component\MarathonManager\Administrator\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

defined('_JEXEC') or die;

/**
 * Shows a list of the available Arrival Dates configured in the event
 */
class EventArrivalDatesField extends ListField{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $type = 'EventArrivalDates';
    protected $event_id;

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   1.0.0
     */
    protected function getOptions(): array
    {
        $eventId = $this->form->getValue('event_id');
        $options = [];
        if(!$eventId) return $options;
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('id, date');
        $query->from('#__com_marathonmanager_arrival_dates');
        $query->where('event_id = ' . $eventId);
        $query->order('ordering ASC');
        $db->setQuery($query);
        $dbValues = $db->loadObjectList();

        foreach ($dbValues as $option)
        {
            $options[] = HTMLHelper::_('select.option', $option->id, HtmlHelper::date($option->date, Text::_('DATE_FORMAT_LC5')));
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

}