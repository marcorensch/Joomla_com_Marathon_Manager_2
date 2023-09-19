<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$app = Factory::getApplication();
$params = $this->get('State')->get('params');
?>

<h1><?php echo $this->msg; ?></h1>
<?php if ($params->get('show_footer', 1)) : ?>
    <div class="footer" style="margin-top:40px; padding:30px; border-radius:6px; background:#1e1e1e; color: #fff; text-align: center;">
        <strong>Footer is shown</strong>
    </div>
<?php endif; ?>