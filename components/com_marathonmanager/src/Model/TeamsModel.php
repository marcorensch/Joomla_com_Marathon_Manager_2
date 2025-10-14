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

    public function getTeams($eventId = null, $onlyPaid = false, $limit = null)
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
        if($limit) $query->setLimit($limit);

        $db->setQuery($query);
        $teams = $db->loadObjectList();

		foreach($teams as $teamData){
			$this->prepareTeamData($teamData);
		}

        return $teams;
    }

	private function prepareTeamData($teamData){
		$teamData->participants = $this->prepareParticipants($teamData->participants);
	}

	private function prepareParticipants(string $participants){
		try {
			$participants = json_decode($participants, true);
			// Prüfe auf JSON-Decode-Fehler
			if (json_last_error() !== JSON_ERROR_NONE) {
				return array();
			}
			// Stelle sicher, dass es ein Array ist
			if(!is_array($participants)) $participants = array();
		} catch (\Exception $e) {
			$participants = array();
		}
		return $participants;
	}
}