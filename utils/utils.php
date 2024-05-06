<?php 


function curlGen($url)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo 'cURL Error #:' . $err;

        return '';
    }

    $xml = simplexml_load_string($response);
    $json = json_encode($xml);

	 
    return json_decode($json, TRUE);
}

const _AUTH_KEY = 'removed for security reasons';

function getPets($species)

{
	
    
    return curlGen('http://ws.petango.com/webservices/wsadoption.asmx/AdoptableSearch?authkey=' . _AUTH_KEY . '&speciesID=' . $species . '&sex=A&ageGroup=ALL&location=&site=Adoptions&onHold=A&orderBy=ID&primaryBreed=All&secondaryBreed=All&specialNeeds=A&noDogs=A&noCats=A&noKids=A&stageid=0');
}

function getPetDetails($id)
{
    return curlGen('http://ws.petango.com/webservices/wsAdoption.asmx/AdoptableDetails?animalID=' . $id . '&authkey=' . _AUTH_KEY);
}


function displayPets($atts, $content = null, $tag): string
{
  $request_uri = rtrim($_SERVER['REQUEST_URI'], '/');
   
  $matching_uris = [
	'/adopt/dogs'  =>  [
		"name" => "dog",
		"species" => "1"
	],
	'/adopt/cats' => [
		"name" => "cat",
		"species" => "2"
	],
	'/adopt/other' => [
		"name" => "other",
		"species" => "1003"
	], 
	'/lost-found/current-strays/dogs' => [
		"name" => "dog",
		"species" => "1"
	],
	'/lost-found/current-strays/cats' => [
		"name" => "cat",
		"species" => "2"
	],
	'/lost-found/current-strays/other' => [
		"name" => "other",
		"species" => "1003"
	]
  
  ];

//  stray or pet conditional 
  $is_stray_or_pet = strpos($request_uri, 'strays') !== false ? 'stray' : 'pet';
 

	 
// new york standard time
date_default_timezone_set('America/New_York');
   $animal_api_call_time = get_option('animal_api_next_call_time');
//   convert to date
	$animal_api_call_time = date("Y-m-d H:i:s", strtotime($animal_api_call_time));
    // time now  in am/pm
	$current_time = date("Y-m-d H:i:s");
	 
    // refresh time in minutes
	$animal_api_refresh_time = get_option('animal_api_refresh_time');

	

	// calculate and round in minutes the difference between current time and next api call time
	$diff =  calculateTimeDifferenceInMinutes($animal_api_call_time, $current_time);
 

  
   

	 
	    $species =  $matching_uris[$request_uri]['species'];

	   $human_readbale_species = $matching_uris[$request_uri]['name'];


	  
	//    if pets is stray then use getStrays else use getPets
	$is_stray_or_pet == 'stray' ? $pets = getStrays($species) : $pets = getPets($species);
      $access_key = $is_stray_or_pet == 'stray' ? 'an' : 'adoptableSearch';
    
	   $pets = $pets['XmlNode'];
	   if($is_stray_or_pet == 'stray') {
		 $pets = array_map(function($pet){
			$new_pet = $pet['an'];
			$new_pet['stray_or_pet'] = 'stray';
			return  $new_pet;
		}, $pets);
	     
	   } else {
		 $pets = array_map(function($pet){
			$new_pet = isset($pet['adoptableSearch']) ? $pet['adoptableSearch'] : $pet;
			$new_pet['stray_or_pet'] = 'pet';
			return  $new_pet;
		}, $pets);

	   }
      
	$pets_posts = $pets;
	//  remove last element from array
	array_pop($pets_posts);
		 
     ob_start();
	 include( PLUGIN_DIR. '/pets_template.php'); 
	$html = ob_get_contents();
	ob_end_clean();

 
	 return    isset($script) ? $html . $script : $html;
	
}

function get_pets_by_species($species){

 

 if($species != 'other'){
	$meta_query= 	array(
		array(
			'key' => 'Species',
			'value' => $species,
			'compare' => 'LIKE'
		)
		);	 
		} else{
			 
        //    everything but dog and cat
		$meta_query= 	array(
			'relation' => 'AND',
			array(
				'key' => 'Species',
				'value' => 'Dog',
				'compare' => '!='
			),
			array(
				'key' => 'Species',
				'value' => 'Cat',
				'compare' => '!='
			)
			);

		}

		 
	$args = array(
		'post_type' => 'animal',
		'posts_per_page' => -1,
		'meta_query' => $meta_query
	);
	$the_query = new WP_Query( $args );
//   map $the_query->posts; to just permalink, title, and image_url
    
	$posts = array();
	foreach($the_query->posts as $post){

		 
		$posts[] = array(
			'permalink' => get_permalink($post->ID),
			'Name' => $post->post_title,
			'Photo' => get_field('Photo', $post->ID),
		);
	}

	return 	 $posts;

}

function displayPetsSeniors($atts, $content = null, $tag): string
{
    $a = shortcode_atts(array(
        'species' => 'species',
    ), $atts);

    $species = $atts['species'];
  


    //0, all; 1, dog; 2, cat; 3, rabbit; 4, NA; 5, small&furry; 1003, all farm
    $pets = getPets($species);
    $pets = $pets['XmlNode'];

    $petHtml = '<div class="pets">';
    $count = 1;
    $length = count($pets);
    foreach ($pets as $pet) {

	
		$pet = $pet['adoptableSearch'];
		print_r($pet);
		$age = $pet['Age'];

		if ((int)$age == 0) {
			$age = "Pending Evaluation";
		}elseif ((int)$age < 12){
			$age = $pet['Age'] . 'm';
			$petYears = 0;
		} else {
			$age = round((int)$pet['Age'] / 12);
			$petYears = round((int)$pet['Age'] / 12);
			$age = (string)$age . 'y';
		}
		
		if ($petYears >= 7 && $age != "Pending Evaluation"){
			$breed = $pet['PrimaryBreed'];
			$name = $pet['Name'];
			$species = $pet['Species'];
			$sex = $pet['Sex'];
			
			$specialNeeds = $pet['SpecialNeeds'][0];
			$spayneuter = $pet['SN'];

			

			$photoUrl = $pet['Photo'];
			$location = $pet['Location'];
			$onHold = $pet['OnHold'];
			$stage = $pet['Stage'];
			$ageGroup = $pet['AgeGroup'];
			$id = $pet['ID'];
			$kids = $pet['NoKids'];
			if($kids[0] == 'Y'){
				$kids = "<p>Pet is suitable for adult owners or owners with older children.</p><hr />";
			}else{
				$kids = "";
			}

			$details = getPet($id);
			//debug_to_console($details);
			$weight = $details['BodyWeight'];
			$name = $details['AnimalName'];
			$sex = $details['Sex'];
			$site = $details['Site'];
			if ($site == "Foster Care"){
				$site = "<a href='https://forms.office.com/pages/responsepage.aspx?id=lG6t1wiPMEOW0_cy63ksOavVvhPTfJlJqzEbUu7umO5UN1RMMUZLOFU4Wks4T1E2RlFPTkdUMVhIUi4u' target='_blank'>" . "Off-site Foster Care Application" . "</a>";
			}
			$petdesc = $details['Dsc'];

			$photo_one_url = $details['Photo1'];
			$photo_two_url = $details['Photo2'];
			$photo_three_url = $details['Photo3'];

			// Build HTML image elements for however many images are loaded
			$all_pet_photos = [
				$photo_one_url,
				$photo_two_url,
				$photo_three_url
			];
			$imageCount = 0;
			$photoHTML = "";
			foreach($all_pet_photos as $key=>$pet_photo) {
				if(!empty($pet_photo)) {
					// Makes opacity for first image full and the rest, 0
					if ($key == 0 ) {
						$opacityIndex = 1;
					} else {
						$opacityIndex = 0;
					}
					$photoHTML .= '<img loading="lazy" src="' . $pet_photo . '" onerror="this.style.display=\'none\'" style="opacity: ' . $opacityIndex .';"/>';
					$imageCount++;
				}
			}

			if($imageCount > 1) {
				$rightArrow = '<h1 id="slider-right-button" class="slider-arrow" onClick="flipForward(this.getAttribute(\'data-animalid\'));" data-animalid="' . $id . '">&#62;</h1>';
				$leftArrow = '<h1 id="slider-left-button" class="slider-arrow" onClick="flipBack(this.getAttribute(\'data-animalid\'));" data-animalid="' . $id . '">&#60;</h1>';
			} else {
				$rightArrow = $leftArrow = "";
			}

			if(is_array($petdesc)){
				$petdesc = "Bio coming soon. Come meet me in person to learn more about my personality!";
			}

			$petHtml .= '<style>.pet-photo img{ padding-bottom: 5px; }</style>';

			$petHtml .= '<div id="' . $id . '" class="pet "><div class="petPhoto" style="height:300px;background: url(' . $photoUrl . ') no-repeat center center / cover;"></div><div class="petName">' . $name . '</div></div><span class="'. $petYears .'"></span>
			<div class="pet-modal modal" id="' . $id . '">
			  <div class="modal-content">
				  <span class="close">&times;</span>
				  <div class="pet-photo">
				  <!-- SLIDER CONTENT-->
							<div id="slider-container" class="slider-container" data-animalid="' . $id . '">
									' . $leftArrow . '
								<div class="images-container" id="slider-images-' . $id . '" data-animalid="' . $id . '">
									' . $photoHTML . '
								</div>
									' . $rightArrow . '
							</div>
				  <!-- SLIDER CONTENT-->
				  </div>
				  <div class="pet-info">  
					<h2>' . $name . '</h2>
					<hr/>
					<p>Breed: ' . $breed . '</p>
					<hr/>
					<p>Age: ' . $age . '</p>
					<hr/>
					<p>Sex: ' . $sex . '</p>
					<hr/>
					<p>Weight: ' . $weight . '</p>
					<hr/>
					<p>Site: ' . $site . '</p>
					<hr/>
					' . $kids . 
					'<p>Animal ID: ' . $id . '
					<p><!--<a href="https://forms.office.com/pages/responsepage.aspx?id=lG6t1wiPMEOW0_cy63ksOavVvhPTfJlJqzEbUu7umO5UN1RMMUZLOFU4Wks4T1E2RlFPTkdUMVhIUi4u" target="_blank"><button>Application</button></a> Please apply in person for this pet! Applications will be given preference in order of arrival.-->
				  </div>
				<div class="pet-desc" style="clear: both;">
				<div style="font-weight: bold; font-size: 1.4rem;"> About Me</div>
					' . $petdesc . '
				</div>
				</div>
				</div>';
		}
    }

    return $petHtml . '</div>';
}

function displayPetsBeagles($atts, $content = null, $tag): string
{
    $a = shortcode_atts(array(
        'species' => 'species',
    ), $atts);

    $species = $atts['species'];


    //0, all; 1, dog; 2, cat; 3, rabbit; 4, NA; 5, small&furry; 1003, all farm
    $pets = getPets($species);
    $pets = $pets['XmlNode'];

    $petHtml = '<div class="pets">';
    $count = 1;
    $length = count($pets);
    foreach ($pets as $pet) {
		$pet = $pet['adoptableSearch'];
		if ($pet['PrimaryBreed'] == "Beagle"){
			$breed = $pet['PrimaryBreed'];
			$name = $pet['Name'];
			$species = $pet['Species'];
			$sex = $pet['Sex'];
			
			$specialNeeds = $pet['SpecialNeeds'][0];
			$spayneuter = $pet['SN'];

			$age = $pet['Age'];

			if ((int)$age == 0) {
				$age = "Pending Evaluation";
			}elseif ((int)$age < 12){
				$age = $pet['Age'] . 'm';
			} else {
				$age = round((int)$pet['Age'] / 12);
				$age = (string)$age . 'y';
			}

			$photoUrl = $pet['Photo'];
			$location = $pet['Location'];
			$onHold = $pet['OnHold'];
			$stage = $pet['Stage'];
			$ageGroup = $pet['AgeGroup'];
			$id = $pet['ID'];
			$kids = $pet['NoKids'];
			if($kids[0] == 'Y'){
				$kids = "<p>Pet is suitable for adult owners or owners with older children.</p><hr />";
			}else{
				$kids = "";
			}

			$details = getPet($id);

		
			//debug_to_console($details);
			$weight = $details['BodyWeight'];
			$name = $details['AnimalName'];
			$sex = $details['Sex'];
			$site = $details['Site'];
			if ($site == "Foster Care"){
				$site = "<a href='https://forms.office.com/pages/responsepage.aspx?id=lG6t1wiPMEOW0_cy63ksOavVvhPTfJlJqzEbUu7umO5UN1RMMUZLOFU4Wks4T1E2RlFPTkdUMVhIUi4u' target='_blank'>" . "Off-site Foster Care Application" . "</a>";
			}
			$petdesc = $details['Dsc'];

			$photo_one_url = $details['Photo1'];
			$photo_two_url = $details['Photo2'];
			$photo_three_url = $details['Photo3'];

			// Build HTML image elements for however many images are loaded
			$all_pet_photos = [
				$photo_one_url,
				$photo_two_url,
				$photo_three_url
			];
			$imageCount = 0;
			$photoHTML = "";
			foreach($all_pet_photos as $key=>$pet_photo) {
				if(!empty($pet_photo)) {
					// Makes opacity for first image full and the rest, 0
					if ($key == 0 ) {
						$opacityIndex = 1;
					} else {
						$opacityIndex = 0;
					}
					$photoHTML .= '<img loading="lazy" src="' . $pet_photo . '" onerror="this.style.display=\'none\'" style="opacity: ' . $opacityIndex .';"/>';
					$imageCount++;
				}
			}

			if($imageCount > 1) {
				$rightArrow = '<h1 id="slider-right-button" class="slider-arrow" onClick="flipForward(this.getAttribute(\'data-animalid\'));" data-animalid="' . $id . '">&#62;</h1>';
				$leftArrow = '<h1 id="slider-left-button" class="slider-arrow" onClick="flipBack(this.getAttribute(\'data-animalid\'));" data-animalid="' . $id . '">&#60;</h1>';
			} else {
				$rightArrow = $leftArrow = "";
			}

			if(is_array($petdesc)){
				$petdesc = "Bio coming soon. Come meet me in person to learn more about my personality!";
			}

			$petHtml .= '<style>.pet-photo img{ padding-bottom: 5px; }</style>';

			$petHtml .= '<div id="' . $id . '" class="pet"><div class="petPhoto" style="height:300px;background: url(' . $photoUrl . ') no-repeat center center / cover;"></div><div class="petName">' . $name . '</div></div>
			<div class="pet-modal modal" id="' . $id . '">
			  <div class="modal-content">
				  <span class="close">&times;</span>
				  <div class="pet-photo">
				  <!-- SLIDER CONTENT-->
							<div id="slider-container" class="slider-container" data-animalid="' . $id . '">
									' . $leftArrow . '
								<div class="images-container" id="slider-images-' . $id . '" data-animalid="' . $id . '">
									' . $photoHTML . '
								</div>
									' . $rightArrow . '
							</div>
				  <!-- SLIDER CONTENT-->
				  </div>
				  <div class="pet-info">  
					<h2>' . $name . '</h2>
					<hr/>
					<p>Breed: ' . $breed . '</p>
					<hr/>
					<p>Age: ' . $age . '</p>
					<hr/>
					<p>Sex: ' . $sex . '</p>
					<hr/>
					<p>Weight: ' . $weight . '</p>
					<hr/>
					<p>Site: ' . $site . '</p>
					<hr/>
					' . $kids . 
					'<p>Animal ID: ' . $id . '
					<p><!--<a href="https://forms.office.com/pages/responsepage.aspx?id=lG6t1wiPMEOW0_cy63ksOavVvhPTfJlJqzEbUu7umO5UN1RMMUZLOFU4Wks4T1E2RlFPTkdUMVhIUi4u" target="_blank"><button>Application</button></a> Please apply in person for this pet! Applications will be given preference in order of arrival.-->
				  </div>
				<div class="pet-desc" style="clear: both;">
				<div style="font-weight: bold; font-size: 1.4rem;"> About Me</div>
					' . $petdesc . '
				</div>
				</div>
				</div>';
		}
    }

    return $petHtml . '</div>';
}

function getPet($id)
{
    $details = getPetDetails($id);
    return $details;
}
 
add_shortcode('pets', 'displayPets');
add_shortcode('beagles', 'displayPetsBeagles');
add_shortcode('seniors', 'displayPetsSeniors');
add_shortcode('strays', 'displayStrays');



function debug_to_console($data)
{
    if (is_array($data) || is_object($data)) {
        echo("<script>console.log('PHP: " . json_encode($data) . "');</script>");
    } else {
        echo("<script>console.log('PHP: " . $data . "');</script>");
    }
}














function getStrays($species)
{
     return curlGen('ws.petango.com/webservices/wsadoption.asmx/foundSearch?speciesID=' . $species . '&sex=A&ageGroup=All&authkey=' . _AUTH_KEY . '&orderBy=ID&searchOption=5');
}

function getStrayDetails($id)
{
	return curlGen('ws.petango.com/webservices/wsadoption.asmx/foundDetails?animalID=' . $id . '&authkey=' . _AUTH_KEY);
}


function displayStrays($atts, $content = null, $tag): string
{
    $a = shortcode_atts(array(
        'species' => 'species',
    ), $atts);

    $species = $atts['species'];
    $pets = getStrays($species);
    $pets = $pets['XmlNode'];
    $petHtml = '<div class="pets">';
    $count = 1;
    $length = count($pets);
	if(!empty($pets)) {
		foreach ($pets as $pet) {
			if ($count === $length) {
				break;
			}
			$pet = $pet['an'];
			$name = $pet['Name'];
			$photoUrl = $pet['Photo'];
			$id = $pet['ID'];
			$petHtml .= '<style>.pet-photo img{ padding-bottom: 5px; } .pet-modal{background: url("/wp-content/uploads/2023/04/YWAC-Loading-GIF-final.gif") no-repeat center/20%;}
	@media screen and (max-width: 450px){
		.pet-modal{background: url("/wp-content/uploads/2023/04/YWAC-Loading-GIF-final.gif") no-repeat center/50%;}
	}
	</style>';
			$petHtml .= '<div id="' . $id . '" class="pet"><div class="petPhoto" style="height:300px;background: url(' . $photoUrl . ') no-repeat center center / cover;"></div><div class="petName">' . $name . '</div></div>
				<div class="pet-modal modal" id="' . $id . '"></div>';
			$count++;
		}
	} else {
		$petHtml .= "There are no results in this category.";
	}
    return $petHtml . '</div>';
}

function getAjaxPet($id){
	return curlGen('http://ws.petango.com/webservices/wsAdoption.asmx/AdoptableDetails?animalID=' . $id . '&authkey=' . _AUTH_KEY);
}
add_action( 'wp_ajax_nopriv_get_pet', 'get_pet_details' );
add_action( 'wp_ajax_get_pet', 'get_pet_details' );

function getAjaxStray($id){
	return curlGen('ws.petango.com/webservices/wsadoption.asmx/foundDetails?animalID=' . $id . '&authkey=' . _AUTH_KEY);
}
add_action( 'wp_ajax_nopriv_get_stray', 'get_stray_details' );
add_action( 'wp_ajax_get_stray', 'get_stray_details' );

function get_pet_details($id) {
	$petHtml = '';

	$id=$_POST['id'];
	$ajaxdetails = getAjaxPet($id);
	$weight = $ajaxdetails['BodyWeight'];
    $name = $ajaxdetails['AnimalName'];
	$sex = $ajaxdetails['Sex'];
	$size = $ajaxdetails['Size'];
	$breed = $ajaxdetails['PrimaryBreed'];
	$kids = $ajaxdetails['NoKids'];
	if($kids[0] == 'Y'){
                $kids = "<p>Pet is suitable for adult owners or owners with older children.</p><hr />";
        }else{
                $kids = "";
        }
	$site = $ajaxdetails['Site'];
	$age = $ajaxdetails['Age'];
        if ((int)$age == 0) {
            $age = "Pending Evaluation";
        }elseif ((int)$age < 12){
            $age = $ajaxdetails['Age'] . 'm';
        } else {
            $age = round((int)$ajaxdetails['Age'] / 12);
            $age = (string)$age . 'y';
        }
        if ($site == "Foster Care"){
        	$site = "<a href='https://forms.office.com/pages/responsepage.aspx?id=lG6t1wiPMEOW0_cy63ksOavVvhPTfJlJqzEbUu7umO5UN1RMMUZLOFU4Wks4T1E2RlFPTkdUMVhIUi4u' target='_blank'>" . "Off-site Foster Care Application" . "</a>";
        }
        $petdesc = $ajaxdetails['Dsc'];
        $photo_one_url = $ajaxdetails['Photo1'];
        $photo_two_url = $ajaxdetails['Photo2'];
        $photo_three_url = $ajaxdetails['Photo3'];
        // Build HTML image elements for however many images are loaded
        $all_pet_photos = [
	        $photo_one_url,
                $photo_two_url,
                $photo_three_url
        ];
        $imageCount = 0;
        $photoHTML = "";
        foreach($all_pet_photos as $key=>$pet_photo) {
        	if(!empty($pet_photo)) {
                	// Makes opacity for first image full and the rest, 0
                        if ($key == 0 ) {
                                $opacityIndex = 1;
                        } else {
                                $opacityIndex = 0;
                        }
                        $photoHTML .= '<img loading="lazy" src="' . $pet_photo . '" onerror="this.style.display=\'none\'" style="opacity: ' . $opacityIndex .';"/>';
                        $imageCount++;
                }
        }
        if($imageCount > 1) {
	        $rightArrow = '<h1 id="slider-right-button" class="slider-arrow" onClick="flipForward(this.getAttribute(\'data-animalid\'));" data-animalid="' . $id . '">&#62;</h1>';
                $leftArrow = '<h1 id="slider-left-button" class="slider-arrow" onClick="flipBack(this.getAttribute(\'data-animalid\'));" data-animalid="' . $id . '">&#60;</h1>';
        } else {
                $rightArrow = $leftArrow = "";
	}
	if(is_array($petdesc)){
                $petdesc = "Bio coming soon. Come meet me in person to learn more about my personality!";
        }
        $petHtml .= '<div class="modal-content">
                          <span class="close">&times;</span>
                          <div class="pet-photo">
                          <!-- SLIDER CONTENT-->
                        	<div id="slider-container" class="slider-container" data-animalid="' . $id . '">
                                 	 ' . $leftArrow . '
                                  	<div class="images-container" id="slider-images-' . $id . '" data-animalid="' . $id . '">
                                        	 ' . $photoHTML . '
                                  	</div>
                                     	 ' . $rightArrow . '
                          	</div>
                          <!-- SLIDER CONTENT-->
                          </div>
                          <div class="pet-info">
                                <h2>' . $name . '</h2>
                                <hr/>
                                <p>Breed: ' . $breed . '</p>
                                <hr/>
                                <p>Age: ' . $age . '</p>
                                <hr/>
                                <p>Sex: ' . $sex . '</p>
                                <hr/>
                                <p>Weight: ' . $weight . '</p>
                                <hr/>
				<p>Site: ' . $site . '</p>
				<hr/>
                                ' . $kids .
                                '<p>Animal ID: ' . $id . '
                                <p><!--<a href="https://forms.office.com/pages/responsepage.aspx?id=lG6t1wiPMEOW0_cy63ksOavVvhPTfJlJqzEbUu7umO5UN1RMMUZLOFU4Wks4T1E2RlFPTkdUMVhIUi4u" target="_blank"><button>Application</button></a> Please apply in person for this pet! Applications will be given preference in order of arrival.-->
                          </div>
                        <div class="pet-desc" style="clear: both;">
                        <div style="font-weight: bold; font-size: 1.4rem;"> About Me</div>
                                ' . $petdesc . '
                        </div>
                    </div>';
	wp_send_json_success( $petHtml );
}

function get_stray_details($id) {
	$petHtml = '';

	$id=$_POST['id'];
	$ajaxdetails = getAjaxStray($id);
	$weight = $ajaxdetails['BodyWeight'];
	if($weight == '0.00' || empty($weight)){
		$weight = "Not weighed yet.";
	}
    $name = $ajaxdetails['AnimalName'];
	$sex = $ajaxdetails['Sex'];
	$breed = $ajaxdetails['PrimaryBreed'] . ' ' . (gettype($ajaxdetails['SecondaryBreed']) == "string" ? $ajaxdetails['SecondaryBreed'] : "");
	$foundDate = $ajaxdetails['FoundDate'];
	$foundLocation = $ajaxdetails['FoundLocation'];
	$cityState = (gettype($ajaxdetails['City']) == "string" ? $ajaxdetails['City'] : "") . ' , ' . $ajaxdetails['State'];
	$size = $ajaxdetails['Size'];
	$color = $ajaxdetails['PrimaryColor'] . '/' . (gettype($ajaxdetails['SecondaryColor']) == "string" ? $ajaxdetails['SecondaryColor'] : "");
	$site = (gettype($ajaxdetails['Site']) == "string" ? $ajaxdetails['Site'] : "Pending");
	$age = $ajaxdetails['Age'];
	if ((int)$age == 0) {
		$age = "Pending Evaluation";
	}elseif ((int)$age < 12){
		$age = $ajaxdetails['Age'] . 'm';
	} else {
		$age = round((int)$ajaxdetails['Age'] / 12);
		$age = (string)$age . 'y';
	}
	if ($site == "Foster Care"){
		$site = "<a href='https://forms.office.com/pages/responsepage.aspx?id=lG6t1wiPMEOW0_cy63ksOavVvhPTfJlJqzEbUu7umO5UN1RMMUZLOFU4Wks4T1E2RlFPTkdUMVhIUi4u' target='_blank'>" . "Off-site Foster Care Application" . "</a>";
	}
	$petdesc = $ajaxdetails['Dsc'];
	$photo_one_url = $ajaxdetails['Photo'];
	$photo_two_url = $ajaxdetails['Photo2'];
	$photo_three_url = $ajaxdetails['Photo3'];
	// Build HTML image elements for however many images are loaded
	$all_pet_photos = [
		$photo_one_url,
		$photo_two_url,
		$photo_three_url
	];
	$imageCount = 0;
	$photoHTML = "";
	foreach($all_pet_photos as $key=>$pet_photo) {
		if(!empty($pet_photo)) {
			// Makes opacity for first image full and the rest, 0
			if ($key == 0 ) {
				$opacityIndex = 1;
			} else {
				$opacityIndex = 0;
			}
			$photoHTML .= '<img loading="lazy" src="' . $pet_photo . '" onerror="this.style.display=\'none\'" style="opacity: ' . $opacityIndex .';"/>';
			$imageCount++;
		}
	}
	if($imageCount > 1) {
		$rightArrow = '<h1 id="slider-right-button" class="slider-arrow" onClick="flipForward(this.getAttribute(\'data-animalid\'));" data-animalid="' . $id . '">&#62;</h1>';
		$leftArrow = '<h1 id="slider-left-button" class="slider-arrow" onClick="flipBack(this.getAttribute(\'data-animalid\'));" data-animalid="' . $id . '">&#60;</h1>';
	} else {
		$rightArrow = $leftArrow = "";
	}
	if(is_array($petdesc)){
		$petdesc = "Bio coming soon. Come meet me in person to learn more about my personality!";
	}
	$petHtml .= '<div class="modal-content">
                          <span class="close">&times;</span>
                          <div class="pet-photo">
                          <!-- SLIDER CONTENT-->
                        	<div id="slider-container" class="slider-container" data-animalid="' . $id . '">
                                 	 ' . $leftArrow . '
                                  	<div class="images-container" id="slider-images-' . $id . '" data-animalid="' . $id . '">
                                        	 ' . $photoHTML . '
                                  	</div>
                                     	 ' . $rightArrow . '
                          	</div>
                          <!-- SLIDER CONTENT-->
                          </div>
                          <div class="pet-info">  
							<h2>' . $name . '</h2>
							<hr/>
							<p>Breed: ' . $breed . '</p>
							<hr/>
							<p>Age: ' . $age . '</p>
							<hr/>
							<p>Size: ' . $size . '</p>
							<hr/>
							<p>Color: ' . $color . '</p>
							<hr/>

							<p>Sex: ' . $sex . '</p>
							<hr/>
							<p>Date Found: ' . $foundDate . '</p>
							<hr/>
							<p>Location Found: ' . $foundLocation . '</p>
							<hr/>
							<p>City/State: ' . $cityState . '</p>
							<hr/>
							<p>Weight: ' . $weight . '</p>
							<hr/>
							<p>Site: ' . $site . '</p>
							<hr/>
							<p>Animal ID: ' . $id . '
							<p><!--<a href="https://forms.office.com/pages/responsepage.aspx?id=lG6t1wiPMEOW0_cy63ksOavVvhPTfJlJqzEbUu7umO5UN1RMMUZLOFU4Wks4T1E2RlFPTkdUMVhIUi4u" target="_blank"><button>Application</button></a> Please apply in person for this pet! Applications will be given preference in order of arrival.-->
				  			</div>
                    </div>';
	wp_send_json_success( $petHtml );


}




/**
 * Check if a specified amount of time has passed since the last update,
 * and run a callback function if it has.
 *
 * @param string   $option_name    The name of the option to check/update.
 * @param int      $time_interval  The time interval in seconds.
 * @param callable $callback       The callback function to run.
 */
function run_callback_if_time_passed($option_name, $time_interval, $callback) {
  
    // Get the last update time from the option
    $last_update_time = get_option($option_name);
 
    // If the option doesn't exist, create it and set the current time
    if ($last_update_time === false) {
        update_option($option_name, time());
        return;
    }
	
    // Calculate the time elapsed since the last update
    $current_time = time();
 

 
    $elapsed_time = $current_time - $last_update_time;

 


    // If the elapsed time is greater than or equal to the specified interval, run the callback
    if ($elapsed_time >= $time_interval) {
        call_user_func($callback, $option_name, $current_time);

        // // Update the option with the current time
		update_option($option_name, $current_time);
        
    }
}


 

 




function get_post_id_by_meta($meta_key, $meta_value) {
    $args = array(
        'post_type' => 'animal', // Replace with your custom post type
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => $meta_key,
                'value' => $meta_value,
            ),
        ),
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        $post = $query->posts[0]; // Get the first post found (assuming there's only one)

        return $post->ID;
    } else {
        return 0; // No matching post found
    }
}

 

 // Define a shortcode function to convert months to a string
function months_to_string_shortcode($atts) {
    // Extract the attributes (in this case, the number of months)
    extract(shortcode_atts(array(
        'months' => '',
    ), $atts));
    
    // Convert the input to an integer
    $months = intval($months);

	if ($months == 0){
		return "Pending Evaluation";
	}

    // Handle the conversion logic
    if ($months < 10) {
        return "$months month" . ($months !== 1 ? 's' : ''); // Pluralize if necessary
    } elseif ($months < 12) {
        return "1 year";
    } else {
        $years = floor($months / 12); // Calculate the number of years
        $remainder = $months % 12; // Calculate the remainder months
        
        // Check if closer to the previous year
        if ($remainder <= 5) {
            return "$years year" . ($years !== 1 ? 's' : ''); // Round down
        } else {
            return ++$years . " year" . ($years !== 1 ? 's' : ''); // Round up
        }
    }
}

// Register the shortcode
add_shortcode('months_to_string', 'months_to_string_shortcode');



// Add a menu page
function create_animal_api_menu() {
    add_menu_page(
        'Animal API Control',    // Page title
        'Animal API Control',    // Menu title
        'manage_options',        // Capability required to access the menu page
        'animal-api-control',    // Menu slug
        'animal_api_control_page', // Callback function to display the page content
        'dashicons-admin-generic', // Icon for the menu item (you can change this)
        100                      // Position in the menu
    );

	    // Handle form submission
		if (isset($_POST['animal_api_refresh_time'])  && current_user_can('manage_options') && isset($_POST['animal_api_next_call_time'])) {
			// Sanitize and update the options in the database
			update_option('animal_api_refresh_time', sanitize_text_field($_POST['animal_api_refresh_time']));
			update_option('animal_api_new_update', sanitize_text_field($_POST['animal_api_new_update']));
			update_option('animal_api_next_call_time', sanitize_text_field($_POST['animal_api_next_call_time']));
		}
}
add_action('admin_menu', 'create_animal_api_menu');

// Callback function to display the page content
function animal_api_control_page() {
//    include 'animal-api-control-page.php'; // Include the HTML for the page

// start buffer
ob_start();
include PLUGIN_DIR . 'utils/templates/animal_api_control_page.php';
// get contents of buffer
$contents = ob_get_contents();
// clean buffer
ob_end_clean();
// output
echo $contents;
 
}


function calculateTimeDifferenceInMinutes($animalApiCallTime, $currentTime) {
    // Create DateTime objects for the input times
    $animalApiCallTime = new DateTime($animalApiCallTime);
    $currentTime = new DateTime($currentTime);

    // Check if animal_api_call_time is later than current_time
    if ($animalApiCallTime > $currentTime) {
        return -1; // Return -1 if animal_api_call_time is later
    }

    // Calculate the difference in minutes
    $interval = $animalApiCallTime->diff($currentTime);
    $minutes = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;

    return $minutes;
}

 



function check_dynamic_pet($data_needed = false) {


	$is_dynamic_pet = false;
	$pet_id = null;
  
	// Get the request URI
	$request_uri = $_SERVER['REQUEST_URI'];
  
 
	// URI regex that checks for /adopt/dogs, /adopt/cats, or /adopt/other at the beginning of the URI
	$uri_regex = '/^\/adopt\/(dogs|cats|other)/';
	$is_one_of_dynamic_routes = preg_match($uri_regex, $request_uri, $matches);
         
	if ($is_one_of_dynamic_routes) {
		// Remove the last slash
		
		
		// if request uri has ? in it, remove it and everything after it
		if(strpos($request_uri, '?') !== false){
			$request_uri = substr($request_uri, 0, strpos($request_uri, "?"));
		}
          
		$request_uri = rtrim($request_uri, '/');
		$request_uri_array = explode('/', $request_uri);

		
       
		// Check if the last item in the array is a number
		$last_item = end($request_uri_array);
		//  trime white space from last item
	 

		
		$is_last_item_number = is_numeric($last_item);
            
		if ($is_last_item_number) {
			$is_dynamic_pet = true;
			$pet_id = $last_item;

		 
		}
	}
	if($data_needed){
		return array(
			'is_dynamic_pet' => $is_dynamic_pet ? true : false,
			'pet_id' => $pet_id
		);
	}
  
	return  $is_dynamic_pet  ? 'is-dynamic-pet' : false;
  }
  
  function petData($atts, $content = null, $tag){
 	$request_uri = $_SERVER['REQUEST_URI'];
	 if(strpos($request_uri, '?') !== false){
		$request_uri = substr($request_uri, 0, strpos($request_uri, "?"));
	}
	$request_uri = rtrim($request_uri, '/');
	$request_uri_array = explode('/', $request_uri);
	$last_item = end($request_uri_array);
	$pet_id = $last_item;
	 $pet_information =  getPetDetails($pet_id);
    //   buffer
	ob_start();
	include PLUGIN_DIR . 'single_pets_post.php'; 
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
}
  
//    add_shortcode('single-pet-post', 'petData');
  
  add_shortcode('dynamic-pet', 'check_dynamic_pet');

 


  function modify_open_graph_tags() {
    if (is_single() || is_page()) {
		extract(check_dynamic_pet(true));
        // Get the current request URI
        $request_uri = $_SERVER['REQUEST_URI'];
        $true_url = get_site_url() . $request_uri;
		// if uri current-stray is in the uri, then we need to get the stray details
		 
        // remove /animal/ and last slash from uri to get page title
		$request_uri =  str_replace('/animal/', '', $request_uri);
		$request_uri = rtrim($request_uri, '/');
		// get post by page title
		$post = get_page_by_path($request_uri, OBJECT, 'animal');
		// get post id
		$pet_id =  isset($post->ID) ? $post->ID : $pet_id;

		$is_stray = get_field('pet_type', $pet_id) == 'stray' ? true : false;
        // Define dynamic values
        $pet_name = get_the_title();
		$thumbnail_id = get_post_thumbnail_id(get_the_ID());
		$pet_image =isset(wp_get_attachment_image_src($thumbnail_id, 'medium')[0]) ? wp_get_attachment_image_src($thumbnail_id, 'medium')[0] : "https://www.young-williams.org/wp-content/uploads/2023/04/YWAC-Loading-GIF-final.gif";
       

	 
        // Define Open Graph meta tags
        $share_card_title =  !$is_stray ?  "Adopt " . $pet_name . " from Young-Williams Animal Center today!" : "With your help, let's assist this lost pet in finding its home and owners!";

        $share_card_description = !$is_stray ?   "Come learn more about " . $pet_name . " and other adoptable pets at Young-Williams Animal Center!" : "Join forces with us to locate the owners of this beloved pet! ";
        $share_card_site_title = get_bloginfo('name');

        // Remove existing Open Graph tags
        remove_action('wp_head', 'wp_graph_do_head_tags');

        // Output custom Open Graph meta tags
        echo '<meta property="og:title" content="' . esc_attr($share_card_title) . '" />' . PHP_EOL;
        echo '<meta property="og:image" content="' . esc_url($pet_image) . '" />' . PHP_EOL;
        echo '<meta property="og:image:url" content="' . esc_url($pet_image) . '" />' . PHP_EOL;
        echo '<meta property="og:image:secure_url" content="' . esc_url($pet_image) . '" />' . PHP_EOL;
        echo '<meta property="og:description" content="' . esc_attr($share_card_description) . '" />' . PHP_EOL;
        echo '<meta property="og:url" content="' . esc_url($true_url) . '" />' . PHP_EOL;
        echo '<meta property="og:site_name" content="' . esc_attr($share_card_site_title) . '" />' . PHP_EOL;
    }
}

  
  // Hook this function into the wp_head action to output the modified tags in the header  make priority extremely low so it runs last
  add_action('wp_head', 'modify_open_graph_tags',
  99999999999999999999
  );
 
   
 add_shortcode('dynamic_link', function(){
	$site_field = get_field('Site');
 
	$link;
 	if(strpos($site_field, 'Foster Care') !== false){
		$link = "<a class='foster-care-application' href='https://forms.office.com/pages/responsepage.aspx?id=lG6t1wiPMEOW0_cy63ksOavVvhPTfJlJqzEbUu7umO5UN1RMMUZLOFU4Wks4T1E2RlFPTkdUMVhIUi4u'>$site_field</a>";
	}else{
		 $link = $site_field;
	}
	  
	 return $link;
 });


 add_shortcode('active_uri', function(){
 $request_uri = $_SERVER['REQUEST_URI'];
 return $request_uri;
 
 });

 add_shortcode('get_pet_type', function(){
	$pet_type = get_field('pet_type'); 
	return $pet_type;
 });
 

 function young_williams_city_state_shortcode($atts) {
    // Extract shortcode attributes, if any
    $atts = shortcode_atts(
        array(
            'pet_id' => 0,
        ),
        $atts,
        'city_state'
    );

    // Extract pet ID from the attribute
    $pet_id = intval($atts['pet_id']);

    // Get ACF values for 'City' and 'State'
    $city = get_field('City', $pet_id);
    $state = get_field('State', $pet_id);

    // Capitalize the first letter of 'City' and 'State'
    $city = ucfirst($city);
    $state = ucfirst($state);

    // Perform the logic
    $cityState = '';

    if ($city && $state) {
        $cityState = $city . ', ' . $state;
    } elseif ($city && !$state) {
        $cityState = $city;
    } elseif (!$city && $state) {
        $cityState = $state;
    }

    // Return the value
    return $cityState;
}

// Register the shortcode
add_shortcode('young_williams_city_state', 'young_williams_city_state_shortcode');




add_shortcode('animal-services-faqs', function(){
	 $faqs = get_field('animal_service_faqs', );
	ob_start();
	include PLUGIN_DIR . 'utils/templates/animal_services_faqs.php'; 
	$contents = ob_get_contents();
	ob_end_clean();
	return $contents;
});