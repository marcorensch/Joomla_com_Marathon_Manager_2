<?php
/**
 * @package     Joomla.Administrator
 *              Joomla.Site
 * @subpackage  com_footballmanager
 * @author      Marco Rensch
 * @since        1.0.0
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @copyright   Copyright (C) 2022 nx-designs NXD
 *
 */

namespace NXD\Component\MarathonManager\Site\Field;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

defined('_JEXEC') or die;

class GroupsField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $type = 'Groups';

    /**
     * Placeholder Method - the field options got set by the Courses Field
     *
     * @return  array  The field option objects.
     *
     * @since   1.0.0
     */
    protected function getOptions(): array
    {
        $options = [];
        $options[] = HTMLHelper::_('select.option', '', Text::_('COM_MARATHONMANAGER_SELECT_COURSE_FIRST'));
        return $options;
    }

}