<?php

// add custom post type for textbooks
function create_textbook_custom_post_type() {
    register_post_type('textbook_annotator',
        array(
            'labels'      => array(
                'name'          => __('Textbooks', 'textdomain'),
                'singular_name' => __('Textbook', 'textdomain'),
            ),
                'public'      => true,
                'has_archive' => true,
                'publicly_queryable' => true,
                'menu_icon' => 'dashicons-book-alt',
                'rewrite'     => array( 'slug' => 'textbooks' ),
        )
    );
}
add_action('init', 'create_textbook_custom_post_type');

// add custom metabox for author in textbook_annotator custom post type
function textbook_annotator_add_author_metabox() {
    add_meta_box(
        'textbook_annotator_textbook_author',                 // Unique ID
        'Textbook Author',      // Box title
        'textbook_annotator_textbook_author_box_html',  // Content callback, must be of type callable
        'textbook_annotator'                            // Post type
    );
}

add_action( 'add_meta_boxes', 'textbook_annotator_add_author_metabox' );

// show custom metabox as a text input
function textbook_annotator_textbook_author_box_html($post){
	$value = get_post_meta( $post->ID, '_textbook_annotator_author_meta_key', true );
    echo "<input type='text' name='textbook_author' value='$value'>";
}

// save the author from the metabox into the database
function textbook_annotator_save_postdata( $post_id ) {
    if ( array_key_exists( 'textbook_author', $_POST ) ) {
        update_post_meta(
            $post_id,
            '_textbook_annotator_author_meta_key',
            $_POST['textbook_author']
        );
    }
}
add_action( 'save_post', 'textbook_annotator_save_postdata' );


// add custom template for textbook custom post type
/* Filter the single_template with our custom function*/
add_filter('single_template', 'textbook_custom_post_template');
function textbook_custom_post_template($single) {

    global $post;

    /* Checks for single template by post type */
    if ( $post->post_type == 'textbook_annotator' ) {
        if ( file_exists( TEXTBOOK_ANNOTATER__PLUGIN_DIR . 'includes/single-textbook_annotator.php' ) ) {
            return TEXTBOOK_ANNOTATER__PLUGIN_DIR . 'includes/single-textbook_annotator.php';
        }
    }

    return $single;

}

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
	$args = array(  
        'post_type' => 'textbook_annotator',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );

    $loop = new WP_Query( $args ); 
    return $loop;
}

// add new textbook into database
function add_new_textbook($name, $author){
	global $user_ID;

	// create post
    $new_post = array(
        'post_title' => "Textbook " . $name,
        'post_content' => "",
        'post_status' => 'publish',
        'post_date' => date('Y-m-d H:i:s'),
        'post_author' => $user_ID,
        'post_type' => 'textbook_annotator',
    );
    $post_id = wp_insert_post($new_post);

    // update meta for the author
    update_post_meta(
        $post_id,
        '_textbook_annotator_author_meta_key',
        $author
    );
}


// delete textbook from database
function delete_textbook($id){
	global $wpdb;
	$table_name = $wpdb->prefix . 'textbooks';
	$wpdb->delete( $table_name, array( 'id' => $id ) );
}