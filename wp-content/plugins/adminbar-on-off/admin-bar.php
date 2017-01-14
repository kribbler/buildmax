<?php
/*
Author: IndoWP.com
Author URI: http://www.indowp.com
Plugin Name: IndoWP AdminBar On Off
Short Name: IndoWp AdminBar On Off
Description: IndoWp AdminBar, you can (admin or users) can set it on your own from your control panel, wp-admin/profile.php. After installing it you there will be 2 additional option there..
Version: 1.0
Tags: adminbar, admin-bar, admin. admin bar
Contributors: IndoWP.com
Plugin URI: http://www.indowp.com/plugins/indowp-adminbar-plugins
*/

( WP_DEBUG === true ) && error_log("!! ".__FILE__.':'.__LINE__." included!");
!defined('ABSPATH') || !function_exists('add_action') || !function_exists('plugins_url') || !function_exists('add_management_page') || !function_exists('wp_die') && exit;



function indowp_adminbar_personal_options_init()
{
	global $wpdb, $wp_query, $current_user;
	if(!is_user_logged_in())return;
	
	$show_admin_bar_backend=_get_admin_bar_pref( 'backend', $current_user->ID );
	
	if(!$show_admin_bar_backend && is_admin())
	{
		add_filter('wp_admin_bar_class', create_function('', 'return "none".rand(1000,666666);'), 9999999);
		add_filter('show_admin_bar', create_function('', 'return false;'), 999999);
		add_filter('show_admin_bar', create_function('', 'return false;'), 1);
		add_action('admin_head', create_function('', 'echo \'<style type="text/css">body.admin-bar {padding-top:0 !important;}</style>\';'));
	}
	
}
add_action( 'init', 'indowp_adminbar_personal_options_init',0); 




function indowp_adminbar_personal_options( $profileuser )
{
	global $wpdb, $wp_query;
	$show_admin_bar_backend=_get_admin_bar_pref( 'backend', $profileuser->ID );
?>
<tr class="show-admin-bar-backend">
<th scope="row">IndoWP AdminBar Frontend</th>
<td><fieldset><legend class="screen-reader-text"><span>IndoWP AdminBar Backend</span></legend>
<label for="admin_bar_backend">
<input name="admin_bar_backend" type="checkbox" id="admin_bar_backend" value="1"<?php checked( _get_admin_bar_pref( 'backend', $profileuser->ID ) ); ?> />
Show Toolbar when in backend</label><br />
</fieldset>
</td>
</tr><?php
}
add_action( 'personal_options', 'indowp_adminbar_personal_options', 10, 1 );




function indowp_adminbar_handle_post($useridtoedit)
{
	global $wpdb, $wp_query, $current_user;
	$show_admin_bar_backend=_get_admin_bar_pref( 'backend', $useridtoedit );
	
	$userID=(int)$useridtoedit;
	if ( !current_user_can('edit_user', $userID) ) wp_die(__('You do not have permission to edit this user.'));
	
	$show_admin_bar_backend=(isset( $_POST['admin_bar_backend'] ) ? 'true' : 'false');
	update_user_option($userID, "show_admin_bar_backend", $show_admin_bar_backend);
}
add_action( 'personal_options_update', 'indowp_adminbar_handle_post', 10, 1);
add_action( 'edit_user_profile_update', 'indowp_adminbar_handle_post', 10, 1);

?>
