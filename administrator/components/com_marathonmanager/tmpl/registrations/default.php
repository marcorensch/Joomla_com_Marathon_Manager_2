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
use Joomla\CMS\Session\Session;

$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns');

$route = Route::_('index.php?option=com_marathonmanager&view=registrations');
$user = Factory::getApplication()->getIdentity();
$canEdit = $user->authorise('core.edit', 'com_marathonmanager');
$canSetPaymentStatus = $user->authorise('marathonmanager.edit.payment', 'com_marathonmanager');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder === 'a.ordering';
$saveOrderingUrl = '';

if ($saveOrder && !empty($this->items)) {
    $saveOrderingUrl = 'index.php?option=com_marathonmanager&task=registrations.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
    HTMLHelper::_('draggablelist.draggable');
}

?>
<form action="<?php echo $route ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-warning">
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else: ?>
                    <table class="table" id="itemsList">
                        <caption class="visually-hidden">
                            <?php echo Text::_('COM_MARATHONMANAGER_TABLE_CAPTION'); ?>,
                            <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                            <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                        </caption>
                        <thead>
                        <tr>
                            <td style="width: 1%" class="text-center">
                                <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </td>
                            <th scope="col" style="width:1%" class="text-center d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-sort'); ?>
                            </th>
                            <th scope="col" style="width: 1%; min-width: 85px" class="text-center">
                                <?php echo TEXT::_('JSTATUS'); ?>
                            </th>
                            <th scope="col" style="min-width: 150px" class="d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_MARATHONMANAGER_TABLE_TABLEHEAD_TEAM_NAME', 'a.team_name', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" style="min-width: 100px" class="d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_MARATHONMANAGER_TABLE_TABLEHEAD_TEAM_PARCOURS', 'course_name', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" style="min-width: 100px" class="d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_MARATHONMANAGER_TABLE_TABLEHEAD_TEAM_GROUP', 'group_name', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" style="min-width: 50px" class="d-none d-md-table-cell">
                                <?php echo Text::_('COM_MARATHONMANAGER_TABLE_TABLEHEAD_PARTICIPANTS'); ?>
                            </th>
                            <th scope="col" style="min-width: 150px" class="d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_MARATHONMANAGER_TABLE_TABLEHEAD_EVENT', 'event_name', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" style="width: 10%" class="d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_MARATHONMANAGER_TABLE_TABLEHEAD_REGISTRATION_DATE', 'a.created', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" style="min-width: 50px" class="d-none d-md-table-cell">
                                <?php echo Text::_('COM_MARATHONMANAGER_TABLE_TABLEHEAD_REFERENCE'); ?>
                            </th>
                            <th scope="col" style="width: 10%; min-width: 85px" class="d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_MARATHONMANAGER_TABLE_TABLEHEAD_PAYMENT_STATUS', 'a.payment_status', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" style="width: 1%;" class="d-none d-md-table-cell">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_MARATHONMANAGER_TABLE_TABLEHEAD_ID', 'a.id', $listDirn, $listOrder); ?>
                            </th>
                        </tr>
                        </thead>
                        <tbody
                            <?php if ($saveOrder && $saveOrderingUrl) : ?>
                                class="js-draggable"
                                data-url="<?php echo $saveOrderingUrl; ?>"
                                data-direction="<?php echo strtolower($listDirn); ?>"
                                data-nested="true"<?php
                            endif; ?>
                        >
                        <?php
                        $n = count($this->items);
                        foreach ($this->items as $i => $item):
                            ?>
                            <tr class="row<?php echo $i % 2; ?>"
                                <?php if($canEdit):?>
                                data-draggable-group="listItems"
                                <?php endif;?>
                            >
                                <td class="text-center">
                                    <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                </td>
                                <td class="text-center d-none d-md-table-cell">
                                    <?php
                                    $iconClass = '';
                                    if (!$canEdit) {
                                        $iconClass = ' inactive';
                                    } elseif (!$saveOrder) {
                                        $iconClass = ' inactive" title="' . Text::_('JORDERINGDISABLED');
                                    }
                                    ?>
                                    <span class="sortable-handler <?php echo $iconClass ?>">
                                            <span class="icon-ellipsis-v" aria-hidden="true"></span>
                                        </span>
                                    <?php if ($canEdit && $saveOrder) : ?>
                                        <input type="text" name="order[]" size="5"
                                               value="<?php echo $item->ordering; ?>"
                                               class="width-20 text-area-order hidden">
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'registrations.', true, 'cb'); ?>
                                </td>

                                <th scope="row" class="has-context">
                                    <?php if($canEdit):?>
                                    <a class="hasTooltip"
                                       href="<?php echo Route::_('index.php?option=com_marathonmanager&task=registration.edit&id=' . (int)$item->id); ?>"
                                       title="<?php echo Text::_('JACTION_EDIT'); ?>">
                                        <?php echo $this->escape($item->team_name); ?>
                                    </a>
                                    <?php else:?>
                                        <?php echo $this->escape($item->team_name); ?>
                                    <?php endif;?>
                                </th>
                                <td class="">
                                    <?php echo $item->course_name ?>
                                </td>
                                <td class="">
                                    <?php echo $item->group_name ?>
                                </td>
                                <td class="small">
                                    <?php
                                    foreach ($item->participants as $participant){
                                        echo $participant->first_name . " " . $participant->last_name . "<br>";
                                    }
                                    ?>
                                </td>

                                <td>
                                    <?php echo $item->event_name ?>
                                </td>

                                <td>
                                    <?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')); ?>
                                </td>
                                <td>
                                    <?php echo $item->reference ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    $task = $item->payment_status == 1 ? 'setunpaid' : 'setpaid';
                                    $class = $item->payment_status == 1 ? 'fas fa-check-circle text-success' : 'fas fa-times-circle text-danger';
                                    $label = $item->payment_status == 0 ? 'COM_MARATHONMANAGER_TABLE_LABEL_PAYMENT_SET_STATUS_PAID' : 'COM_MARATHONMANAGER_TABLE_LABEL_PAYMENT_SET_STATUS_UNPAID';
                                    $action = Text::_($label);
                                    echo '<a href="#" onclick="return Joomla.listItemTask(\'cb' . $i . '\',\'' . 'registration.' . $task . '\')" title="' . $action . '">';
                                    echo '<i class="' . $class . ' fa-lg" title="' . Text::_($label) . '"></i>';
                                    echo '</a>';
                                    ?>
                                </td>
                                <td>
                                    <?php echo $item->id ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <input type="hidden" name="task" value="">
                <input type="hidden" name="boxchecked" value="">
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>