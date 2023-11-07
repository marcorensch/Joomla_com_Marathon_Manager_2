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

class ExportController extends FormController
{
	// Optional: Declare  view name
	protected $default_view = 'export';

    public function export()
    {
        error_log('Exporting in CONTROLLER');
        // Do some stuff here
        $data = $this->input->post->get('jform', [], 'array');
        $model = $this->getModel();
        $model->export($data);
        $this->setRedirect('index.php?option=com_marathonmanager&view=export');
    }

}