<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\Model;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use NXD\Component\MarathonManager\Site\Model\RegistrationModel;

/**
 * (DB) Event Model
 *
 * @since  1.0.0
 */

class ResultsModel extends BaseDatabaseModel
{

    public function getResults($eventId): array
    {
        if(empty($eventId)){
            throw new \Exception(Text::_('COM_MARATHONMANAGER_EVENT_NOT_DEFINED'), 424);
        }
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(['a.*', 'b.participants'])
            ->from($db->quoteName('#__com_marathonmanager_results', 'a'))
            ->where($db->quoteName('a.event_id') . ' = ' . $db->quote($eventId));

        //Join over the Registration data if registration_id is set
        $query->join('LEFT', $db->quoteName('#__com_marathonmanager_registrations', 'b') . ' ON (' . $db->quoteName('a.registration_id') . ' = ' . $db->quoteName('b.id') . ')');

        // Order the results by place int
        $query->order($db->quoteName('a.place') . ' ASC');
        $db->setQuery($query);
        $items = $db->loadObjectList();

        // Decode Participants
        foreach ($items as $item) {
            if($item->participants) $item->participants = json_decode($item->participants, true);
        }

        // Move the results where the place is "NULL" to the end of the array
        $nullItems = [];
        foreach ($items as $index => $item) {
            if(!$item->place){
                $nullItems[] = $item;
                unset($items[$index]);
            }
        }
        $items = array_merge($items, $nullItems);

        return $items;
    }
}