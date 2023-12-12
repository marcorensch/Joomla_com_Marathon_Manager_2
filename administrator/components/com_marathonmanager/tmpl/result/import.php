<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

\defined('_JEXEC') or die;
$app   = Factory::getApplication();
$input = $app->input;

$this->ignore_fieldsets = ['item_associations'];
$this->useCoreUI        = true;

$isModal = $input->get('layout') === 'modal';

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

$type = $input->get('type', 'default', 'cmd');
$tmpl   = $input->get('tmpl', '', 'cmd') === 'component' ? '&tmpl=component' : '';

$current_user = Factory::getApplication()->getIdentity();

$title = 'Import';
$formActionUrl = "index.php?option=com_marathonmanager&view=result{$tmpl}";
?>
<form action="<?php echo Route::_($formActionUrl); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm">
    <div class="row d-flex justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <?php echo $this->getForm()->renderFieldset('import'); ?>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary" type="submit"><?php echo Text::_("COM_MARATHONMANAGER_UPLOAD_FILE");?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="task" value="results.import">
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
