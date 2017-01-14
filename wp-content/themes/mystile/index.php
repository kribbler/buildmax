<?php
// File Security Check
if ( ! function_exists( 'wp' ) && ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
    die ( 'You do not have sufficient permissions to access this page!' );
}
?><?php
/**
 * Index Template
 *
 * Here we setup all logic and XHTML that is required for the index template, used as both the homepage
 * and as a fallback template, if a more appropriate template file doesn't exist for a specific context.
 *
 * @package WooFramework
 * @subpackage Template
 */
	get_header();
	global $woo_options;
	
?>

    <?php if ( $woo_options[ 'woo_homepage_banner' ] == "true" ) { ?>
    	
    	<div class="homepage-banner">
    		<?php
				if ( $woo_options[ 'woo_homepage_banner' ] == "true" ) { $banner = $woo_options['woo_homepage_banner_path']; }
				if ( $woo_options[ 'woo_homepage_banner' ] == "true" && is_ssl() ) { $banner = preg_replace("/^http:/", "https:", $woo_options['woo_homepage_banner_path']); }
			?>
			    <img src="<?php echo $banner; ?>" alt="" />
    		<h1><span><?php echo $woo_options['woo_homepage_banner_headline']; ?></span></h1>
    		<div class="description"><?php echo wpautop($woo_options['woo_homepage_banner_standfirst']); ?></div>
    	</div>
    	
    <?php } ?>

    

    <?php echo do_shortcode("[metaslider id=105]"); ?>
    <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('Home Icons')) :endif; ?>
    <div class="homefeatured">
    	<h1>Featured Lasers & Surveying Equipment</h1>
    	<?php echo do_shortcode("[featured_products per_page=\"5\" columns=\"5\" orderby=\"date\" orderby=\"rand\"]"); ?>

    	<!-- Display About Us Post -->
    	<div style="width: 100%; min-height: 50px;">
    	<h1>About Buildmax</h1>
<?php iinclude_page(1057); ?></div>
		<!-- Display industry news category -->
		<div class="industrynews">
			<?php echo '<h1>'.trim(strip_tags(category_description(25))).'</h1>'; ?> 
			<ul>
				<?php 
					$args = array(
						'posts_per_page'   => 2,
						'category'         => '25' 
					);
					$myposts = get_posts( $args );
					foreach ( $myposts as $post ) : setup_postdata( $post ); ?>
						<li>
							<?php 
								echo get_the_post_thumbnail(get_the_ID(), 'full', array('class'	=> "aboutus")); 
								$content = get_the_content();
								$content = strip_tags($content);
								$trimmed_content = wp_trim_words( $content, 30, '<a href="'. get_permalink() .'"> Read More</a>' );
								echo '<p>'.$trimmed_content.'</p>';
							?>
						</li>
					<?php endforeach; 
					wp_reset_postdata();
				?>
			</ul>
		</div>

		<!-- This week's special -->
		<div class="specials">
			<h2>THIS WEEK'S SPECIAL DEAL</h2>
			<?php echo do_shortcode("[product_category category=\"Special Deal\" per_page=1 columns=1 orderby=\"date\" order=\"desc\"]")?>
		</div>
    </div>
    

    


		
<?php get_footer(); ?>