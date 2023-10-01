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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\QueryInterface;
use Joomla\Utilities\ArrayHelper;

// The use of the list model allows us to simply extend the list model and just ask for the data we need.

/**
 * Methods supporting a list of Hello events records.
 *
 * @since  1.0
 */

class RegistrationsModel extends ListModel
{
	public function __construct($config = [])
	{
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'team_name', 'a.team_name',
                'access', 'a.access', 'access_level',
                'ordering', 'a.ordering',
                'created_by', 'a.created_by',
                'modified_by', 'a.modified_by',
                'created', 'a.created',
                'modified', 'a.modified',
                'team_category', 'a.team_category_id',
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
			$db->quoteName(['a.id','a.team_name','a.alias', 'a.ordering', 'a.access', 'a.payment_status','a.created', 'a.reference'])
		);
		// From the table
		$query->from($db->quoteName('#__com_marathonmanager_registrations','a'));

		// Join over the asset groups
		$query->select($db->quoteName('ag.title','access_level'))
			->join(
				'LEFT',
				$db->quoteName('#__viewlevels', 'ag') . ' ON ' . $db->quoteName('ag.id') . ' = ' . $db->quoteName('a.access')
			);

        // Join over the event name
        $query->select($db->quoteName('e.title','event_name'))
            ->join(
                'LEFT',
                $db->quoteName('#__com_marathonmanager_events', 'e') . ' ON ' . $db->quoteName('e.id') . ' = ' . $db->quoteName('a.event_id')
            );
        // Join over the team categories
        $query->select($db->quoteName('tc.title','team_category'))
            ->join(
                'LEFT',
                $db->quoteName('#__com_marathonmanager_team_categories', 'tc') . ' ON ' . $db->quoteName('tc.id') . ' = ' . $db->quoteName('a.team_category_id')
            );
        // Filter by access level.
        if ($access = $this->getState('filter.access'))
        {
            $query->where($db->quoteName('a.access') . ' = ' . (int) $access);
        }

        // Filter by Payment Status
        $paymentStatus = $this->getState('filter.payment_status');
        if (is_numeric($paymentStatus))
        {
            $query->where($db->quoteName('a.payment_status') . ' = ' . (int) $paymentStatus);
        }

        // Filter by Event ID
        $eventID = $this->getState('filter.event_id');
        if (is_numeric($eventID))
        {
            $query->where($db->quoteName('a.event_id') . ' = ' . (int) $eventID);
        }

        // Filter by a single or group of team categories.
        $teamCategoryID = $this->getState('filter.team_category_id');
        if (is_numeric($teamCategoryID))
        {
            $query->where($db->quoteName('a.team_category_id') . ' = ' . (int) $teamCategoryID);
        }
        elseif (is_array($teamCategoryID))
        {
            $teamCategoryID = ArrayHelper::toInteger($teamCategoryID);
            $teamCategoryID = implode(',', $teamCategoryID);
            $query->where($db->quoteName('a.team_category_id') . ' IN (' . $teamCategoryID . ')');
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

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.created');
        $orderDirn = $this->state->get('list.direction', 'desc');

        $query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}
}