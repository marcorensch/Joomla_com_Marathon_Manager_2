<?php

namespace NXD\Component\MarathonManager\Site\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use NXD\Component\MarathonManager\Site\Helper\RegistrationHelper;

class MyRegistrationsModel extends BaseDatabaseModel
{

    public function getMyRegistrations(): array
    {
        $userId = Factory::getApplication()->getIdentity()->id;
        if(!$userId) return [];

        $db = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select(['a.*', 'b.title AS event_title', 'b.event_date', 'b.event_duration','d.date AS arrival_date', 'c.title AS arrival_option_title'])
            ->from($db->quoteName('#__com_marathonmanager_registrations', 'a'))
            ->where($db->quoteName('a.created_by') . ' = ' . $db->quote($userId));

        //Join over the Event data
        $query->join('LEFT', $db->quoteName('#__com_marathonmanager_events', 'b') . ' ON (' . $db->quoteName('a.event_id') . ' = ' . $db->quoteName('b.id') . ')');

        //Join over the Arrival Date
        $query->join('LEFT', $db->quoteName('#__com_marathonmanager_arrival_dates', 'd') . ' ON (' . $db->quoteName('a.arrival_date_id') . ' = ' . $db->quoteName('d.id') . ')');

        //Join over Arrival Option
        $query->join('LEFT', $db->quoteName('#__com_marathonmanager_arrival_options', 'c') . ' ON (' . $db->quoteName('a.arrival_option_id') . ' = ' . $db->quoteName('c.id') . ')');
        // Order the results by place int
        $query->order($db->quoteName('b.event_date') . ' DESC');
        $db->setQuery($query);
        $registrations = $db->loadObjectList();

        foreach ($registrations as $registration) {
            $registration->participants = json_decode($registration->participants);
            $registration->course = RegistrationHelper::getCourse($registration->course_id);
            $registration->group = RegistrationHelper::getGroup($registration->group_id);

            $registration->paymentInformation = RegistrationHelper::getPaymentInformation($registration->event_id, $registration->created);
        }

        return $registrations;
    }

}