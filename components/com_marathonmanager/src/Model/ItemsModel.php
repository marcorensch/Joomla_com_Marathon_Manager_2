<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\Hello\Site\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Language\Text;

/**
 * (DB) Event Model
 *
 * @since  1.0.0
 */

class ItemsModel extends BaseDatabaseModel
{
	protected $_items = null;

	public function getItems(){
		$app = Factory::getApplication();

		if($this->_items === null)
		{
			$this->_items = array();
		}

		try{
			$db = $this->getDatabase();
			$query = $db->getQuery(true);
			$now = Factory::getDate()->toSql();

			$query->select('*')
				->from($db->quoteName('#__hello_items', 'a'))
				->where($db->quoteName('a.published') . ' = 1')
				->where('(' . $db->quoteName('a.publish_up') . ' IS NULL OR ' . $db->quoteName('a.publish_up') . ' <= ' . $db->quote($now) . ')')
				->where('(' . $db->quoteName('a.publish_down') . ' IS NULL OR ' . $db->quoteName('a.publish_down') . ' >= ' . $db->quote($now) . ')');

			$db->setQuery($query);
			$data = $db->loadObjectList();

			if(empty($data))
			{
				throw new \Exception(Text::_('COM_MARATHONMANAGER_ITEMS_NOT_FOUND'), 404);
			}

			$this->_items = $data;
		}
		catch(\Exception $e)
		{
			$this->setError($e->getMessage());
			$this->_items = false;
		}

		return $this->_items;
	}

	protected function populateState()
	{
		$app = Factory::getApplication();
		$this->setState('params', $app->getParams());
	}
}