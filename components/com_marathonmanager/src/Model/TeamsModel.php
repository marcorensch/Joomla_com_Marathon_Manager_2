<?php

namespace NXD\Component\MarathonManager\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Database\DatabaseInterface;

class TeamsModel extends BaseDatabaseModel
{

    /**
     * @var \Joomla\Database\DatabaseInterface|mixed
     */
    private mixed $event_id;

    public function __construct()
    {
    }

    public function getTeams($eventId = null, $onlyPaid = false)
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);

        $query->select('*')
            ->from($db->quoteName('#__com_marathonmanager_registrations', 'a'));
        // Join over the parcours
        $query->select('c.title AS parcours_title')
            ->join('LEFT', $db->quoteName('#__com_marathonmanager_courses', 'c') . ' ON ' . $db->quoteName('a.course_id') . ' = ' . $db->quoteName('c.id'));
        // Join over the category
        $query->select('d.title AS category_title')
            ->join('LEFT', $db->quoteName('#__com_marathonmanager_groups', 'd') . ' ON ' . $db->quoteName('a.group_id') . ' = ' . $db->quoteName('d.id'));

        if ($eventId) $query->where($db->quoteName('a.event_id') . ' = ' . $db->quote($eventId));
        if ($onlyPaid) $query->where($db->quoteName('a.payment_status') . ' = 1');

        $db->setQuery($query);
        $data = $db->loadObjectList();

        return $data;
    }
}