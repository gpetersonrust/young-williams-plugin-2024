import Moxcar_Dynamic_Scroll from '../../../library/utilities/dynamic/moxcar_scroll';
import '../scss/app.scss'; // Importing SCSS file

let adoption_link = document.querySelector('.x-anchor.x-anchor-menu-item.m5-14.m5-15.m5-16.m5-17.m5-19.m5-1a.m5-1d.m5-1e.m5-1f.m5-1h.m5-1j.m5-1m.m5-1p.m5-1u.m5-1z.m5-20');

const faq_arrows = [...document.querySelectorAll('.arrow-down-svgrepo-com')];
 
faq_arrows.forEach((arrow) => { 

    arrow.onclick = function () {
     let parent = arrow.closest('.faq');
        
    parent.classList.toggle('active');
      
    }
});
 
 if(adoption_link){
    // add classlist of young-williiams-adopt-button
    adoption_link.classList.add('young-williams-adopt-button');
    
 }
 
 // pet card template


let pet_grid = document.querySelector('#petGrid');
 
 
let current_url = window.location.href;
let return_element ;


function buildPetCard(test_pet) {
    // Clone the template
    const template = document.getElementById('pet-card-template');
    const clone = document.importNode(template.content, true);
     const pet_card = clone.querySelector('.pet-card');
    // Set data attributes
   pet_card.setAttribute('data-pet-spieces', test_pet.Species);
   pet_card.setAttribute('data-pet_status', test_pet.stray_or_pet);

    // Set button id (replace 'buttonId' with the actual property name of the button id in test_pet)
    pet_card.querySelector('button').setAttribute('id', test_pet.ID);

    // Set thumbnail image source and alt text
    const thumbnail = pet_card.querySelector('.pet-thumbnail img');
    thumbnail.setAttribute('src', test_pet.Photo);
    thumbnail.setAttribute('alt', test_pet.Name);

    // Set pet name
    pet_card.querySelector('.pet-name').textContent = test_pet.Name;

    pet_card.onclick =  () => petClickHandler(pet_card);

  
    
    return pet_card;
}

// Example usage:
   pet_posts.slice(0,12).forEach ((pet) => {   pet_grid.appendChild( buildPetCard(pet));  })
 
  let moxcar_dynamic_scroll = new Moxcar_Dynamic_Scroll(pet_grid,pet_posts, youngWilliamsAddElements);

  function youngWilliamsAddElements(elementToWatch, elements, paginationLimit, index) {
    
    const minimum = index * paginationLimit;
    const maximum = (index + 1) * paginationLimit;

   
    const elementsToAdd = elements.slice(minimum, maximum);
    elementsToAdd.forEach((pet) => {
        let element_exists = document.querySelector(`.pet-card button[id="${pet.ID}"]`);
         !element_exists &&
        pet_grid.appendChild(buildPetCard(pet));
  });
 
}




//  // add event listener to pagination buttons
 

 

let pet_cards = document.querySelectorAll('.pet-card');

 let pet_slider_arrows = document.querySelectorAll('.animal-slide-arrow');

 
async function petClickHandler (card)  {
    let button = card.querySelector('button');
    let id = button.id;
    let pet_status  = card.dataset.pet_status
      return_element = card;
     
    try {
        // alert message
        createAlertMessage('petGrid', 'progress', 'Loading pet, please wait...', false); // Persistent alert with a custom message
        let api_url  = app.site_url + '/wp-json/animal-api/v1/load-pets' + '?ID=' + id + '&pet_status=' + pet_status;

        let response = await fetch(api_url);

        if (!response.ok) {
            throw new Error('Request failed');
        }

        const data = await response.json();

 

        // Assuming the API sends back a "link" property in the response
        const link = data.link;
 // Alert message of opening new page
 createAlertMessage('petGrid', 'success', 'Opening Pet Page'); // Persistent alert with a custom message


        if(!link) {
            alert('Pet not found, please select another pet.');
        }
       
    //    filter pet_posts by id
    let pet = pet_posts.filter((pet) => {
        return pet.ID === id;
    });
    pet = pet[0];
     
     
    
        //  petModal
        let pet_link_modal = petLinkModal(link, data.post_data, pet.stray_or_pet);

        // attach pet link modal to body
       !document.querySelector('#pet-card')  &&  document.body.appendChild(pet_link_modal);
        // lock body scroll
        document.body.classList.add('modal-open');

    // change url without reloading page 
    window.history.pushState({id: id}, '', link);
       
      
    } catch (error) {
        console.error('Error:', error);
        // Handle the error here, e.g., display an error message to the user
    }
}
let animal_slider = [...document.querySelectorAll('.animal-layout-slide')].filter((slide) => {
    let image = slide.querySelector('img');
    let url = window.location.href;
 
    return image && image?.src && image.src !== "_" && image.src !== url;
});

 
if(animal_slider.length > 0){

 
 let anmials_slider_length = animal_slider.length;

 let currentSlide = 0;
 
 if(anmials_slider_length > 1){

 let interval  = 5000;



let timer = setInterval(() => {
    nextSlide();
}, interval)




animal_slider[currentSlide].classList.add('active');




// give first slide a class of active

 
function nextSlide(){
    animal_slider[currentSlide].classList.remove('active');
    
    currentSlide = (currentSlide + 1) % animal_slider.length;
    animal_slider[currentSlide].classList.add('active');
     
    clearInterval(timer);
    timer = setInterval(() => {
        nextSlide();
    }, interval)
}



 function prevSlide(){
    animal_slider[currentSlide].classList.remove('active');
    
    currentSlide = (currentSlide - 1) % animal_slider.length;
        // if current slide is less than 0, set it to the last slide
        if(currentSlide < 0){
            currentSlide = animal_slider.length - 1;
        }
    animal_slider[currentSlide].classList.add('active');

    clearInterval(timer);
    timer = setInterval(() => {
        nextSlide();
    }, interval)
 }

//  loop through each arrow and add event listener
pet_slider_arrows.forEach((arrow) => {
    arrow.addEventListener('click', () => {
        arrow = arrow.closest('.animal-slide-arrow');
        // clear the interval  
 
        if(arrow.id.includes('forward')){
            nextSlide();
        } else {
            prevSlide();
        }
    });

    // reset the interval
   
});



 } else {
    animal_slider[currentSlide].classList.add('active');
 
 pet_slider_arrows.forEach((arrow) => {
    arrow.remove();
    // b
 });    

 }

}


 function createAlertMessage(id, state, message, persistent = false) {
    // find current alert message and remove it
    const currentAlert = document.querySelector(`.alert-message`);

    if (currentAlert) {
        currentAlert.remove();
        }

    const alertDiv = document.createElement('div');
    alertDiv.className = `alert-message ${state}`;
    alertDiv.innerHTML = `<p>${message}</p>`;
  
    // Find the element with the specified ID
    const targetElement = document.getElementById(id);
  
    if (targetElement) {
      // Insert the alert message after the target element
      targetElement.parentNode.insertBefore(alertDiv, targetElement.nextSibling);
  
      // Add the "show" class immediately after adding the element
      alertDiv.classList.add('show');
  
      if (!persistent) {
        // Remove the alert message after 5 seconds
        setTimeout(() => {
          alertDiv.classList.remove('show');
          setTimeout(() => {
            alertDiv.remove();
          }, 500); // Delay removal to allow for transition effect (if any)
        }, 5000);
      }
    } else {
      console.error(`Element with ID ${id} not found.`);
    }
  }
 

  

  function petLinkModal(link, pet, stray_or_pet){
    let pet_link_modal = document.createElement('div');
    pet_link_modal.id =  'pet-card';
    pet_link_modal.setAttribute('data-strayOrPet', stray_or_pet);
    pet_link_modal.setAttribute('data-pageUrl', link);
    pet_link_modal.setAttribute('data-species', pet?.Species);
    pet_link_modal.classList.add('pet-link-modal');
    pet_link_modal.addEventListener('click', function(e){
      let target = e.target;
  
 
       
  
    
      if(target.classList.contains('pet-link-modal'))  {
        pet_link_modal.remove();
         //   unlock body scroll
    document.body.classList.remove('modal-open');
        //   scroll to pet card
        return_element.scrollIntoView({behavior: 'smooth', block: 'start'});
          //   have url change back to original url when modal is closed
          window.history.pushState({id: pet.ID}, '', current_url);
      }
     
   


  
 
    });
   let encoded_link = encodeURI(link);
    const Species = pet?.Species;

     const stray_or_pet_class = stray_or_pet == 'pet' ? 'pet-single-card-pet' : 'pet-single-card-stray';
      let pet_details = [
        {
            label: 'Breed',
            property: 'PrimaryBreed',
            pet_type: 'both'
        },
        {
            label: 'Age',
            property: 'Age',
            pet_type: 'both'
        }, 
        {
            label: 'Size',
            property: 'Size',
            pet_type: 'stray'

        }, 
        {
            label: 'Color',
            property: 'PrimaryColor',
            pet_type: 'stray'
        },
       
        {
            label: "Sex", 
             property: "Sex",
               pet_type: 'both'
             
           }, 
        {
            label: 'Found Date',
            property: 'FoundDate',
            pet_type: 'stray'
        
        }, 
        {
            label: 'Found Location',
            property: 'FoundLocation',
            pet_type: 'stray'
        }, 
        {
            label: "City/State", 
            property: "CityState",
            pet_type: 'stray'
        },
       
        {
            label: "Weight",
            property: "BodyWeight",
            pet_type: 'both'
        }, 
        {
            label: "Site",
            property: "Site",
            pet_type: 'stray'
        }, 
      
        {
            label: "Apply",
            property: "Site",
            pet_type: 'pet'
        },
        {
            label: "Animal ID",
            property: "ID",
            pet_type: 'both'
        },
        
      
      ]
    let logos  = `
    <div class="x-col e17592-e49 mdko-g mdko-i">
        <div class="x-text x-content e17592-e50 mdko-14">
            <div class="heateor_sss_sharing_container heateor_sss_horizontal_sharing" data-heateor-ss-offset="0" data-heateor-sss-href="${link}">
                <div class="heateor_sss_sharing_ul">
                    <a aria-label="Facebook" class="heateor_sss_facebook" href="https://www.facebook.com/sharer/sharer.php?u=${encoded_link}" title="Facebook" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle" data-feathr-click-track="true" data-feathr-link-aids="617844865cc52155a7987c13">
                        <span class="heateor_sss_svg" style="background-color:#3c589a;width:35px;height:35px;border-radius:999px;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;box-sizing:content-box">
                            <svg style="display:block;border-radius:999px;" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-5 -5 42 42">
                                <path d="M17.78 27.5V17.008h3.522l.527-4.09h-4.05v-2.61c0-1.182.33-1.99 2.023-1.99h2.166V4.66c-.375-.05-1.66-.16-3.155-.16-3.123 0-5.26 1.905-5.26 5.405v3.016h-3.53v4.09h3.53V27.5h4.223z" fill="#fff"></path>
                            </svg>
                        </span>
                    </a>
                    <a aria-label="Twitter" class="heateor_sss_button_x" href="https://twitter.com/intent/tweet?text=54850366&amp;url=${encoded_link}" title="Twitter" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle" data-feathr-click-track="true" data-feathr-link-aids="617844865cc52155a7987c13">
                        <span class="heateor_sss_svg heateor_sss_s__default heateor_sss_s_x" style="background-color:#2a2a2a;width:35px;height:35px;border-radius:999px;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;box-sizing:content-box">
                            <svg width="100%" height="100%" style="display:block;border-radius:999px;" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
                                <path fill="#fff" d="M21.751 7h3.067l-6.7 7.658L26 25.078h-6.172l-4.833-6.32-5.531 6.32h-3.07l7.167-8.19L6 7h6.328l4.37 5.777L21.75 7Zm-1.076 16.242h1.7L11.404 8.74H9.58l11.094 14.503Z"></path>
                            </svg>
                        </span>
                    </a>
                    <a aria-label="Copy Link" class="heateor_sss_button_copy_link" title="Copy Link" rel="nofollow noopener" href="${link}" onclick="event.preventDefault()" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle" data-feathr-click-track="true" data-feathr-link-aids="617844865cc52155a7987c13">
                        <span class="heateor_sss_svg heateor_sss_s__default heateor_sss_s_copy_link" style="background-color:#ffc112;width:35px;height:35px;border-radius:999px;display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;box-sizing:content-box">
                            <svg style="display:block;border-radius:999px;" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-4 -4 40 40">
                                <path fill="#fff" d="M24.412 21.177c0-.36-.126-.665-.377-.917l-2.804-2.804a1.235 1.235 0 0 0-.913-.378c-.377 0-.7.144-.97.43.026.028.11.11.255.25.144.14.24.236.29.29s.117.14.2.256c.087.117.146.232.177.344.03.112.046.236.046.37 0 .36-.126.666-.377.918a1.25 1.25 0 0 1-.918.377 1.4 1.4 0 0 1-.373-.047 1.062 1.062 0 0 1-.345-.175 2.268 2.268 0 0 1-.256-.2 6.815 6.815 0 0 1-.29-.29c-.14-.142-.223-.23-.25-.254-.297.28-.445.607-.445.984 0 .36.126.664.377.916l2.778 2.79c.243.243.548.364.917.364.36 0 .665-.118.917-.35l1.982-1.97c.252-.25.378-.55.378-.9zm-9.477-9.504c0-.36-.126-.665-.377-.917l-2.777-2.79a1.235 1.235 0 0 0-.913-.378c-.35 0-.656.12-.917.364L7.967 9.92c-.254.252-.38.553-.38.903 0 .36.126.665.38.917l2.802 2.804c.242.243.547.364.916.364.377 0 .7-.14.97-.418-.026-.027-.11-.11-.255-.25s-.24-.235-.29-.29a2.675 2.675 0 0 1-.2-.255 1.052 1.052 0 0 1-.176-.344 1.396 1.396 0 0 1-.047-.37c0-.36.126-.662.377-.914.252-.252.557-.377.917-.377.136 0 .26.015.37.046.114.03.23.09.346.175.117.085.202.153.256.2.054.05.15.148.29.29.14.146.222.23.25.258.294-.278.442-.606.442-.983zM27 21.177c0 1.078-.382 1.99-1.146 2.736l-1.982 1.968c-.745.75-1.658 1.12-2.736 1.12-1.087 0-2.004-.38-2.75-1.143l-2.777-2.79c-.75-.747-1.12-1.66-1.12-2.737 0-1.106.392-2.046 1.183-2.818l-1.186-1.185c-.774.79-1.708 1.186-2.805 1.186-1.078 0-1.995-.376-2.75-1.13l-2.803-2.81C5.377 12.82 5 11.903 5 10.826c0-1.08.382-1.993 1.146-2.738L8.128 6.12C8.873 5.372 9.785 5 10.864 5c1.087 0 2.004.382 2.75 1.146l2.777 2.79c.75.747 1.12 1.66 1.12 2.737 0 1.105-.392 2.045-1.183 2.817l1.186 1.186c.774-.79 1.708-1.186 2.805-1.186 1.078 0 1.995.377 2.75 1.132l2.804 2.804c.754.755 1.13 1.672 1.13 2.75z"></path>
                            </svg>
                        </span>
                    </a>
                    <!-- Add other social media links as needed -->
                    <span style="
                    margin-left: .5rem;
                    font-weight: 800;
                    text-transform: uppercase;
                "> - Share Me!</span>
                </div>
                <div class="heateorSssClear"></div>
            </div>
        </div>
    </div>
`;
    pet_link_modal.innerHTML = `
    <div  class="pet-single-card">
        <div class="pet-single-card-column">

       
       <div class="pet-single-card-slider">
       <div class="x-row-inner" style="
    height: 100%;
    /* background: red; */
    width: 100%;
    z-index: 9999999999;
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
"><div class="x-col e17592-e11 mdko-g mdko- j" style="
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    padding: 0 2rem;
    color: #22395b;
"><div class="x-div e17592-e12 mdko-l mdko-n animal-slide-arrow" id="backward"><i class="x-icon e17592-e13 mdko-p" aria-hidden="true" data-x-icon-s=""></i></div><div class="x-div e17592-e14 mdko-l mdko-n animal-slide-arrow" id="forward"><i class="x-icon e17592-e15 mdko-p" aria-hidden="true" data-x-icon-s=""></i></div></div></div>
       ${
        pet?.Photo && !Array.isArray(pet?.Photo) ? `
       <div class="pet-single-card-slide">
        <img src="${pet?.Photo}" alt="${pet?.AnimalName}" />
     </div> `: ""}

        ${
        pet?.Photo1 && !Array.isArray(pet?.Photo1) ? `
         <div class="pet-single-card-slide">
            <img src="${pet?.Photo1  }" alt="${pet?.AnimalName}" />
        </div> `: ""}
        ${
        pet?.Photo2  && !Array.isArray(pet?.Photo2)? `
            <div class="pet-single-card-slide">
                <img src="${pet?.Photo2  }" alt="${pet?.AnimalName}" />
            </div> `: ""}
        ${pet?.Photo3 && !Array.isArray(pet?.Photo3) ? `
            <div class="pet-single-card-slide">
                <img src="${pet?.Photo3  }" alt="${pet?.AnimalName}" />
            </div> `: ""}


      
         
       </div>
       </div>
       <div class="pet-single-card-column">
        <h2> ${pet.AnimalName || pet.Name}

            <span id="close_modal" class="close-modal" >
                X
            </span>
        </h2>
        ${ pet?.Dsc && !Array.isArray(pet?.Dsc) ? 
        `<div class="pet-single-card-description">
            <p style="display:inline-block; margin: 1rem 0 !important;" id="pet-description" data-short=""  >${ pet.Dsc }  </p>  
        </div>` : ""}
        <div class="pet-single-data ${stray_or_pet_class}">
            ${generatePetDetails(stray_or_pet, stray_or_pet_class, pet, pet_details)}
                 
           </div>
           ${logos}
         
       </div>
       
       <div> 
       ${stray_or_pet == 'pet' ? `
       <p><i>For animals located at Young-Williams Animal Center or Young-Williams Animal Village, please apply </i><b><i>in person</i></b><i>. For animals currently in a foster home, please click the link to schedule a meet and greet. Responses from foster may take up to 5-7 business days. </i><b><i>Please note: Application does not place animals on hold or guarantee approval for adoption.</i></b></p>
        <p><i>&nbsp;</i><i><a class="young-williiams-adopt-button" style="margin-right: 3px;" href="https://www.young-williams.org/adopt/" data-feathr-click-track="true" data-feathr-link-aids="[&quot;617844865cc52155a7987c13&quot;],617844865cc52155a7987c13">Click here</a></i><i>&nbsp;for general information about the adoption process and options!</i></p>
      </div>` : ``}
    </div> 
</div> `;

  


 

let close_modal = pet_link_modal.querySelector('#close_modal');
close_modal.addEventListener('click', () => {
    pet_link_modal.remove();
    // unlock body scroll
    document.body.classList.remove('modal-open');
    // scroll to pet card
    return_element.scrollIntoView({behavior: 'smooth', block: 'start'});
}
);

let pet_single_card_sliders = [...pet_link_modal.querySelectorAll('.pet-single-card-slide')].map((slide, index) => {
    slide.dataset.slide = index;
    return slide;
});
let animal_slide_arrows = [...pet_link_modal.querySelectorAll('.animal-slide-arrow')];
 let forward_arrow = animal_slide_arrows.filter((arrow) => {
        return arrow.id.includes('forward');
    }
);
let backward_arrow = animal_slide_arrows.filter((arrow) => {
        return arrow.id.includes('backward');
    }
);


//   first slider is active
pet_single_card_sliders[0].classList.add('active');

// add event listener to forward arrow using dataset.slide of each slide 
forward_arrow[0].addEventListener('click', () => {
    let active_slide = pet_link_modal.querySelector('.pet-single-card-slide.active');
    let active_slide_index = active_slide.dataset.slide;
    let next_slide_index = parseInt(active_slide_index) + 1;
    let next_slide = pet_link_modal.querySelector(`.pet-single-card-slide[data-slide="${next_slide_index}"]`);
    if(next_slide){
        active_slide.classList.remove('active');
        next_slide.classList.add('active');
    }
});

// add event listener to backward arrow using dataset.slide of each slide
backward_arrow[0].addEventListener('click', () => {
    let active_slide = pet_link_modal.querySelector('.pet-single-card-slide.active');
    let active_slide_index = active_slide.dataset.slide;
    let prev_slide_index = parseInt(active_slide_index) - 1;
    let prev_slide = pet_link_modal.querySelector(`.pet-single-card-slide[data-slide="${prev_slide_index}"]`);
    if(prev_slide){
        active_slide.classList.remove('active');
        prev_slide.classList.add('active');
    }
});
 
 
 

return pet_link_modal;
  }


  function petLinksMonthsToString(months) {
  
    // Convert the input to an integer
    months = parseInt(months);
   
    if (months === 0) {
        return "Pending Evaluation";
    }
 
    

    // Handle the conversion logic
    if (months < 10) {
        return `${months} month${months !== 1 ? 's' : ''}`; // Pluralize if necessary
    } else if (months < 12) {
        return "1 year";
    } else {
        let years = Math.floor(months / 12); // Calculate the number of years
        let remainder = months % 12; // Calculate the remainder months

        // Check if closer to the previous year
        if (remainder <= 5) {
            return `${years} year${years !== 1 ? 's' : ''}`; // Round down
        } else {
            return `${++years} year${years !== 1 ? 's' : ''}`; // Round up
        }
    }
}

function trim_content(content, length){

 
    //  break content into array of words
    let content_array = content.split(' ');
    //  get first 10 words
    let trimmed_content = content_array.slice(0, length);
    //  join words back together
    trimmed_content = trimmed_content.join(' ');
    //  add ellipsis
    trimmed_content = trimmed_content + '...';
    // create read more button 
    let read_more_button = document.createElement('button');
    read_more_button.classList.add('read-more-button');
    read_more_button.textContent = 'Read More';
    // add event listener to read more button
     

    return trimmed_content;
}

 
function generatePetDetails(stray_or_pet, stray_or_pet_class, pet, details) {
    const petDetails = [];
 
        details.forEach(detail => {
            let pet_type_detail = detail.pet_type;
            let value = pet[detail.property];
            if(detail.property == 'Age'){
                value = petLinksMonthsToString(value);
            } else if ( detail.property == 'CityState'){
                let city = pet.City ? Array.isArray(pet.City) ? "" :  pet.City : "";
                let state = pet.State ? Array.isArray(pet.State) ? "" :  pet.State : "";
                let cityState;
                 if(city && state){
                    cityState = city + ', ' + state;
                 } else if(city && !state){
                    cityState = city;
                 } else if(!city && state){
                    cityState = state;
                 }
              value = cityState;
            }

             if(detail.label == 'Apply'){ 
                let value = pet[detail.property];
                 if(value == 'Foster Care') { 
                    value = `<a class="foster-care-application" href="https://forms.office.com/pages/responsepage.aspx?id=lG6t1wiPMEOW0_cy63ksOavVvhPTfJlJqzEbUu7umO5UN1RMMUZLOFU4Wks4T1E2RlFPTkdUMVhIUi4u" data-feathr-click-track="true" data-feathr-link-aids="617844865cc52155a7987c13" style="
    text-align: center;
    display: block;
">Foster Care</a>`;
                    petDetails.push(`
                     <div class="pet-single-data-column ${stray_or_pet_class}">
                            <h4>${detail.label}:</h4>
                            ${value}
                        </div>

                    `);
                        
                 }  else {
                    petDetails.push(`
                    <div class="pet-single-data-column ${stray_or_pet_class}">
                        <h4>${detail.label}:</h4>
                        <span>${ value }</span>
                    </div>

                    `);

                 }
          
             }
             
            if((pet_type_detail == 'both' || pet_type_detail == stray_or_pet) && detail.label != 'Apply'){
            petDetails.push(`
                <div class="pet-single-data-column ${stray_or_pet_class}">
                    <h4>${detail.label}:</h4>
                    <span>${ value }</span>
                </div>
            `);
            }
        });
     

    return petDetails.join('');
}



