<?php
/**
 * WC Email Inquiry Functions
 *
 * Table Of Contents
 *
 * check_hide_add_cart_button()
 * check_hide_price()
 * check_add_email_inquiry_button()
 * check_add_email_inquiry_button_on_shoppage()
 * reset_products_to_global_settings()
 * email_inquiry()
 * get_from_address()
 * get_from_name()
 * get_content_type()
 * plugin_extension()
 * wc_ei_yellow_message_dontshow()
 * wc_ei_yellow_message_dismiss()
 * upgrade_version_1_0_3()
 * upgrade_version_1_0_6()
 * ultimate_upgrade_version_1_0_5()
 */
class WC_Email_Inquiry_Functions 
{	
	
	public static function check_hide_add_cart_button ($product_id) {
		global $wc_email_inquiry_rules_roles_settings;
		$wc_email_inquiry_settings_custom = get_post_meta( $product_id, '_wc_email_inquiry_settings_custom', true);
			
		if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_addcartbt'])) $wc_email_inquiry_hide_addcartbt = $wc_email_inquiry_rules_roles_settings['hide_addcartbt'] ;
		else $wc_email_inquiry_hide_addcartbt = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_addcartbt']);
		
		// dont hide add to cart button if setting is not checked and not logged in users
		if ($wc_email_inquiry_hide_addcartbt == 'no' && !is_user_logged_in() ) return false;
		
		// hide add to cart button if setting is checked and not logged in users
		if ($wc_email_inquiry_hide_addcartbt != 'no' &&  !is_user_logged_in()) return true;
		
		if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_addcartbt_after_login'])) $wc_email_inquiry_hide_addcartbt_after_login = $wc_email_inquiry_rules_roles_settings['hide_addcartbt_after_login'] ;
		else $wc_email_inquiry_hide_addcartbt_after_login = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_addcartbt_after_login']);		

		// don't hide add to cart if for logged in users is deacticated
		if ( $wc_email_inquiry_hide_addcartbt_after_login != 'yes' ) return false;
		
		if (!isset($wc_email_inquiry_settings_custom['role_apply_hide_cart'])) {
			$role_apply_hide_cart = (array) $wc_email_inquiry_rules_roles_settings['role_apply_hide_cart'];
		} else {
			$role_apply_hide_cart = (array) $wc_email_inquiry_settings_custom['role_apply_hide_cart'];
		}
		
		$user_login = wp_get_current_user();
		if (is_array($user_login->roles) && count($user_login->roles) > 0) {
			$user_role = '';
			foreach ($user_login->roles as $role_name) {
				$user_role = $role_name;
				break;
			}
			// hide add to cart button if current user role in list apply role
			if ( in_array($user_role, $role_apply_hide_cart) ) return true;
		}
		return false;
		
	}
	
	public static function check_hide_price ($product_id) {
		global $wc_email_inquiry_rules_roles_settings;
		$wc_email_inquiry_settings_custom = get_post_meta( $product_id, '_wc_email_inquiry_settings_custom', true);
			
		if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_price'])) $wc_email_inquiry_hide_price = $wc_email_inquiry_rules_roles_settings['hide_price'];
		else $wc_email_inquiry_hide_price = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_price']);
			
		// dont hide price if setting is not checked and not logged in users
		if ($wc_email_inquiry_hide_price == 'no' && !is_user_logged_in() ) return false;
		
		// alway hide price if setting is checked and not logged in users
		if ($wc_email_inquiry_hide_price != 'no' && !is_user_logged_in()) return true;
		
		if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_price_after_login'])) $wc_email_inquiry_hide_price_after_login = $wc_email_inquiry_rules_roles_settings['hide_price_after_login'] ;
		else $wc_email_inquiry_hide_price_after_login = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_price_after_login']);		

		// don't hide price if for logged in users is deacticated
		if ( $wc_email_inquiry_hide_price_after_login != 'yes' ) return false;
		
		if (!isset($wc_email_inquiry_settings_custom['role_apply_hide_price'])) {
			$role_apply_hide_price = (array) $wc_email_inquiry_rules_roles_settings['role_apply_hide_price'];
		} else {
			$role_apply_hide_price = (array) $wc_email_inquiry_settings_custom['role_apply_hide_price'];
		}
		
		$user_login = wp_get_current_user();		
		if (is_array($user_login->roles) && count($user_login->roles) > 0) {
			$user_role = '';
			foreach ($user_login->roles as $role_name) {
				$user_role = $role_name;
				break;
			}
			// hide price if current user role in list apply role
			if ( in_array($user_role, $role_apply_hide_price) ) return true;
		}
		
		return false;
	}
	
	public static function check_add_email_inquiry_button ($product_id) {
		global $wc_email_inquiry_global_settings;
		$wc_email_inquiry_settings_custom = get_post_meta( $product_id, '_wc_email_inquiry_settings_custom', true);
			
		if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_show_button'])) $wc_email_inquiry_show_button = $wc_email_inquiry_global_settings['show_button'];
		else $wc_email_inquiry_show_button = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_show_button']);
		
		// dont show email inquiry button if setting is not checked and not logged in users
		if ($wc_email_inquiry_show_button == 'no' && !is_user_logged_in() ) return false;
		
		// alway show email inquiry button if setting is checked and not logged in users
		if ($wc_email_inquiry_show_button != 'no' && !is_user_logged_in()) return true;
		
		if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_show_button_after_login'])) $wc_email_inquiry_show_button_after_login = $wc_email_inquiry_global_settings['show_button_after_login'] ;
		else $wc_email_inquiry_show_button_after_login = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_show_button_after_login']);		

		// don't show email inquiry button if for logged in users is deacticated
		if ( $wc_email_inquiry_show_button_after_login != 'yes' ) return false;
		
		if (!isset($wc_email_inquiry_settings_custom['role_apply_show_inquiry_button'])) $role_apply_show_inquiry_button = (array) $wc_email_inquiry_global_settings['role_apply_show_inquiry_button'];
		else $role_apply_show_inquiry_button = (array) $wc_email_inquiry_settings_custom['role_apply_show_inquiry_button'];
		
		
		$user_login = wp_get_current_user();		
		if (is_array($user_login->roles) && count($user_login->roles) > 0) {
			$user_role = '';
			foreach ($user_login->roles as $role_name) {
				$user_role = $role_name;
				break;
			}
			// show email inquiry button if current user role in list apply role
			if ( in_array($user_role, $role_apply_show_inquiry_button) ) return true;
		}
		
		return false;
		
	}
	
	public static function check_add_email_inquiry_button_on_shoppage ($product_id=0) {
		global $wc_email_inquiry_global_settings;
		$wc_email_inquiry_settings_custom = get_post_meta( $product_id, '_wc_email_inquiry_settings_custom', true);
			
		if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_single_only'])) $wc_email_inquiry_single_only = $wc_email_inquiry_global_settings['inquiry_single_only'];
		else $wc_email_inquiry_single_only = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_single_only']);
		
		if ($wc_email_inquiry_single_only == 'yes') return false;
		
		return WC_Email_Inquiry_Functions::check_add_email_inquiry_button($product_id);
		
	}
	
	public static function reset_products_to_global_settings() {
		global $wpdb;
		$wpdb->query( "DELETE FROM ".$wpdb->postmeta." WHERE meta_key='_wc_email_inquiry_settings_custom' " );
	}
	
	public static function email_inquiry($product_id, $your_name, $your_email, $your_phone, $your_message, $send_copy_yourself = 1) {
		global $wc_email_inquiry_contact_form_settings;
		$wc_email_inquiry_contact_success = stripslashes( get_option( 'wc_email_inquiry_contact_success', '' ) );
		$wc_email_inquiry_settings_custom = get_post_meta( $product_id, '_wc_email_inquiry_settings_custom', true);
		
		if ( WC_Email_Inquiry_Functions::check_add_email_inquiry_button($product_id) ) {
			
			if ( trim( $wc_email_inquiry_contact_success ) != '') $wc_email_inquiry_contact_success = wpautop(wptexturize(   $wc_email_inquiry_contact_success ));
			else $wc_email_inquiry_contact_success = __("Thanks for your inquiry - we'll be in touch with you as soon as possible!", 'wc_email_inquiry');
		
			if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_email_to']) || trim(esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_email_to'])) == '') $to_email = $wc_email_inquiry_contact_form_settings['inquiry_email_to'];
			else $to_email = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_email_to']);
			if (trim($to_email) == '') $to_email = get_option('admin_email');
			
			if ( $wc_email_inquiry_contact_form_settings['inquiry_email_from_address'] == '' )
				$from_email = get_option('admin_email');
			else
				$from_email = $wc_email_inquiry_contact_form_settings['inquiry_email_from_address'];
				
			if ( $wc_email_inquiry_contact_form_settings['inquiry_email_from_name'] == '' )
				$from_name = ( function_exists('icl_t') ? icl_t( 'WP',__('Blog Title','wpml-string-translation'), get_option('blogname') ) : get_option('blogname') );
			else
				$from_name = $wc_email_inquiry_contact_form_settings['inquiry_email_from_name'];
			
			if (!isset($wc_email_inquiry_settings_custom['wc_email_inquiry_email_cc']) || trim(esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_email_cc'])) == '') $cc_emails = $wc_email_inquiry_contact_form_settings['inquiry_email_cc'];
			else $cc_emails = esc_attr($wc_email_inquiry_settings_custom['wc_email_inquiry_email_cc']);
			if (trim($cc_emails) == '') $cc_emails = '';
			
			$headers = array();
			$headers[] = 'MIME-Version: 1.0';
			$headers[] = 'Content-type: text/html; charset='. get_option('blog_charset');
			$headers[] = 'From: '.$from_name.' <'.$from_email.'>';
			$headers_yourself = $headers;
			
			if (trim($cc_emails) != '') {
				$cc_emails_a = explode("," , $cc_emails);
				if (is_array($cc_emails_a) && count($cc_emails_a) > 0) {
					foreach ($cc_emails_a as $cc_email) {
						$headers[] = 'Cc: '.$cc_email;
					}
				} else {
					$headers[] = 'Cc: '.$cc_emails;
				}
			}
			
			$product_name = get_the_title($product_id);
			$product_url = get_permalink($product_id);
			$subject = wc_ei_ict_t__( 'Default Form - Email Subject', __('Email inquiry for', 'wc_email_inquiry') ).' '.$product_name;
			$subject_yourself = wc_ei_ict_t__( 'Default Form - Copy Email Subject', __('[Copy]: Email inquiry for', 'wc_email_inquiry') ).' '.$product_name;
			
			$content = '
	<table width="99%" cellspacing="0" cellpadding="1" border="0" bgcolor="#eaeaea"><tbody>
	  <tr>
		<td>
		  <table width="100%" cellspacing="0" cellpadding="5" border="0" bgcolor="#ffffff"><tbody>
			<tr bgcolor="#eaf2fa">
			  <td colspan="2"><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><strong>'.wc_ei_ict_t__( 'Default Form - Contact Name', __('Name', 'wc_email_inquiry') ).'</strong></font> 
			  </td></tr>
			<tr bgcolor="#ffffff">
			  <td width="20">&nbsp;</td>
			  <td><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px">[your_name]</font> </td></tr>
			<tr bgcolor="#eaf2fa">
			  <td colspan="2"><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><strong>'.wc_ei_ict_t__( 'Default Form - Contact Email', __('Email', 'wc_email_inquiry') ).'</strong></font> </td></tr>
			<tr bgcolor="#ffffff">
			  <td width="20">&nbsp;</td>
			  <td><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><a target="_blank" href="mailto:[your_email]">[your_email]</a></font> 
			  </td></tr>
			<tr bgcolor="#eaf2fa">
			  <td colspan="2"><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><strong>'.wc_ei_ict_t__( 'Default Form - Contact Phone', __('Phone', 'wc_email_inquiry') ).'</strong></font> </td></tr>
			<tr bgcolor="#ffffff">
			  <td width="20">&nbsp;</td>
			  <td><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px">[your_phone]</font> </td></tr>
			<tr bgcolor="#eaf2fa">
			  <td colspan="2"><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><strong>'.wc_ei_ict_t__( 'Default Form - Contact Product Name', __('Product Name', 'wc_email_inquiry') ).'</strong></font> </td></tr>
			<tr bgcolor="#ffffff">
			  <td width="20">&nbsp;</td>
			  <td><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><a target="_blank" href="[product_url]">[product_name]</a></font> </td></tr>
			<tr bgcolor="#eaf2fa">
			  <td colspan="2"><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px"><strong>'.wc_ei_ict_t__( 'Default Form - Contact Message', __('Message', 'wc_email_inquiry') ).'</strong></font> </td></tr>
			<tr bgcolor="#ffffff">
			  <td width="20">&nbsp;</td>
			  <td><font style="FONT-FAMILY:sans-serif;FONT-SIZE:12px">[your_message]</font> 
		  </td></tr></tbody></table></td></tr></tbody></table>';
		  
			$content = str_replace('[your_name]', $your_name, $content);
			$content = str_replace('[your_email]', $your_email, $content);
			$content = str_replace('[your_phone]', $your_phone, $content);
			$content = str_replace('[product_name]', $product_name, $content);
			$content = str_replace('[product_url]', $product_url, $content);
			$your_message = str_replace( '://', ':&#173;­//', $your_message );
			$your_message = str_replace( '.com', '&#173;.com', $your_message );
			$your_message = str_replace( '.net', '&#173;.net', $your_message );
			$your_message = str_replace( '.info', '&#173;.info', $your_message );
			$your_message = str_replace( '.org', '&#173;.org', $your_message );
			$your_message = str_replace( '.au', '&#173;.au', $your_message );
			$content = str_replace('[your_message]', wpautop( $your_message ), $content);
			
			$content = apply_filters('wc_email_inquiry_inquiry_content', $content);
			
			// Filters for the email
			add_filter( 'wp_mail_from', array( 'WC_Email_Inquiry_Functions', 'get_from_address' ) );
			add_filter( 'wp_mail_from_name', array( 'WC_Email_Inquiry_Functions', 'get_from_name' ) );
			add_filter( 'wp_mail_content_type', array( 'WC_Email_Inquiry_Functions', 'get_content_type' ) );
			
			wp_mail( $to_email, $subject, $content, $headers, '' );
			
			if ($send_copy_yourself == 1) {
				wp_mail( $your_email, $subject_yourself, $content, $headers_yourself, '' );
			}
			
			// Unhook filters
			remove_filter( 'wp_mail_from', array( 'WC_Email_Inquiry_Functions', 'get_from_address' ) );
			remove_filter( 'wp_mail_from_name', array( 'WC_Email_Inquiry_Functions', 'get_from_name' ) );
			remove_filter( 'wp_mail_content_type', array( 'WC_Email_Inquiry_Functions', 'get_content_type' ) );
			
			return $wc_email_inquiry_contact_success;
		} else {
			return wc_ei_ict_t__( 'Default Form - Contact Not Allow', __("Sorry, this product don't enable email inquiry.", 'wc_email_inquiry') );
		}
	}
	
	public static function get_from_address() {
		global $wc_email_inquiry_contact_form_settings;
		if ( $wc_email_inquiry_contact_form_settings['inquiry_email_from_address'] == '' )
			$from_email = get_option('admin_email');
		else
			$from_email = $wc_email_inquiry_contact_form_settings['inquiry_email_from_address'];
			
		return $from_email;
	}
	
	public static function get_from_name() {
		global $wc_email_inquiry_contact_form_settings;
		if ( $wc_email_inquiry_contact_form_settings['inquiry_email_from_name'] == '' )
			$from_name = ( function_exists('icl_t') ? icl_t( 'WP',__('Blog Title','wpml-string-translation'), get_option('blogname') ) : get_option('blogname') );
		else
			$from_name = $wc_email_inquiry_contact_form_settings['inquiry_email_from_name'];
			
		return $from_name;
	}
	
	public static function get_content_type() {
		return 'text/html';
	}
	
	/**
	 * Create Page
	 */
	public static function create_page( $slug, $option, $page_title = '', $page_content = '', $post_parent = 0 ) {
		global $wpdb;
				
		$page_id = $wpdb->get_var( "SELECT ID FROM `" . $wpdb->posts . "` WHERE `post_content` LIKE '%$page_content%'  AND `post_type` = 'page' ORDER BY ID DESC LIMIT 1" );
		 
		if ( $page_id != NULL ) 
			return $page_id;
		
		$page_data = array(
			'post_status' 		=> 'publish',
			'post_type' 		=> 'page',
			'post_name' 		=> $slug,
			'post_title' 		=> $page_title,
			'post_content' 		=> $page_content,
			'post_parent' 		=> $post_parent,
			'comment_status' 	=> 'closed'
		);
		$page_id = wp_insert_post( $page_data );
		
		if ( class_exists('SitePress') ) {
			global $sitepress;
			$source_lang_code = $sitepress->get_default_language();
			$wpdb->query( "UPDATE ".$wpdb->prefix . "icl_translations SET trid=".$page_id." WHERE element_id=".$page_id." AND language_code='".$source_lang_code."' AND element_type='post_page' " );
		}
						
		return $page_id;
	}
	
	public static function create_page_wpml( $trid, $lang_code, $source_lang_code, $slug, $page_title = '', $page_content = '' ) {
		global $wpdb;
		
		$element_id = $wpdb->get_var( "SELECT ID FROM " . $wpdb->posts . " AS p INNER JOIN " . $wpdb->prefix . "icl_translations AS ic ON p.ID = ic.element_id WHERE p.post_content LIKE '%$page_content%' AND p.post_type = 'page' AND p.post_status = 'publish' AND ic.trid=".$trid." AND ic.language_code = '".$lang_code."' AND ic.element_type = 'post_page' ORDER BY p.ID ASC LIMIT 1" );
		 
		if ( $element_id != NULL ) :
			return $element_id;
		endif;
		
		$page_data = array(
			'post_date'			=> gmdate( 'Y-m-d H:i:s' ),
			'post_modified'		=> gmdate( 'Y-m-d H:i:s' ),
			'post_status' 		=> 'publish',
			'post_type' 		=> 'page',
			'post_author' 		=> 1,
			'post_name' 		=> $slug,
			'post_title' 		=> $page_title,
			'post_content' 		=> $page_content,
			'comment_status' 	=> 'closed'
		);
		$wpdb->insert( $wpdb->posts , $page_data);
		$element_id = $wpdb->insert_id;
		
		//$element_id = wp_insert_post( $page_data );
		
		$wpdb->insert( $wpdb->prefix . "icl_translations", array(
				'element_type'			=> 'post_page',
				'element_id'			=> $element_id,
				'trid'					=> $trid,
				'language_code'			=> $lang_code,
				'source_language_code'	=> $source_lang_code,
			) );
				
		return $element_id;
	}
	
	public static function auto_create_page_for_wpml(  $trid, $slug, $page_title = '', $page_content = '' ) {
		if ( class_exists('SitePress') ) {
			global $sitepress;
			$active_languages = $sitepress->get_active_languages();
			if ( is_array($active_languages)  && count($active_languages) > 0 ) {
				$source_lang_code = $sitepress->get_default_language();
				foreach ( $active_languages as $language ) {
					if ( $language['code'] == $source_lang_code ) continue;
					WC_Email_Inquiry_Functions::create_page_wpml( $trid, $language['code'], $source_lang_code, $slug.'-'.$language['code'], $page_title.' '.$language['display_name'], $page_content );
				}
			}
		}
	}
		
	public static function get_product_information( $product_id, $show_product_name = 0, $width = 220, $height = 180, $class_image = '' ) {
		$image_src = WC_Email_Inquiry_Functions::get_post_thumbnail( $product_id, $width, $height, $class_image );
		if ( trim($image_src) == '' ) {
			$image_src = '<img alt="" src="'. ( ( version_compare( WOOCOMMERCE_VERSION, '2.1', '<' ) ) ? woocommerce_placeholder_img_src() : wc_placeholder_img_src() ) .'" class="'.$class_image.'" style="max-width:'.$width.'px !important; max-height:'.$height.'px !important;" />';
		}
		
		$product_information = '';
		ob_start();
	?>
    	<?php if ($show_product_name == 1) { ?>
        <div style="clear:both; margin-top:10px"></div>
		<div style="float:left; margin-right:10px;" class="wc_email_inquiry_default_image_container"><?php echo $image_src; ?></div>
        <div style="display:block; margin-bottom:10px; padding-left:22%;" class="wc_email_inquiry_product_heading_container">
        	<h1 class="wc_email_inquiry_custom_form_product_heading"><?php echo esc_html( get_the_title($product_id) ); ?></h1>
			<div class="wc_email_inquiry_custom_form_product_url_div"><a class="wc_email_inquiry_custom_form_product_url" href="<?php echo esc_url( get_permalink($product_id) ); ?>" title=""><?php echo esc_url( get_permalink($product_id) ); ?></a></div>
        </div>
        <div style="clear:both;"></div>
        <?php } else { ?>
        <?php echo $image_src; ?>
        <?php } ?>
	<?php
		$product_information = ob_get_clean();
		
		return $product_information;
	}
	
	public static function get_post_thumbnail( $postid=0, $width=220, $height=180, $class='') {
		$mediumSRC = '';
		// Get the product ID if none was passed
		if ( empty( $postid ) )
			$postid = get_the_ID();

		// Load the product
		$product = get_post( $postid );

		if (has_post_thumbnail($postid)) {
			$thumbid = get_post_thumbnail_id($postid);
			$attachmentArray = wp_get_attachment_image_src($thumbid, array(0 => $width, 1 => $height), false);
			$mediumSRC = $attachmentArray[0];
			if (trim($mediumSRC != '')) {
				return '<img class="'.$class.'" src="'.$mediumSRC.'" style="max-width:'.$width.'px !important; max-height:'.$height.'px !important;" />';
			}
		}
		if (trim($mediumSRC == '')) {
			$args = array( 'post_parent' => $postid , 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'DESC', 'orderby' => 'ID', 'post_status' => null);
			$attachments = get_posts($args);
			if ($attachments) {
				foreach ( $attachments as $attachment ) {
					$mediumSRC = wp_get_attachment_image( $attachment->ID, array(0 => $width, 1 => $height), true, array('class' => $class, 'style' => 'max-width:'.$width.'px !important; max-height:'.$height.'px !important;' ) );
					break;
				}
			}
		}

		if (trim($mediumSRC == '')) {
			// Get ID of parent product if one exists
			if ( !empty( $product->post_parent ) )
				$postid = $product->post_parent;

			if (has_post_thumbnail($postid)) {
				$thumbid = get_post_thumbnail_id($postid);
				$attachmentArray = wp_get_attachment_image_src($thumbid, array(0 => $width, 1 => $height), false);
				$mediumSRC = $attachmentArray[0];
				if (trim($mediumSRC != '')) {
					return '<img class="'.$class.'" src="'.$mediumSRC.'" style="max-width:'.$width.'px !important; max-height:'.$height.'px !important;" />';
				}
			}
			if (trim($mediumSRC == '')) {
				$args = array( 'post_parent' => $postid , 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'DESC', 'orderby' => 'ID', 'post_status' => null);
				$attachments = get_posts($args);
				if ($attachments) {
					foreach ( $attachments as $attachment ) {
						$mediumSRC = wp_get_attachment_image( $attachment->ID, array(0 => $width, 1 => $height), true, array('class' => $class, 'style' => 'max-width:'.$width.'px !important; max-height:'.$height.'px !important;' ) );
						break;
					}
				}
			}
		}
		return $mediumSRC;
	}
	
	public static function get_page_id_from_shortcode( $shortcode, $option ) {
		global $wpdb;
		$page_id = get_option($option);
		$shortcode = esc_sql( like_escape( $shortcode ) );
		$page_data = null;
		if ($page_id)
			$page_data = $wpdb->get_row( "SELECT ID FROM " . $wpdb->posts . " WHERE post_content LIKE '%[{$shortcode}]%' AND ID = '".$page_id."' AND post_type = 'page' LIMIT 1" );
		if ( $page_data == null )
			$page_data = $wpdb->get_row( "SELECT ID FROM `" . $wpdb->posts . "` WHERE `post_content` LIKE '%[{$shortcode}]%' AND `post_type` = 'page' ORDER BY post_date DESC LIMIT 1" );
			
		$page_id = $page_data->ID;
		
		// For WPML
		if ( class_exists('SitePress') ) {
			global $sitepress;
			$translation_page_data = null;
			$translation_page_data = $wpdb->get_row( $wpdb->prepare( "SELECT element_id FROM " . $wpdb->prefix . "icl_translations WHERE trid = %d AND element_type='post_page' AND language_code = %s LIMIT 1", $page_id , $sitepress->get_current_language() ) );
			if ( $translation_page_data != null )
				$page_id = $translation_page_data->element_id;
		}
		
		return $page_id;
	}

	public static function plugin_extension_start() {
		global $wc_ei_admin_init;
		
		$wc_ei_admin_init->plugin_extension_start();
	}
	
	public static function plugin_extension_end() {
		global $wc_ei_admin_init;
		
		$wc_ei_admin_init->plugin_extension_end();
	}
	
	public static function plugin_extension() {
		$html = '';
		$html .= '<a href="http://a3rev.com/shop/" target="_blank" style="float:right;margin-top:5px; margin-left:10px; clear:right;" ><div class="a3-plugin-ui-icon a3-plugin-ui-a3-rev-logo"></div></a>';
		$html .= '<h3>'.__('Thanks for purchasing a WooCommerce Email Inquiry Ultimate License.', 'wc_email_inquiry').'</h3>';
		$html .= '<p>'.__("All of that plugins features have been activated and are ready for you to use. Please view the", 'wc_email_inquiry').' <a href="http://docs.a3rev.com/user-guides/plugins-extensions/woocommerce/email-inquiry-ultimate/" target="_blank">'.__("plugins docs", 'wc_email_inquiry').'</a> '.__("for any assistance you might need.", 'wc_email_inquiry').':</p>';
		$html .= '<h3>'.__('Support', 'wc_email_inquiry').'</h3>';
		$html .= '<p>'.__('Please post all support requests to the plugins', 'wc_email_inquiry').' <a href="https://a3rev.com/forums/forum/woocommerce-plugins/email-inquiry-ultimate/" target="_blank">'.__('a3rev support forum', 'wc_email_inquiry').'</a></p>';
		$html .= '<h3>'.__('Whats this Yellow Section about?', 'wc_email_inquiry').'</h3>';
		$html .= '<p>'.__('Inside the Yellow border you can see all of the additional features that are available in the', 'wc_email_inquiry').' <a href="http://a3rev.com/shop/woocommerce-quotes-and-orders/" target="_blank">'.__('WooCommerce Quotes and Orders', 'wc_email_inquiry').'</a> '.__('plugin', 'wc_email_inquiry').'</p>';
		$html .= '<h3>'.__('Quotes and Orders 7 Day Free Trial.', 'wc_email_inquiry').'</h3>';
		$html .= '<p>'.__("If you'd like to see what all of the Quotes and Orders options features inside the yellow borders on here can enable you to make on your site you can do that for 7 days completely Free. Just go to the WooCommerce Quotes and Orders plugin page and sign up for the 7 day free trail.", 'wc_email_inquiry').'</p>';
		$html .= '<p>'.__('If  the plugin does not meet your need you can cancel the trail right from your', 'wc_email_inquiry').' <a href="http://a3rev.com/my-account/" target="_blank">'.__('a3rev dashboard', 'wc_email_inquiry').'</a> '.__("and you won't be charge the annual license subscription fee", 'wc_email_inquiry').'</p>';
		
		$html .= '<h3>'.__('If you do upgrade ...', 'wc_email_inquiry').'</h3>';
		$html .= '<p>'.__("Please note if you do that and are installing it on this site you must deactivate this plugin before you activate the Quotes and Orders plugin.", 'wc_email_inquiry').'</p>';
		
		$html .= '<h3>'.__('Special Offer', 'wc_email_inquiry').'</h3>';
		$html .= '<p>'.__("If you upgrade to the WooCommerce Quotes and Orders annual subscription license within 60 days of purchasing this plugin we will automatically send you a 100% refund for the WooCommerce Email Inquiry Ultimate Pro License you have purchased.", 'wc_email_inquiry').'</p>';
		
		$html .= '<p>&nbsp;</p><p>'.__("Thank you and all the best.", 'wc_email_inquiry').'</p>';
		$html .= '<p>'.__("Steve and the team @ a3rev.", 'wc_email_inquiry').'</p>';
		
		return $html;
	}
	
	public static function wc_ei_yellow_message_dontshow() {
		check_ajax_referer( 'wc_ei_yellow_message_dontshow', 'security' );
		$option_name   = $_REQUEST['option_name'];
		update_option( $option_name, 1 );
		die();
	}
	
	public static function wc_ei_yellow_message_dismiss() {
		check_ajax_referer( 'wc_ei_yellow_message_dismiss', 'security' );
		$session_name   = $_REQUEST['session_name'];
		if ( !isset($_SESSION) ) { session_start(); } 
		$_SESSION[$session_name] = 1 ;
		die();
	}
	
	public static function upgrade_version_1_0_3() {
		global $wpdb, $wp_roles;
		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
		$roles = $wp_roles->get_names();
		$wc_email_inquiry_user = esc_attr(get_option('wc_email_inquiry_user'));
		if ($wc_email_inquiry_user == 'yes') {
			update_option('wc_email_inquiry_role_apply_hide_cart', (array) array_keys($roles));
			update_option('wc_email_inquiry_role_apply_hide_price', (array) array_keys($roles));
			update_option('wc_email_inquiry_role_apply_show_inquiry_button', (array) array_keys($roles));
		}
		
		$products_email_inquiry_settings_custom = $wpdb->get_results( "SELECT * FROM ".$wpdb->postmeta." WHERE meta_key='_wc_email_inquiry_settings_custom' AND meta_value != '' " );
		if (is_array($products_email_inquiry_settings_custom) && count($products_email_inquiry_settings_custom) > 0) {
			foreach ($products_email_inquiry_settings_custom as $product_meta) {
				$wc_email_inquiry_settings_custom = unserialize($product_meta->meta_value);
				if (isset($wc_email_inquiry_settings_custom['wc_email_inquiry_user'])) {
					if ($wc_email_inquiry_settings_custom['wc_email_inquiry_user'] == 'yes') {
						$wc_email_inquiry_settings_custom['role_apply_hide_cart'] = (array) array_keys($roles);
						$wc_email_inquiry_settings_custom['role_apply_hide_price'] = (array) array_keys($roles);
						$wc_email_inquiry_settings_custom['role_apply_show_inquiry_button'] = (array) array_keys($roles);
					} else {
						$wc_email_inquiry_settings_custom['role_apply_hide_cart'] = array();
						$wc_email_inquiry_settings_custom['role_apply_hide_price'] = array();
						$wc_email_inquiry_settings_custom['role_apply_show_inquiry_button'] = array();
					}
					
					if ($wc_email_inquiry_settings_custom['wc_email_inquiry_hide_addcartbt'] == 'yes') {
						$wc_email_inquiry_settings_custom['wc_email_inquiry_hide_price'] = 'yes';
					} else {
						$wc_email_inquiry_settings_custom['wc_email_inquiry_hide_price'] = 'no';
					}
					update_post_meta($product_meta->post_id, '_wc_email_inquiry_settings_custom', (array) $wc_email_inquiry_settings_custom);
				}
			}
		}
	}
	
	public static function upgrade_version_1_0_6() {
		global $wc_email_inquiry_rules_roles_settings;
		$wc_email_inquiry_rules_roles_settings = WC_Email_Inquiry_Rules_Roles_Panel::get_settings();
		$old_rules_roles_settings = array(
			'hide_addcartbt'						=> ( get_option('wc_email_inquiry_hide_addcartbt') === false || get_option('wc_email_inquiry_hide_addcartbt') == '' ) ? $wc_email_inquiry_rules_roles_settings['hide_addcartbt'] : get_option('wc_email_inquiry_hide_addcartbt') ,
			'role_apply_hide_cart'					=> (array) get_option('wc_email_inquiry_role_apply_hide_cart'),
			'show_button'							=> ( get_option('wc_email_inquiry_show_button') === false || get_option('wc_email_inquiry_show_button') == '' ) ? $wc_email_inquiry_rules_roles_settings['show_button'] : get_option('wc_email_inquiry_show_button') ,
			'role_apply_show_inquiry_button'		=> (array) get_option('wc_email_inquiry_role_apply_show_inquiry_button'),
			'hide_price'							=> ( get_option('wc_email_inquiry_hide_price') === false || get_option('wc_email_inquiry_hide_price') == '' ) ? $wc_email_inquiry_rules_roles_settings['hide_price'] : get_option('wc_email_inquiry_hide_price') ,
			'role_apply_hide_price'					=> (array) get_option('wc_email_inquiry_role_apply_hide_price'),
		);
		$wc_email_inquiry_rules_roles_settings = array_merge($wc_email_inquiry_rules_roles_settings, $old_rules_roles_settings);
		update_option( 'wc_email_inquiry_rules_roles_settings', $wc_email_inquiry_rules_roles_settings);
		
		$wc_email_inquiry_global_settings = WC_Email_Inquiry_Global_Settings::get_settings();
		$old_email_inquiry_global_settings =  array(
			'inquiry_button_type'					=> ( get_option('wc_email_inquiry_button_type') == '' ) ? $wc_email_inquiry_global_settings['inquiry_button_type'] : get_option('wc_email_inquiry_button_type') ,
			'inquiry_button_position'				=> ( get_option('wc_email_inquiry_button_position') == '' ) ? $wc_email_inquiry_global_settings['inquiry_button_position'] : get_option('wc_email_inquiry_button_position') ,
			'inquiry_button_padding'				=> get_option('wc_email_inquiry_button_padding'),
			'inquiry_single_only'					=> ( get_option('wc_email_inquiry_single_only') == '' ) ? $wc_email_inquiry_global_settings['inquiry_single_only'] : get_option('wc_email_inquiry_single_only') ,
		);
		$wc_email_inquiry_global_settings = array_merge($wc_email_inquiry_global_settings, $old_email_inquiry_global_settings);
		update_option( 'wc_email_inquiry_global_settings', $wc_email_inquiry_global_settings);
		
		$wc_email_inquiry_email_options = WC_Email_Inquiry_Email_Options::get_settings();
		$old_email_inquiry_email_options =  array(
			'inquiry_email_from_name'				=> get_option('wc_email_inquiry_email_from_name'),
			'inquiry_email_from_address'			=> get_option('wc_email_inquiry_email_from_address'),
			'inquiry_send_copy'						=> get_option('wc_email_inquiry_send_copy'),
			'inquiry_email_to'						=> get_option('wc_email_inquiry_email_to'),
			'inquiry_email_cc'						=> get_option('wc_email_inquiry_email_cc'),
		);
		$wc_email_inquiry_email_options = array_merge($wc_email_inquiry_email_options, $old_email_inquiry_email_options);
		update_option( 'wc_email_inquiry_email_options', $wc_email_inquiry_email_options);
		
		$wc_email_inquiry_customize_email_button = WC_Email_Inquiry_Customize_Email_Button::get_settings();
		$old_email_inquiry_customize_email_button =  array(
			'inquiry_text_before'					=> get_option('wc_email_inquiry_text_before'),
			'inquiry_hyperlink_text'				=> get_option('wc_email_inquiry_hyperlink_text'),
			'inquiry_trailing_text'					=> get_option('wc_email_inquiry_trailing_text'),
			
			'inquiry_button_title'					=> get_option('wc_email_inquiry_button_title'),
			'inquiry_button_bg_colour'				=> get_option('wc_email_inquiry_button_bg_colour'),
			'inquiry_button_bg_colour_from'			=> get_option('wc_email_inquiry_button_bg_colour'),
			'inquiry_button_bg_colour_to'			=> get_option('wc_email_inquiry_button_bg_colour'),
			'inquiry_button_border_colour'			=> get_option('wc_email_inquiry_button_border_colour'),
			'inquiry_button_rounded_corner'			=> ( get_option('wc_email_inquiry_border_rounded') == '' ) ? $wc_email_inquiry_customize_email_button['inquiry_button_rounded_corner'] : get_option('wc_email_inquiry_border_rounded') ,
			'inquiry_button_rounded_value'			=> get_option('wc_email_inquiry_rounded_value'),
			
			'inquiry_button_font_size'				=> get_option('wc_email_inquiry_button_text_size'),
			'inquiry_button_font_style'				=> get_option('wc_email_inquiry_button_text_style'),
			'inquiry_button_font_colour'			=> get_option('wc_email_inquiry_button_text_colour'),
			'inquiry_button_class'					=> get_option('wc_email_inquiry_button_class'),
		);
		$wc_email_inquiry_customize_email_button = array_merge($wc_email_inquiry_customize_email_button, $old_email_inquiry_customize_email_button);
		update_option( 'wc_email_inquiry_customize_email_button', $wc_email_inquiry_customize_email_button);
		
		$wc_email_inquiry_customize_email_popup = WC_Email_Inquiry_Customize_Email_Popup::get_settings();
		$old_email_inquiry_customize_email_popup =  array(
			'inquiry_popup_type'					=> get_option('wc_email_inquiry_popup_type'),
			'inquiry_contact_heading'				=> get_option('wc_email_inquiry_contact_heading'),
			
			'inquiry_contact_text_button'			=> get_option('wc_email_inquiry_contact_text_button'),
			'inquiry_contact_button_bg_colour'		=> get_option('wc_email_inquiry_contact_button_bg_colour'),
			'inquiry_contact_button_bg_colour_from'	=> get_option('wc_email_inquiry_contact_button_bg_colour'),
			'inquiry_contact_button_bg_colour_to'	=> get_option('wc_email_inquiry_contact_button_bg_colour'),
			'inquiry_contact_button_border_colour'	=> get_option('wc_email_inquiry_contact_button_border_colour'),
			'inquiry_contact_button_rounded_corner'	=> ( get_option('wc_email_inquiry_contact_border_rounded') == '' ) ? $wc_email_inquiry_customize_email_popup['inquiry_contact_button_rounded_corner'] : get_option('wc_email_inquiry_contact_border_rounded') ,
			'inquiry_contact_button_rounded_value'	=> get_option('wc_email_inquiry_contact_rounded_value'),
			
			'inquiry_contact_button_font_size'		=> get_option('wc_email_inquiry_contact_button_text_size'),
			'inquiry_contact_button_font_style'		=> get_option('wc_email_inquiry_contact_button_text_style'),
			'inquiry_contact_button_font_colour'	=> get_option('wc_email_inquiry_contact_button_text_colour'),
			'inquiry_contact_button_class'			=> get_option('wc_email_inquiry_contact_button_class'),
			
			'inquiry_contact_form_class'			=> get_option('wc_email_inquiry_contact_form_class'),
		);
		$wc_email_inquiry_customize_email_popup = array_merge($wc_email_inquiry_customize_email_popup, $old_email_inquiry_customize_email_popup);
		update_option( 'wc_email_inquiry_customize_email_popup', $wc_email_inquiry_customize_email_popup);
	}
	
	public static function ultimate_upgrade_version_1_0_5() {
		
		$wc_email_inquiry_rules_roles_settings = get_option( 'wc_email_inquiry_rules_roles_settings', array() );
		$wc_email_inquiry_global_settings = get_option( 'wc_email_inquiry_global_settings', array() );
		$wc_email_inquiry_email_options = get_option( 'wc_email_inquiry_email_options', array() );
		$wc_email_inquiry_3rd_contactforms_settings = get_option( 'wc_email_inquiry_3rd_contactforms_settings', array() );
		$wc_email_inquiry_customize_email_popup = get_option( 'wc_email_inquiry_customize_email_popup', array() );
		$wc_email_inquiry_customize_email_button = get_option( 'wc_email_inquiry_customize_email_button', array() );
		
		$wc_email_inquiry_rules_roles_settings_new = array_merge( $wc_email_inquiry_rules_roles_settings, array( 
			'manual_quote_rule'					=> ( $wc_email_inquiry_rules_roles_settings['quote_mode_rule'] == 'manual' ) ? 'yes' : 'no',
			'auto_quote_rule'					=> ( $wc_email_inquiry_rules_roles_settings['quote_mode_rule'] == 'auto' ) ? 'yes' : 'no',
			'add_to_order_rule'					=> $wc_email_inquiry_rules_roles_settings['add_to_order'],
		) );
		
		// Process for Auto Quote rule
		$wc_email_inquiry_rules_roles_settings_new['role_apply_auto_quote'] = array_diff ( (array) $wc_email_inquiry_rules_roles_settings['role_apply_auto_quote'], (array) $wc_email_inquiry_rules_roles_settings['role_apply_manual_quote'] );
			
		// Process for Add to Order rule
		$wc_email_inquiry_rules_roles_settings_new['role_apply_activate_order_logged_in'] = $wc_email_inquiry_rules_roles_settings['role_apply_activate_order_logged_in'];
		$wc_email_inquiry_rules_roles_settings_new['role_apply_activate_order_logged_in'] = array_diff ( (array) $wc_email_inquiry_rules_roles_settings_new['role_apply_activate_order_logged_in'], (array) $wc_email_inquiry_rules_roles_settings['role_apply_manual_quote'] );
		$wc_email_inquiry_rules_roles_settings_new['role_apply_activate_order_logged_in'] = array_diff ( (array) $wc_email_inquiry_rules_roles_settings_new['role_apply_activate_order_logged_in'], (array) $wc_email_inquiry_rules_roles_settings['role_apply_auto_quote'] );
			
		// Process for Hide Cart rule
		$wc_email_inquiry_rules_roles_settings_new['role_apply_hide_cart'] = $wc_email_inquiry_rules_roles_settings['role_apply_hide_cart'];
		$wc_email_inquiry_rules_roles_settings_new['role_apply_hide_cart'] = array_diff ( (array) $wc_email_inquiry_rules_roles_settings_new['role_apply_hide_cart'], (array) $wc_email_inquiry_rules_roles_settings['role_apply_manual_quote'] );
		$wc_email_inquiry_rules_roles_settings_new['role_apply_hide_cart'] = array_diff ( (array) $wc_email_inquiry_rules_roles_settings_new['role_apply_hide_cart'], (array) $wc_email_inquiry_rules_roles_settings['role_apply_auto_quote'] );
		$wc_email_inquiry_rules_roles_settings_new['role_apply_hide_cart'] = array_diff ( (array) $wc_email_inquiry_rules_roles_settings_new['role_apply_hide_cart'], (array) $wc_email_inquiry_rules_roles_settings['role_apply_activate_order_logged_in'] );
			
		// Process for Hide Price rule
		$wc_email_inquiry_rules_roles_settings_new['role_apply_hide_price'] = array_diff ( (array) $wc_email_inquiry_rules_roles_settings['role_apply_hide_price'], (array) $wc_email_inquiry_rules_roles_settings['role_apply_activate_order_logged_in'] );
		
		update_option( 'wc_email_inquiry_rules_roles_settings', $wc_email_inquiry_rules_roles_settings_new );
		
		$wc_email_inquiry_contact_form_settings = array(
			
			'inquiry_email_from_name'			=> $wc_email_inquiry_email_options['inquiry_email_from_name'],
			'inquiry_email_from_address'		=> $wc_email_inquiry_email_options['inquiry_email_from_address'],
			'inquiry_send_copy'					=> $wc_email_inquiry_email_options['inquiry_send_copy'],
			'inquiry_email_to'					=> $wc_email_inquiry_email_options['inquiry_email_to'],
			'inquiry_email_cc'					=> $wc_email_inquiry_email_options['inquiry_email_cc'],
			
			'defaul_product_page_open_form_type'	=> $wc_email_inquiry_global_settings['defaul_product_page_open_form_type'],
			'defaul_category_page_open_form_type'	=> $wc_email_inquiry_global_settings['defaul_category_page_open_form_type'],
		);
		update_option( 'wc_email_inquiry_contact_form_settings', $wc_email_inquiry_contact_form_settings );
		
		$wc_email_inquiry_3rd_contact_form_settings = array(
			'contact_form_shortcode'			=> $wc_email_inquiry_3rd_contactforms_settings['contact_form_shortcode'],
			'product_page_open_form_type'		=> $wc_email_inquiry_3rd_contactforms_settings['product_page_open_form_type'],
			'category_page_open_form_type'		=> $wc_email_inquiry_3rd_contactforms_settings['category_page_open_form_type'],
		);
		update_option( 'wc_email_inquiry_3rd_contact_form_settings', $wc_email_inquiry_3rd_contact_form_settings );
		
		$wc_email_inquiry_global_settings = array(
			'inquiry_popup_type'				=> $wc_email_inquiry_customize_email_popup['inquiry_popup_type'],
			'enable_3rd_contact_form_plugin'	=> $wc_email_inquiry_3rd_contactforms_settings['enable_3rd_contact_form_plugin'],
		);
		update_option( 'wc_email_inquiry_global_settings', $wc_email_inquiry_global_settings );
		
		$wc_email_inquiry_customize_email_button_new = array_merge( $wc_email_inquiry_customize_email_button, array( 
			'inquiry_button_type'				=> $wc_email_inquiry_global_settings['inquiry_button_type'],
			'inquiry_button_position'			=> $wc_email_inquiry_global_settings['inquiry_button_position'],
			'inquiry_button_margin_top'			=> $wc_email_inquiry_global_settings['inquiry_button_padding_top'],
			'inquiry_button_margin_bottom'		=> $wc_email_inquiry_global_settings['inquiry_button_padding_bottom'],
			'inquiry_single_only'				=> $wc_email_inquiry_global_settings['inquiry_single_only'],
			
			'inquiry_button_border'				=>  array(
						'width'		=> $wc_email_inquiry_customize_email_button['inquiry_button_border_size'],
						'style'		=> $wc_email_inquiry_customize_email_button['inquiry_button_border_style'],
						'color'		=> $wc_email_inquiry_customize_email_button['inquiry_button_border_colour'],
						'corner'	=> $wc_email_inquiry_customize_email_button['inquiry_button_rounded_corner'],
						'rounded_value'	=> $wc_email_inquiry_customize_email_button['inquiry_button_rounded_value'],
			),
			'inquiry_button_font'				=> array(
						'size'		=> $wc_email_inquiry_customize_email_button['inquiry_button_font_size'],
						'face'		=> $wc_email_inquiry_customize_email_button['inquiry_button_font'],
						'style'		=> $wc_email_inquiry_customize_email_button['inquiry_button_font_style'],
						'color'		=> $wc_email_inquiry_customize_email_button['inquiry_button_font_colour'],
			),
		) );
		update_option( 'wc_email_inquiry_customize_email_button', $wc_email_inquiry_customize_email_button_new );
		
		$wc_email_inquiry_customize_email_popup_new = array_merge( $wc_email_inquiry_customize_email_popup, array( 
			'inquiry_contact_popup_text_font'	=> array(
						'size'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_popup_text_font_size'],
						'face'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_popup_text_font'],
						'style'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_popup_text_font_style'],
						'color'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_popup_text_font_colour'],
			),
			
			'inquiry_contact_button_border'		=>  array(
						'width'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_border_size'],
						'style'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_border_style'],
						'color'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_border_colour'],
						'corner'	=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_rounded_corner'],
						'rounded_value'	=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_rounded_value'],
			),
			'inquiry_contact_button_font'		=> array(
						'size'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_font_size'],
						'face'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_font'],
						'style'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_font_style'],
						'color'		=> $wc_email_inquiry_customize_email_popup['inquiry_contact_button_font_colour'],
			),
		) );
		update_option( 'wc_email_inquiry_customize_email_popup', $wc_email_inquiry_customize_email_popup_new );
	}
	
	public static function upgrade_ultimate_version_1_0_8() {
		$wc_email_inquiry_rules_roles_settings = get_option( 'wc_email_inquiry_rules_roles_settings', array() );
		$wc_email_inquiry_global_settings = get_option( 'wc_email_inquiry_global_settings', array() );
		$wc_email_inquiry_customize_email_button = get_option( 'wc_email_inquiry_customize_email_button', array('inquiry_single_only' => 'no') );
		
		$wc_email_inquiry_global_settings['show_button'] = $wc_email_inquiry_rules_roles_settings['show_button'];
		$wc_email_inquiry_global_settings['show_button_after_login'] = $wc_email_inquiry_rules_roles_settings['show_button_after_login'];
		$wc_email_inquiry_global_settings['role_apply_show_inquiry_button'] = $wc_email_inquiry_rules_roles_settings['role_apply_show_inquiry_button'];
		$wc_email_inquiry_global_settings['inquiry_single_only'] = $wc_email_inquiry_customize_email_button['inquiry_single_only'];
		
		update_option( 'wc_email_inquiry_global_settings', $wc_email_inquiry_global_settings );
	}
}

?>
