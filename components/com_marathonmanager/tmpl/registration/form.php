<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @var         $event object the current event object
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

\defined('_JEXEC') or die;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate')
    ->useScript('com_marathonmanager.form-edit')
    ->useScript('com_marathonmanager.rule-checked')
    ->useScript('com_marathonmanager.rule-age-check');

$wa->useStyle('com_marathonmanager.form-edit');
$wa->addInlineStyle('.switcher { width: auto; } .control-group {position:relative;}');

?>

<form class="form-validate" action="<?php echo Route::_('index.php?option=com_marathonmanager&id='.$this->event->id); ?>"
      method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

    <div class="uk-grid-small uk-child-width-1-1 uk-child-width-1-2@m uk-grid-match" uk-grid>

        <div>
            <div class="uk-card uk-card-default uk-card-body uk-border-rounded">
                <h3 class="uk-h4"><i class="fas fa-users"></i> <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_FORM_TEAM_TITLE");?></h3>
                <?php echo $this->form->renderFieldset('teamdata'); ?>
            </div>
            <div class="uk-card uk-card-default uk-card-body uk-border-rounded uk-margin-small-top">
                <h3 class="uk-h4"><i class="fas fa-life-ring"></i> <?php echo Text::_("COM_MARATHONMANAGER_EMERGENCY_CONTACT_TITLE");?></h3>
                <?php echo $this->form->renderFieldset('emergency'); ?>
            </div>
        </div>

        <div>
            <div class="uk-card uk-card-default uk-card-body uk-border-rounded">
                <h3 class="uk-h4"><i class="fas fa-envelope"></i> <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_FORM_CONTACT_TITLE");?></h3>
                <div class="uk-text-meta"><?php echo Text::_("COM_MARATHONMANAGER_CONTACT_INTRO");?></div>
                <?php echo $this->form->renderFieldset('contact'); ?>
            </div>
        </div>

        <div class="uk-width-1-1@m">
            <div class="uk-card uk-card-default uk-card-body uk-border-rounded">
                <h3 class="uk-h4"><i class="fas fa-route"></i> <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_FORM_PARCOURS_CATEGORY_TITLE");?></h3>
                <div class="uk-child-width-1-2@m uk-grid-small" uk-grid>
                    <div><?php echo $this->form->renderField('course_id'); ?></div>
                    <div><?php echo $this->form->renderField('group_id'); ?></div>
                </div>
            </div>
        </div>

        <?php if($this->mapoption):?>
        <div class="uk-width-1-1@m">
            <div class="uk-card uk-card-default uk-card-body uk-border-rounded">
                <h3 class="uk-h4"><i class="fas fa-map"></i> <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_FORM_MAPS_TITLE");?></h3>
                <div class="uk-child-width-1-4@m uk-grid-small" uk-grid>
                    <div class="uk-width-expand">
                        <div>
                            <?php echo $this->mapoption->description; ?>
                        </div>
                        <div>
                            <?php echo Text::sprintf('COM_MARATHONMANAGER_TEXT_MAPS_ADDITIONAL_PRICE', 0); ?>
                        </div>
                    </div>
                    <div><?php echo $this->form->renderField('maps_count'); ?></div>
                </div>
            </div>
        </div>
        <?php endif;?>

        <div class="uk-width-1-1@m">
            <div class="uk-card uk-card-default uk-card-body uk-border-rounded">
                <h3 class="uk-h4"><i uk-icon="users"></i> <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_FORM_PARTICIPANTS_TITLE");?></h3>
                <?php echo $this->form->renderFieldset('participants'); ?>
            </div>
        </div>

        <div class="uk-width-1-1@m">
            <div class="uk-card uk-card-default uk-card-body uk-border-rounded">
                <h3 class="uk-h4"><i uk-icon="pencil"></i> <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_FORM_LEGAL_TITLE");?></h3>
                <?php
                if(isset($this->event->privacy_policy_article_id)) {
                    $link = Route::_('index.php?option=com_content&view=article&id=' . $this->event->privacy_policy_article_id);
                    echo $this->form->renderField('privacy_policy');
                    echo '<small class="form-text">' . Text::sprintf('COM_MARATHONMANAGER_PRIVACY_POLICY_TEXT', $link) . '</small>';
                }
                echo $this->form->renderField('insurance_notice');
                ?>
            </div>
        </div>
    </div>
    <div class="uk-margin">
        <div class="uk-grid-small uk-flex uk-flex-right">
            <div>
                <!-- Save Form -->
                <button class="uk-button-primary uk-button-large uk-border-rounded" data-submit-task="registration.submit"><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_REGISTER_NOW_BTN");?></button>
            </div>
        </div>
    </div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="return" value="<?php echo $this->return_page; ?>">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
