<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package llorix-one-lite
 */
?>

    <footer itemscope itemtype="http://schema.org/WPFooter" id="footer" role="contentinfo" class = "footer grey-bg">

        <div class="container">
            <div class="footer-widget-wrap">
			
				<?php
					if( is_active_sidebar( 'footer-area' ) ){
				?>
						<div itemscope itemtype="http://schema.org/WPSideBar" role="complementary" id="sidebar-widgets-area-1" class="col-md-3 col-sm-6 col-xs-12 widget-box" aria-label="<?php esc_html_e('Widgets Area 1','llorix-one-lite'); ?>">
							<?php
								dynamic_sidebar( 'footer-area' );
							?>
						</div>
				


				<?php
					}
				?>

            </div><!-- .footer-widget-wrap -->

	        
	            


    </footer>

	<?php wp_footer(); ?>
	<!-- test -->
</body>
</html>
