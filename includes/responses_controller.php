<?php




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
