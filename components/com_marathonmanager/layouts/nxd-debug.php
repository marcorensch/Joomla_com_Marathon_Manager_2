<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;
$items = $displayData['items'];
$params = $displayData['params'];

?>


<section class="uk-section uk-section-muted uk-padding-remove">
    <div class="uk-card uk-card-body">
        <h3 class="uk-h3">NXD DEBUG</h3>
        <div class="nxd-accordion uk-position-relative">
            <ul uk-accordion>
                <li>
                    <a class="uk-accordion-title" href>Component Parameters</a>
                    <div class="uk-accordion-content">
                        <pre>
                            <?php echo json_encode($params, JSON_PRETTY_PRINT); ?>
                        </pre>
                    </div>
                </li>
                <li>
                    <a class="uk-accordion-title" href>Component Items</a>
                    <div class="uk-accordion-content">
                        <pre><?php echo json_encode($items, JSON_PRETTY_PRINT); ?></pre>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</section>
