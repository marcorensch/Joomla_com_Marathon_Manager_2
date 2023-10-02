<?php
/**
 * @package     Joomla.Administrator
 *              Joomla.Site
 * @subpackage  com_footballmanager
 * @author      Marco Rensch
 * @since 	    1.0.0
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @copyright   Copyright (C) 2022 nx-designs NXD
 *
 */

namespace NXD\Component\MarathonManager\Administrator\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\SpacerField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

defined('_JEXEC') or die;

class CreatedByUserField extends SpacerField {
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $type = 'CreatedByUser';

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   1.0.0
     */
    protected function getLabel(): string
    {
        $app = Factory::getApplication();
        $doc = $app->getDocument();
        $wa = $doc->getWebAssetManager();
        $wa->addInlineStyle('
        div.control-label:has(.nxd-createdby-field) { padding-right:0; } 
        .nxd-createdby-field{  
            background: #e8e8e8;
            border-radius:4px;
            border: 1px solid rgba(0,0,0,.125);
        }
        .nxd-createdby-field a {
        margin-top: 6px;
        display:block;
        }
        .nxd-createdby-field a:hover {
        }'
        );

        $createdByUserID = $this->form->getValue('created_by');
        $user = Factory::getContainer()->get(UserFactoryInterface::class)->loadUserById($createdByUserID);
        $name = $user->name ?: $user->username;
        $link = 'index.php?option=com_users&task=user.edit&id=' . $user->id;
        $text = '<span class="">' . Text::sprintf('COM_MARATHONMANAGER_FIELD_CREATED_BY_USER', $user->username, $user->id, $name, $link) . '</span>';
        $html = '<div class="nxd-createdby-field p-3 text-center">' . $text . '</div>';
        return $html;
    }

    protected function getTitle()
    {
        return $this->getLabel();
    }

}