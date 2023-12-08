<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Language\Text;

\defined('_JEXEC') or die;

?>
<section>
    <h2><?php echo Text::_("COM_MARATHONMANAGER_SITE_DESCRIPTION_LABEL")?></h2>
    <div id="event-description">
        <?php echo $this->item->description; ?>
    </div>
</section>

