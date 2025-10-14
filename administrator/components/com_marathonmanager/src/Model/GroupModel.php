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
use Joomla\CMS\Language\Text;

class GroupModel extends \Joomla\CMS\MVC\Model\AdminModel
{

	public $typeAlias = 'com_marathonmanager.group';

	/**
	 * @inheritDoc
	 */
	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm($this->typeAlias, 'group', ['control' => 'jform', 'load_data' => $loadData]);

		if(empty($form)){
			return false;
		}

		return $form;
	}

	protected function loadFormData()
	{
		$app = Factory::getApplication();

		$data = $app->getUserState('com_marathonmanager.edit.group.data', []);

		if (empty($data)) {
			$data = $this->getItem();
		}


		$this->preprocessData($this->typeAlias, $data);

		return $data;
	}

	protected function prepareTable($table)
	{
		$table->generateAlias();
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

                if ($origTable->title === $data['title'])
                {
                    /**
                     * If title of article is not changed, set alias to original article alias so that Joomla! will generate
                     * new Title and Alias for the copied article
                     */
                    $data['alias'] = $origTable->alias;
                }
                else
                {
                    $data['alias'] = '';
                }
            }
            else
            {
                $origTable->load($input->getInt('id'));
            }

            if ($data['title'] == $origTable->title)
            {
                list($title, $alias) = $this->generateNewTitle(null, $data['alias'], $data['title']);
                $data['title'] = $title;
                $data['alias'] = $alias;
            }
            elseif ($data['alias'] == $origTable->alias)
            {
                $data['alias'] = '';
            }
        }

        // Automatic handling of alias for empty fields
        if (in_array($input->get('task'), ['apply', 'save', 'save2new']) && $data['alias'] == null)
        {
            if ($app->get('unicodeslugs') == 1)
            {
                $data['alias'] = OutputFilter::stringUrlUnicodeSlug($data['title']);
            }
            else
            {
                $data['alias'] = OutputFilter::stringURLSafe($data['title']);
            }

            $table = $this->getTable();

            if ($table->load(['alias' => $data['alias']]))
            {
                $msg = Text::_('COM_MARATHONMANAGER_SAVE_WARNING');
            }

            list($title, $alias) = $this->generateNewTitle(null, $data['alias'], $data['title']);
            $data['alias'] = $alias;

            if (isset($msg))
            {
                $app->enqueueMessage($msg, 'warning');
            }
        }

        return parent::save($data);
    }
}