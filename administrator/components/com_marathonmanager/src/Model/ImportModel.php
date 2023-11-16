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

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\CMS\User\UserHelper;
use Joomla\Registry\Registry;
use NXD\Component\MarathonManager\Administrator\Helper\RegistrationHelper;

class ImportModel extends AdminModel
{

    public $typeAlias = 'com_marathonmanager.import';

    /**
     * @inheritDoc
     */
    public function getForm($data = [], $loadData = false)
    {
        $importType = Factory::getApplication()->input->get('type', 'default');
        $form = $this->loadForm($this->typeAlias, 'import_' . $importType, ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        return $form;
    }
}