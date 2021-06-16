<?php 

// template header
get_header();


//loop code + comments code goes here
while ( have_posts() ) : the_post();
?>
	<h3><?php the_title(); ?></h3>
<?php
endwhile;
// template footer
get_footer();