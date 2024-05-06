<?php
// Register the admin menu item
function register_download_reports_page() {
    add_submenu_page(
        'tools.php', // Parent menu slug
        'Download Young Williams Reports', // Page title
        'Download Young Williams  Reports', // Menu title
        'manage_options', // Capability required
        'download-reports', // Menu slug
        'download_reports_page_content' // Callback function to display content
    );
}
add_action('admin_menu', 'register_download_reports_page');

// Callback function to display the content of the admin page
function download_reports_page_content() {
    ?>
<div class="wrap">
    <h1>Download Reports</h1>
    <form method="post">
        <label for="start_date">Select Start Date:</label>
        <input type="date" id="start_date" name="start_date" required>
        <input type="submit" name="download_reports" value="Download Reports">
    </form>
</div>
<?php
} 

// Handle form submission
if (isset($_POST['download_reports'])) {
    // Get the selected start date
    if (isset($_POST['start_date'])) {
        $start_date = sanitize_text_field($_POST['start_date']);

        // 
    } else {
        $start_date = date('Y-m-d');
    }

    // Query to fetch reports based on the start date
    $args = array(
        'post_type' => array('report', 'neglect-report'), // Custom post types
        'posts_per_page' => -1, // Get all reports
        'date_query' => array(
            array(
                'after' => $start_date,
                'inclusive' => true,
            ),
        ),
    );

    $reports_posts = get_posts($args);
 
    // if report posts are not found refresh page with message='No reports found'
    if (empty($reports_posts)) { ?>
<script>
window.location.href = '<?php echo admin_url('tools.php?page=download-reports&message=no-reports-found'); ?>';
</script>
<?php } else {

    $reports = []; // Array to store 'report' post type
    $neglect_reports = []; // Array to store 'neglect-report' post type

    foreach ($reports_posts as $report) {
        $report_type = get_post_type($report->ID);
        if ($report_type === 'neglect-report') {
            $neglect_reports[] = $report;
        } else {
            $reports[] = $report;
        }
    }

    // Create a CSV file for 'report' post type
    $report_file = fopen(PLUGIN_DIR . 'reports/reports.csv', 'w');
    fputcsv($report_file, ['Report ID',  
    'Name',
    'Email',
    'Phone Number',
    'Add Contact to Case',
    'Issues Reported',
    'Date Time',
    'Location',
    'Concerns',
    'Attachments'
    
]);
    foreach ($reports as $report) {
        fputcsv($report_file, [$report->ID,
        
        get_field('name', $report->ID),
        get_field('email', $report->ID),
        get_field('phone_number', $report->ID),
        get_field('add_contact_to_case', $report->ID),
        get_field('issues_reported', $report->ID),
        get_field('date_time', $report->ID),
        get_field('location', $report->ID),
        get_field('concerns', $report->ID),
        get_field('attachments', $report->ID)
        ]);
        
    }
    fclose($report_file);

    // Create a CSV file for 'neglect-report' post type
    $neglect_report_file = fopen(PLUGIN_DIR . 'reports/neglect_reports.csv', 'w');
    fputcsv($neglect_report_file, ['Report ID', 
    'Animals Involved',
    'Is Emergency',
    'Species',
    'Breed',
    'Color',
    'Date Time',
    'Location',
    'Incident',
    'Person',
    'Person Description',
    'Person Street',
    'Person Street2',
    'Person City',
    'Person State',
    'Person Zip',
    'Person Country',
    'Location of Animal on Property',
    'Person Car',
    'Animal Shelter',
    'Animal Confinement',
    'Water Supply',
    'Food Supply',
    'Sanitation Concerns',
    'Skin Condition',
    'Physical Condition',
    'Injuries',
    'Injuries Location',
    'Vet Information',
    'Additional Information',
    'Attachments',
    'Report Name',
    'Report Email',
    'Report Street',
    'Report Street2',
    'Report City',
    'Report State',
    'Report Zip',
    'Report Country',
    'Contacted Authorities']);

   
    
    foreach ($neglect_reports as $report) {
        fputcsv($neglect_report_file, [$report->ID, 
     get_field('animals_involved', $report->ID),
        get_field('is_emergency', $report->ID),
        get_field('species', $report->ID),
        get_field('breed', $report->ID),
        get_field('color', $report->ID),
        get_field('date_time', $report->ID),
        get_field('location', $report->ID),
        get_field('incident', $report->ID),
        get_field('person', $report->ID),
        get_field('person_description', $report->ID),
        get_field('person_street', $report->ID),
        get_field('person_street2', $report->ID),
        get_field('person_city', $report->ID),
        get_field('person_state', $report->ID),
        get_field('person_zip', $report->ID),
        get_field('person_country', $report->ID),
        get_field('location_of_animal_on_property', $report->ID),
        get_field('person_car', $report->ID),
        get_field('animal_shelter', $report->ID),
        get_field('animal_confinement', $report->ID),
        get_field('water_supply', $report->ID),
        get_field('food_supply', $report->ID),
        get_field('sanitation_concerns', $report->ID),
        get_field('skin_condition', $report->ID),
        get_field('physical_condition', $report->ID),
        get_field('injuries', $report->ID),
        get_field('injuries_location', $report->ID),
        get_field('vet_information', $report->ID),
        get_field('additional_information', $report->ID),
        get_field('attachments', $report->ID),
        get_field('report_name', $report->ID),
        get_field('report_email', $report->ID),
        get_field('report_street', $report->ID),
        get_field('report_street2', $report->ID),
        get_field('report_city', $report->ID),
        get_field('report_state', $report->ID),
        get_field('report_zip', $report->ID),
        get_field('report_country', $report->ID),
        get_field('contacted_authorities', $report->ID)

    ]);
    }
    fclose($neglect_report_file);

    // Create a zip file containing the CSV files
    $zip_file_name = 'reports-' . $date . '.zip';
    $zip_file_path = PLUGIN_DIR . 'reports/' . $zip_file_name;
    $zip = new ZipArchive;
    if ($zip->open($zip_file_path, ZipArchive::CREATE) === TRUE) {
        $zip->addFile(PLUGIN_DIR . 'reports/reports.csv', 'reports.csv');
        $zip->addFile(PLUGIN_DIR . 'reports/neglect_reports.csv', 'neglect_reports.csv');
        $zip->close();
    }

    // Force download the zip file
    if (file_exists($zip_file_path)) {
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zip_file_name . '"');
        readfile($zip_file_path);
        // Delete all files after download
        unlink(PLUGIN_DIR . 'reports/reports.csv');
        unlink(PLUGIN_DIR . 'reports/neglect_reports.csv');
        unlink($zip_file_path);
    }

    wp_reset_postdata(); // Restore global post data
    // reload the page
 
}

}


if (isset($_GET['message']) && $_GET['message'] === 'no-reports-found') { ?>

<div class="notice notice-error is-dismissible">
    <p>No reports found for the selected date.</p>
</div>


<?php


}



// admin page called young williams reports status page

function register_yw_reports_status_page() {
   add_menu_page( 
         'Young Williams Reports Status', 
         'Young Williams Reports Status', 
         'manage_options', 
         'yw-reports-status', 
         'yw_reports_status_page_content', 
         'dashicons-media-spreadsheet', 
        
    );

    // submenu page called assignment page
    add_submenu_page(
        'yw-reports-status', // Parent menu slug
        'Assignments', // Page title
        'Assignments', // Menu title
        'manage_options', // Capability required
        'assignments', // Menu slug
        'assignments_page_content' // Callback function to display content
    );
}

add_action('admin_menu', 'register_yw_reports_status_page');

function yw_reports_status_page_content() {
    $neglect_reports = get_posts(array(
    //   neglect-report and report custom post types
        'post_type' => array('report', 'neglect-report'),
        'posts_per_page' => -1,
        // make sure status is not closed 
        'meta_query' => array(
            array(
                'key' => 'status',
                'value' => 'closed',
                'compare' => '!=',
            ),
        ),
    ));
// map for title , date, assigned to, status
    $neglect_reports = array_map(function ($report) {
        $status = get_field('status', $report->ID);
        if (!$status) {
            $status = 'Open';
        }
        return [
            'ID' => $report->ID, // 'ID' => 'ID
            'title' => get_the_title($report->ID),
            // get date ex. March 20, 2021
            'date' => get_the_date('F j, Y', $report->ID),
            'assigned_to' => get_field('assigned_to', $report->ID),
            'status' =>  $status,
        ];
    }, $neglect_reports);

    

    ?>
<div class="wrap">
    <div class="young-williams-report-table">
        <div class="report-header">
            <div class="cell">
                <p class="text">Report</p>
            </div>
            <div class="cell">
                <p class="text">Date</p>
            </div>
            <div class="cell">
                <p class="text">Assigned To</p>
            </div>
            <div class="cell">
                <p class="text">Status</p>
            </div>
        </div>
        <?php foreach ($neglect_reports as $report) {
            $ID = $report['ID'];
            $permalink = get_permalink($ID);
            $title = $report['title'];
            $date = $report['date'];
            $assigned_to = $report['assigned_to'];
            $status = $report['status']; 
            include PLUGIN_DIR . 'pages/templates/report-row.php';
        }
            ?>



    </div>
</div>


</div>
<?php
}
// use gravity form shortcode id 6

function assignments_page_content() {
    // if no post_id is set, redirect to the main page
    if (!isset($_GET['post_id'])) {
        wp_redirect(admin_url('admin.php?page=yw-reports-status'));
        exit;
    }
    echo do_shortcode('[gravityform id="10" title="false" description="false"]');
}

//  admin page to review single report
function register_review_report_page() {
    add_submenu_page(
        'yw-reports-status', // Parent menu slug
        'Review Report', // Page title
        'Review Report', // Menu title
        'manage_options', // Capability required
        'review-report', // Menu slug
        'review_report_page_content' // Callback function to display content
    );
}

add_action('admin_menu', 'register_review_report_page');

function review_report_page_content() {
    $neglect_fields = ['animals_involved', 'is_emergency', 'species', 'breed', 'color', 'date_time', 'location', 'incident', 'person', 'person_description', 'person_street', 'person_street_2', 'person_city', 'person_state', 'person_zip', 'person_country', 'location_of_animal_on_property', 'person_car', 'animal_shelter', 'animal_confinement', 'water_supply', 'food_supply', 'sanitation_concerns', 'skin_condition', 'physical_condition', 'injuries', 'injuries_location', 'vet_information', 'additional_information', 'attachments', 'report_name', 'report_email', 'report_street', 'report_street2', 'report_city', 'report_state', 'report_zip', 'report_country', 'contacted_authorities', 'assigned_to', 'status'];
    $report_fields = ['name', 'email', 'phone_number', 'add_contact_to_case', 'issues_reported', 'date_time', 'location', 'concerns', 'attachments', 'assigned_to', 'status'];
    // if no post_id is set, redirect to the main page
    if (!isset($_GET['post_id'])) {
        wp_redirect(admin_url('admin.php?page=yw-reports-status'));
        exit;
    }

    $title = get_the_title($_GET['post_id']);
    // get acf fields form for the report 
    $fields = get_fields($_GET['post_id']);
     $post_type = get_post_type($_GET['post_id']);

      $form_fields = $post_type === 'report' ? $report_fields : $neglect_fields;

    
//    get acf fields form for the report
$close_admin_page_redirect = admin_url('admin.php?page=close-report&post_id=' . $_GET['post_id']);
$acf_form = '<form method="post" class="young-williams-report-acf-form" action="'. $close_admin_page_redirect . '">';

foreach ($form_fields as $field) {
    $value = get_field($field, $_GET['post_id']);

    $acf_form .= '<div class="young-williams-report-form-group">';
    $acf_form .= '<label class="young-williams-report-form-label" for="' . $field . '">' . ucwords(str_replace('_', ' ', $field)) . '</label>';
    $acf_form .= '<input class="young-williams-report-form-control" type="text" id="' . $field . '" name="' . $field . '" value="' . $value . '">';
    $acf_form .= '</div>';
}
$acf_form .= '<input class="button-primary" type="submit" name="update_report" value="Close Report">';
$acf_form .= '</form>';

    
    ?>
<div class="wrap">
    <h1>Review Report</h1>
    <h2><?php echo $title ?></h2>
    <!-- back to main page -->
    <a class="button-primary" href="<?php echo admin_url('admin.php?page=yw-reports-status') ?>" class="button">Back to
        Main Page</a>
    <!-- close report button -->
    <a class="button-primary" href="<?php echo $close_admin_page_redirect ?>" class="button">Close Report</a>
    <?php echo $acf_form ?>
</div>
<?php

}

// close report admin page

add_action('admin_menu', 'register_close_report_page');

function register_close_report_page() {
    add_submenu_page(
        'yw-reports-status', // Parent menu slug
        'Close Report', // Page title
        'Close Report', // Menu title
        'manage_options', // Capability required
        'close-report', // Menu slug
        'close_report_page_content' // Callback function to display content
    );
}


function close_report_page_content(){
    // if no post_id is set, redirect to the main page
    if (!isset($_GET['post_id'])) {
        wp_redirect(admin_url('admin.php?page=yw-reports-status'));
        exit;
    }

    echo do_shortcode('[gravityform id="12" title="false" description="false"]');

    // echo back to the main page button 
    echo '<a class="button-primary" href="' . admin_url('admin.php?page=yw-reports-status') . '" class="button">Back to Main Page</a>';
    
}