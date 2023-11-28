<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\View\Import;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	protected $form;
	protected $item;
    protected $type;
    protected $cancelContext;
    public function display($tpl = null): void
    {
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
        $this->type = Factory::getApplication()->input->get('type', 'default');
        $this->cancelContext = Factory::getApplication()->input->get('context', null);

		$this->addToolbar();

        parent::display($tpl);
    }

	protected function addToolbar(): void
	{
		Factory::getApplication()->input->set('hidemainmenu', false);

        $user = Factory::getApplication()->getIdentity();

        ToolbarHelper::title(Text::_('COM_MARATHONMANAGER_IMPORT_' . strtoupper($this->getLayout())), 'fas fa-calendar');

        if($this->cancelContext){
            ToolbarHelper::cancel( $this->cancelContext . '.cancel', 'JTOOLBAR_CLOSE');
        }
	}
}