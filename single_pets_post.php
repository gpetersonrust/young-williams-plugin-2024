<?php 
// get current url from  server
$Server_url = $_SERVER['REQUEST_URI'];
$site_url = get_site_url();
$true_url = $site_url.$Server_url;

 $photo_1 = $pet_information['Photo1'];
$photo_2 = $pet_information['Photo2'];
$photo_3 = $pet_information['Photo3'];

function monthsToYears($months) {
  if ($months < 12) {
      return $months . ($months > 1 ? ' months' : ' month');
  } else {
      $years = floor($months / 12);
      return $years . ($years > 1 ? ' years' : ' year');
  }
}

 

?>

<div
  class="x-row x-container max width e23880-e2 mifc-3 mifc-4 mifc-5 mifc-6 mifc-d mifc-e"
>
  <div class="x-row-inner">
    <div class="x-col e23880-e3 mifc-k mifc-l">
    <div class="x-div e23880-e4 mifc-o mifc-p" style="
    background-image: url(<?php  echo $pet_information['Photo1']?>);
    background-size: cover;
"></div>
    </div>
    <div class="x-col e23880-e6 mifc-k mifc-m">
      <div class="x-text x-text-headline e23880-e7 mifc-t mifc-u mifc-v mifc-w">
        <div class="x-text-content">
          <div class="x-text-content-text">
            <h1 class="x-text-content-text-primary">About <?php  echo $pet_information['AnimalName']?></h1>
          </div>
        </div>
      </div>
      <div class="x-text x-text-headline e23880-e8 mifc-u mifc-x mifc-y">
        <div class="x-text-content">
          <div class="x-text-content-text">
            <h1 class="x-text-content-text-primary"></h1>
          </div>
        </div>
      </div>
      <hr class="x-line e23880-e9 mifc-12" />
      <div
        class="x-row e23880-e10 mifc-3 mifc-7 mifc-8 mifc-9 mifc-a mifc-d mifc-f"
      >
        <div class="x-row-inner">
          <div class="x-col e23880-e11 mifc-k mifc-l">
            <div
              class="x-text x-text-headline e23880-e12 mifc-u mifc-v mifc-w mifc-x mifc-z mifc-10"
            >
              <div class="x-text-content">
                <div class="x-text-content-text">
                  <h4 class="x-text-content-text-primary">Breed:</h4>
                </div>
              </div>
            </div>
            <div
              class="x-text x-text-headline e23880-e13 mifc-u mifc-v mifc-x mifc-z mifc-11"
            >
              <div class="x-text-content">
                <div class="x-text-content-text">
                  <span class="x-text-content-text-primary">
                   
                    <?php echo $pet_information['PrimaryBreed']?>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="x-col e23880-e14 mifc-k mifc-l">
            <div
              class="x-text x-text-headline e23880-e15 mifc-u mifc-v mifc-w mifc-x mifc-z mifc-10"
            >
              <div class="x-text-content">
                <div class="x-text-content-text">
                  <h4 class="x-text-content-text-primary">Age</h4>
                </div>
              </div>
            </div>
            <div
              class="x-text x-text-headline e23880-e16 mifc-u mifc-v mifc-x mifc-z mifc-11"
            >
              <div class="x-text-content">
                <div class="x-text-content-text">
                  <span class="x-text-content-text-primary"> <?php echo  monthsToYears($pet_information['Age'])?></span>
                </div>
              </div>
            </div>
          </div>
          <div class="x-col e23880-e17 mifc-k mifc-l">
            <div
              class="x-text x-text-headline e23880-e18 mifc-u mifc-v mifc-w mifc-x mifc-z mifc-10"
            >
              <div class="x-text-content">
                <div class="x-text-content-text">
                  <h4 class="x-text-content-text-primary">Sex:</h4>
                </div>
              </div>
            </div>
            <div
              class="x-text x-text-headline e23880-e19 mifc-u mifc-v mifc-x mifc-z mifc-11"
            >
              <div class="x-text-content">
                <div class="x-text-content-text">
                  <span class="x-text-content-text-primary">
                  <?php echo $pet_information['Sex']?>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        class="x-row e23880-e20 mifc-3 mifc-7 mifc-8 mifc-9 mifc-a mifc-d mifc-g"
      >
        <div class="x-row-inner">
          <div class="x-col e23880-e21 mifc-k mifc-l">
            <div
              class="x-text x-text-headline e23880-e22 mifc-u mifc-v mifc-w mifc-x mifc-z mifc-10"
            >
              <div class="x-text-content">
                <div class="x-text-content-text">
                  <h4 class="x-text-content-text-primary">Weight:</h4>
                </div>
              </div>
            </div>
            <div
              class="x-text x-text-headline e23880-e23 mifc-u mifc-v mifc-x mifc-z mifc-11"
            >
              <div class="x-text-content">
                <div class="x-text-content-text">
                  <span class="x-text-content-text-primary">
                  <?php echo $pet_information['BodyWeight']?>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="x-col e23880-e24 mifc-k mifc-l">
            <div
              class="x-text x-text-headline e23880-e25 mifc-u mifc-v mifc-w mifc-x mifc-z mifc-10"
            >
              <div class="x-text-content">
                <div class="x-text-content-text">
                  <h4 class="x-text-content-text-primary">Site</h4>
                </div>
              </div>
            </div>
            <div
              class="x-text x-text-headline e23880-e26 mifc-u mifc-v mifc-x mifc-z mifc-11"
            >
              <div class="x-text-content">
                <div class="x-text-content-text">
                  <span class="x-text-content-text-primary">
                  <?php echo $pet_information['Site']?>
                  </span>
                </div>
              </div>
            </div>
          </div>
          <div class="x-col e23880-e27 mifc-k mifc-l">
            <div
              class="x-text x-text-headline e23880-e28 mifc-u mifc-v mifc-w mifc-x mifc-z mifc-10"
            >
              <div class="x-text-content">
                <div class="x-text-content-text">
                  <h4 class="x-text-content-text-primary">Animal ID:</h4>
                </div>
              </div>
            </div>
            <div
              class="x-text x-text-headline e23880-e29 mifc-u mifc-v mifc-x mifc-z mifc-11"
            >
              <div class="x-text-content">
                <div class="x-text-content-text">
                  <span class="x-text-content-text-primary">
                  <?php echo $pet_information['ID']?>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div
        class="x-row x-container max width e23880-e30 mifc-3 mifc-4 mifc-9 mifc-a mifc-d mifc-h"
      >
        <div class="x-row-inner">
          <div class="x-col e23880-e31 mifc-k mifc-l mifc-n">
            <div class="x-div e23880-e32 mifc-o mifc-q"><span class="x-image e23880-e33 mifc-r"><img src="<?php echo $photo_1 ?>" width="800" height="400" alt="Image" loading="lazy"></span></div>
            <?php if(!empty($photo_2)): ?> <div class="x-div e23880-e34 mifc-o mifc-q"><span class="x-image e23880-e35 mifc-r"><img src="<?php echo $photo_2 ?>" width="800" height="400" alt="Image" loading="lazy"></span></div> <?php endif; ?>
                <?php if(!empty($photo_3)): ?> <div class="x-div e23880-e34 mifc-o mifc-q"><span class="x-image e23880-e35 mifc-r"><img src="<?php echo $photo_3 ?>" width="800" height="400" alt="Image" loading="lazy"></span></div> <?php endif; ?> </div>
         </div>
      </div>
      <div
        class="x-row e23880-e38 mifc-3 mifc-4 mifc-6 mifc-7 mifc-9 mifc-b mifc-d mifc-i"
      >
        <div class="x-row-inner">
          <div class="x-col e23880-e39 mifc-k mifc-l">
            <a
              class="x-anchor x-anchor-button e23880-e40 mifc-13 mifc-14"
              tabindex="0"
              href="/adopt"
              data-feathr-click-track="true"
              data-feathr-link-aids='["617844865cc52155a7987c13"]'
              ><div class="x-anchor-content">
                <div class="x-anchor-text">
                  <span class="x-anchor-text-primary">ADOPT ME</span>
                </div>
              </div></a
            >
          </div>
          <div class="x-col e23880-e41 mifc-k mifc-l">
            <a
              class="x-anchor x-anchor-button has-graphic e23880-e42 mifc-14 mifc-15"
              tabindex="0"
              href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $true_url?>"
              data-feathr-click-track="true"
              data-feathr-link-aids='["617844865cc52155a7987c13"]'
              ><div class="x-anchor-content">
                <span class="x-graphic" aria-hidden="true"
                  ><i
                    class="x-icon x-graphic-child x-graphic-icon x-graphic-primary"
                    aria-hidden="true"
                    data-x-icon-s=""
                  ></i
                ></span></div
            ></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

 