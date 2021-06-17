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
                'supports' => array('title','comments'),
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

// ensuring that comments are on by default for textbook custom post type 
function textbook_annotator_default_comments_on( $data ) {
    if( $data['post_type'] == 'textbook_annotator' ) {
        $data['comment_status'] = 'open';
    }

    return $data;
}
add_filter( 'wp_insert_post_data', 'textbook_annotator_default_comments_on' );



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
