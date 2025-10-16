<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\View\Country;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarFactoryInterface;
use Joomla\CMS\Toolbar\ToolbarHelper;
use NXD\Component\MarathonManager\Administrator\Model\CountryModel;

class HtmlView extends BaseHtmlView
{
    protected $form;
    protected $item;

    public function display($tpl = null): void
    {
		/* @var CountryModel $model */
	    $model = $this->getModel();
        $this->form = $model->getForm();
        $this->item = $model->getItem();

        $this->addToolbar();

        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        Factory::getApplication()->input->set('hidemainmenu', true);
        $user = Factory::getApplication()->getIdentity();
        $isNew = (!$this->item->id);
        $toolbarButtons = [];
        ToolbarHelper::title($isNew ? Text::_('COM_MARATHONMANAGER_COUNTRY_NEW') : Text::_('COM_MARATHONMANAGER_COUNTRY_EDIT'), 'fas fa-globe');

        // Build the actions for new and existing records.
        if ($isNew) {
            if ($user->authorise('core.create', 'com_marathonmanager') || count($user->getAuthorisedCategories('com_marathonmanager', 'core.create')) > 0) {
                ToolbarHelper::apply('country.apply');
                $toolbarButtons = [
                    ['save', 'country.save'],
                    ['save2new', 'country.save2new'],
                    ['save2copy', 'country.save2copy']
                ];
            }
        } else {
            $itemEditable = $user->authorise('core.edit', 'com_marathonmanager') || ($user->authorise('core.edit.own', 'com_marathonmanager') && $this->item->created_by == $user->id);

            if ($itemEditable) {
                ToolbarHelper::apply('country.apply');
                $toolbarButtons[] = ['save', 'country.save'];
                // We can save this record, but check the create permission to see if we can return to make a new one.
                if ($user->authorise('core.create', 'com_marathonmanager')) {
                    $toolbarButtons[] = ['save2new', 'country.save2new'];
                    $toolbarButtons[] = ['save2copy', 'country.save2copy'];
                }
            }
        }

        ToolbarHelper::saveGroup($toolbarButtons);
        ToolbarHelper::cancel('country.cancel', 'JTOOLBAR_CLOSE');
	    ToolbarHelper::inlinehelp();

    }
}