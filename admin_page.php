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
		'post_content'  => 'Content of your page for textbook ' . $texbook_name . ' with id: ' . $textbook_id,
		'post_status'   => 'publish',
		'post_author'   => 1,
		'post_type' => 'page'
	);
	wp_insert_post( $post_details );

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
			if(isset($_POST['submit']) && $_POST['submit'] == "add_new_textbook") {
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
				foreach($all_textbooks as $texbook){
					echo  "<p> $texbook->name <strong>by</strong> $texbook->author </p>";
					echo "<form method='post'>";
					echo "<input type='hidden' name='id' value='$texbook->id'>";
					echo "<input type='hidden' name='textbook_name' value='$texbook->name'>";
					echo "<button type='submit' name='delete_textbook' class='btn btn-danger'> Delete $texbook->name </button><br>";
					echo "<button type='submit' name='add_textbook_page' class='btn btn-primary'> Add page for $texbook->name </button><br>";
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

					<?php submit_button($name = 'add_new_textbook')?>
				</form>
			</div>
		</div>

		<div id="Responses" class="tabcontent">
			<h3>Responses</h3>
			<p>Manage student responses here!</p>
		</div>

		<div id="About" class="tabcontent">
			<h3>About</h3>
			<p>Who we are and what we do.</p>
		</div>
	</div>
<?php
}
?>