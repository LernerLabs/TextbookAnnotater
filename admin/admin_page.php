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
			<hr>
			<?php
				$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
				if ( !in_array( 'dco-comment-attachment/dco-comment-attachment.php', $active_plugins ) ) {
					echo "<div style='margin-top:50px;' class='alert alert-danger alert-dismissible fade show' role='alert'>";
					echo "Textbook Annotator plugin requires the DCO-comment-attachment plugin as a dependency to include images in student responses. Please install the DCO-comment-attachment plugin if you are planning to let students upload images of scientists.";
					echo "<br>";
					echo "You can install and activate this plugin here: ";
					$DCO_CA_link = admin_url('plugin-install.php?s=DCO-comment-attachment&tab=search&type=term');
					echo "<a href='$DCO_CA_link'>Install DCO-comment-attachment</a>";
					echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
					echo "</div>";
				}
			?>
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
			<h5>To approve/delete student responses go to: <a href="<?php echo admin_url( 'edit-comments.php');?>">Comments</a></h5>
			<p>Manage student responses here!</p>
			<hr>
			<?php 
				$all_comments = get_all_student_responses();
				foreach ($all_comments as $comment) {
					$scientist_name_meta = get_comment_meta( $comment->comment_ID, 'scientist_name', true );
					$textbook_chapter_meta = get_comment_meta( $comment->comment_ID, 'textbook_chapter', true );
					$textbook_section_meta = get_comment_meta( $comment->comment_ID, 'textbook_section', true );
					if($scientist_name_meta != "") {echo "<p><strong>Scientist:</strong> $scientist_name_meta</p>";}
					if($textbook_chapter_meta != ""){echo "<p><strong>Chapter:</strong> $textbook_chapter_meta</p>";}
					if($textbook_section_meta != ""){echo "<p><strong>Section:</strong> $textbook_section_meta</p>";}
					echo "<p><strong>Student Name:</strong> $comment->comment_author</p>";
					echo "<p><strong>Scientist Description:</strong> $comment->comment_content</p>";
					$attachment_id = get_comment_meta( $comment->comment_ID, 'attachment_id', true );
					$attachment_url = wp_get_attachment_image_url($attachment_id);
					echo "<img src='$attachment_url' />";
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