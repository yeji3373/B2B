<main>
<?=view('/dash/map')?>
<div class='w-100'>
  <div id='mainSliderCarousel' class='carousel carousel-dark slide' data-bs-ride='carousel'>
    <!-- <div class='carousel-indicators'>
      <button type='button' data-bs-target='#mainSliderCarousel' data-bs-slide-to='0' class='shadow-none active' aria-current='true'></button>
      <button type='button' data-bs-target='#mainSliderCarousel' data-bs-slide-to='0' class='shadow-none'></button>
    </div> -->
    <div class='carousel-inner'>
      <div class='carousel-item active'>
        <img class='w-100' src='//beautynetkorea.daouimg.com/2023_b2b/b2b_main_open.jpg'/>
      </div>
    </div>
  </div>
</div>
<div id='introduction' class='section mt-2'>
  <div class='d-flex flex-row w-70 justify-content-center mx-auto border-dark mt-5'>
    <div class='w-50'>
      <div class='title fs-1 manlope manlope800 mb-3'>
        Unique shopping experience<br class='sm-hide'/> 
        with BEAUTYNETKOREA
      </div>
      <div class='mb-3'>
        BEAUTYNETKOREA is a global network specializing in Korean beauty.<br class='sm-hide'/> 
        Based in South Korea, the company offers a wide range of cosmetics,<br class='sm-hide'/> 
        skincare, and beauty-related items sourced from popular Korean brands.<br class='sm-hide'/> 
      </div>
      <div>
        BEAUTYNETKOREA has gained recognition for its extensive product<br class='sm-hide'/> 
        selection, competitive pricing, and commitment to providing customers<br class='sm-hide'/> 
        with access to the latest trends in K-beauty.
      </div>
      <div>
        <a href='/login' class='btn get-started mt-5'>GET STARTED</a>
        <a href='//zrr.kr/5GGw' target='_blank' class='btn learn-more mt-5'>LEARN MORE</a>
      </div>
    </div>
    <div class='w-50 my-auto'>
      <div id="infoCarousel" class="carousel carousel-dark slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src='/img/image_1.jpg' class="d-block w-80 align-middle mx-auto align-items-center" alt="...">
        </div>
        <div class="carousel-item">
          <img src="/img/image_2.jpg" class="d-block w-80 align-middle mx-auto align-items-center" alt="...">
        </div>
        <div class="carousel-item">
          <img src="/img/image_3.jpg" class="d-block w-80 align-middle mx-auto align-items-center" alt="...">
        </div>
      </div>
      <button id="infoPrev" class="carousel-control-prev shadow-none" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button id="infoNext" class="carousel-control-next shadow-none" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
      </div>
    </div>
  </div>
</div>
<hr/>
<div id='ourbrands' class='section'>
  <div class='w-75 mx-auto justify-content-center'>
    <p class='title fs-1 manlope manlope800 text-center'>OUR BRANDS</p>
    <div class='title fs-4 manlope manlope800 text-center fst-italic'>
      <div class='fw-bold'>Private Brands</div>
      <a href='//zrr.kr/E2XS' target='_blank' class='btn learn-more py-1 px-3 mt-1 mb-3 fs-08r w-auto border-thin text-capitalize'>Catalog</a>
    </div>
    <div class='w-80 d-flex flex-row mx-auto mb-5'>
      <div class='w-50 text-center d-flex flex-column align-items-center'>
        <img src='/img/brand_logo/b2b_logo_Eyenlip.jpg' class='border border-1 rounded-1'>
        <a href='//zrr.kr/PhrH' target='_blank' class='btn learn-more py-1 px-3 mt-1 w-auto fs-08r border-thin'>Learn more</a>
      </div>
      <div class='w-50 text-center d-flex flex-column align-items-center'>
        <img src='/img/brand_logo/b2b_logo_Fabyou.jpg' class='border border-1 rounded-1'>
        <a href='//zrr.kr/fwtQ' target='_blank' class='btn learn-more py-1 px-3 mt-2 fs-08r w-auto border-thin'>Learn more</a>
      </div>
      <!-- <div class='w-33 text-center d-flex flex-column align-items-center'>
        <img src='/img/brand_logo/b2b_logo_Sumhair.jpg' class='border border-1 rounded-1 w-72'>
        <a href='//zrr.kr/YxZX' target='_blank' class='btn learn-more py-1 px-3 mt-2 fs-08r w-auto border-thin'>Learn more</a>
      </div> -->
    </div>
    <?php if ( !empty($brandList) ) : ?>
    <p class='title fs-4 manlope manlope800 text-center fst-italic'>Other Brands</p>
    <div>
      <?php 
      $brandTotal = count($brandList);
      $a = 20;
      $b = ceil($brandTotal / $a);
      $c = 0;
      $d = $a;
      ?>
      <div id='brandCarousel' class='carousel carousel-dark slide' data-bs-ride='carousel'>
        <div class="carousel-indicators">
          <?php for ($i=0; $i < $b; $i++) : ?>
          <button type="button" 
              data-bs-target="#brandCarousel"
              data-bs-slide-to="<?=$i?>" 
              class="shadow-none <?=$i == 0 ? 'active' : ''?>" 
              <?=$i == 0 ? 'aria-current="true"' : ''?>></button>
          <?php endfor;?>
        </div>
        <div class='carousel-inner'>
          <?php for ($i=0; $i < $b; $i++) : ?>
            <div class='carousel-item <?= $i == 0 ? 'active': ''?>' style='width: 100%; min-height: 20rem;'>
              <div class='w-95 mx-auto' style='height: 100%;'>
              <ul class='d-flex flex-wrap w-90 justify-content-start mx-auto'>
              <?php for($j = $c; $j < $d; $j++) : 
                if ( !empty($brandList[$j]['logo']) ) : ?>
                <li class='w-19 text-center border border-1 d-block rounded-1 fs-5
                    <?=(($j % 5) > 0 ? 'ms-2' : '')?>
                    <?=($j > 4 ) ? 'mt-3': ''?>' >
                  <img class='w-85' src="/img/brand_logo/other_brand_logo/<?=esc($brandList[$j]['logo'])?>" />
                </li>
              <?php endif;
              endfor;
              $c = $d;
              $d = ($d + $a); ?>
              </ul>
              </div>
            </div>
          <?php endfor;?>
        </div>
        <button class="carousel-control-prev shadow-none w-auto" data-bs-target="#brandCarousel" type="button" data-bs-slide="prev">
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next shadow-none w-auto" data-bs-target="#brandCarousel" type="button" data-bs-slide="next">
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<hr/>
<div id='ourfeaturs' class='section w-75 mx-auto'>
  <div>
    <p class='title fs-1 manlope manlope800 text-center'>Our Features</p>
  </div>
  <div class='d-flex flex-row justify-content-center flex-wrap mt-4 content'>
    <div class='w-30 d-flex flex-column text-center p-2'>
      <div class='mb-3'><img class='icon' src='/img/icon/main/icon_Brand.png'/></div>
      <div class='sub-title manlope manlope700 mb-3'>
        Brands Beyond Boundaries
      </div>
      <div>
        Discover our world of beauty with<br class='sm-hide'/> 
        three exclusive private brands and<br class='sm-hide'/> 
        an extensive selection of popular<br class='sm-hide'/> 
        Korean cosmetics. We are constantly<br class='sm-hide'/> 
        searching for new brands to bring<br class='sm-hide'/> 
        you the latest in beauty innovations
      </div>
    </div>
    <div class='w-30 d-flex flex-column text-center p-2'>
      <div class='mb-3'><img class='icon' src='/img/icon/main/icon_Fast.png'/></div>
      <div class='sub-title manlope manlope700 mb-3'>
        Fast, Reliable, Anytime
      </div>
      <div>
        Experience fast, safe, and<br class='sm-hide'/> 
        accurate delivery with our express<br class='sm-hide'/> 
        services. We also accommodate<br class='sm-hide'/> 
        shipping by your appointed<br class='sm-hide'/> 
        forwarders for your convenience
      </div>
    </div>
    <div class='w-30 d-flex flex-column text-center p-2'>
      <div class='mb-3'><img class='icon' src='/img/icon/main/icon_Payments.png'/></div>
      <div class='sub-title manlope manlope700 mb-3'>
        Flexible Payments
      </div>
      <div>
        Tailored payment solutions to suit<br class='sm-hide'/> 
        everyone's needs, including bank<br class='sm-hide'/> 
        and online payments, available<br class='sm-hide'/> 
        for any country. Also, we allow the<br class='sm-hide'/> 
        option to split payments for your<br class='sm-hide'/> 
        convenience
      </div>
    </div>
    <div class='w-30 d-flex flex-column text-center p-2'>
      <div class='mb-3'><img class='icon' src='/img/icon/main/icon_Global.png'/></div>
      <div class='sub-title manlope manlope700 mb-3'>
        Global Communication
      </div>
      <div>
        Experience the ease of<br class='sm-hide'/> 
        communication with our<br class='sm-hide'/> 
        multilingual support team. We are<br class='sm-hide'/> 
        here to ensure that language is never<br class='sm-hide'/> 
        a barrier and make your experience<br class='sm-hide'/> 
        with us as smooth as possible
      </div>
    </div>
    <div class='w-30 d-flex flex-column text-center p-2'>
      <div class='mb-3'><img class='icon' src='/img/icon/main/icon_SNS.png'/></div>
      <div class='sub-title manlope manlope700 mb-3'>
        Promotion, SNS 
      </div>
      <div>
        Open affordability with our<br class='sm-hide'/> 
        promotion events, making products<br class='sm-hide'/> 
        accessible to all. Our global<br class='sm-hide'/> 
        marketing efforts ensure that our<br class='sm-hide'/> 
        products resonate worldwide, making<br class='sm-hide'/> 
        them a recognizable choice for all
      </div>
    </div>
  </div>
</div>
<hr/>
<div id='join-us' class='section'>
  <div class='w-75 mx-auto'>
    <p class='title fs-1 manlope manlope800 text-center'>
      Join us to get unique<br class='sm-hide'/> 
      shopping experience
    </p>
    <div class='d-flex flex-row text-center mt-4 pt-4'>
      <div class='w-25 justify-content-center'>
        <div>
          <p class='manlope manlope700 sub-title text-break pb-3'>
            Fill out<br class='sm-hide'/> registration form
          </p>
        </div>
        <div class='d-flex flex-column pb-5'>
          This verification process ensures<br class='sm-hide'/> 
          you're a business owner seeking<br class='sm-hide'/> 
          wholesale opportunities.<br class='sm-hide'/> 
          Please note that approval may<br class='sm-hide'/> 
          take up to 24 hours
        </div>
        <div class='d-flex flex-column'>
          <img src="/img/icon1.png" class='icon-image'>
        </div>
      </div>
      <div class='w-25 justify-content-center'>
        <div class='d-flex flex-column'>
          <p class='manlope manlope700 sub-title pb-3'>
            Choose the best<br class='sm-hide'/> 
            products you need
          </p>
        </div>
        <div class='d-flex flex-column pb-5'>
          Explore our curated recommendations<br class='sm-hide'/> 
          and streamline your search with our<br class='sm-hide'/> 
          user-friendly filtering options to find<br class='sm-hide'/> 
          the perfect products for your business
        </div>
        <div class='d-flex flex-column'>
          <img src="/img/icon2.png" class='icon-image'>
        </div>
      </div>
      <div class='w-25 justify-content-center'>
        <div class='d-flex flex-column'>
          <p class='manlope manlope700 sub-title pb-3'>
            Check out, pay &<br class='sm-hide'/> 
            track shipment
          </p>
        </div>
        <div class='d-flex flex-column pb-5'>
          We've streamlined our wholesale<br class='sm-hide'/> 
          experience, prioritizing transparency,<br class='sm-hide'/> 
          safety, and security in both payment<br class='sm-hide'/> 
          and shipping processes
        </div>
        <div class='d-flex flex-column'>
          <img src="/img/icon3.png" class='icon-image'>
        </div>
      </div>
      <div class='w-25 justify-content-center'>
        <div class='d-flex flex-column'>
          <p class='manlope manlope700 sub-title pb-3'>
            Check previous orders<br class='sm-hide'/> 
            and plan new ones
          </p>
        </div>
        <div class='d-flex flex-column pb-5'>
          Stay ahead of the curve with our<br class='sm-hide'/> 
          latest updates! Explore our B2C<br class='sm-hide'/> 
          website, monthly newsletter,<br class='sm-hide'/> 
          and social channels to discover<br class='sm-hide'/> 
          your next best-selling product
        </div>
        <div class='d-flex flex-column'>
          <img src="/img/icon4.png" class='icon-image'>
        </div>
      </div>
    </div>
    <div class='d-flex flex-row justify-content-center join-us-group'>
      <a class='btn terms mx-4' href='/board/getboard/1/2'>
        WHOLESALE ORDER</br>
        TERMS AND CONDITIONS
      </a>
      <!-- <button class='btn manual mx-4'>DETAILED USER MANUAL</button> -->
    </div>
  </div>
</div>
<hr/>
<div id='get-in-touch' class='section'>
  <div class='w-75 mx-auto'>
    <div class='mb-5'>
      <p class='title fs-1 manlope manlope800 text-center'>Get In Touch</p>
    </div>
    <div class='mb-4'>
      <p class='sub-title manlope manlope500 text-center'>Stay connected with us.</p>
    </div>
    <div class='describe text-center'>
      Stay connected with us. Subscribe today and get in touch for the latest updates and offers with our<br class='sm-hide'/> 
      monthly news
    </div>
      <div class='d-flex flex-row text-center justify-content-center mt-4 pt-4'>
        <div class='px-2'>
          <input type='text' name='email-address' class='form-control email-address' placeholder='Email address'>
        </div>
        <div class='px-2'>
          <input type='text' name='full-name' class='form-control full-name' placeholder='Full name'>
        </div>
        <div class='px-2'>
          <button class='btn subscribe'>Subscribe</button>
        </div>
      </div>
  </div>
</div>
<hr/>
<div id='need-help' class='section mb-1'>
  <div class='w-75 mx-auto'>
    <div class='mb-5'>
      <p class='title fs-1 manlope manlope800 text-center'>Need more help?</p>
    </div>
    <div class='mb-5'>
      <p class='describe text-center'>
        Have additional questions or need further assistance? Don't hesitate to reach out to us. Our dedicated team is<br class='sm-hide'/> 
        ready to provide the multy-language support, ensuring your experience is smooth and satisfactory
      </p>
    </div>
    <div>
      <div class='d-flex flex-row text-center justify-content-center'>
        <div class='box'>
          <div>
            <p class='manlope manlope700 country fs-4'>CIS</p>
            <p class='fst-italic name fw-bolder'>Kristina</p>
            <a class='text-decoration-none fw-bolder' href='mailto:cis@beautynetkorea.com' target='_blank'>cis@beautynetkorea.com</a>
          </div>
        </div>
        <div class='box'>
          <div>
            <p class='manlope manlope700 country fs-4'>CIS</p>
            <p class='fst-italic name fw-bolder'>Mikhail</p>
            <a class='text-decoration-none fw-bolder' href='mailto:cisinfo@beautynetkorea.com' target='_blank'>cisinfo@beautynetkorea.com</a>
          </div>
        </div>
        <div class='box'>
          <div>
            <p class='manlope manlope700 country fs-4'>EUROPE</p>
            <p class='fst-italic name fw-bolder'>Anis</p>
            <a class='text-decoration-none fw-bolder' href='mailto:europe@beautynetkorea.com' target='_blank'>europe@beautynetkorea.com</a>
          </div>
        </div>
      </div>
      <div class='d-flex flex-row text-center justify-content-center'>
        <div class='box'>
          <div>
            <p class='manlope manlope700 country fs-4'>North, Central& South America</p>
            <p class='fst-italic name fw-bolder'>Jongtae</p>
            <a class='text-decoration-none fw-bolder' href='mailto:america@beautynetkorea.com' target='_blank'>america@beautynetkorea.com</a>
          </div>
        </div>
        <div class='box'>
          <div>
            <p class='manlope manlope700 country fs-4'>Asia</p>
            <p class='fst-italic name fw-bolder'>Suzie</p>
            <a class='text-decoration-none fw-bolder' href='mailto:asia@beautynetkorea.com' target='_blank'>asia@beautynetkorea.com</a>
          </div>
        </div>
        <div class='box'>
          <div>
            <p class='manlope manlope700 country fs-4'>Middle East, Africa</p>
            <p class='fst-italic name fw-bolder'>Ismahene</p>
            <a class='text-decoration-none fw-bolder' href='mailto:middleeast@beautynetkorea.com' target='_blank'>middleeast@beautynetkorea.com</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class='modal'>
  <div class='modal-popup'>
    <div class='modal-wrap w-100 h-100'>
      <div class='modal-contents d-flex flex-row h-90'>
        <div class='catalog w-40'>
          <div class='what-we-made'>
            <p class='title fs-1 manlope manlope800 text-center'>Find out<br class='sm-hide'/>what<br class='sm-hide'/>we<br class='sm-hide'/>made</p>
          </div>
          <div class='catalog-btn-wrap'>
            <a href='//zrr.kr/E2XS' target='_blank' class='catalog-btn fs-1 manlope manlope800 text-center fst-italic mx-auto'>Catalog&#8594;</a>
          </div>
        </div>
        <div class='d-flex flex-column w-60 h-100'>
          <div class='eyenlip h-50 d-flex flex-column align-items-end'>
            <img src='/img/brand_logo/b2b_logo_Eyenlip_2.png' class='w-100 h-80'>
            <a href='//zrr.kr/PhrH' target='_blank' class='eyenlip-btn fst-italic text-end me-3'>learn more&#8594;</a>
          </div>
          <div class='fabyou h-50 d-flex flex-column align-items-end border-bottom-0'>
            <div class='d-flex h-80 w-100 align-items-center'>
              <img src='/img/brand_logo/b2b_logo_Fabyou_2.png' class='w-75 h-50 mx-auto'>
            </div>
            <div class='d-flex h-20 align-self-end'>
            <a href='//zrr.kr/fwtQ' target='_blank' class='fabyou-btn fst-italic text-end me-3'>learn more&#8594;</a>
            </div>
          </div>
          <!-- <div class='sumhair h-33 d-flex flex-column align-items-end'>
            <img src='/img/brand_logo/b2b_logo_Sumhair_2.png' class='w-100 h-80'>
            <a href='//zrr.kr/YxZX' target='_blank' class='sumhair-btn fst-italic text-end me-3'>learn more&#8594;</a>
          </div> -->
        </div>
      </div>
      <div class='d-flex flex-row h-10 justify-content-end align-items-center'>
        <button type='button' class='btn close-btn no-today me-1'>Do not show this message today</button>
        <button type='button' class='btn close-btn me-1'>Close</button>
      </div>
    </div>
  </div>
</div>
</main>