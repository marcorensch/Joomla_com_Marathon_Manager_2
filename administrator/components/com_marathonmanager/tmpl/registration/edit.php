<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

$app = Factory::getApplication();
$doc = $app->getDocument();
$wa = $doc->getWebAssetManager();
$input = $app->input;

$this->useCoreUI = true;

$wa->useScript('keepalive')
    ->useScript('form.validate');
//    ->useScript('com_marathonmanager.rule-checked');
$wa->addInlineStyle('.control-group .control-label{width:100%;} .control-group .controls{min-width:10px}');
if($this->item->event_id){
    $wa->addInlineScript('SELECTED_EVENT_ID = ' . $this->item->event_id . ';');
}else{
    $wa->addInlineScript('SELECTED_EVENT_ID = false;');
}
$wa->addInlineScript(<<<JS
function eventChange(newId){
    let alertBox = document.querySelector("#eventChangedInfoBox");
    let arrivalDate = document.querySelector("#jform_arrival_date");
    if(SELECTED_EVENT_ID && newId != SELECTED_EVENT_ID){
        alertBox.classList.remove("d-none");
        arrivalDate.attributes["disabled"] = "disabled";
    }else{
        alertBox.classList.add("d-none");
        arrivalDate.removeAttribute("disabled");
    }
    updateArrivalDates(newId);
}

function updateArrivalDates(newId){
    let xhttpArrivalDates = new XMLHttpRequest();
    xhttpArrivalDates.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let arrivalDates = JSON.parse(this.responseText);
            updateArrivalDateSelection(arrivalDates);
        }
    };
    xhttpArrivalDates.open("GET", "index.php?option=com_marathonmanager&task=registration.getArrivalDates&format=raw&event_id=" + newId, true);
    xhttpArrivalDates.send();
}

function updateArrivalDateSelection(dates){
   let arrivalDateSelect = document.querySelector("#jform_arrival_date");
    arrivalDateSelect.innerHTML = "";
    dates.forEach(function(date){
        let option = document.createElement("option");
        option.value = date.value;
        option.text = date.text;
        option.innerHTML = date.text;
        option.disabled = date.disable;
        arrivalDateSelect.appendChild(option);
    });
}

document.addEventListener("DOMContentLoaded", function(event) {
    const eventSelect = document.querySelector("#jform_event_id");
    eventChange(eventSelect.value);
});
JS
);

$layout = 'edit';
$tmpl = $input->get('tmpl', '', 'CMD') === 'component' ? '&tmpl=component' : '';
$action = Route::_('index.php?option=com_marathonmanager&layout=' . $layout . $tmpl . '&id=' . (int)$this->item->id);
?>

<form action="<?php echo $action; ?>" method="post" name="adminForm" id="item-form" class="form-validate form-vertical">

    <div class="row title-alias form-vertical mb-3">
        <div class="col-12 col-md-6">
            <?php echo $this->getForm()->renderField('team_name'); ?>
        </div>
        <div class="col-12 col-md-6">
            <?php echo $this->getForm()->renderField('alias'); ?>
        </div>
    </div>

    <div class="main-card">
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'base']); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'base', '<i class="fas fa-database"></i> ' . Text::_('COM_MARATHONMANAGER_DETAILS_TAB_TITLE')); ?>
        <div class="row gx-5">
            <div class="col-lg-4 col-xxl-6">
                <div class="row">
                    <div class="col-xxl-6">
                        <fieldset id="fieldset-event" class="options-form">
                            <legend><?php echo Text::_('COM_MARATHONMANAGER_FIELD_EVENT_FIELDSET_LABEL') ?></legend>
                            <div>
                                <?php echo $this->getForm()->renderField('event_id'); ?>
                                <div id="eventChangedInfoBox" class="d-none alert alert-warning" role="alert">
                                    <?php echo Text::_('COM_MARATHONMANAGER_FIELD_EVENT_CHANGED_INFO'); ?>
                                </div>
                                <?php echo $this->getForm()->renderField('team_category_id'); ?>
                                <?php echo $this->getForm()->renderField('arrival_date_id'); ?>
                                <?php echo $this->getForm()->renderField('arrival_option_id'); ?>
                                <?php echo $this->getForm()->renderField('maps_count'); ?>
                                <?php echo $this->getForm()->renderField('privacy_policy'); ?>
                                <?php echo $this->getForm()->renderField('insurance_notice'); ?>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-xxl-6">
                        <fieldset id="fieldset-contact" class="options-form">
                            <legend><?php echo Text::_('COM_MARATHONMANAGER_FIELD_CONTACT_FIELDSET_LABEL') ?></legend>
                            <div>
                                <?php echo $this->getForm()->renderField('createdbyuserinfo'); ?>
                                <?php echo $this->getForm()->renderField('contact_first_name'); ?>
                                <?php echo $this->getForm()->renderField('contact_last_name'); ?>
                                <?php echo $this->getForm()->renderField('contact_phone'); ?>
                                <?php echo $this->getForm()->renderField('contact_email'); ?>
                                <?php echo $this->getForm()->renderField('team_language_id'); ?>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <fieldset id="fieldset-payment" class="options-form">
                    <legend><?php echo Text::_('COM_MARATHONMANAGER_FIELD_PAYMENT_FIELDSET_LABEL') ?></legend>
                    <div class="row">
                        <div class="col-xl-8">
                            <?php echo $this->getForm()->renderField('reference'); ?>
                            <?php echo $this->getForm()->renderField('registration_fee'); ?>
                        </div>
                        <div class="col-xl-4">
                            <?php echo $this->getForm()->renderField('payment_status'); ?>
                        </div>
                    </div>
                </fieldset>

            </div>
            <div class="col-lg-8 col-xxl-6">
                <fieldset id="fieldset-parcours" class="options-form">
                    <legend><?php echo Text::_('COM_MARATHONMANAGER_FIELD_PARCOURS_FIELDSET_LABEL') ?></legend>
                    <div>
                        <?php echo $this->getForm()->renderField('course_id'); ?>
                        <?php echo $this->getForm()->renderField('group_id'); ?>
                    </div>
                </fieldset>
                <fieldset id="fieldset-participants" class="options-form">
                    <legend><?php echo Text::_('COM_MARATHONMANAGER_FIELD_PARTICIPANTS_FIELDSET_LABEL') ?></legend>
                    <div>
                        <?php echo $this->getForm()->renderField('participants'); ?>
                    </div>
                </fieldset>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', '<i class="fas fa-eye"></i>&nbsp;' . Text::_('COM_MARATHONMANAGER_PUBLISHING_TAB_TITLE')); ?>
        <div class="row">
            <div class="col-lg-6">
                <?php echo $this->getForm()->renderField('access'); ?>
                <?php echo $this->getForm()->renderField('published'); ?>
                <?php echo $this->getForm()->renderField('publish_up'); ?>
                <?php echo $this->getForm()->renderField('publish_down'); ?>
                <?php echo $this->getForm()->renderField('created_by'); ?>
                <?php echo $this->getForm()->renderField('modified_by'); ?>
                <?php echo $this->getForm()->renderField('catid'); ?>
                <?php echo $this->getForm()->renderField('created'); ?>
                <?php echo $this->getForm()->renderField('modified'); ?>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo LayoutHelper::render('joomla.edit.params', $this); ?>

        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
    </div>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

