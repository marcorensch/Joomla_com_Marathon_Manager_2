<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
// Helper Script to create Export Filename WITHOUT extension
$filenameScript = <<<JS
     document.addEventListener("DOMContentLoaded", () => {
        const fileNameInput = document.querySelector('#jform_filename');
        const exportTypeInput = document.querySelector('#jform_export_type');
        const eventIdInput = document.querySelector('#jform_event_id');
        let eventSelectionLabel = eventIdInput.options[eventIdInput.selectedIndex].innerHTML;
        const currentDate = new Date();
        const dateString = currentDate.getFullYear() + '-' + (currentDate.getMonth() + 1) + '-' + currentDate.getDate();
        
        buildNewFilename();
        
        function buildNewFilename() {
            eventSelectionLabel = eventIdInput.options[eventIdInput.selectedIndex].innerHTML;
            eventSelectionLabel = eventSelectionLabel.replace(/\s/g, '_').toLowerCase();
            fileNameInput.value = exportTypeInput.value + '_' + eventSelectionLabel + '_' + dateString;
        }
        
        exportTypeInput.addEventListener('change', () => {
            buildNewFilename();
        });
        
        eventIdInput.addEventListener('change', () => {
            buildNewFilename();
        });
        
      });
 JS;
$wa = $this->document->getWebAssetManager();
$wa->addInlineScript($filenameScript);

$route = Route::_('index.php?option=com_marathonmanager&view=export');

?>
<div class="row d-flex justify-content-center">
    <div class="col-lg-6">
        <h1><?php echo Text::_('COM_MARATHONMANAGER_EXPORT'); ?></h1>
        <p>Blablabla</p>
    </div>
</div>
<form action="<?php echo $route ?>" method="post" name="adminForm" id="adminForm">
    <div class="row d-flex justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <?php echo $this->getForm()->renderField('export_type'); ?>
                    <?php echo $this->getForm()->renderField('event_id'); ?>
                    <?php echo $this->getForm()->renderField('only_paid'); ?>
                    <?php echo $this->getForm()->renderField('create_team_numbers'); ?>
                    <?php echo $this->getForm()->renderField('filename'); ?>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary" type="submit">Start Export</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="task" value="export.export">
    <?php echo HTMLHelper::_('form.token'); ?>

</form>