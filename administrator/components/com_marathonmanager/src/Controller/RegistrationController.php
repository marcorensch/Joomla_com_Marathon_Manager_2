<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\Controller;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

class RegistrationController extends FormController
{
	// Optional: Declare  view name
	protected $view_list = 'registrations';
	protected $default_view = 'registration';

    public function setpaid()
    {
        $ids = (array) $this->input->get('cid', [], 'int');
        $this->setPaymentStatus($ids, 1);
    }

    public function setunpaid()
    {
        $ids = (array) $this->input->get('cid', [], 'int');
        $this->setPaymentStatus($ids, 0);
    }

    protected function setPaymentStatus($ids, $status)
    {
        $model = $this->getModel('Registration');
        $model->setPaymentStatus($ids, $status);

        // Force a re-rendering of the listview and keep filter and current page thanks to input
        $url = 'index.php?option=com_marathonmanager&view=registrations';
        $this->setRedirect(Route::_($url, false));

    }

    // Ajax calls
    /**
     * Get the available team categories for the selected event
     */
    public function getTeamCategories(){
        Session::checkToken();
        $event_id = $this->input->get('event_id', 0, 'int');
        $model = $this->getModel('Event');
        $teamcategories = $model->getEventEnabledTeamCategories($event_id);
        $options = array();
        foreach ($teamcategories as $option)
        {
            $options[] = HTMLHelper::_('select.option', $option->id, $option->title);
        }
        echo json_encode($options);
        exit;
    }

    /**
     * Get the available arrival dates for the selected event
     */
    public function getArrivalDates(){
        Session::checkToken();
        $event_id = $this->input->get('event_id', 0, 'int');
        $model = $this->getModel('Event');
        $arrivaldates = $model->getEventArrivalDates($event_id);
        $options = array();
        foreach ($arrivaldates as $option)
        {
            $options[] = HTMLHelper::_('select.option', $option->id, HtmlHelper::date($option->date, Text::_('DATE_FORMAT_LC5')));
        }
        echo json_encode($options);
        exit;
    }
}