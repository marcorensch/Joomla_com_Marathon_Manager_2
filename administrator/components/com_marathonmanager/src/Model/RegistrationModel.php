<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Language\Text;

use Joomla\CMS\User\UserFactoryInterface;
use Joomla\CMS\User\UserHelper;
use Joomla\Registry\Registry;
use NXD\Component\MarathonManager\Administrator\Helper\RegistrationHelper;

class RegistrationModel extends \Joomla\CMS\MVC\Model\AdminModel
{

    public $typeAlias = 'com_marathonmanager.registration';

    /**
     * @inheritDoc
     */
    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm($this->typeAlias, 'registration', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $app = Factory::getApplication();

        $data = $app->getUserState('com_marathonmanager.edit.registration.data', []);

        if (empty($data)) {
            $data = $this->getItem();

            // Prime some default values.
            if ($this->getState('registration.id') == 0) {
                $data->set('catid', $app->input->get('catid', $app->getUserState('com_marathonmanager.registrations.filter.category_id'), 'int'));
            }
        }

        $this->preprocessData($this->typeAlias, $data);

        return $data;
    }

    protected function prepareTable($table)
    {
        $table->generateAlias();
    }

    /**
     * @throws \Exception
     */
    public function save($data)
    {
        $app = Factory::getApplication();
        $input = $app->getInput();
        $user = $app->getIdentity();

        // new element tasks
        if (!isset($data['id']) || (int)$data['id'] === 0) {
            $data['created_by'] = $user->id;
        }

        $data['modified_by'] = $user->id;

        $data['registration_fee'] = RegistrationHelper::calculateRegistrationFee($data['event_id'], $data['maps_count']);

        // Alter the title for save as copy
        if ($input->get('task') == 'save2copy') {
            $origTable = $this->getTable();

            if ($app->isClient('site')) {
                $origTable->load($input->getInt('a_id'));

                if ($origTable->team_name === $data['team_name']) {
                    /**
                     * If title of article is not changed, set alias to original article alias so that Joomla! will generate
                     * new Title and Alias for the copied article
                     */
                    $data['alias'] = $origTable->alias;
                } else {
                    $data['alias'] = '';
                }
            } else {
                $origTable->load($input->getInt('id'));
            }

            if ($data['team_name'] == $origTable->title) {
                list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['team_name']);
                $data['team_name'] = $title;
                $data['alias'] = $alias;
            } elseif ($data['alias'] == $origTable->alias) {
                $data['alias'] = '';
            }
        }

        // Automatic handling of alias for empty fields
        if (in_array($input->get('task'), ['apply', 'save', 'save2new']) && $data['alias'] == null) {
            if ($app->get('unicodeslugs') == 1) {
                $data['alias'] = OutputFilter::stringUrlUnicodeSlug($data['team_name']);
            } else {
                $data['alias'] = OutputFilter::stringURLSafe($data['team_name']);
            }

            $table = $this->getTable();

            if ($table->load(['alias' => $data['alias'], 'catid' => $data['catid']])) {
                $msg = Text::_('COM_MARATHONMANAGER_SAVE_WARNING');
            }

            list($team_name, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['team_name']);
            $data['alias'] = $alias;

            if (isset($msg)) {
                $app->enqueueMessage($msg, 'warning');
            }
        }

        // Generate Reference Number
        if (in_array($input->get('task'), ['apply', 'save', 'save2new']) && $data['reference'] == null) {
            $table = $this->getTable();
            $data['reference'] = $table->generateReference($data);
        }

        // Handle Subforms & MultiSelect Fields on save in foreach loop
        $specFields = ['participants'];
        foreach ($specFields as $fieldName) {
            if (isset($data[$fieldName]) && is_array($data[$fieldName])) {
                $registry = new Registry;
                $registry->loadArray($data[$fieldName]);
                $data[$fieldName] = (string)$registry;
            }
        }

         error_log(var_export($data, true));

        return parent::save($data);
    }

    public function setPaymentStatus($arrayOfIds, $status): void
    {
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->update($db->quoteName('#__com_marathonmanager_registrations'));
        $query->set($db->quoteName('payment_status') . ' = ' . $status);
        $query->where($db->quoteName('id') . ' IN (' . implode(',', $arrayOfIds) . ')');
        $db->setQuery($query);
        $db->execute();
    }
}