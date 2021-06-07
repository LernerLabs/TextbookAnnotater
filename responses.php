<?php


function student_responses_html_form_code($textbook_id) {
	if ($textbook_id == 0){
		echo "unknown textbook";
	} else {
		$textbook = get_textbook_by_id($textbook_id)[0];
		echo "<p>" . $textbook->name . "</p>"; 
	}
	echo '<form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">';
	echo '<p>';
	echo 'Your Name (required) <br/>';
	echo '<input type="text" name="student_name" pattern="[a-zA-Z0-9 ]+" value="' . ( isset( $_POST["student_name"] ) ? esc_attr( $_POST["student_name"] ) : '' ) . '" size="40" />';
	echo '</p>';


	echo '<p>';
	echo 'Scientist Description (required) <br/>';
	echo '<textarea rows="10" cols="35" name="scientist_description">' . ( isset( $_POST["scientist_description"] ) ? esc_attr( $_POST["scientist_description"] ) : '' ) . '</textarea>';
	echo '</p>';

	echo "<input type='hidden' name='textbook_id' value='$textbook_id' >";

	echo '<p><input type="submit" name="textbook_respones_submitted" value="Submit"></p>';
	echo '</form>';
}


function create_student_textbook_response(){
	if ( isset( $_POST['textbook_respones_submitted'] ) ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'student_responses';
		$wpdb->insert( 
			$table_name,
			array( 
				'time' => current_time( 'mysql' ), 
				'student_name' => $_POST['student_name'], 
				'description' => $_POST['scientist_description'], 
				'textbook_id' => $_POST['textbook_id'],
			)
		);
	}
}

function response_shortcode($atts){
	$a = shortcode_atts( array(
      'textbook_id' => 0
   ), $atts );

	ob_start();
	create_student_textbook_response();
	student_responses_html_form_code($a['textbook_id']);

	return ob_get_clean();
}


add_shortcode( 'student_response_form', 'response_shortcode' );