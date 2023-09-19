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
 * Simplepage Model
 *
 * @since  1.0.0
 */

class SimplepageModel extends BaseDatabaseModel
{
	protected $layout;

	public function getLayout(): string
	{
		$app = Factory::getApplication();
		$this->layout = $app->input->get('select_layout', 'list');

		return $this->layout;
	}
}