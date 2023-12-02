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

$countOfColumnsInLongestRow = 0;
foreach ($this->importData as $row) {
    if ($row && count($row) > $countOfColumnsInLongestRow) {
        $countOfColumnsInLongestRow = count($row);
    }
}

// create the table header
$columnAlphabetical = [];
$i = 0;
for($x = 'A'; $x < 'ZZ'; $x++) {
    $columnAlphabetical[] = $x;
    $i++;
    if($i == $countOfColumnsInLongestRow){
        break;
    }
}

$action = 'index.php?option=com_marathonmanager&task=results.processdata&tmpl=component&' . Session::getFormToken() . '=1'
?>
<div class="row pb-4">
    <div class="col-12">
        <h2><?php echo Text::_("COM_MARATHONMANAGER_SELECT_COLUMNS_HEADER"); ?></h2>
        <p><?php echo Text::_("COM_MARATHONMANAGER_SELECT_COLUMNS_TEXT"); ?></p>
    </div>
</div>
<form action="<?php echo $action;?>" method="post" name="adminForm" id="adminForm">
    <div class="col-8 col-md-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="control-group">
                    <div class="control-label">
                        <label for="column_main_import_trigger"><?php echo Text::_("COM_MARATHONMANAGER_COLUMN_MAIN_IMPORT_TRIGGER_LABEL"); ?></label>
                    </div>
                    <div class="controls">
                        <select name="jform[main_import_trigger_column]" id="jform[main_import_trigger_column]"
                                class="form-control">
                            <?php for ($i = 0; $i < $countOfColumnsInLongestRow; $i++) {
                                echo '<option value="' . $i . '">' . Text::sprintf("COM_MARATHONMANAGER_COLUMN_WITH_INDEX_LABEL", $columnAlphabetical[$i] . ' ('.($i+1).')') . '</option>';
                            } ?>
                        </select>
                    </div>
                    <div>
                        <small class="form-text"><?php echo Text::_("COM_MARATHONMANAGER_COLUMN_MAIN_IMPORT_TRIGGER_DESC"); ?></small>
                    </div>
                </div>
                <div class="control-group">
                    <button type="reset" class="btn btn-danger" onclick="return Joomla.task('results.cancelImport')">Cancel</button>
                    <button type="submit" class="btn btn-success">Import Data</button>
                </div>
            </div>
        </div>
        <input type="hidden" value="">
    </div>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th scope="col"></th>
            <?php
            for ($colIndex = 0; $colIndex < $countOfColumnsInLongestRow; $colIndex++) {
                $colNumber = $colIndex + 1;
                echo "<th scope='col' class='text-center'>{$columnAlphabetical[$colIndex]} ({$colNumber})</th>";
            } ?>
        </tr>
        </thead>
        <tbody>
        <!-- DropDown Row -->
        <tr>
            <td></td>
            <?php
            for ($colIndex = 0; $colIndex < $countOfColumnsInLongestRow; $colIndex++) {
                echo "    <td style='min-width:50px'>
                                <select name='jform[import_map][{$colIndex}]' class='form-control'>
                                    <option value=''>".Text::_('COM_MARATHONMANAGER_SELECT_COLUMN_LABEL')."</option>
                                    <option value='place'>".Text::_('COM_MARATHONMANAGER_SELECT_COLUMN_PLACE')."</option>
                                    <option value='team_name'>".Text::_('COM_MARATHONMANAGER_SELECT_COLUMN_TEAM_NAME')."</option>
                                    <option value='team_number'>".Text::_('COM_MARATHONMANAGER_SELECT_COLUMN_TEAM_NUMBER')."</option>
                                    <option value='category'>".Text::_('COM_MARATHONMANAGER_SELECT_COLUMN_CATEGORY')."</option>
                                    <option value='points_total'>".Text::_('COM_MARATHONMANAGER_SELECT_COLUMN_PTS_TOT')."</option>
                                    <option value='time_total'>".Text::_('COM_MARATHONMANAGER_SELECT_COLUMN_TIME_TOT')."</option>
                                    <option value='penalties'>".Text::_('COM_MARATHONMANAGER_SELECT_COLUMN_PENALTIES')."</option>
                                </select> 
                         </td>";
            } ?>
        </tr>
        <?php
        $rowIndex = 0;
        foreach ($this->importData as $row) :
            if ($rowIndex > 20) break;
            if ($row):
                $rowIndex++;
                ?>
                <tr>
                    <th scope="row"><?php echo $rowIndex; ?></th>
                    <?php foreach ($row as $key => $value) : ?>
                        <td class="small">
                            <?php echo $value; ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php
            endif;
        endforeach;
        ?>
        </tbody>
    </table>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>