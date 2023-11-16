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
                'published', 'a.published',
                'created_by', 'a.created_by',
                'modified_by', 'a.modified_by',
                'created', 'a.created',
                'modified', 'a.modified',
            );

            $assoc = Associations::isEnabled();
            if ($assoc)
            {
                $config['filter_fields'][] = 'association';
            }
        }

		parent::__construct($config);
	}

	protected function getListQuery(): QueryInterface|DatabaseQuery
	{
		$db = $this->getDatabase();
		$query = $db->getQuery(true);
		// Select the required fields from the table.
		$query->select(
			$db->quoteName(['a.id','a.team_name','a.group_id', 'a.published'])
		);
		// From the table
		$query->from($db->quoteName('#__com_marathonmanager_results','a'));

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
        $orderDirn = $this->state->get('list.direction', 'desc');

        $query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

    public function getItems()
    {
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
        error_log(print_r($columns, true));
        error_log(print_r($formData, true));
    }
}