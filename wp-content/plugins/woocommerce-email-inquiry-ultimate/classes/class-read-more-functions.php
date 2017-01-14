<?php
/**
 * WC E Read More Functions Class
 *
 * Table Of Contents
 *
 * WC_EI_Read_More_Functions()
 * init()
 * check_add_read_more_button()
 * shop_add_read_more_button_above()
 * shop_add_read_more_button_below()
 * add_read_more_button()
 * add_read_more_bt_hover_each_products()
 * add_google_fonts()
 * footer_print_scripts()
 */
class WC_EI_Read_More_Functions
{
	public function WC_EI_Read_More_Functions() {
		$this->init();
	}
	
	public function init () {
		$wc_email_inquiry_read_more_settings = get_option( 'wc_email_inquiry_read_more_settings', array( 'display_type' => 'under' ) );
		$wc_ei_read_more_under_image_style = get_option( 'wc_ei_read_more_under_image_style', array( 'under_image_bt_position' => 'below' ) );
		
		if ( $wc_email_inquiry_read_more_settings['display_type'] == 'hover' ) {
			//Add Read More Button Hover on Shop Page
			add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'add_read_more_bt_hover_each_products'), 10 );
		} else {
			// Add Read More Button Under Image on Shop page
			if ( $wc_ei_read_more_under_image_style['under_image_bt_position'] == 'above' )
				add_action('woocommerce_before_template_part', array( $this, 'shop_add_read_more_button_above'), 9, 3);
			else
				add_action('woocommerce_after_shop_loop_item', array( $this, 'shop_add_read_more_button_below'), 12);
		}		
		
		// Include google fonts into header
		add_filter( 'wc_ei_google_fonts', array( $this, 'add_google_fonts') );
		
	}
	
	public function check_add_read_more_button ($product_id) {
		global $wc_email_inquiry_read_more_settings;
		$wc_email_inquiry_settings_custom = get_post_meta( $product_id, '_wc_email_inquiry_settings_custom', true);
			
		if (!isset($wc_email_inquiry_settings_custom['show_read_more_button_before_login'])) $show_read_more_button_before_login = $wc_email_inquiry_read_more_settings['show_read_more_button_before_login'];
		else $show_read_more_button_before_login = esc_attr($wc_email_inquiry_settings_custom['show_read_more_button_before_login']);
		
		// dont show read more button if setting is not checked and not logged in users
		if ($show_read_more_button_before_login == 'no' && !is_user_logged_in() ) return false;
		
		// alway show read more button if setting is checked and not logged in users
		if ($show_read_more_button_before_login != 'no' && !is_user_logged_in()) return true;
		
		if (!isset($wc_email_inquiry_settings_custom['show_read_more_button_after_login'])) $show_read_more_button_after_login = $wc_email_inquiry_read_more_settings['show_read_more_button_after_login'] ;
		else $show_read_more_button_after_login = esc_attr($wc_email_inquiry_settings_custom['show_read_more_button_after_login']);		

		// don't show read more button if for logged in users is deacticated
		if ( $show_read_more_button_after_login != 'yes' ) return false;
		
		if (!isset($wc_email_inquiry_settings_custom['role_apply_show_read_more'])) $role_apply_show_read_more = (array) $wc_email_inquiry_read_more_settings['role_apply_show_read_more'];
		else $role_apply_show_read_more = (array) $wc_email_inquiry_settings_custom['role_apply_show_read_more'];
		
		
		$user_login = wp_get_current_user();		
		if (is_array($user_login->roles) && count($user_login->roles) > 0) {
			$user_role = '';
			foreach ($user_login->roles as $role_name) {
				$user_role = $role_name;
				break;
			}
			// show email inquiry button if current user role in list apply role
			if ( in_array($user_role, $role_apply_show_read_more) ) return true;
		}
		
		return false;
		
	}
	
	public function shop_add_read_more_button_above($template_name, $template_path, $located) {
		global $post;
		global $product;
		if ($template_name == 'loop/add-to-cart.php') {
			$product_id = $product->id;
			
			if ( ($post->post_type == 'product' || $post->post_type == 'product_variation') && $this->check_add_read_more_button($product_id) ) {
				echo $this->add_read_more_button($product_id);
			}
		}
	}
	
	public function shop_add_read_more_button_below() {
		global $post;
		global $product;
		$product_id = $product->id;
			
		if ( ($post->post_type == 'product' || $post->post_type == 'product_variation') && $this->check_add_read_more_button($product_id) ) {
			echo $this->add_read_more_button($product_id);
		}
	}
	
	public function add_read_more_button($product_id) {
		global $post;
		global $wc_ei_read_more_under_image_style;
		
		$read_more_button_class = 'wc_ei_read_more_button';
		
		$wc_email_inquiry_settings_custom = get_post_meta( $product_id, '_wc_email_inquiry_settings_custom', true);
		
		if ( $wc_ei_read_more_under_image_style['under_image_bt_type'] == 'link' ) {
			$read_more_text = $wc_ei_read_more_under_image_style['under_image_link_text'];
			$read_more_button_class .= ' wc_ei_read_more_link_type';
		} else {
			$read_more_text = $wc_ei_read_more_under_image_style['under_image_bt_text'];
			$read_more_button_class .= ' wc_ei_read_more_button_type';
			if ( trim( $wc_ei_read_more_under_image_style['under_image_bt_class'] != '' ) ) $read_more_button_class .= ' ' . trim( $wc_ei_read_more_under_image_style['under_image_bt_class'] );
		}
		
		if ( isset( $wc_email_inquiry_settings_custom['read_more_text'] ) && trim( $wc_email_inquiry_settings_custom['read_more_text'] ) != '' ) $read_more_text = esc_attr( trim( $wc_email_inquiry_settings_custom['read_more_text'] ) );
		
		if (trim($read_more_text) == '') $read_more_text = __('Read More', 'wc_email_inquiry');
		
		$read_more_bt = '<a href="' . get_permalink( $product_id ) . '" class="' . $read_more_button_class . '" id="wc_ei_read_more_'.$product_id.'">'.$read_more_text.'</a>';
				
		$button_ouput = '<span class="wc_ei_read_more_button_container">';
		$button_ouput .= $read_more_bt;
		$button_ouput .= '</span>';
			
		return $button_ouput;
	}
	
	public function add_read_more_bt_hover_each_products(){
		global $post;
		global $product;
		global $wc_ei_read_more_hover_position_style;
		$product_id = $product->id;
			
		if ( ($post->post_type == 'product' || $post->post_type == 'product_variation') && $this->check_add_read_more_button($product_id) ) {
	
			$read_more_button_class = 'wc_ei_read_more_hover_button';
			
			$wc_email_inquiry_settings_custom = get_post_meta( $product_id, '_wc_email_inquiry_settings_custom', true);
			
			$read_more_text = $wc_ei_read_more_hover_position_style['hover_bt_text'];
			
			$hover_bt_alink = $wc_ei_read_more_hover_position_style['hover_bt_alink'];
			
			if ( isset( $wc_email_inquiry_settings_custom['read_more_text'] ) && trim( $wc_email_inquiry_settings_custom['read_more_text'] ) != '' ) $read_more_text = esc_attr( trim( $wc_email_inquiry_settings_custom['read_more_text'] ) );
			
			if (trim($read_more_text) == '') $read_more_text = __('Read More', 'wc_email_inquiry');
			
			$read_more_bt = '<div class="wc_ei_read_more_hover_container" position="'.$hover_bt_alink.'"><div class="wc_ei_read_more_hover_content"><span product-link="'.get_permalink( $product_id ).'" class="'.$read_more_button_class.'">'.$read_more_text.'</span></div></div>';
			
			add_action('wp_footer', array( $this, 'footer_print_scripts') );
			
			echo $read_more_bt;
		}
		
	}
	
	
	public function add_google_fonts( $google_fonts ) {
		global $wc_email_inquiry_read_more_settings;
		global $wc_ei_read_more_hover_position_style, $wc_ei_read_more_under_image_style;
		
		if ( ! is_array( $google_fonts ) ) $google_fonts = array();
		if ( $wc_email_inquiry_read_more_settings['display_type'] == 'under' ) {
			if ( $wc_ei_read_more_under_image_style['under_image_bt_type'] == 'link' ) 
				$google_fonts[] = $wc_ei_read_more_under_image_style['under_image_link_font']['face'];
			else
				$google_fonts[] = $wc_ei_read_more_under_image_style['under_image_bt_font']['face'];
		} else {
			$google_fonts[] = $wc_ei_read_more_hover_position_style['hover_bt_font']['face'];
		}
		
		return $google_fonts;
	}
	
	public function footer_print_scripts(){
		wp_enqueue_script('jquery');
		wp_register_script( 'wc-ei-read-more-hover-script', WC_EMAIL_INQUIRY_JS_URL.'/read_more_hover.js');
		wp_enqueue_script( 'wc-ei-read-more-hover-script' );
	}
	
}
?>