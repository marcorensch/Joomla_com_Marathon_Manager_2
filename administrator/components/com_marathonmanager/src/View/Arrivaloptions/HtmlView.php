<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\View\Arrivaloptions;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarFactoryInterface;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Helper\ContentHelper;

class HtmlView extends BaseHtmlView
{
	protected $items;
    public function display($tpl = null): void
    {
        $this->items            = $this->get('Items');
        $this->pagination       = $this->get('Pagination');
        $this->filterForm       = $this->get('FilterForm');
        $this->activeFilters    = $this->get('ActiveFilters');
        $this->state            = $this->get('State');

		if(!count($this->items) && $this->get('IsEmptyState'))
		{
			$this->setLayout('emptystate');
		}

		$this->addToolbar();

        parent::display($tpl);
    }

	protected function addToolbar(): void
	{
		$canDo = ContentHelper::getActions('com_marathonmanager');

		ToolbarHelper::title(Text::_('COM_MARATHONMANAGER_ARRIVAL_OPTIONS_TITLE'), 'fas fa-car');

		// Add New Button if user has permissions to create
		if($canDo->get('core.create')){
			ToolbarHelper::addNew('arrivaloption.add');
		}

		// Add Options Button if user has permissions to edit
		if($canDo->get('core.options')){
			ToolbarHelper::preferences('com_marathonmanager');
		}

	}
}