<?php


// add css and js for the admin panel page
function textbook_annotater_add_css_js() {
	// add bootstrap cdn
	wp_register_style( 'bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css' );
	wp_enqueue_style('bootstrap');

	// add bootstrap js
	wp_register_script( 'bootstrap_js', "https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js");
	wp_enqueue_script('bootstrap_js');

	// add css
	wp_register_style( 'admin_page_css', TEXTBOOK_ANNOTATER__PLUGIN_URL . 'admin/assets/css/admin_page.css' );
	wp_enqueue_style('admin_page_css');

	// add js
	wp_register_script( 'admin_page_js',TEXTBOOK_ANNOTATER__PLUGIN_URL . "admin/assets/js/admin_page.js");
	wp_enqueue_script('admin_page_js');
}
add_action( 'admin_enqueue_scripts', 'textbook_annotater_add_css_js' );




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


			<!-- show alert for approving student response -->
			<?php
				if (isset($_POST['approve_student_response']) ){
					approve_student_response($_POST["id"]);
					echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>";
					echo "student response approved!";
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
				if ($all_textbooks->have_posts() ) : 
					while ( $all_textbooks->have_posts() ) : $all_textbooks->the_post();
						echo  "<p>" .  get_the_title() . " <strong>by</strong> " . get_post_meta( get_the_ID(), '_textbook_annotator_author_meta_key', true ) . " ";
						echo "<a target='_blank' href='" . get_the_permalink() . "' >View Textbook</a>";
						echo "<a style='color:red;' href='" . get_delete_post_link(get_the_ID()) . "'>Delete</a>";
						echo "<a target='_blank' href='" . get_edit_post_link(get_the_ID()) . "'>Edit</a></p>";
					endwhile;
				wp_reset_postdata();
				endif;
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
			<hr>
		</div>

		<div id="About" class="tabcontent">
			<h3>About</h3>
			<p>Who we are and what we do.</p>
		</div>
	</div>
<?php
}
?>