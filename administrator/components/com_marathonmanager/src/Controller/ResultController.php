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

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;

class ResultController extends FormController
{
	// Optional: Declare  view name
	protected $view_list = 'results';
	protected $default_view = 'result';

    public function cancelImport(): void
    {
        error_log('Cancel Import');
        // Cleanup User State Data
        Factory::getApplication()->setUserState('com_marathonmanager.results.import.data', []);
        Factory::getApplication()->setUserState('com_marathonmanager.results.import.event_id', 0);
        $this->setRedirect(Route::_('index.php?option=com_marathonmanager&view=results', false));
    }
}