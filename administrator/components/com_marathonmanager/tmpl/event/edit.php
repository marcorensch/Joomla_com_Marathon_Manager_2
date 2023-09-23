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

$layout = 'edit';
$tmpl = $input->get('tmpl', '', 'CMD') === 'component' ? '&tmpl=component' : '';
$action = Route::_('index.php?option=com_marathonmanager&layout=' . $layout . $tmpl . '&id=' . (int)$this->item->id);

?>

<form action="<?php echo $action; ?>" method="post" name="adminForm" id="item-form" class="form-validate form-vertical">

    <?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <div class="main-card">
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'description']); ?>
        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'description', Text::_('COM_MARATHONMANAGER_DESCRIPTION')); ?>
        <div class="row">
            <div>
                <?php echo $this->getForm()->renderField('description'); ?>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('COM_MARATHONMANAGER_DETAILS_TAB_TITLE')); ?>
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
                        <?php echo $this->getForm()->renderField('arrival_options'); ?>
                        <?php echo $this->getForm()->renderField('lastinfos_newsletter_list_id'); ?>
                        <div class="row">
                            <div class="col-lg-8">
                                <?php echo $this->getForm()->renderField('map_option_id'); ?>
                            </div>
                            <div class="col-lg-4">
                                <?php echo $this->getForm()->renderField('price_per_map'); ?>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="col-lg-6">
                <fieldset id="registration-data" class="options-form">
                    <legend><?php echo Text::_('COM_MARATHONMANAGER_REGISTRATION'); ?></legend>
                    <div>
                        <?php echo $this->getForm()->renderField('earlybird_fee'); ?>
                        <?php echo $this->getForm()->renderField('regular_fee'); ?>
                        <?php echo $this->getForm()->renderField('registration_start_date'); ?>
                        <?php echo $this->getForm()->renderField('earlybird_end_date'); ?>
                        <?php echo $this->getForm()->renderField('registration_end_date'); ?>
                    </div>
                </fieldset>
            </div>

        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>


        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'media', Text::_('COM_MARATHONMANAGER_MEDIA_TAB_TITLE')); ?>
        <div class="row">
            <div class="col-lg-6">
                <?php echo $this->getForm()->renderField('image'); ?>
            </div>
            <div class="col-lg-6">
                <?php echo $this->getForm()->renderField('gallery_content'); ?>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>


        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'attachments', Text::_('COM_MARATHONMANAGER_ATTACHMENTS_TAB_TITLE')); ?>
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

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('COM_MARATHONMANAGER_PUBLISHING_TAB_TITLE')); ?>
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
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo LayoutHelper::render('joomla.edit.params', $this); ?>

        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
    </div>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>

