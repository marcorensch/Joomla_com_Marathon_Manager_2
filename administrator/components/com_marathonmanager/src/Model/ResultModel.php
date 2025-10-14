<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\Model;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;

class ResultModel extends \Joomla\CMS\MVC\Model\AdminModel
{

	public $typeAlias = 'com_marathonmanager.result';

	/**
	 * @inheritDoc
	 */
	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm($this->typeAlias, 'result', ['control' => 'jform', 'load_data' => $loadData]);

		if(empty($form)){
			return false;
		}

		return $form;
	}

    /**
     * @throws \Exception
     */
    public function getImportForm($data = [], $loadData = false)
    {
        $form = $this->loadForm($this->typeAlias, 'result_import', ['control' => 'jform', 'load_data' => $loadData]);

        if(empty($form)){
            return false;
        }

        return $form;
    }

	protected function loadFormData()
	{
		$app = Factory::getApplication();

		$data = $app->getUserState('com_marathonmanager.edit.result.data', []);

		if (empty($data)) {
			$data = $this->getItem();
		}


		$this->preprocessData($this->typeAlias, $data);

		return $data;
	}

	protected function prepareTable($table)
	{

	}

    public function save($data)
    {
        $app   = Factory::getApplication();
        $input = $app->getInput();
        $user  = $app->getIdentity();

        // new element tasks
        if (!isset($data['id']) || (int) $data['id'] === 0)
        {
            $data['created_by'] = $user->id;
        }

        $data['modified_by'] = $user->id;

        // Alter the title for save as copy
        if ($input->get('task') == 'save2copy')
        {
            $origTable = $this->getTable();

            if ($app->isClient('site'))
            {
                $origTable->load($input->getInt('a_id'));
            }
            else
            {
                $origTable->load($input->getInt('id'));
            }

            if ($data['title'] == $origTable->title)
            {
                list($title, $alias) = $this->generateNewTitle(null, '', $data['team_name']);
                $data['team_name'] = $title;
            }
        }

        return parent::save($data);
    }

    // ***************** Import ***************** //

}