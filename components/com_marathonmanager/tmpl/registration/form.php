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
    ->useScript('com_marathonmanager.form-edit');

$wa->useStyle('com_marathonmanager.form-edit');

?>

<form class="uk-form" action="<?php echo Route::_('index.php?option=com_marathonmanager&id='.$this->event->id); ?>"
      method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
    <div class="uk-grid-small uk-child-width-1-1 uk-child-width-1-2@m" uk-grid>
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-border-rounded">
                <h3 class="uk-h4"><i uk-icon="users"></i> Team</h3>
                <?php echo $this->form->renderFieldset('teamdata'); ?>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default uk-card-body uk-border-rounded">
                <h3 class="uk-h4"><i uk-icon="mail"></i> Kontakt</h3>
                <?php echo $this->form->renderFieldset('contact'); ?>
            </div>
        </div>
        <div class="uk-width-1-1@m">
            <div class="uk-card uk-card-default uk-card-body uk-border-rounded">
                <h3 class="uk-h4"><i uk-icon="users"></i> Participants</h3>
                <?php echo $this->form->renderFieldset('participants'); ?>
            </div>
        </div>
        <div class="uk-width-1-1@m">
            <div class="uk-card uk-card-default uk-card-body uk-border-rounded">
                <h3 class="uk-h4"><i uk-icon="pencil"></i> Legal Agreements</h3>
                <?php
                if(isset($this->event->privacy_policy_article_id)) {
                    echo $this->form->renderField('privacy_policy');
                    echo Text::_('COM_MARATHONMANAGER_PRIVACY_POLICY_TEXT', $this->event->privacy_policy_article_id);
                }
                ?>
            </div>
        </div>
    </div>
    <div class="uk-margin">
        <div class="uk-grid-small uk-flex uk-flex-right">
            <div>
                <button class="uk-button-danger uk-button-large uk-border-rounded">Löschen</button>
            </div>
            <div>
                <!-- Save Form -->
                <button class="uk-button-primary uk-button-large uk-border-rounded" data-submit-task="registration.submit">Registrieren</button>
            </div>
        </div>
    </div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="return" value="<?php echo $this->return_page; ?>">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
