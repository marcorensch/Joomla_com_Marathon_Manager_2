let PARTICIPANTS_MAX = 2;
// parcoursIds and parcoursGroups are set in the CoursesField.php as JSON string
document.addEventListener('DOMContentLoaded', function() {
    const parcourIds = JSON.parse(parcoursIds);
    const groups = JSON.parse(parcoursGroups);

    const parcoursSelect = document.getElementById('jform_course_id');
    const groupSelect = document.getElementById('jform_group_id');

    // Initialize the group options
    if(parcoursSelect.value) setGroupOptionsByParcoursId(parcoursSelect.value);

    // handle parcours change
    parcoursSelect.addEventListener('change', function() {
        setGroupOptionsByParcoursId(parcoursSelect.value);
    });

    // handle group change
    groupSelect.addEventListener('change', function() {
        if(groups.length === 0) return;
        const groupId = groupSelect.value;
        if(!groupId){
            PARTICIPANTS_MAX = 99;
            return;
        }

        PARTICIPANTS_MAX = groups[parcoursSelect.value].group_ids.find(group => group.id === parseInt(groupId) )['max'];

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
     * Set the group options by parcours id
     * @param parcoursId
     */
    function setGroupOptionsByParcoursId(parcoursId) {
        const oldValue = groupSelect.value;
        const groupOptions = Object.keys(groups).length ? groups[parcoursId].group_ids : null;
        if(!groupOptions) return;
        groupSelect.innerHTML = '<option value="">'+Joomla.Text._("COM_MARATHONMANAGER_SELECT_GROUP", "- Please Select -")+'</option>';
        groupOptions.forEach(group => {
            const option = document.createElement('option');
            option.value = group.id;
            option.text = group.title;
            groupSelect.appendChild(option);
        });

        // reset old value for group if still available in this parcours
        if(groupOptions.find(group => group.id === parseInt(oldValue))) {
            groupSelect.value = oldValue;
        }
    }

    /**
     * Check if the max participants are reached
     * @param       parent      The subform container
     * @returns     {boolean}   True if max participants are reached
     */
    function checkMaxParticipantsReached(parent) {
        const subFormRows = parent.querySelectorAll('.subform-repeatable-group');
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