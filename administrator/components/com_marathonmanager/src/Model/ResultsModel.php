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
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\QueryInterface;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Session\Session;

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
                ['a.id','a.team_name','a.team_id','a.group_id', 'a.published', 'a.place','a.place_msg','a.event_id', 'e.title', 'e.event_date'],
                ['id','team_name','team_id','group_id', 'published', 'place','place_msg','event_id', 'event_name', 'event_date']
            )
		);
		// From the table
		$query->from($db->quoteName('#__com_marathonmanager_results','a'));

        // Join over the event
        $query->join('LEFT', $db->quoteName('#__com_marathonmanager_events', 'e') . ' ON ' . $db->quoteName('a.event_id') . ' = ' . $db->quoteName('e.id'));

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

        // Filter by search title
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

    public function processData($formData, $fileData){

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
        $this->store($dataToStore);
    }

    private function prepareData($fileData, $columns, $importTriggerColumn, $eventId):array
    {
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
                    continue; // @TODO: Implement Category handling
                }
                // all other columns
                if(trim($resultRow[$column->index])){
                    $rowForDb->{$column->dbColumn} = trim($resultRow[$column->index]);
                }
            }
            $rowForDb->event_id = $eventId;
            $dataToStore[] = $rowForDb;
        }
        return $dataToStore;
    }

    private function store($dataset): void
    {
        $db = $this->getDatabase();
        foreach ($dataset as $row) {
            // Insert the object into the user profile table.
            try {
                $result = $db->insertObject('#__com_marathonmanager_results', $row);
            }catch (\Exception $e){
                error_log('Error while inserting row: ' . print_r($row, true));
                error_log('Error: ' . $e->getMessage());
            }
        }

    }

    public function getCancelContext(): string
    {
        return 'result';
    }
}