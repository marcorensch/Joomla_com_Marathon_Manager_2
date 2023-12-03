<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\QueryInterface;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Session\Session;
use NXD\Component\MarathonManager\Administrator\Helper\ImportHelper;
use NXD\Component\MarathonManager\Administrator\Helper\RegistrationHelper;

// The use of the list model allows us to simply extend the list model and just ask for the data we need.

/**
 * Methods supporting a list of Team category records.
 *
 * @since  1.0
 */

class ResultsModel extends ListModel
{
    protected $importData = [];
    protected $importEventId = 0;

	public function __construct($config = [])
	{
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'team_name', 'a.team_name',
                'place','a.place',
                'published', 'a.published',
            );
        }

		parent::__construct($config);
	}

	protected function getListQuery(): QueryInterface|DatabaseQuery
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);
		// Select the required fields from the table.
		$query->select(
			$db->quoteName(
                ['a.id','a.team_name','a.team_id','g.title','p.title', 'a.published', 'a.place','a.place_msg','a.event_id', 'e.title', 'e.event_date'],
                ['id','team_name','team_id','group_title','parcours_title', 'published', 'place','place_msg','event_id', 'event_name', 'event_date']
            )
		);
		// From the table
		$query->from($db->quoteName('#__com_marathonmanager_results','a'));

        // Join over the event
        $query->join('LEFT', $db->quoteName('#__com_marathonmanager_events', 'e') . ' ON ' . $db->quoteName('a.event_id') . ' = ' . $db->quoteName('e.id'));

        // Join over the group
        $query->join('LEFT', $db->quoteName('#__com_marathonmanager_groups', 'g') . ' ON ' . $db->quoteName('a.group_id') . ' = ' . $db->quoteName('g.id'));

        // Join over the parcours
        $query->join('LEFT', $db->quoteName('#__com_marathonmanager_courses', 'p') . ' ON ' . $db->quoteName('a.parcours_id') . ' = ' . $db->quoteName('p.id'));

        // Join over the asset groups
        $query->select($db->quoteName('ag.title','access_level'))
            ->join(
                'LEFT',
                $db->quoteName('#__viewlevels', 'ag') . ' ON ' . $db->quoteName('ag.id') . ' = ' . $db->quoteName('a.access')
            );
        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published))
        {
            $query->where($db->quoteName('a.published') . ' = ' . (int) $published);
        }
        elseif ($published === '*')
        {
            // all filter selected
        }
        else
        {
            // none filter selected by default show published only
            $query->where('(' . $db->quoteName('a.published') . ' IN (0, 1))');
        }

        // Filter by event id
        $eventId = $this->getState('filter.event_id');
        if (is_numeric($eventId))
        {
            $query->where($db->quoteName('a.event_id') . ' = ' . (int) $eventId);
        }

        // Filter by parcours id
        $parcoursId = $this->getState('filter.parcours_id');
        if(is_numeric($parcoursId))
        {
            $query->where($db->quoteName('a.parcours_id') . ' = ' . (int) $parcoursId);
        }

        // Filter by group id
        $groupId = $this->getState('filter.group_id');
        if(is_numeric($groupId))
        {
            $query->where($db->quoteName('a.group_id') . ' = ' . (int) $groupId);
        }

        // Filter by search team name
        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            if (stripos($search, 'id:') === 0)
            {
                $query->where($db->quoteName('a.id') . ' = ' . (int) substr($search, 3));
            }
            else
            {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                $query->where('(' . $db->quoteName('a.team_name') . ' LIKE ' . $search . ')');
            }
        }

        // Add the list ordering clause.ordering
        $orderCol  = $this->state->get('list.ordering', 'a.id');
        $orderDirn = $this->state->get('list.direction', 'asc');

        $query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

    public function getItems()
    {
        // If we sort by place we need to move the "null" values to the end of the list
        if($this->getState('list.ordering') === 'a.place'){
            $items = parent::getItems();
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

        return parent::getItems();
    }

    public function processData($formData, $fileData): bool
    {

        // Check if we have data
        if(!count($fileData)) {
            throw new \Exception(Text::_('COM_MARATHONMANAGER_ERROR_NO_DATA'), 500);
        }

        // Define Import Map
        $columns = [];
        foreach ($formData['import_map'] as $index => $value) {
            if(!$value) continue;
            $importConfig = new \stdClass();
            $importConfig->index = $index;
            $importConfig->dbColumn = $value;
            $columns[] = $importConfig;
        }
        $dataToStore = $this->prepareData($fileData, $columns, $formData['main_import_trigger_column'], $formData['event_id']);
//        error_log('Data to store: ' . print_r($dataToStore, true));
        return $this->store($dataToStore);
    }

    private function prepareData($fileData, $columns, $importTriggerColumn, $eventId):array
    {
        // Get the public access level.
        $publicAccessId = ImportHelper::getPublicAccessLevel() ?: null;
        // Group Id's
        $groupIds = ImportHelper::getGroupIds();
        $parcoursIds = ImportHelper::getParcourIds();
        error_log('Parcours Ids: ' . print_r($parcoursIds, true));

        $dataToStore = [];
        // Loop over data
        $rowCounter = 0;
        foreach ($fileData as $resultRow) {
            $rowCounter++;
            if(!$resultRow[$importTriggerColumn]){
//                error_log('No value for trigger column found in row ' . $rowCounter);
                continue;
            }
            $rowForDb = new \stdClass();
            foreach ($columns as $column) {
                // if column is place, and we have no integer store as "place_msg"
                if($column->dbColumn === 'place') {
                    // set to empty string as default for place_msg custom column
                    $rowForDb->{$column->dbColumn . '_msg'} = "";
                    if (!$resultRow[$column->index] || !is_numeric($resultRow[$column->index])) {
                        $rowForDb->{$column->dbColumn . '_msg'} =  trim($resultRow[$column->index]);
                        continue;
                    }
                }
                if($column->dbColumn === 'category'){
                    // SPECIAL CASE! >> TimeyBoy sends the category as 1.D, 4.M etc. so we need to split it BUT Women are listed as "D" and not "W"
                    $category = explode('.', $resultRow[$column->index]);
                    if($category[1] === 'D'){
                        $category[1] = 'W';
                    }
                    // first part is the place in group
                    $rowForDb->place_in_group = intval($category[0]) ?: null;
                    // second part is the group ShortCode, so we need to get the group id
                    $rowForDb->group_id = $groupIds[$category[1]]->id ?: null;
                }
                // all other columns
                if(trim($resultRow[$column->index])){
                    $rowForDb->{$column->dbColumn} = trim($resultRow[$column->index]);
                }
            }
            $rowForDb->event_id = $eventId;
            $rowForDb->access = $publicAccessId;
            // Map the team based on the team name with the registration
            if(isset($rowForDb->team_name)){
                $rowForDb->team_id = $this->getTeamIdFromRegistration($rowForDb->team_name, $eventId) ?: null;
            }
            // Set the parcours id based on the Start number 5xx means parcours with id 5 3xx means parcours with id 3, ...
            if(isset($rowForDb->start_number)) {
                $parcoursNumber = substr($rowForDb->start_number, 0, 1);
                $rowForDb->parcours_id = $parcoursIds[$parcoursNumber]->id ?: null;
            }
            $rowForDb->created_by = Factory::getApplication()->getIdentity()->id;
            $rowForDb->created = Factory::getDate()->toSql();
            $dataToStore[] = $rowForDb;
        }
        return $dataToStore;
    }

    private function getTeamIdFromRegistration($teamName, $eventId): int
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select($db->quoteName('id'))
            ->from($db->quoteName('#__com_marathonmanager_registrations'))
            ->where($db->quoteName('team_name') . ' = ' . $db->quote($teamName))
            ->where($db->quoteName('event_id') . ' = ' . $db->quote($eventId));
        $db->setQuery($query);
        return (int)$db->loadResult() ?: 0;
    }

    private function store($dataset): bool
    {
        $db = $this->getDatabase();
        foreach ($dataset as $row) {
            // Insert the object into the results table.
            try {
                $result = $db->insertObject('#__com_marathonmanager_results', $row);
            }catch (\Exception $e){
                error_log('Error while inserting row: ' . print_r($row, true));
                error_log('Error: ' . $e->getMessage());
                return false;
            }
        }
        return true;

    }
}