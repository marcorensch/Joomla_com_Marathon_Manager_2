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
 * Methods supporting a list of Registration records.
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
                'published', 'a.published',
                'payment_status', 'a.payment_status',
                'course_id', 'a.course_id', 'course_name',
                'group_id', 'a.group_id', 'group_name',
                'event_id', 'a.event_id', 'event_name',
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
			$db->quoteName(['a.id','a.team_name','a.published', 'a.alias', 'a.ordering', 'a.access', 'a.payment_status','a.created', 'a.reference', 'a.participants'])
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
        // Join over the parcours / course name
        $query->select(
            array(
                $db->quoteName('course.title','course_name'),
                $db->quoteName('course.course_id','course_id')
            )
        )
            ->join(
                'LEFT',
                $db->quoteName('#__com_marathonmanager_courses', 'course') . ' ON ' . $db->quoteName('course.id') . ' = ' . $db->quoteName('a.course_id')
            );
        // Join over the group / category name
        $query->select(
            array(
                $db->quoteName('group.title','group_name'),
                $db->quoteName('group.group_id','group_id')
            )
        )
            ->join(
                'LEFT',
                $db->quoteName('#__com_marathonmanager_groups', 'group') . ' ON ' . $db->quoteName('group.id') . ' = ' . $db->quoteName('a.group_id')
            );
        // Filter by access level.
        if ($access = $this->getState('filter.access'))
        {
            $query->where($db->quoteName('a.access') . ' = ' . (int) $access);
        }

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

        // Filter by a team category.
        $courseId = $this->getState('filter.course_id');
        if (is_numeric($courseId))
        {
            $query->where($db->quoteName('a.course_id') . ' = ' . (int) $courseId);
        }
        elseif (is_array($courseId))
        {
            $courseId = ArrayHelper::toInteger($courseId);
            $courseId = implode(',', $courseId);
            $query->where($db->quoteName('a.course_id') . ' IN (' . $courseId . ')');
        }

        // Filter by search team name / Reference
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
                $query->orWhere('(' . $db->quoteName('a.reference') . ' LIKE ' . $search . ')');
                $query->orWhere('(' . $db->quoteName('a.participants') . ' LIKE ' . $search . ')');
            }
        }

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.created');
        $orderDirn = $this->state->get('list.direction', 'desc');

        $query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

    public function getItems()
    {
        $items = parent::getItems();
        foreach ($items as &$item)
        {
            $item->participants = json_decode($item->participants);
        }

        return $items;
    }
}