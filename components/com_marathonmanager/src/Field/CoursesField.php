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

class CoursesField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $type = 'Courses';
    protected $eventId;

    protected function getInput()
    {
        // Get the current EventID
        $app = Factory::getApplication();
        $this->eventId = $app->input->getInt('event_id');
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
        $ids = null;

        if (!empty($this->eventId)) {
            $ids = $this->getEventParcoursIds();
        }

        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('id, title');
        $query->from('#__com_marathonmanager_courses');
        $query->where('published = 1');
        if (!empty($ids)) {
            $query->where('id IN (' . $ids . ')');
        }
        $query->order('ordering ASC');
        $db->setQuery($query);
        $dbValues = $db->loadObjectList();

        foreach ($dbValues as $option) {
            $options[] = HTMLHelper::_('select.option', $option->id, $option->title);
        }

        return $options;
    }

    protected function getEventParcoursIds(): string
    {
        $ids = [];
        $groups = $this->getGroups();
        $parcourGroups = [];

        error_log(print_r($groups, true));

        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('parcours');
        $query->from('#__com_marathonmanager_events');
        $query->where('id = ' . $this->eventId);
        $db->setQuery($query);
        $parcours = $db->loadObject();

        // Get the Parcours IDs from the JSON string
        $parcours = json_decode($parcours->parcours);
        foreach ($parcours as $key => $parcour) {
            $ids[] = $parcour->course_id;
            // Add the groups to the parcour to be later used in JS
            $parcourGroup = new \stdClass();
            $parcourGroup->parcour_id = $parcour->course_id;
            $parcourGroup->group_ids = array();

            foreach($parcour->group_id as $groupId) {
                $parcourGroup->group_ids[] = $groups[$groupId];
            }

            $parcourGroups[$parcour->course_id] = $parcourGroup;
        }

        // Append JS to site
        $this->appendJavascript(json_encode($ids), json_encode($parcourGroups));


        // Return the IDs as comma separated string for the query
        return implode(',', $ids);

    }

    protected function getGroups(): array
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        $query->select('id, title, max_participants as max');
        $query->from('#__com_marathonmanager_groups');
        $query->where('published = 1');
        $query->order('ordering ASC');
        $db->setQuery($query);
        return $db->loadObjectList($key = 'id');

    }

    protected function appendJavascript($parcourIds, $parcourGroups): void
    {
        Text::script('COM_MARATHONMANAGER_SELECT_GROUP');
        Text::script('COM_MARATHONMANAGER_MAX_REACHED');
        $parcoursScript = <<<JS
            let PARTICIPANTS_MAX = 99;
            document.addEventListener('DOMContentLoaded', function() {
                const parcourIds = JSON.parse('$parcourIds');
                const groups = JSON.parse('$parcourGroups');
                console.log(parcourIds);
                console.log(groups);
                
                const parcourSelect = document.getElementById('jform_course_id');
                const groupSelect = document.getElementById('jform_group_id');
                const selectedParcourId = parcourSelect.value;
                if(selectedParcourId) setGroupOptionsByParcourId(selectedParcourId);
                    
                function setGroupOptionsByParcourId(parcourId) {
                    const oldValue = groupSelect.value;
                    const groupOptions = groups.length ? groups[parcourId].group_ids : null;
                    if(!groupOptions) return;
                    groupSelect.innerHTML = '<option value="">'+Joomla.Text._("COM_MARATHONMANAGER_SELECT_GROUP", "- Please Select -")+'</option>';
                    groupOptions.forEach(group => {
                        const option = document.createElement('option');
                        option.value = group.id;
                        option.text = group.title;
                        groupSelect.appendChild(option);
                    });
                    // re set old value if still available
                    if(groupOptions.find(group => group.id === parseInt(oldValue))) {
                        groupSelect.value = oldValue;
                    }
                }
                // handle parcour change
                parcourSelect.addEventListener('change', function() {
                    const parcourId = parcourSelect.value;
                    setGroupOptionsByParcourId(parcourId);
                });
                
                // handle group change
                groupSelect.addEventListener('change', function() {
                    if(groups.length === 0) return;
                    const groupId = groupSelect.value;
                    if(!groupId){ 
                        PARTICIPANTS_MAX = 99; 
                        return;
                    }
                    PARTICIPANTS_MAX = groups[selectedParcourId].group_ids.find(group => group.id === parseInt(groupId) )['max'];
                    console.log(PARTICIPANTS_MAX);
                    
                    // Set the max participants for the subformField
                    const subFormField = document.querySelector('joomla-field-subform[name="jform[participants]"]');
                    subFormField.setAttribute('maximum', PARTICIPANTS_MAX);
                });
                
                // Check if the max participants are reached on add participant
                const participantsSubform = document.querySelector('joomla-field-subform[name="jform[participants]"]');
                participantsSubform.addEventListener('subform-row-add', function() {
                    handleUserNotification(participantsSubform);
                });
                
                participantsSubform.addEventListener('subform-row-remove', function() {
                    setTimeout(() =>{
                        handleUserNotification(participantsSubform);
                    }, 200)
                });
                
                // Check if the max participants are reached on change of Group
                groupSelect.addEventListener('change', function() {
                    setTimeout(() =>{
                        handleUserNotification(participantsSubform);
                    }, 200)
                });
                
                /**
                 * Check if the max participants are reached
                 * @param       parent      The subform container
                 * @returns     {boolean}   True if max participants are reached
                 */
                function checkMaxParticipantsReached(parent) {
                    const subFormRows = parent.querySelectorAll('.subform-repeatable-group');
                    console.log(subFormRows)
                    console.log("checkMaxParticipantsReached", subFormRows.length, PARTICIPANTS_MAX);
                    return subFormRows.length > PARTICIPANTS_MAX
                }
                
                /**
                 * Do the CSS stuff
                 * @param       participantsSubform      The subform container
                 */
                function handleUserNotification(participantsSubform){
                    const parent = participantsSubform.closest('.subform-repeatable-wrapper');
                    const controls = parent.closest('.controls');
                    if(checkMaxParticipantsReached(participantsSubform)){
                        parent.classList.add('form-control-danger', 'invalid', 'uk-border-rounded');
                        showMaxReachedMessage(controls);
                    }else{
                        parent.classList.remove('form-control-danger', 'invalid');
                        removeMaxReachedMessage(controls);
                    }
                }
                
                function showMaxReachedMessage(container){
                    const id = 'max-participants-reached-message';
                    if(document.getElementById(id)) return;
                    const maxReachedDomElement = document.createElement('div');
                    maxReachedDomElement.id = id;
                    maxReachedDomElement.classList.add('invalid-subform-message', 'uk-padding-small', 'uk-margin-small-bottom');
                    maxReachedDomElement.innerHTML = Joomla.Text._("COM_MARATHONMANAGER_MAX_REACHED", "Max Participants reached");
                    const containerFirstChild = container.firstChild;
                    container.insertBefore(maxReachedDomElement, containerFirstChild);
                }
                
                function removeMaxReachedMessage(container){
                    const id = 'max-participants-reached-message';
                    const maxReachedDomElement = document.getElementById(id);
                    if(maxReachedDomElement) container.removeChild(maxReachedDomElement);
                }
            });
            
        JS;
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->addInlineScript( $parcoursScript, ['defer'=> true]);

    }
}