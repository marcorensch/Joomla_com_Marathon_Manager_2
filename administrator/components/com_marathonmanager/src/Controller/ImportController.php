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
use NXD\Component\Footballmanager\Administrator\Helper\ImportHelper;

class ImportController extends FormController
{
	// Optional: Declare  view name
	protected $default_view = 'default';

    public function import(): void
    {
        // Will be called on the Controller of the type
    }

    public function cancel($key = null)
    {
        $url = 'index.php?option=com_marathonmanager';
        if($type = $this->input->get('type', null))
        {
            $url .= '&view='.$type;
        }
        $this->setRedirect($url);
    }
}