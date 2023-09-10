<?php
/**
 * Comments content template.
 *
 * @package Clipper\Templates
 * @since 2.0.0
 */

// If comments are open or there's at least one comment.
if ( comments_open() || get_comments_number() ) :
?>

	<?php comments_template(); ?>

<?php
endif;
