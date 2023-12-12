<?php
/**
 * @package     Joomla.Administrator
 *              Joomla.Site
 * @subpackage  com_marathonmanager
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

class EventsField extends ListField{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $type = 'Events';

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   1.0.0
     */

    protected $direction = 'asc';
    protected function getOptions(): array
    {
        // Get the direction from the xml form field
        $direction = $this->element['direction'] ?? 'asc';
        $this->direction = $direction;

        $options = [];
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('id, title');
        $query->from('#__com_marathonmanager_events');
//        $query->where('published = 1');
        $query->order('event_date ' . $this->direction . ', id ' . $this->direction);
        $db->setQuery($query);
        $dbValues = $db->loadObjectList();

        foreach ($dbValues as $option)
        {
            $options[] = HTMLHelper::_('select.option', $option->id, $option->title);
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

}