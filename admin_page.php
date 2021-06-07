<?php


// add bootstrap cdn
wp_register_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css' );
wp_enqueue_style('bootstrap');

// add bootstrap js
wp_register_script( 'bootstrap_js', "https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js");
wp_enqueue_script('bootstrap_js');

// add css
wp_register_style( 'admin_page_css', TEXTBOOK_ANNOTATER__PLUGIN_URL . '/assets/css/admin_page.css' );
wp_enqueue_style('admin_page_css');

// add js
wp_register_script( 'admin_page_js',TEXTBOOK_ANNOTATER__PLUGIN_URL . "assets/js/admin_page.js");
wp_enqueue_script('admin_page_js');

// add student response form page for textbook
function add_student_response_page($textbook_id, $texbook_name){
	
	$post_details = array(
		'post_title'    => "Textbook " . $texbook_name,
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
	$wpdb->update($textbookTable, array("page_url" => $page[0]->guid), array('id' => $textbook_id));

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


// admin page view
function show_admin_page(){
	?>
	<div class="container">
		<div class="col-sm-6">
			<!-- show alert for creating textbook -->
			<?php 
			if(isset($_POST['submit']) && $_POST['submit'] == "Add new textbook") {
				add_new_textbook($_POST["name"], $_POST["author"]);
				echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>";
				echo "textbook " . $_POST['name'] . " created!";
				echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
				echo "</div>";
			}
			?>

			<!-- show alert for deleting textbook -->
			<?php
				if (isset($_POST['delete_textbook']) ){
					delete_textbook($_POST["id"]);
					echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>";
					echo "textbook with id " . $_POST['id'] . " deleted!";
					echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
					echo "</div>";
				}
			?>

			<!-- show alert for adding page -->
			<?php
				if (isset($_POST['add_textbook_page']) ){
					add_student_response_page($_POST["id"], $_POST["textbook_name"]);
					echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>";
					echo "page added!";
					echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
					echo "</div>";
				}
			?>


		</div>

		<!-- tabview button -->
		<button class="tablink" onclick="openPage('Home', this)">Home</button>
		<button class="tablink" onclick="openPage('Textbooks', this)" >Textbooks</button>
		<button class="tablink" onclick="openPage('Responses', this)">Responses</button>
		<button class="tablink" onclick="openPage('About', this)">About</button>

		<!-- tabview content -->
		<div id="Home" class="tabcontent">
			<h3>Home</h3>
			<p>Plugin main settings!</p>
		</div>

		<div id="Textbooks" class="tabcontent">
			<h3>Textbooks</h3>
			<p>Add and manage textbooks!</p> 

			<hr>
			<h4>Current Textbooks</h4>
			<?php 
				$all_textbooks = get_all_textbooks();
				foreach($all_textbooks as $textbook){
					echo  "<p> $textbook->name <strong>by</strong> $textbook->author </p>";
					echo "<form method='post'>";
					echo "<input type='hidden' name='id' value='$textbook->id'>";
					echo "<input type='hidden' name='textbook_name' value='$textbook->name'>";
					echo "<button type='submit' name='delete_textbook' class='btn btn-danger'> Delete $textbook->name </button><br>";
					if ($textbook->page_url == Null){
						echo "<button type='submit' name='add_textbook_page' class='btn btn-primary'> Add page for $textbook->name </button><br>";
					} else {
						echo "<a target='_blank' href='$textbook->page_url'>View Page</a>";
					}
					echo "</form>";
				}
			?>

			<hr>
			<h4>Add Textbook</h4>
			<div class="col-sm-6">
				<form method="post">
					<label for="textbook_name" class="form-label">Textbook Name</label>
					<input type="text" class="form-control" id="textbook_name" name="name" required>

					<label for="textbook_author" class="form-label">Textbook Author</label>
					<input type="text" class="form-control" id="textbook_author" name="author" required>

					<?php submit_button($name = 'Add new textbook')?>
				</form>
			</div>
		</div>

		<div id="Responses" class="tabcontent">
			<h3>Responses</h3>
			<p>Manage student responses here!</p>
			<hr>
			<?php 
				$all_student_responses = get_all_student_responses();
				foreach($all_student_responses as $response){
					echo  "<p> $response->student_name <strong>/</strong> $response->description </p>";
					$textbook = get_textbook_by_id($response->textbook_id)[0];
					echo "<p>for textbook: $textbook->name </p>";
					echo "<hr>";
				}
			?>
		</div>

		<div id="About" class="tabcontent">
			<h3>About</h3>
			<p>Who we are and what we do.</p>
		</div>
	</div>
<?php
}
?>