<?php

// add form for response page
function student_responses_html_form_code($textbook_id) {
	if ($textbook_id == 0){
		echo "unknown textbook";
	} else {
		$textbook = get_textbook_by_id($textbook_id)[0];
		echo "<p>" . $textbook->name . "</p>"; 
	}
	echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post" enctype="multipart/form-data">';
	echo '<p>';
	echo 'Your Name (required) <br/>';
	echo '<input type="text" name="student_name" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST["student_name"] ) ? esc_attr( $_POST["student_name"] ) : '' ) . '" size="40" />';
	echo '</p>';


	echo '<p>';
	echo 'Scientist Description (required) <br/>';
	echo '<textarea rows="10" cols="35" name="scientist_description">' . ( isset( $_POST["scientist_description"] ) ? esc_attr( $_POST["scientist_description"] ) : '' ) . '</textarea>';
	echo '</p>';

	echo "<input type='hidden' name='textbook_id' value='$textbook_id' >";

	echo "<p> Image <input type='file' id='response_image' name='response_image'></input> </p>";

	echo '<p><input type="submit" name="textbook_respones_submitted" value="Submit"></p>';
	echo '</form>';
}


// create new response in database (from form)
function create_student_textbook_response(){
	if ( isset( $_POST['textbook_respones_submitted'] ) ) {
		if(isset($_FILES['response_image'])){
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/media.php' );
			$image = $_FILES['response_image'];
			$uploaded=media_handle_upload('response_image', 0);
			if(is_wp_error($uploaded)){
				echo "Error uploading file: " . $uploaded->get_error_message();
			}
		}
		global $wpdb;
		$table_name = $wpdb->prefix . 'student_responses';
		$wpdb->insert( 
			$table_name,
			array( 
				'time' => current_time( 'mysql' ), 
				'student_name' => $_POST['student_name'], 
				'description' => $_POST['scientist_description'], 
				'textbook_id' => $_POST['textbook_id'],
				'image_url' => wp_get_attachment_url($uploaded),
			)
		);
	}
}

// create shortcode for displaying the page form
function response_form_shortcode($atts){
	$a = shortcode_atts( array(
		'textbook_id' => 0
	), $atts );

	ob_start();
	create_student_textbook_response();
	student_responses_html_form_code($a['textbook_id']);

	return ob_get_clean();
}
add_shortcode( 'student_response_form', 'response_form_shortcode' );

// add view for approved student responses page
function approved_student_responses_page($textbook_id){
	global $wpdb;
	$responsesTable = $wpdb->prefix.'student_responses';
	$result = $wpdb->get_results ( "SELECT * FROM $responsesTable WHERE textbook_id = $textbook_id");

	foreach ($result as $response) {
		echo "<p> $response->student_name </p>";
		echo "<p> $response->description";
		echo "<hr>";
	}

}

// create shortcode for approved student responses page
function approved_student_responses_shortcode($atts){
	$a = shortcode_atts( array(
		'textbook_id' => 0
	), $atts );

	ob_start();
	approved_student_responses_page($a['textbook_id']);
	return ob_get_clean();

}
add_shortcode( 'approved_student_responses', 'approved_student_responses_shortcode' );


// helper to get array of all students
function get_all_student_responses(){
	global $wpdb;    
	$responsesTable = $wpdb->prefix.'student_responses';
	$result = $wpdb->get_results ( "SELECT * FROM $responsesTable");
	return $result;
}


// change student response status to approved
function approve_student_response($id){
	global $wpdb; 
	$responsestable = $wpdb->prefix.'student_responses'; 
	$wpdb->update($responsestable, array("approved" => 1), array('id' => $id));
}
