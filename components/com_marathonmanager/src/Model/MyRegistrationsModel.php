<?php

namespace NXD\Component\MarathonManager\Site\Model;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class MyRegistrationsModel extends BaseDatabaseModel
{

    public function getMyRegistrations($userId): array
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(['a.*', 'b.title', 'b.event_date'])
            ->from($db->quoteName('#__com_marathonmanager_registrations', 'a'))
            ->where($db->quoteName('a.user_id') . ' = ' . $db->quote($userId));

        //Join over the Event data
        $query->join('LEFT', $db->quoteName('#__com_marathonmanager_events', 'b') . ' ON (' . $db->quoteName('a.event_id') . ' = ' . $db->quoteName('b.id') . ')');

        // Order the results by place int
        $query->order($db->quoteName('b.event_date') . ' DESC');
        $db->setQuery($query);
        $items = $db->loadObjectList();

        return $items;
    }

}