<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Date\Date;

\defined('_JEXEC') or die;
$result = $displayData['result'];
?>

<?php if ($result->participants): ?>
    <ul class="nxd-result-accordion" uk-accordion>
        <li>
            <a class="uk-accordion-title" href>
                <span class="nxd-team-name"><?php echo $result->team_name; ?></span>
            </a>
            <div class="uk-accordion-content">
                <?php foreach ($result->participants as $participant): ?>
                    <div class="uk-grid-small uk-child-width-1-2@s" uk-grid>
                        <div>
                            <span class="nxd-participant-firstname">
                                <?php echo $participant['first_name']; ?>
                            </span>
                            <span class="nxd-participant-lastname">
                                <?php echo $participant['last_name']; ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </li>
    </ul>
<?php else: ?>
    <span class="nxd-team-name"><?php echo $result->team_name; ?></span>
<?php endif; ?>
