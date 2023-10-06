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
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;

use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;
use Joomla\CMS\HTML\HTMLHelper;

class EventModel extends \Joomla\CMS\MVC\Model\AdminModel
{

	public $typeAlias = 'com_marathonmanager.event';

	/**
	 * @inheritDoc
	 */
	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm($this->typeAlias, 'event', ['control' => 'jform', 'load_data' => $loadData]);

		if(empty($form)){
			return false;
		}

		return $form;
	}

	protected function loadFormData()
	{
		$app = Factory::getApplication();

		$data = $app->getUserState('com_marathonmanager.edit.event.data', []);

		if (empty($data)) {
			$data = $this->getItem();

			// Prime some default values.
			if ($this->getState('event.id') == 0) {
				$data->set('catid', $app->input->get('catid', $app->getUserState('com_marathonmanager.events.filter.category_id'), 'int'));
			}
		}

		$this->preprocessData($this->typeAlias, $data);

		return $data;
	}


	protected function prepareTable($table)
	{
		$table->generateAlias();
	}

    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);

        // Load the "linked arrival dates" data
        if($item->id > 0) $item->arrival_dates = $this->getArrivalDates($item->id);

        return $item;
    }

    public function save($data)
    {
        $app   = Factory::getApplication();
        $input = $app->getInput();
        $user  = $app->getIdentity();

        // new element tasks
        if (!isset($data['id']) || (int) $data['id'] === 0)
        {
            $data['created_by'] = $user->id;
        }

        $data['modified_by'] = $user->id;

        // Alter the title for save as copy
        if ($input->get('task') == 'save2copy')
        {
            $origTable = $this->getTable();

            if ($app->isClient('site'))
            {
                $origTable->load($input->getInt('a_id'));

                if ($origTable->title === $data['title'])
                {
                    /**
                     * If title of article is not changed, set alias to original article alias so that Joomla! will generate
                     * new Title and Alias for the copied article
                     */
                    $data['alias'] = $origTable->alias;
                }
                else
                {
                    $data['alias'] = '';
                }
            }
            else
            {
                $origTable->load($input->getInt('id'));
            }

            if ($data['title'] == $origTable->title)
            {
                list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
                $data['title'] = $title;
                $data['alias'] = $alias;
            }
            elseif ($data['alias'] == $origTable->alias)
            {
                $data['alias'] = '';
            }
        }

        // Automatic handling of alias for empty fields
        if (in_array($input->get('task'), ['apply', 'save', 'save2new']) && $data['alias'] == null)
        {
            if ($app->get('unicodeslugs') == 1)
            {
                $data['alias'] = OutputFilter::stringUrlUnicodeSlug($data['title']);
            }
            else
            {
                $data['alias'] = OutputFilter::stringURLSafe($data['title']);
            }

            $table = $this->getTable();

            if ($table->load(['alias' => $data['alias'], 'catid' => $data['catid']]))
            {
                $msg = Text::_('COM_MARATHONMANAGER_SAVE_WARNING');
            }

            list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
            $data['alias'] = $alias;

            if (isset($msg))
            {
                $app->enqueueMessage($msg, 'warning');
            }
        }

        // Handle Subforms & MultiSelect Fields on save in foreach loop
        $multiSelectFields = ['attachments', 'result_files', 'arrival_options', 'team_categories'];
        foreach ($multiSelectFields as $fieldName)
        {
            if (isset($data[$fieldName]) && is_array($data[$fieldName]))
            {
                $registry = new Registry;
                $registry->loadArray($data[$fieldName]);
                $data[$fieldName] = (string) $registry;
            }
        }

        $defaultNullFields = ['publish_up', 'publish_down', 'registration_start_date', 'registration_end_date', 'event_date', 'earlybird_fee','price_per_map','lastinfos_newsletter_list_id'];
        foreach ($defaultNullFields as $fieldName)
        {
            if (isset($data[$fieldName]) && empty($data[$fieldName]))
            {
                $data[$fieldName] = null;
            }
        }

        $status = parent::save($data);

        if($status) $this->handleArrivalDates($data);

        return $status;
    }

    private function getEventIdByAlias($alias){
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__com_marathonmanager_events');
        $query->where('alias = ' . $db->quote($alias));
        $db->setQuery($query);
        return $db->loadResult();
    }

    /**
     * @throws \Exception
     */
    private function handleArrivalDates($data): void
    {
        // Check if we have an ID if not we have added a new event get the id by alias
        $eventId = $data['id'] ?: $this->getEventIdByAlias($data['alias']);

        $storedArrivalDates = $this->getArrivalDates($eventId); // Get the already stored arrival dates before potentially add new ones
        $updatedArrivalDates = [];

        if($data['arrival_dates']) {
            $arrival_dates = $data['arrival_dates'];
            foreach ($arrival_dates as $key => $date_option) {
                if($date_option['id'] == 0) {
                    $this->storeNewArrivalOption($date_option, $eventId);
                }else{
                    $this->updateArrivalOption($date_option);
                    $updatedArrivalDates[] = $date_option['id'];
                }
            }
        }

        // Delete all arrival dates that are not in the updated arrival dates array
        foreach ($storedArrivalDates as $date) {
            if(!in_array($date->id, $updatedArrivalDates)){
                if(!$this->deleteArrivalOption($date->id)){
                    Factory::getApplication()->enqueueMessage('Could not delete arrival date ' . HtmlHelper::date($date->date, Text::_('DATE_FORMAT_FILTER_DATETIME')), 'error');
                }
            }
        }
    }

    private function deleteArrivalOption($id){
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $conditions = array(
            $db->quoteName('id') . ' = ' . $db->quote($id)
        );
        $query->delete($db->quoteName('#__com_marathonmanager_arrival_dates'))->where($conditions);
        $db->setQuery($query);
        return $db->execute();
    }

    private function getArrivalDates($eventId){
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('id, date, ordering')
            ->from('#__com_marathonmanager_arrival_dates')
            ->where('event_id = ' . $eventId)
            ->order('ordering ASC')
            ->order('id ASC');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    private function storeNewArrivalOption($option, $eventId): void
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $columns = array('event_id','date', 'ordering');
        $values = array($db->quote($eventId), $db->quote($option['date']), $db->quote(intval($option['ordering'])));
        $query->insert($db->quoteName('#__com_marathonmanager_arrival_dates'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));
        $db->setQuery($query);
        $db->execute();
    }

    private function updateArrivalOption($option){
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('date') . ' = ' . $db->quote($option['date']),
            $db->quoteName('ordering') . ' = ' . $db->quote(intval($option['ordering']))
        );
        $conditions = array(
            $db->quoteName('id') . ' = ' . $db->quote($option['id'])
        );
        $query->update($db->quoteName('#__com_marathonmanager_arrival_dates'))->set($fields)->where($conditions);
        $db->setQuery($query);
        $db->execute();
    }

    public function getEventEnabledTeamCategories($eventId){
        $ids = [];
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('team_categories')
            ->from('#__com_marathonmanager_events')
            ->where('id = ' . $eventId);
        $db->setQuery($query);
        $dbValues = $db->loadAssocList();
        $array = json_decode($dbValues[0]['team_categories']);
        foreach ($array as $item) array_push($ids, $item);
        $commaSeparatedListOfIds = implode(',', $ids);

        $query = $db->getQuery(true);
        $query->select('id, title')
            ->from('#__com_marathonmanager_team_categories')
            ->where('published = 1');
            if($commaSeparatedListOfIds) $query->where('id IN (' . $commaSeparatedListOfIds . ')');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public function getEventArrivalDates($eventId){
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('id, date');
        $query->from('#__com_marathonmanager_arrival_dates');
        $query->where('event_id = ' . $eventId);
        $query->order('ordering ASC');
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}