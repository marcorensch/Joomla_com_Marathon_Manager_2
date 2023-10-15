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
 * Selection for Team Categories that are available for the selected event
 * requires the event_id to be set in the form
 */

class EventTeamCategoriesField extends ListField{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $type = 'EventTeamCategories';
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
        	// get event_id from form
        $form = $this->form;
        $this->event_id = $form->getValue('event_id');
        if(!$this->event_id) return array();
        $dbValues = $this->getEventEnabledTeamCategories($this->event_id);

        $options[] = HTMLHelper::_('select.option', "", Text::_('COM_MARATHONMANAGER_FIELD_TEAM_CATEGORY_NOT_SET'));

        foreach ($dbValues as $option)
        {
            $options[] = HTMLHelper::_('select.option', $option->id, $option->title);
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

    protected function getEventEnabledTeamCategories($eventId){
        $ids = [];
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('team_categories');
        $query->from('#__com_marathonmanager_events');
        $query->where('id = ' . $eventId);
        $db->setQuery($query);
        $dbValues = $db->loadAssocList();
        if(!$dbValues) return array();
        $array = json_decode($dbValues[0]['team_categories']);
        foreach ($array as $item) array_push($ids, $item);
        $commaSeparatedListOfIds = implode(',', $ids);
        return $this->getTeamCategories($commaSeparatedListOfIds);
    }

    private function getTeamCategories($listOfIds){
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('id, title');
        $query->from('#__com_marathonmanager_team_categories');
        $query->where('id IN (' . $listOfIds . ')');
        $query->where('published = 1');
        $query->order('ordering ASC');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

}