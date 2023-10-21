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

use Joomla\CMS\Language\Associations;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\QueryInterface;
use Joomla\Utilities\ArrayHelper;

// The use of the list model allows us to simply extend the list model and just ask for the data we need.

/**
 * Methods supporting a list of Team category records.
 *
 * @since  1.0
 */

class GroupsModel extends ListModel
{
	public function __construct($config = [])
	{
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'published', 'a.published',
                'access', 'a.access', 'access_level',
                'ordering', 'a.ordering',
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
			$db->quoteName(['a.id','a.title','a.alias','a.group_id', 'a.ordering', 'a.access', 'a.published'])
		);
		// From the table
		$query->from($db->quoteName('#__com_marathonmanager_groups','a'));

		// Join over the asset groups
		$query->select($db->quoteName('ag.title','access_level'))
			->join(
				'LEFT',
				$db->quoteName('#__viewlevels', 'ag') . ' ON ' . $db->quoteName('ag.id') . ' = ' . $db->quoteName('a.access')
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
                $query->where('(' . $db->quoteName('a.title') . ' LIKE ' . $search . ')');
            }
        }

        // Add the list ordering clause.
        $orderCol  = $this->state->get('list.ordering', 'a.group_id');
        $orderDirn = $this->state->get('list.direction', 'asc');

        $query->order($db->escape($orderCol . ' ' . $orderDirn));

		return $query;
	}

    public function getItems()
    {
        return parent::getItems();
    }
}