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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;

class ExportController extends FormController
{
	// Optional: Declare  view name
	protected $default_view = 'export';

    public function export()
    {
        // Do some stuff here
        $data = $this->input->post->get('jform', [], 'array');
        $model = $this->getModel();

        try {
            if ($model->export($data)) $this->setMessage(Text::_('COM_MARATHONMANAGER_EXPORT_SUCCESS'));
        } catch (\Exception $e) {
            $this->setMessage($e->getMessage(), 'error');
        }

        $this->setRedirect('index.php?option=com_marathonmanager&view=export');
    }

}