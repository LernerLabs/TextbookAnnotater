<?php


// add custom fields to the comments form
add_filter ( 'comment_form_default_fields','textbook_add_custom_fields', 10, 1 );
function textbook_add_custom_fields ( $fields ) {
  if ( 'textbook_annotator' == get_post_type() ) { //only add these custom fields if the post is a textbook custom post type

  	// remove the website url field 
  	if(isset($fields['url'])){
  		unset($fields['url']);
  	}
  	// custom field for scientist name
    $fields['scientist_name'] = '<p>
    <label for="scientist_name">Scientist Name (required)</label>
    <input name="scientist_name" type="text" value="" size="30" required /></p>';
    // custom field for textbook chapter
    $fields['textbook_chapter'] = '<p>
    <label for="textbook_chapter">Chapter</label>
	<input type="number" name="textbook_chapter" min="0" max="1000">';
	// custom field for textbook section
    $fields['textbook_section'] = '<p>
    <label for="textbook_section">Section</label>
	<input type="number" name="textbook_section" min="0" max="1000">';
  }
  return $fields;
}


// save the custom fields values into database as comment meta
add_action( 'comment_post', 'save_textbook_comment_meta_data' );
function save_textbook_comment_meta_data( $comment_id ) {
	//save scientist name
  	if ( ( isset( $_POST['scientist_name'] ) ) && ( $_POST['scientist_name'] != '') ){
		$scientist_name = wp_filter_nohtml_kses($_POST['scientist_name']);
	  	update_comment_meta( $comment_id, 'scientist_name', $scientist_name );
  	}
  
  	//save textbook chapter
	if ( ( isset( $_POST['textbook_chapter'] ) ) && ( $_POST['textbook_chapter'] != '') ){
		$textbook_chapter = wp_filter_nohtml_kses($_POST['textbook_chapter']);
		update_comment_meta( $comment_id, 'textbook_chapter', $textbook_chapter );
	}
	  
  	//save textbook section
	if ( ( isset( $_POST['textbook_section'] ) ) && ( $_POST['textbook_section'] != '') ){
  		$textbook_section = wp_filter_nohtml_kses($_POST['textbook_section']);
  		update_comment_meta( $comment_id, 'textbook_section', $textbook_section );
	}
  
  
}

// make scientist name a required field
// Add the filter to check whether the comment meta data has been filled
add_filter( 'pre_comment_on_post', 'verify_textbook_comment_meta_data' );
function verify_textbook_comment_meta_data( $commentdata ) {
	
  if ( !isset( $_POST['scientist_name'] )  || !( $_POST['scientist_name'] != '')   ){
  	wp_die( __( 'Error: You did not add a Scientist Name. Hit the Back button on your Web browser and resubmit your comment with a Scientist Name.' ) );
  }
  return $commentdata;
}

// customize the comment_text to show the comment meta
add_filter( 'comment_text', 'customizing_textbook_comment_text', 20, 3 );
function customizing_textbook_comment_text( $comment_text, $comment, $args ) {
	$new_comment_text = "";
	if(get_post_type($comment->comment_post_ID) == "textbook_annotator"){ //only customize if this is a textbook custom post type
	    $scientist_name_meta = get_comment_meta( $comment->comment_ID, 'scientist_name', true );
	    $textbook_chapter_meta = get_comment_meta( $comment->comment_ID, 'textbook_chapter', true );
	    $textbook_section_meta = get_comment_meta( $comment->comment_ID, 'textbook_section', true );
	    if($scientist_name_meta != "") {$new_comment_text .= "<p><strong>Scientist:</strong> $scientist_name_meta</p>";}
	    if($textbook_chapter_meta != ""){$new_comment_text .= "<p><strong>Chapter:</strong> $textbook_chapter_meta</p>";}
	    if($textbook_section_meta != ""){$new_comment_text .= "<p><strong>Section:</strong> $textbook_section_meta</p>";}
	}
	$new_comment_text .= $comment_text;
    return $new_comment_text;
}