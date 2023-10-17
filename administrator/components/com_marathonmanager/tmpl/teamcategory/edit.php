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
$wa->addInlineStyle('.control-group .control-label{width:100%;} .control-group .controls{min-width:10px}');

$layout = 'edit';
$tmpl = $input->get('tmpl', '', 'CMD') === 'component' ? '&tmpl=component' : '';
$action = Route::_('index.php?option=com_marathonmanager&layout=' . $layout . $tmpl . '&id=' . (int)$this->item->id);
?>

<form action="<?php echo $action; ?>" method="post" name="adminForm" id="item-form" class="form-validate form-vertical">

    <?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <div class="main-card">
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'base']); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'base', Text::_('COM_MARATHONMANAGER_DETAILS_TAB_TITLE')); ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-4">
                        <?php echo $this->getForm()->renderField('marathon_id'); ?>
                    </div>
                    <div class="col-lg-4">
                        <?php echo $this->getForm()->renderField('group_id'); ?>
                    </div>
                    <div class="col-lg-4">
                        <?php echo $this->getForm()->renderField('max_participants'); ?>
                    </div>
                </div>
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

