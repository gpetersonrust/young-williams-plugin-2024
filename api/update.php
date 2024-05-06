<?php 
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

register_rest_route( 'animal-api/v1', '/load-pets', array(
    'methods' => 'get',
    'callback' => 'update_pets_in_database'
));

function update_pets_in_database( $data) {
   $ID =  sanitize_text_field($data['ID']);
//    pet_status
    $pet_status =  sanitize_text_field($data['pet_status']);
 
   $post_data = $pet_status == 'pet' ?  getPetDetails($ID) : getStrayDetails($ID);

   

   if(!$post_data || !$ID):
       return  array(
        'success' => false,
        'message' => 'No pets found', 
        // update status code
        'stauts_code' => 404, 
        "ok" => false
    );

    endif;


    // check if post exists ID should be used with csv_id
    $existingPostID = get_post_id_by_meta('csv_id', $ID);
    $post_id;
    if($existingPostID) {
        $post_id = $existingPostID;
    } else {
        $post_id = wp_insert_post([
            'post_title'    => $post_data['AnimalName'],
            'post_content'  => $post_data['Desc'],
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_type'     => 'animal'
        
            
           ]);
        

    }
    
 
    update_field('csv_id', $ID, $post_id);
    update_field('pet_type', $pet_status, $post_id);
   $pet_status  == 'stray'   &&  ($post_data['Photo1']  =  $post_data['Photo']);

    foreach ($post_data as $key => $value) {
        if ($key !== 'ID') {
            // Convert arrays and objects to JSON strings

            if (is_array($value) || is_object($value)) {
// If $value is an array or an object, check if it's empty
if (empty($value)) {
    // If it's an empty array or object, return an empty string
    $value = false;
} else {
    // If it's not empty, encode it as JSON
    $value = json_encode($value);
}
} else {
// If $value is not an array or object, sanitize it as text field
$value = sanitize_text_field($value);
}

           $value &&   update_field($key, $value, $post_id);
        }
    }

    // // use Photo1 which is a URL  as featured image
    $thumbanil_id = get_post_thumbnail_id($post_id);
    if($thumbanil_id) {
        wp_delete_attachment($thumbanil_id, true);
    }
    $photo = $pet_status  == 'stray'  ? $post_data['Photo'] :  $post_data['Photo1'];
    $photo_id = media_sideload_image($photo, $post_id, $post_data['AnimalName'], 'id');
    set_post_thumbnail($post_id, $photo_id);


    // get the peramlink for the post

    $permalink = get_permalink($post_id);
    


    return   array(
        'success' => true,
        'message' => 'Pets updated successfully', 
        // update status code
        'stauts_code' => 201,
        'post_id' => $post_id,
        'link' => $permalink,
        'post_data' => $post_data,
        "ok" => true
    );
}
