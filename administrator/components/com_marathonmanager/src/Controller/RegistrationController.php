<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\Controller;

\defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;

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
}