<?php
/**
 * WooCommerce Tab Manager
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@foxrunsoftware.net so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Tab Manager to newer
 * versions in the future. If you wish to customize WooCommerce Tab Manager for your
 * needs please refer to http://wcdocs.woothemes.com/user-guide/extensions/tab-manager/
 *
 * @package     WC-Tab-Manager/Templates
 * @author      Justin Stern
 * @copyright   Copyright (c) 2012-2013, Justin Stern
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

/**
 * Tab Manager product tab template
 *
 * WC < 2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product;
?>
<li class="<?php echo $tab['name']; ?>_tab"><a href="#tab-<?php echo $tab['name']; ?>"><?php echo apply_filters( 'woocommerce_tab_manager_tab_title', __( $tab['title'], WC_Tab_Manager::TEXT_DOMAIN ), $product, $tab ) ?></a></li>
