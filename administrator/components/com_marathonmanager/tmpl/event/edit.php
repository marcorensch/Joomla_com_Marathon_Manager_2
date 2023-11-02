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
    ->useScript('form.validate')
    ->useScript('com_marathonmanager.rule-letters-only');
$wa->addInlineStyle('.control-group .control-label{width:100%;} .control-group .controls{min-width:10px}');
$wa->addInlineScript('
// Add Eventlistener for Row Add - Subform needs to have class "nxd-external-table"
document.addEventListener("DOMContentLoaded", function(event) {
    //get subform by name attribute
    $subform = document.querySelector(".subform-repeatable.nxd-external-table");
    //add Eventlistener to subform
    $subform.addEventListener("subform-row-add", function(event) {
        updateOrderingValues($subform);
    });
    $subform.addEventListener("dragend", function() {
        updateOrderingValues($subform);
    });
});

function updateOrderingValues($subform){
    let $subFormRows = $subform.querySelectorAll(".subform-repeatable-group");
        for (let i = 0; i < $subFormRows.length; i++){
            let $row = $subFormRows[i];
            let $orderingInput = $row.querySelector("input.ordering");
            $orderingInput.value = i+1;
        }
};
');

$layout = 'edit';
$tmpl = $input->get('tmpl', '', 'CMD') === 'component' ? '&tmpl=component' : '';
$action = Route::_('index.php?option=com_marathonmanager&layout=' . $layout . $tmpl . '&id=' . (int)$this->item->id);

?>

<form action="<?php echo $action; ?>" method="post" name="adminForm" id="item-form" class="form-validate form-vertical">

    <?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <div class="main-card">
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details']); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', '<i class="fas fa-database"></i> ' . Text::_('COM_MARATHONMANAGER_DETAILS_TAB_TITLE')); ?>
        <div class="row">
            <div class="col-lg-6">
                <fieldset id="location-data" class="options-form">
                    <legend><?php echo Text::_('COM_MARATHONMANAGER_LOCATION'); ?></legend>
                    <div>
                        <?php echo $this->getForm()->renderField('street'); ?>
                        <div class="row">
                            <div class="col col-lg-2">
                                <div>
                                    <?php echo $this->getForm()->renderField('zip'); ?>
                                </div>
                            </div>
                            <div class="col col-lg-10">
                                <div>
                                    <?php echo $this->getForm()->renderField('city'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <?php echo $this->getForm()->renderField('lat'); ?>
                            </div>
                            <div class="col-lg-6">
                                <?php echo $this->getForm()->renderField('lng'); ?>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <fieldset id="event-data" class="options-form">
                    <legend><?php echo Text::_('COM_MARATHONMANAGER_EVENT_DETAILS_FIELDSET_LABEL'); ?></legend>
                    <div>
                        <?php echo $this->getForm()->renderField('event_date'); ?>
                        <?php echo $this->getForm()->renderField('lastinfos_newsletter_list_id'); ?>
                        <div class="row">
                            <div class="col-lg-8">
                                <?php echo $this->getForm()->renderField('map_option_id'); ?>
                            </div>
                            <div class="col-lg-4">
                                <?php echo $this->getForm()->renderField('price_per_map'); ?>
                            </div>
                        </div>
                        <?php echo $this->getForm()->renderField('team_categories'); ?>
                    </div>
                </fieldset>
                <fieldset id="event-data" class="options-form">
                    <legend><?php echo Text::_('COM_MARATHONMANAGER_EVENT_LEGAL_LABEL'); ?></legend>
                    <div>
                        <?php echo $this->getForm()->renderField('privacy_policy_article_id'); ?>
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-6">
                <fieldset id="registration-data" class="options-form">
                    <legend><?php echo Text::_('COM_MARATHONMANAGER_REGISTRATION'); ?></legend>
                    <div>
                        <?php echo $this->getForm()->renderField('registration_start_date'); ?>
                        <?php echo $this->getForm()->renderField('registration_end_date'); ?>
                        <?php echo $this->getForm()->renderField('regular_fee'); ?>
                        <?php echo $this->getForm()->renderField('earlybird_end_date'); ?>
                        <?php echo $this->getForm()->renderField('earlybird_fee'); ?>
                    </div>
                </fieldset>
                <fieldset id="arrival-data" class="options-form">
                    <legend><?php echo Text::_('COM_MARATHONMANAGER_ARRIVAL'); ?></legend>
                    <div>
                        <?php echo $this->getForm()->renderField('arrival_options'); ?>
                        <?php echo $this->getForm()->renderField('arrival_dates'); ?>
                    </div>
                </fieldset>
            </div>

        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>


        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'description', '<i class="far fa-file-alt"></i> ' . Text::_('COM_MARATHONMANAGER_DESCRIPTION')); ?>
        <div class="row">
            <div>
                <?php echo $this->getForm()->renderField('description'); ?>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'parcours', '<i class="fas fa-map-signs"></i> ' . Text::_('COM_MARATHONMANAGER_PARCOURS_TAB_TITLE')); ?>
        <div class="row">
            <div class="col-md-12">
                <fieldset id="parcours-data" class="options-form">
                    <legend><?php echo Text::_('COM_MARATHONMANAGER_PARCOURS_FIELDSET_LABEL'); ?></legend>
                    <div>
                        <?php echo $this->getForm()->renderField('parcours'); ?>
                    </div>
                </fieldset>
            </div>
        </div>

        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'qrcodes', '<i class="fas fa-qrcode"></i> ' . Text::_('COM_MARATHONMANAGER_QR_TAB_TITLE')); ?>
        <div class="row">
            <div class="col-lg-4">
                <fieldset id="earlybird-qr-codes" class="options-form">
                    <legend><?php echo Text::_('COM_MARATHONMANAGER_QR_EARLYBIRD_FIELDSET_LABEL'); ?></legend>
                    <div>
                        <?php echo $this->getForm()->renderField('qr_bank_earlybird'); ?>
                        <?php echo $this->getForm()->renderField('qr_twint_earlybird'); ?>
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-4">
                <fieldset id="default-qr-codes" class="options-form">
                    <legend><?php echo Text::_('COM_MARATHONMANAGER_QR_FIELDSET_LABEL'); ?></legend>
                    <div>
                        <?php echo $this->getForm()->renderField('qr_bank'); ?>
                        <?php echo $this->getForm()->renderField('qr_twint'); ?>
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-4">
                <fieldset id="event-banking-details" class="options-form">
                    <legend><?php echo Text::_('COM_MARATHONMANAGER_BANKING_DETAILS_FIELDSET_LABEL'); ?></legend>
                    <div>
                        <div class="alert alert-info" role="alert"><?php echo Text::_('COM_MARATHONMANAGER_BANK_LEAVE_EMPTY_NOTE');?></div>
                        <?php echo $this->getForm()->renderField('banking_details'); ?>
                    </div>
                </fieldset>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'media', '<i class="fas fa-images"></i> ' . Text::_('COM_MARATHONMANAGER_MEDIA_TAB_TITLE')); ?>
        <div class="row">
            <div class="col-lg-6">
                <?php echo $this->getForm()->renderField('image'); ?>
            </div>
            <div class="col-lg-6">
                <?php echo $this->getForm()->renderField('gallery_content'); ?>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>


        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'attachments', '<i class="fas fa-paperclip"></i> ' . Text::_('COM_MARATHONMANAGER_ATTACHMENTS_TAB_TITLE')); ?>
        <div class="row">
            <div class="col-lg-6">
                <fieldset id="registration-data" class="options-form">
                    <legend><?php echo Text::_('COM_MARATHONMANAGER_ATTACHMENTS_FIELDSET_LABEL'); ?></legend>
                    <div>
                        <p><?php echo Text::_('COM_MARATHONMANAGER_ATTACHMENTS_DESC'); ?></p>
                        <?php echo $this->getForm()->renderField('attachments'); ?>
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-6">
                <fieldset id="registration-data" class="options-form">
                    <legend><?php echo Text::_('COM_MARATHONMANAGER_RESULTS_FIELDSET_LABEL'); ?></legend>
                    <div>
                        <p><?php echo Text::_('COM_MARATHONMANAGER_RESULT_FILES_DESC'); ?></p>
                        <?php echo $this->getForm()->renderField('result_files'); ?>
                    </div>
                </fieldset>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', '<i class="fas fa-eye"></i> ' . Text::_('COM_MARATHONMANAGER_PUBLISHING_TAB_TITLE')); ?>
        <div class="row">
            <div class="col-lg-6">
                <?php echo $this->getForm()->renderField('access'); ?>
                <?php echo $this->getForm()->renderField('published'); ?>
                <?php echo $this->getForm()->renderField('publish_up'); ?>
                <?php echo $this->getForm()->renderField('publish_down'); ?>
                <?php echo $this->getForm()->renderField('catid'); ?>
            </div>
            <div class="col-lg-6">
                <?php echo $this->getForm()->renderField('created_by'); ?>
                <?php echo $this->getForm()->renderField('modified_by'); ?>
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

