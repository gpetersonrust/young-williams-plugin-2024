
<?php 
$pagination_count = 8;
 

if(count($pets_posts) == 0) {
    echo '<h2>No pets found</h2>';
    return;
}
?>
<div id="petGrid" class="pet-grid">
      

    <template id="pet-card-template">
        <div data-pet_id="" data-pet-spieces="" data-pet_status="" class="pet-card">
            <button id="">
                <div class="pet-thumbnail">
                    <img src="" alt="">
                </div>
                <div class="pet-name">
                </div>
            </button>
        </div>
    </template>
</div>

 
 
 <script>
  
 let pet_posts = <?php echo json_encode($pets_posts) ?>;
 let pagination_count = <?php echo $pagination_count ?>;

 
 </script>