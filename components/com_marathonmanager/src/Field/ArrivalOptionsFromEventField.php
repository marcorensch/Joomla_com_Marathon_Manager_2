<?php
/**
 * @package     Joomla.Administrator
 *              Joomla.Site
 * @subpackage  com_marathonmanager
 * @author      Marco Rensch
 * @since 	    1.0.0
 *
 * @description This field is used to select an arrival option from the event
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @copyright   Copyright (C) 2022 nx-designs NXD
 *
 */

namespace NXD\Component\MarathonManager\Site\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

defined('_JEXEC') or die;

class ArrivalOptionsFromEventField extends ListField{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $type = 'ArrivalOptionsFromEvent';
    protected $eventId = null;

    // Get the xml form field attributes
    protected function getInput()
    {
        // Get the current EventID
        $this->eventId = $this->form->getValue('event_id');
        if(!$this->eventId) return Text::_('COM_MARATHONMANAGER_FIELD_OPT_SELECT_EVENT_FIRST');
        return parent::getInput();
    }

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   1.0.0
     */
    protected function getOptions(): array
    {
        $options = $this->getArrivalOptionsFromEvent();

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

    protected function getArrivalOptionsFromEvent():array
    {
        $options = [];
        if(!$this->eventId) return $options;
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select(array('event.arrival_options'));
        $query->from($db->quoteName('#__com_marathonmanager_events', 'event'));
        $query->where('event.id = ' . $this->eventId);
        $db->setQuery($query);
        $dbValues = $db->loadObjectList();

        if(!$dbValues) return $options;

        $arrivalOptionIds = json_decode($dbValues[0]->arrival_options, true);

        return $this->buildArrivalOptionsSelect($arrivalOptionIds);
    }

    protected function buildArrivalOptionsSelect($ids)
    {
        $options = [];
        if(!$ids) return $options;
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select(array('arrival_option.title', 'arrival_option.id'));
        $query->from($db->quoteName('#__com_marathonmanager_arrival_options', 'arrival_option'));
        $query->where('arrival_option.id IN (' . implode(',', $ids) . ')');
        $db->setQuery($query);
        $dbValues = $db->loadObjectList();

        if(!$dbValues) return $options;

        foreach ($dbValues as $option)
        {
            $options[] = HTMLHelper::_('select.option', $option->id, Text::_($option->title));
        }

        return $options;
    }

}