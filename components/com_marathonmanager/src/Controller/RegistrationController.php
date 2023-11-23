<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

class RegistrationController extends FormController
{
    public function submit($key = null, $urlVar = null)
    {

        $app = Factory::getApplication();
        $model = $this->getModel('Registration');
        $form = $model->getForm();
        $data = $app->input->post->get('jform', [], 'array');

        // Validate the posted data.
        $validData = $model->validate($form, $data);

        if(!$validData) {
            error_log('Invalid Data from Registration Form: ' . var_export($data, true));
            $url =  isset($data['event_id']) ? 'index.php?option=com_marathonmanager&view=registration&layout=edit&event_id=' . $data['event_id'] : $this->getReturnPage();
            $this->setRedirect(Route::_($url, false));
            return false;
        }
        // Store the registration data.
        $status = $model->save($validData);

        if ($status) {
            $url = $this->getReturnPage();
        } else {
            $url = isset($data['event_id']) ? 'index.php?option=com_marathonmanager&view=registration&layout=edit&event_id=' . $data['event_id'] : $this->getReturnPage();
        }
        $this->setRedirect(Route::_($url, false));

        return true;
    }

    protected function getReturnPage(): string
    {
        $return = $this->input->get('return', null, 'base64');

        if (empty($return)) return Uri::base();

        return base64_decode($return);
    }
}