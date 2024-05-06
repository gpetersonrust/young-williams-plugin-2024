<?php 



add_action( 'gform_after_submission_5', 'young_williams_report_form', 10, 2 );


function young_williams_report_form($entry, $form){ 
    $name = sanitize_text_field($entry['3.3']) . ' ' . sanitize_text_field($entry['3.6']);
    $phone_number = sanitize_text_field($entry['4']);
    $email = sanitize_email($entry['5']);

    $add_contact_to_case = sanitize_text_field($entry[6.1]) ? 'Yes' : 'No';
    
    $issues_reported = [];
    for ($i = 1; $i <= 8; $i++) {
        if (isset($entry[7 . '.' . $i]) && $entry[7 . '.' . $i]) {
            $issues_reported[] = sanitize_text_field($entry[7 . '.' . $i]);
        }
    }
    $issues_reported = implode('\n', $issues_reported);

    $date_time = sanitize_text_field($entry[8]);
    $location = sanitize_text_field($entry[9]);
    $concern = sanitize_text_field($entry[10]);
    $attachments = json_decode($entry[12]);
//    implode attachments with new line
    $attachments = implode("\n", $attachments);


    $data_array = [
        'name' => $name,
        'email' => $email,
        'phone_number' => $phone_number,
        'add_contact_to_case' => $add_contact_to_case,
        'issues_reported' => $issues_reported,
        'date_time' => $date_time,
        'location' => $location,
        'concerns' => $concern,
        'attachments' => $attachments
    ];

    // Inserting the post with the name as post title
    $post_data = [
        'post_title' => $name,
        'post_content' => '', // Set post content if needed
        'post_status' => 'publish', // Set post status
        'post_type' => 'report', // Set post type
    ];
    $post_id = wp_insert_post($post_data);

    // Adding post metadata
    foreach ($data_array as $meta_key => $meta_value) {
        add_post_meta($post_id, $meta_key, $meta_value, true);
    }

   
}


add_action( 'gform_after_submission_4', 'young_williams_cruelty_form', 10, 2 );


function young_williams_cruelty_form($entry, $form){
    $animals_involved = sanitize_text_field($entry['3']);

    if ($entry['4.1'] != 'Yes' && $entry['4.1'] != 'No') {
        $is_emergency = "I don't know";
    } else {
        $is_emergency = $entry['4.1'] == 'Yes' ? 'Yes' : 'No';
    }

    $species = sanitize_text_field($entry['8']);
    $breed = sanitize_text_field($entry['9']);
    $color = sanitize_text_field($entry['10']);
    $date_time = sanitize_text_field($entry['15']);
    $location = sanitize_text_field($entry['16']);
    $incident = sanitize_text_field($entry['17']);
    $person = sanitize_text_field($entry['19']);
    $person_description = sanitize_text_field($entry['20']);
    $person_street = sanitize_text_field($entry['21.1']);
    $person_street2 = sanitize_text_field($entry['21.2']);
    $person_city = sanitize_text_field($entry['21.3']);
    $person_state = sanitize_text_field($entry['21.4']);
    $person_zip = sanitize_text_field($entry['21.5']);
    $person_country = sanitize_text_field($entry['21.6']);
    $location_of_animal_on_property = sanitize_text_field($entry['22']);
    $person_car = sanitize_text_field($entry['23']);
    $animal_shelter = sanitize_text_field($entry['26']);
    $animal_confinement = sanitize_text_field($entry['27']);
    $water_supply = sanitize_text_field($entry['28']);
    $food_supply = sanitize_text_field($entry['29']);
    $sanitation_concerns = sanitize_text_field($entry['30']);
    $skin_condition = sanitize_text_field($entry['31']);
    $physical_condition = sanitize_text_field($entry['32']);
    $injuries = sanitize_text_field($entry['33']);
    $injuries_location = sanitize_text_field($entry['34']);
    $vet_information = sanitize_text_field($entry['35']);
    $additional_information = sanitize_text_field($entry['36']);
    $attachments = json_decode($entry['44']);
    $attachments = implode("\n", $attachments);
    $report_name = sanitize_text_field($entry['40.3']) . ' ' . sanitize_text_field($entry['40.6']);
    $report_email = sanitize_email($entry['41']);
    $report_street = sanitize_text_field($entry['42.1']);
    $report_street2 = sanitize_text_field($entry['42.2']);
    $report_city = sanitize_text_field($entry['42.3']);
    $report_state = sanitize_text_field($entry['42.4']);
    $report_zip = sanitize_text_field($entry['42.5']);
    $report_country = sanitize_text_field($entry['42.6']);
    $contacted_authorities = sanitize_text_field($entry['43']);

    $data_array = [
        'animals_involved' => $animals_involved, // 1
        'is_emergency' => $is_emergency, // 2
        'species' => $species, //3
        'breed' => $breed, // 4
        'color' => $color, // 5
        'date_time' => $date_time,
        'location' => $location,
        'incident' => $incident,
        'person' => $person,
        'person_description' => $person_description,
        'person_street' => $person_street,
        'person_street2' => $person_street2,
        'person_city' => $person_city,
        'person_state' => $person_state,
        'person_zip' => $person_zip,
        'person_country' => $person_country,
        'location_of_animal_on_property' => $location_of_animal_on_property,
        'person_car' => $person_car,
        'animal_shelter' => $animal_shelter,
        'animal_confinement' => $animal_confinement,
        'water_supply' => $water_supply,
        'food_supply' => $food_supply,
        'sanitation_concerns' => $sanitation_concerns,
        'skin_condition' => $skin_condition,
        'physical_condition' => $physical_condition,
        'injuries' => $injuries,
        'injuries_location' => $injuries_location,
        'vet_information' => $vet_information,
        'additional_information' => $additional_information,
        'attachments' => $attachments,
        'report_name' => $report_name,
        'report_email' => $report_email,
        'report_street' => $report_street,
        'report_street2' => $report_street2,
        'report_city' => $report_city,
        'report_state' => $report_state,
        'report_zip' => $report_zip,
        'report_country' => $report_country,
        'contacted_authorities' => $contacted_authorities
    ];

    // Inserting the post with the name as post title
    $post_data = [
        'post_title' => $report_name,
    
        'post_status' => 'publish', // Set post status
        'post_type' => 'neglect-report', // Set post type
    ];
    $post_id = wp_insert_post($post_data);

    // Adding post metadata
    foreach ($data_array as $meta_key => $meta_value) {
        add_post_meta($post_id, $meta_key, $meta_value, true);
    }
}

 
add_action( 'gform_after_submission_6', 'assign_to_user', 10, 2 );

function assign_to_user($entry, $form){
    $post_id = sanitize_text_field($entry['1']);
    $assigned_to = sanitize_text_field($entry['3']);
 
    // updated acf field
    update_field('assigned_to', $assigned_to, $post_id);
    wp_redirect(admin_url('admin.php?page=yw-reports-status'));
    exit;
   
}

add_action('gform_after_submission_7',  'close_report', 10, 2);

function close_report($entry, $form){
    $post_id = sanitize_text_field($entry['1']);
    $status = sanitize_text_field($entry['4']);
 
    // updated acf field
    update_field('status', $status, $post_id);
    
    wp_redirect(admin_url('admin.php?page=yw-reports-status'));
    exit;
}