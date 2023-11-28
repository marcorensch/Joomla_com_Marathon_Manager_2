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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use NXD\Component\MarathonManager\Administrator\Helper\ImportHelper;

/**
 * Events Controller
 *
 * @since  1.0.0
 */

class ResultsController extends AdminController
{
    private $typeAlias = 'com_marathonmanager.results';
    private $importedData = [];
	/**
	 * @throws \Exception
	 */
	public function __construct($config = array(), MVCFactory $factory = null, $app = null, $input = null)
	{
		parent::__construct($config, $factory, $app, $input);
	}

	/**
	 * Proxy for getModel.
	 */
	public function getModel($name = 'Result', $prefix = 'Administrator', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

    public function showImport()
    {
        $this->setRedirect('index.php?option=com_marathonmanager&view=import&type=results&context=result');
    }

    public function import()
    {
        // Get the uploaded file data from the Input class
        $app = Factory::getApplication();
        // Get the uploaded file data from the input
        $input = Factory::getApplication()->input;
        // Get the uploaded file data
        $files = $input->files->get('jform', array(), 'array');
        // Get the data from the form
        $data = $input->get('jform', array(), 'array');


        // Check if the file was uploaded successfully (error code 0 means success)
        if ($files['upload_file']) {
            $app->enqueueMessage(Text::sprintf('COM_MARATHONMANAGER_TEXT_UPLOAD_SUCCESS', $files['upload_file']['name'] ), 'notice');
            $file = $files['upload_file'];
            // Get the temporary file path
//			$tmpFilePath = $files['upload_file']['tmp_name'];

            // Get the original file name
            $fileName = $files['upload_file']['name'];

            // Do something with the file, e.g., move it to a desired location
//			$destinationPath = 'path/to/your/desired/location/' . $fileName;
//			move_uploaded_file($tmpFilePath, $destinationPath);

            // ... Process the uploaded file further as needed ...
            $importHelper = new ImportHelper($file);
            $dataset = $importHelper->import();
            $app->enqueueMessage(Text::sprintf('COM_MARATHONMANAGER_ROWS_PARSED', count($dataset) ), 'notice');
//            echo '<pre>' . print_r($dataset, true) . '</pre>';
            // Redirect or return a response after processing
//            $app->enqueueMessage(Text::sprintf('COM_FOOTBALLMANAGER_TEXT_IMPORT_SUCCESS', $fileName ), 'message');
            // Send data back to view via User State
            Factory::getApplication()->setUserState('com_marathonmanager.results.import.data', $dataset);
            Factory::getApplication()->setUserState('com_marathonmanager.results.import.event_id', $data['event_id']);

            $view = $this->getView('Results', 'html');
            $model = $this->getModel('Results');
            $view->setModel($model, true);
            $view->display();


        } else {
            // Handle the upload error
            // @TODO
            // Redirect or return a response to show the error to the user
            $app->enqueueMessage(Text::_("COM_MARATHONMANAGER_TEXT_UPLOAD_FAILED"), 'error');
        }

//        $app->enqueueMessage('Importing seasons is not supported yet', 'warning');
//        $this->setRedirect(Route::_('index.php?option=com_footballmanager&view=seasons', false));
    }

    public function processdata(){
        if(!Session::checkToken()) {
            throw new \Exception(Text::_('JINVALID_TOKEN_NOTICE'), 403);
        }

        $input = Factory::getApplication()->input;
        $formData = $input->get('jform', array(), 'array');
        $model = $this->getModel('Results');


        // Get the data array from the User State
        $fileData = Factory::getApplication()->getUserState('com_marathonmanager.results.import.data', []);
        $formData['event_id'] = Factory::getApplication()->getUserState('com_marathonmanager.results.import.event_id', 0);

        // Cleanup User State Data
        Factory::getApplication()->setUserState('com_marathonmanager.results.import.data', []);
        Factory::getApplication()->setUserState('com_marathonmanager.results.import.event_id', 0);

        $model->processData($formData,$fileData);
    }

    public function cancelImport(){
        // Cleanup User State Data
        Factory::getApplication()->setUserState('com_marathonmanager.results.import.data', []);
        Factory::getApplication()->setUserState('com_marathonmanager.results.import.event_id', 0);
        $this->setRedirect(Route::_('index.php?option=com_marathonmanager&view=results', false));
    }

}