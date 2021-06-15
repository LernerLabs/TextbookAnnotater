<?php

// add student response form page for textbook
function add_student_response_page($textbook_id, $texbook_name){
	
	$post_details = array(
		'post_title'    => "Textbook " . $texbook_name . " Form",
		'post_content'  => '[student_response_form textbook_id=' . $textbook_id . ']',
		'post_status'   => 'publish',
		'post_author'   => 1,
		'post_type' => 'page'
	);
	$page_id = wp_insert_post( $post_details );

	global $wpdb; 
	$postsTable = $wpdb->prefix.'posts'; 
	$page = $wpdb->get_results ( "SELECT * FROM $postsTable WHERE ID = $page_id");
	$textbookTable = $wpdb->prefix.'textbooks'; 
	$texbook = $wpdb->get_results ( "SELECT * FROM $textbookTable WHERE id = $textbook_id");
	$wpdb->update($textbookTable, array("form_page_url" => $page[0]->guid), array('id' => $textbook_id));

}

// add approved student responses page for textbook
function add_approved_student_responses_page($textbook_id, $texbook_name){
	
	$post_details = array(
		'post_title'    => "Textbook " . $texbook_name . " Responses",
		'post_content'  => '[approved_student_responses textbook_id=' . $textbook_id . ']',
		'post_status'   => 'publish',
		'post_author'   => 1,
		'post_type' => 'page'
	);
	$page_id = wp_insert_post( $post_details );

	global $wpdb; 
	$postsTable = $wpdb->prefix.'posts'; 
	$page = $wpdb->get_results ( "SELECT * FROM $postsTable WHERE ID = $page_id");
	$textbookTable = $wpdb->prefix.'textbooks'; 
	$texbook = $wpdb->get_results ( "SELECT * FROM $textbookTable WHERE id = $textbook_id");
	$wpdb->update($textbookTable, array("responses_page_url" => $page[0]->guid), array('id' => $textbook_id));

}

// get textbook by id
function get_textbook_by_id($id){
	global $wpdb;    
	$textbookTable = $wpdb->prefix.'textbooks';
	$result = $wpdb->get_results ( "SELECT * FROM $textbookTable WHERE id = $id");
	return $result;
}


// get all textbooks from database
function get_all_textbooks(){

	global $wpdb;    
	$textbookTable = $wpdb->prefix.'textbooks';
	$result = $wpdb->get_results ( "SELECT * FROM $textbookTable");
	return $result;
}

// add new textbook into database
function add_new_textbook($name, $author){
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'textbooks';
	$wpdb->insert( 
		$table_name,
		array( 
			'time' => current_time( 'mysql' ), 
			'name' => $name, 
			'author' => $author, 
		)
	);
}

// delete textbook from database
function delete_textbook($id){
	global $wpdb;
	$table_name = $wpdb->prefix . 'textbooks';
	$wpdb->delete( $table_name, array( 'id' => $id ) );
}