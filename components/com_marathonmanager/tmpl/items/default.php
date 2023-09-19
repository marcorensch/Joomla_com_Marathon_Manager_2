<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


\defined('_JEXEC') or die;

$params = $this->get('State')->get('params');

?>

<?php echo '<pre>' . var_export( $params, 1) . '</pre>'; ?>
<?php echo '<pre>' . var_export( $this->params, 1) . '</pre>'; ?>
<?php echo '<pre>' . var_export( $this->items, 1) . '</pre>'; ?>