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

namespace NXD\Component\MarathonManager\Site\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

defined('_JEXEC') or die;

class CoursesField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $type = 'Courses';
    protected $eventId;

    protected function getInput()
    {
        // Get the current EventID
        $app = Factory::getApplication();
        $this->eventId = $app->input->getInt('event_id');
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
        $options = [];
        $ids = null;

        if (!empty($this->eventId)) {
            $ids = $this->getEventParcoursIds();
        }

        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('id, title');
        $query->from('#__com_marathonmanager_courses');
        $query->where('published = 1');
        if (!empty($ids)) {
            $query->where('id IN (' . $ids . ')');
        }
        $query->order('ordering ASC');
        $db->setQuery($query);
        $dbValues = $db->loadObjectList();

        foreach ($dbValues as $option) {
            $options[] = HTMLHelper::_('select.option', $option->id, $option->title);
        }

        return $options;
    }

    protected function getEventParcoursIds(): string
    {
        $ids = [];
        $groups = $this->getGroups();
        $parcourGroups = [];

        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('parcours');
        $query->from('#__com_marathonmanager_events');
        $query->where('id = ' . $this->eventId);
        $db->setQuery($query);
        $parcours = $db->loadObject();

        // Get the Parcours IDs from the JSON string
        $parcours = json_decode($parcours->parcours);
        foreach ($parcours as $key => $parcour) {
            $ids[] = $parcour->course_id;
            // Add the groups to the parcour to be later used in JS
            $parcourGroup = new \stdClass();
            $parcourGroup->parcour_id = $parcour->course_id;
            $parcourGroup->group_ids = array();

            foreach($parcour->group_id as $groupId) {
                $parcourGroup->group_ids[] = $groups[$groupId];
            }

            $parcourGroups[$parcour->course_id] = $parcourGroup;
        }

        // Append JS to site
        $this->appendJavascript(json_encode($ids), json_encode($parcourGroups));


        // Return the IDs as comma separated string for the query
        return implode(',', $ids);

    }

    protected function getGroups(): array
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('id, title, max_participants as max');
        $query->from('#__com_marathonmanager_groups');
        $query->where('published = 1');
        $query->order('ordering ASC');
        $db->setQuery($query);
        return $db->loadObjectList($key = 'id');

    }

    protected function appendJavascript($parcourIds, $parcourGroups): void
    {
        Text::script('COM_MARATHONMANAGER_SELECT_GROUP');
        Text::script('COM_MARATHONMANAGER_MAX_REACHED');
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->addInlineScript('const parcoursIds = ' . json_encode($parcourIds) . ';' . 'const parcoursGroups = ' . json_encode($parcourGroups) . ';', ['type' => 'text/javascript']);
        $wa->useScript('com_marathonmanager.parcours-groups');
    }
}