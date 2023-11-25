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
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

defined('_JEXEC') or die;

class NxdArticleSelectionField extends ListField{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $type = 'NxdArticleSelection';
    protected $filter = null;

    // Get the xml form field attributes
    protected function getInput()
    {
        // Get the filter attribute from the xml form field
        $filters = $this->element['filter'] ?? null;
        if($filters) $this->filter = explode(',', $filters);
        return parent::getInput();
    }

    /**
     * Method to get the field options.
     *
     * @return  array  The field option objects.
     *
     * @since   1.0.0
     */
    protected function getOptions(): array
    {
        $options = [];
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select(
            $db->quoteName(['a.id', 'a.title'])
        )
            ->from($db->quoteName('#__content','a'));
        // Filter where article alias is in the filters array
        if($this->filter) {
            $query->where($db->quoteName('a.alias') . ' IN (' . $db->quote(implode(',', $this->filter)) . ')');
            $index = 0;
            foreach ($this->filter as $search) {
                $search = $db->quote('%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%'));
                if($index ===0){
                    $query->where('(' . $db->quoteName('a.title') . ' LIKE ' . $search . ')');
                }else{
                    $query->orWhere('(' . $db->quoteName('a.title') . ' LIKE ' . $search . ')');
                    $query->orWhere('(' . $db->quoteName('a.alias') . ' LIKE ' . $search . ')');
                }
                $index++;
            }
        }
        $query->order('a.ordering ASC, a.title ASC');
        $db->setQuery($query);
        $dbValues = $db->loadObjectList();

        $options[] = HTMLHelper::_('select.option', '', Text::_('COM_MARATHONMANAGER_FIELD_OPT_SELECT_ARTICLE'));

        foreach ($dbValues as $option)
        {
            $options[] = HTMLHelper::_('select.option', $option->id, $option->title);
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }

}