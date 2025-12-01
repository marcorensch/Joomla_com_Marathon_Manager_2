<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\Controller;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use NXD\Component\MarathonManager\Site\Model\RegistrationModel;

class RegistrationController extends FormController
{
    public function clear()
    {
        $app = Factory::getApplication();
        $session = Factory::getApplication()->getSession();
        $session->clear('com_marathonmanager.registration');
        $url = $this->getReturnPage();
        $app->redirect(Route::_($url, false));
    }
    public function submit($key = null, $urlVar = null): bool
    {
		/* @var $model RegistrationModel */
        $app = Factory::getApplication();
        $model = $this->getModel('Registration');
        $form = $model->getForm();
        $data = $app->input->post->get('jform', [], 'array');

        // Validate the posted data.
        $validData = $model->validate($form, $data);

        if(!$validData) {
			Log::add("MarathonManager::Site.RegistrationController: Invalid Data from Registration Form received: \n" . var_export($data, true), Log::ERROR, 'com_marathonmanager.registration');
            error_log('Invalid Data from Registration Form: ' . var_export($data, true));
            $url =  isset($data['event_id']) ? 'index.php?option=com_marathonmanager&view=registration&layout=edit&event_id=' . $data['event_id'] : $this->getReturnPage();
            $this->setRedirect(Route::_($url, false));
            return false;
        }
        // Store the registration data.
	    try
	    {
		    $status = $model->save($validData);
	    }catch (\Exception $e) {
			Log::add('Could not save registration data: ' . $e->getMessage(), Log::ERROR);
			$status = false;
	    }

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