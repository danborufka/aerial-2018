<?php
/**
 * @package llorix-one-lite
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('content-single-page'); ?>>
	<header class="entry-header single-header">
		<?php the_title( '<h1 itemprop="headline" class="entry-title single-title">', '</h1>' ); ?>
		<div class="colored-line-left"></div>
		<div class="clearfix"></div>

		<div class="entry-meta single-entry-meta">
			<span class="author-link" itemprop="author" itemscope="" itemtype="http://schema.org/Person">
				<span itemprop="name" class="post-author author vcard">
					
					


				</span>
        	</span>
			<time class="post-time posted-on published" datetime="<?php the_time('c'); ?>" itemprop="datePublished">
				<i class="fa fa-clock-o" aria-hidden="true"></i>
				<?php the_time( get_option('date_format') ); ?>
			</time>


		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<div itemprop="text" class="entry-content">
		<?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'llorix-one-lite' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php llorix_one_lite_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
