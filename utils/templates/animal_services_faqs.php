<?php 
 

foreach ($faqs as  $faq) { 
    $header = $faq['animal_service_faq_header'];
    $content = $faq['animal_service_faq_body']; ?>
    <div class="faq">
    <div class="faq-header">
        <div class="faq-header-text-container">
            <div class="faq-header-text">  <?php echo $header ?> </div>
        </div><svg class="arrow-down-svgrepo-com" width="23" height="24" viewBox="0 0 23 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5.46931 9.91794C5.09505 10.2922 5.09505 10.899 5.46931 11.2732L10.1577 15.957C10.9063 16.7049 12.1194 16.7046 12.8676 15.9564L17.5542 11.2698C17.9285 10.8955 17.9285 10.2887 17.5542 9.91448C17.1799 9.54022 16.5731 9.54022 16.1989 9.91448L12.1877 13.9257C11.8135 14.3 11.2067 14.2999 10.8324 13.9257L6.8246 9.91794C6.45035 9.54368 5.84356 9.54368 5.46931 9.91794Z" fill="white" />
        </svg>
    </div>
    <div class="faq-body">
     
        <div class="faq-body-container-text">
            <div class="faq-body-text">  
            <?php echo $content ?>
          </div>
        </div>
    </div>
</div>
   
<?php }
