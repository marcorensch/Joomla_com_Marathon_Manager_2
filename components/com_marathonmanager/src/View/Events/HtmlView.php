<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\View\Events;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
	protected $items;
    protected $params;

	public function display($tpl = null): void
	{
        $state = $this->get('State');
        $this->items = $this->get('Events');
        $this->params = $state->get('params');

		parent::display($tpl);
	}
}