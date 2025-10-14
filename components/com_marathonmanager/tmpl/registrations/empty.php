<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;

$headerLayout = new FileLayout('marathon-header', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
echo $headerLayout->render();

?>

<div class="uk-section">
    <div class="uk-container">
        <div class="uk-card uk-card-default uk-card-large">
            <div class="uk-card-body uk-text-center">
                <span class="uk-text-lead">
                    <?php echo Text::_("COM_MARATHONMANAGER_NO_REGISTRATIONS");?>
                </span>
            </div>
        </div>
    </div>
</div>
