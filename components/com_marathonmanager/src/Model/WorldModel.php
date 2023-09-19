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

/**
 * World Model
 *
 * @since  1.0.0
 */

class WorldModel extends BaseDatabaseModel
{
	protected $message;

	public function getMsg(): string
	{
		$app = Factory::getApplication();
		$this->message = $app->input->get('show_text', 'Hello World from input default value');

		return $this->message;
	}

	protected function populateState()
	{
		$app = Factory::getApplication();
		$this->setState('world.id', $app->input->getInt('id'));
		$this->setState('params', $app->getParams());
	}
}