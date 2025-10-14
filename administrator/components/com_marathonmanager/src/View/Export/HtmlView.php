<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\View\Export;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarFactoryInterface;
use Joomla\CMS\Toolbar\ToolbarHelper;
use NXD\Component\MarathonManager\Administrator\Model\ExportModel;

class HtmlView extends BaseHtmlView
{
    public function display($tpl = null): void
    {
		/** @var ExportModel $model */
	    $model = $this->getModel();
        $this->form = $model->getForm();

		$this->addToolbar();

        parent::display($tpl);
    }

	protected function addToolbar(): void
	{
		Factory::getApplication()->input->set('hidemainmenu', false);
        $user = Factory::getApplication()->getIdentity();
		$toolbar = $this->getDocument()->getToolbar();

        ToolbarHelper::title(Text::_('COM_MARATHONMANAGER_EXPORT'), 'fas fa-file-download');
        $canExport = $user->authorise('marathonmanager.export', 'com_marathonmanager');

        if ($user->authorise('core.admin', 'com_marathonmanager') || $user->authorise('core.options', 'com_marathonmanager'))
        {
            $toolbar->preferences('com_marathonmanager');
        }

	}
}