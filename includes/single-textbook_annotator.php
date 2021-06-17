<?php 

// template header
get_header();


//loop code + comments code goes here
while ( have_posts() ) : the_post();
?>
	<h3><?php the_title(); ?></h3>

<?php


if ( isset( $_POST['textbook_respones_submitted'] ) ) {

		// upload the scientist image if one is given
		if(isset($_FILES['response_image']) && !empty( $_FILES["response_image"]["name"] )){
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			$image = $_FILES['response_image'];
			$image_attachment_id=media_handle_upload('response_image', 0); //this returns the attachment id if success
			if(is_wp_error($image_attachment_id)){
				echo "Error uploading file: " . $image_attachment_id->get_error_message();
			}
		} else {
			$image_attachment_id = 0;
		}

		// add the comment into the database
		$comment_author = $_POST["student_name"];
		$scientist_name = $_POST["scientist_name"];
		$textbook_chapter = $_POST["textbook_chapter"];
		$textbook_section = $_POST["textbook_section"];
		$comment_content = $_POST["scientist_description"];

		$data = array(
			'comment_post_ID' => $_POST["textbook_id"],
			'comment_author' => $comment_author,
			'comment_content' => $comment_content,
			'comment_date' => current_time('mysql'),
			'comment_approved' => 0,
			'comment_type' => 'textbook_response',
			// add comment meta for custom fields into the database
			'comment_meta' => array(
				"scientist_name" => $scientist_name, 
				"textbook_chapter" => $textbook_chapter,
				"textbook_section" => $textbook_section,
				"scientist_image_id" => $image_attachment_id
			),
		);

		$inserted = wp_insert_comment($data);

		if($inserted){
			echo "Your response has been submitted and is now pending admin approval!";
		}

	}
?>
<!-- comments form -->
	<form action="<?php esc_url( $_SERVER['REQUEST_URI'] ) ?>" method="post" enctype="multipart/form-data">
		<p>
		Your Name (required) <br/>
		<input type="text" name="student_name" pattern="[a-zA-Z0-9 ]+" value="" size="40" required />
		</p>

		<p>
		Scientist Name (required) <br/>
		<input type="text" name="scientist_name" pattern="[a-zA-Z0-9 ]+" value="" size="40" required />
		</p>

		<p>
		Chapter<br/>
		<input type="number" name="textbook_chapter" min="0" max="1000">
		</p>

		<p>
		Section <br/>
		<input type="number" name="textbook_section" min="0" max="1000">
		</p>

		<p>Scientist Image <input type='file' id='response_image' name='response_image'></input> </p>

		<p>
		Scientist Description (required) <br/>
		<textarea rows="10" cols="35" name="scientist_description" required></textarea>
		</p>

		<input type='hidden' name='textbook_id' value="<?php the_ID(); ?>" >

		<p><input type="submit" name="textbook_respones_submitted" value="Submit"></p>
	</form>

<!-- approved comments -->
<?php 
	$args = array(
		'type' => "textbook_response",
	);
	$approved_comments = get_approved_comments(get_the_ID(), $args);
	if ( $approved_comments ){
		foreach ($approved_comments as $comment) {
			$meta = get_comment_meta($comment->comment_ID);
			echo "<h4>" . $meta['scientist_name'][0] . "</h4>";
			echo "chapter: " . $meta['textbook_chapter'][0];
			echo "section: " . $meta['textbook_section'][0];
			echo "submmited by $comment->comment_author";
			// echo wp_get_attachment_image($meta['scientist_image_id'][0],"thumbnail", "", array( "class" => "img-responsive" ));
			echo "<img width='150px' src='". wp_get_attachment_image_url($meta['scientist_image_id'][0]) . "'/>";
			echo "<p>$comment->comment_content</p>";
			echo "<hr>";	
		}
	}
	
	

?>

<?php
endwhile;
// template footer
get_footer();