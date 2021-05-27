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

// add bootstrap js
wp_register_script( 'admin_page_js',TEXTBOOK_ANNOTATER__PLUGIN_URL . "assets/js/admin_page.js");
wp_enqueue_script('admin_page_js');


// admin page view
function show_admin_page(){
    ?>
    <div class="container">
        <button class="tablink" onclick="openPage('Home', this)">Home</button>
        <button class="tablink" onclick="openPage('Textbooks', this)" >Textbooks</button>
        <button class="tablink" onclick="openPage('Responses', this)">Responses</button>
        <button class="tablink" onclick="openPage('About', this)">About</button>

        <div id="Home" class="tabcontent">
          <h3>Home</h3>
          <p>Plugin main settings!</p>
        </div>

        <div id="Textbooks" class="tabcontent">
          <h3>Textbooks</h3>
          <p>Add and manage textbooks!</p> 
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