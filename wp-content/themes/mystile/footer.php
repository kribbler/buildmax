<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}
?>
<?php
/**
 * Footer Template
 *
 * Here we setup all logic and XHTML that is required for the footer section of all screens.
 *
 * @package WooFramework
 * @subpackage Template
 */
	global $woo_options;
	?>
</div><!-- /#wrapper -->
	<?php
	echo '<div class="footer-wrap" style="margin-bottom: -26px">';

	$total = 4;
	if ( isset( $woo_options['woo_footer_sidebars'] ) && ( $woo_options['woo_footer_sidebars'] != '' ) ) {
		$total = $woo_options['woo_footer_sidebars'];
	}

	if ( ( woo_active_sidebar( 'footer-1' ) ||
		   woo_active_sidebar( 'footer-2' ) ||
		   woo_active_sidebar( 'footer-3' ) ||
		   woo_active_sidebar( 'footer-4' ) ) && $total > 0 ) {

?>
	<?php woo_footer_before(); ?>
	
		<section id="footer-widgets" class="col-full col-<?php echo $total; ?> fix">
	
			<?php $i = 0; while ( $i < $total ) { $i++; ?>
				<?php if ( woo_active_sidebar( 'footer-' . $i ) ) { ?>
	
			<div class="block footer-widget-<?php echo $i; ?>">
	        	<?php woo_sidebar( 'footer-' . $i ); ?>
			</div>
	
		        <?php } ?>
			<?php } // End WHILE Loop ?>
			<div class="clear"></div>
		</section><!-- /#footer-widgets  -->
	<?php } // End IF Statement ?>
		
	
			
	
		
	
	</div><!-- / footer-wrap -->
	<div class="footer-wrap-bottom">

		<footer id="footer" class="col-full">
			<?php wp_nav_menu( array( 'theme_location' => 'footer-menu', 'after' => '<span> / </span>' ) ); ?>
			<span>Copyright &copy; <?php bloginfo(); ?> <?php echo date( 'Y' ); ?>. <?php _e( 'All Rights Reserved.', 'woothemes' ); ?></span>
			<span>
				<img class="paymentmethods" src="http://buildmax.co.nz/wp-content/themes/mystile/images/payment-methods.png" alt="Payment methods" />
			</span><br />All prices listed exclusive of GST
			<span class="StudioEleven">Website by <a href="www.studioeleven.co.nz">Studio Eleven</a> </span>
		</footer><!-- /#footer  -->
	</div>


<?php wp_footer(); ?>
<?php woo_foot(); ?>
</body>
</html>