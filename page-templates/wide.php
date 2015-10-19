<?php
/**
 * Template Name: Wide
 */

get_header(); ?>

<div class="section wrapper<?php echo trustedd_wrapper_classes(); ?>">

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
		<?php
			// Start the Loop.
			while ( have_posts() ) : the_post();

				// Include the page content template.
				get_template_part( 'content', 'page' );

			endwhile;
		?>
		</main>
	</div>

</div>
<?php
get_footer();