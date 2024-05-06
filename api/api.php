<?php 

// rest api init

function register_animal_api() {
    require_once(PLUGIN_DIR . 'api/update.php');
}


add_action( 'rest_api_init', 'register_animal_api' );