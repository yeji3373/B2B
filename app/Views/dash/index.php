<main>
<div class='w-100'>
  <img class='w-100' src='//beautynetkorea.daouimg.com/2023_b2b/b2b_main_open.jpg'/>
</div>
<div class='introduction'>
  <div class='d-flex flex-row w-70 justify-content-center mx-auto border-dark mt-5'>
    <div class='w-50'>
      <p class='title fs-1 manlope800'>Unique shopping experience</p>
      <p class='title fs-1 manlope800'>with BEAUTYNETKOREA</p>
      <p><br></p>
      <p class='pretendard'>BEAUTYNETKOREA is a global network specializing in Korean beauty.</p>
      <p class='pretendard'>Based in South Korea, the company offers a wide range of cosmetics,</p>
      <p class='pb-3 pretendard'>skincare, and beauty-related items sourced from popular Korean brands.</p>
      <p class='pretendard'>BEAUTYNETKOREA has gained recognition for its extensive product</p>
      <p class='pretendard'>selection, competitive pricing, and commitment to providing customers</p>
      <p class='pretendard'>with access to the latest trends in K-beauty.</p>
      <!-- <button class='btn get-started mt-5'>
        GET STARTED
      </button>
      <button class='btn learn-more mt-5'>
        LEARN MORE
      </button> -->
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
<!-- <div class='w-100'>
  <div class='w-70 mx-auto my-5'>
    <h2 class='fw-bold text-center mb-4'>We has 200+ cosmetics brands</h2>
    <ul class='d-flex flex-wrap ps-0 m-0 w-100 justify-content-between'>
      <?php for ( $i = 1; $i <= 20; $i++ ) : ?>
      <li class='w-19 text-center border border-1 d-block rounded-1 fs-5
              <?=(($i % 5) > 0 ? 'me-2' : '')?>
              <?=($i > 5 ) ? 'mt-3': ''?>' >
        <img class='w-85'
            src='//beautynetkorea.daouimg.com/2023_b2b/brand_logo/b2b_logo_<?=($i < 10 ? '0'.$i : $i)?>.jpg' />
      </li>
      <?php endfor; ?>
    </ul>
  </div>
</div> -->
<div class='ourbrands'>
  <div class='w-75 mx-auto justify-content-center'>
    <p class='title fs-1 manlope800 text-center'>OUR BRANDS</p>
    <p class='title fs-4 manlope800 text-center italic'>Private Brands</p>
    <div class='w-80 d-flex flex-row mx-auto mb-5'>
      <div class='w-33 text-center'>
        <img src='/img/brand_logo/b2b_logo_Eyenlip.jpg' class='border border-1 rounded-1'>
      </div>
      <div class='w-33 text-center'>
        <img src='/img/brand_logo/b2b_logo_Fabyou.jpg' class='border border-1 rounded-1'>
      </div>
      <div class='w-33 text-center'>
        <img src='/img/brand_logo/b2b_logo_Sumhair.jpg' class='border border-1 rounded-1'>
      </div>
    </div>
    <p class='title fs-4 manlope800 text-center italic'>Other Brands</p>
    <div>
      <ul class='d-flex flex-wrap w-90 justify-content-between mx-auto px-1'>
      <?php $idx = 0; ?>
      <?php foreach ( $brandList as $brand ) : ?>
      <?php $idx++; ?>
      <li class='w-19 text-center border border-1 d-block rounded-1 fs-5
              <?=(($idx % 5) > 0 ? 'me-2' : '')?>
              <?=($idx > 5 ) ? 'mt-3': ''?>' >
        <!-- <a href="https://eyenlip.co.kr/" target="blank"> -->
          <img class='w-85' src="/img/brand_logo/top20/<?=($brand)?>" />
        </a>
      </li>
      <?php endforeach; ?>
      </ul>
    </div>
  </div>
</div>
<div class='join-us'>
  <div class='w-75 mx-auto'>
    <p class='title fs-1 manlope800 text-center'>Join us to get unique</p>
    <p class='title fs-1 manlope800 text-center'>shopping experience</p>
    <div class='d-flex flex-row text-center mt-4 pt-4'>
      <div class='w-25 justify-content-center'>
        <div class='d-flex flex-column'>
          <p class='manlope700 sub-title text-break'>Fill out</p>
          <p class='manlope700 sub-title text-break pb-3'>registration form</p>
        </div>
        <div class='d-flex flex-column pb-5'>
          <p class='pretendard'>This verification process ensures</p>
          <p class='pretendard'>you're a business owner seeking</p>
          <p class='pretendard'>wholesale opportunities.</p>
          <p class='pretendard'>Please note that approval may</p>
          <p class='pretendard'>take up to 24 hours.</p>
        </div>
        <div class='d-flex flex-column'>
          <img src="/img/icon1.png" class='icon-image'>
        </div>
      </div>
      <div class='w-25 justify-content-center'>
        <div class='d-flex flex-column'>
          <p class='manlope700 sub-title'>Choose the best</p>
          <p class='manlope700 sub-title pb-3'>products you need</p>
        </div>
        <div class='d-flex flex-column pb-5'>
          <p class='pretendard'>Explore our curated recommendations</p>
          <p class='pretendard'>and streamline your search with our</p>
          <p class='pretendard'>user-friendly filtering options to find</p>
          <p class='pretendard'>the perfect products for your business.</p>
          <p class='pretendard'><br></p>
        </div>
        <div class='d-flex flex-column'>
          <img src="/img/icon2.png" class='icon-image'>
        </div>
      </div>
      <div class='w-25 justify-content-center'>
        <div class='d-flex flex-column'>
          <p class='manlope700 sub-title'>Check out, pay &</p>
          <p class='manlope700 sub-title pb-3'>track shipment</p>
        </div>
        <div class='d-flex flex-column pb-5'>
          <p class='pretendard'>We've streamlined our wholesale</p>
          <p class='pretendard'>experience, prioritizing transparency,</p>
          <p class='pretendard'>safety, and security in both payment</p>
          <p class='pretendard'>and shipping processes.</p>
          <p class='pretendard'><br></p>
        </div>
        <div class='d-flex flex-column'>
          <img src="/img/icon3.png" class='icon-image'>
        </div>
      </div>
      <div class='w-25 justify-content-center'>
        <div class='d-flex flex-column'>
          <p class='manlope700 sub-title'>Check previous orders</p>
          <p class='manlope700 sub-title pb-3'>and plan new ones</p>
        </div>
        <div class='d-flex flex-column pb-5'>
          <p class='pretendard'>Stay ahead of the curve with our</p>
          <p class='pretendard'>latest updates! Explore our B2C</p>
          <p class='pretendard'>website, monthly newsletter,</p>
          <p class='pretendard'>and social channels to discover</p>
          <p class='pretendard'>your next best-selling product.</p>
        </div>
        <div class='d-flex flex-column'>
          <img src="/img/icon4.png" class='icon-image'>
        </div>
      </div>
    </div>
    <div class='d-flex flex-row justify-content-center join-us-group'>
      <button class='btn terms mx-4' onclick="location.href='/board/getboard/1/2'">
        <p>WHOLESALE ORDER</p>
        <P>TERMS AND CONDITIONS</P>
      </button>
      <!-- <button class='btn manual mx-4'>DETAILED USER MANUAL</button> -->
    </div>
  </div>
</div>
<!-- <div class='bg-secondary bg-opacity-10 py-4 '>
  <div class='d-flex flex-row justify-content-center w-75 mx-auto about_this'>
    <div class='w-30 d-flex flex-column me-4'>
      <div class='w-100 rounded-top' style='background-image: url("//beautynetkorea.daouimg.com/2023_b2b/b2b_01.jpg");'></div>
      <div class='py-3 px-2 bg-white bg-opacity-50 rounded-bottom'>
        <p>· Product introduction and customer service after adapting local language/users/culture</p>
        <p>· Management of regional warehouse</p>
        <p>· Fast and accurate delivery</p>
        <p>· Offline connection builds a simple local storage process</p>
      </div>
    </div>
    <div class='w-30 d-flex flex-column me-4'>
      <div class='w-100 rounded-top' style='background-image: url("//beautynetkorea.daouimg.com/2023_b2b/b2b_02.jpg");'></div>
      <div class='py-3 px-2 bg-white bg-opacity-50 rounded-bottom'>
        <p>· Launch new items at the same time with the global market</p>
        <p>· Effective promotion of new products at website and channels</p>
        <p>· Collaboration with MDs in each market for country and seasonal promotions and marketing</p>
      </div>
    </div>
    <div class='w-30 d-flex flex-column'>
      <div class='w-100 rounded-top' style='background-image: url("//beautynetkorea.daouimg.com/2023_b2b/b2b_03.jpg");'></div>
      <div class='py-3 px-2 bg-white bg-opacity-50 rounded-bottom'>
        <p>· Promotion Event</p>
        <p>· Practical promotion for existing customers</p>
        <p>· Link SNS marketing promotion on search engine</p>
        <p>· Promotion of major products with other kinds of products will have synergy effect and Manage sellers</p>
      </div>
    </div>
  </div>
</div>
<div class='join_us'>
  <div class='w-75 mx-auto join_us_container'>
    <h2 class='fw-bold text-center'>Join us</h2>
    <div class='d-flex flex-row justify-content-center join_us_wrapper'>
      <div style='background-image: url("//beautynetkorea.daouimg.com/2023_b2b/b2b_icon_01.png");'></div>
      <div style='background-image: url("//beautynetkorea.daouimg.com/2023_b2b/b2b_icon_02.png");'></div>
      <div style='background-image: url("//beautynetkorea.daouimg.com/2023_b2b/b2b_icon_03.png");'></div>
      <div style='background-image: url("//beautynetkorea.daouimg.com/2023_b2b/b2b_icon_04.png");'></div>
    </div>
  </div>
</div> -->
<div class='get-in-touch'>
  <div class='under-line w-75 mx-auto'>
    <div class='mb-5'>
      <p class='title fs-1 manlope800 text-center'>Get In Touch</p>
    </div>
    <div class='mb-4'>
      <p class='sub-title manlope500 text-center'>Stay connected with us.</p>
    </div>
    <div>
      <p class='describe pretendard text-center'>Subscribe today and get in touch for the latest</p>
      <p class='describe pretendard text-center'>updates and offers with our monthly news.</p>
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
<div class='need-help'>
  <div class='w-75 mx-auto'>
    <div class='mb-5'>
      <p class='title fs-1 manlope800 text-center'>Need more help?</p>
    </div>
    <div class='mb-5'>
      <p class='describe pretendard text-center'>Have additional questions or need further assistance?</p>
      <p class='describe pretendard text-center'>Don't hesitate to reach out to us.</p>
      <p class='describe pretendard text-center'>Our dedicated team is ready to provide the multy-language support,</p>
      <p class='describe pretendard text-center'>ensuring your experience is smooth and satisfactory</p>
    </div>
    <div>
      <div class='d-flex flex-row text-center justify-content-center'>
        <div class='box'>
          <div>
            <p class='manlope700 country'>CIS</p>
            <p class='pretendard italic name'>Kristina</p>
            <a class='text-decoration-none pretendard' href='mailto:cis@beautynetkorea.com' target='_blank'>cis@beautynetkorea.com</a>
          </div>
        </div>
        <div class='box'>
          <div>
            <p class='manlope700 country'>CIS</p>
            <p class='pretendard italic name'>Mikhail</p>
            <a class='text-decoration-none pretendard' href='mailto:cisinfo@beautynetkorea.com' target='_blank'>cisinfo@beautynetkorea.com</a>
          </div>
        </div>
        <div class='box'>
          <div>
            <p class='manlope700 country'>EUROPE</p>
            <p class='pretendard italic name'>Anis</p>
            <a class='text-decoration-none pretendard' href='mailto:europe@beautynetkorea.com' target='_blank'>europe@beautynetkorea.com</a>
          </div>
        </div>
      </div>
      <div class='d-flex flex-row text-center justify-content-center'>
        <div class='box'>
          <div>
            <p class='manlope700 country' style='margin-bottom:-10px;'>North, Central</p>
            <p class='manlope700 country'>& South America</p>
            <p class='pretendard italic name'>Jongtae</p>
            <a class='text-decoration-none pretendard' href='mailto:america@beautynetkorea.com' target='_blank'>america@beautynetkorea.com</a>
          </div>
        </div>
        <div class='box'>
          <div>
            <p class='manlope700 country'>Asia</p>
            <p class='pretendard italic name'>Suzie</p>
            <a class='text-decoration-none pretendard' href='mailto:asia@beautynetkorea.com' target='_blank'>asia@beautynetkorea.com</a>
          </div>
        </div>
        <div class='box'>
          <div>
            <p class='manlope700 country' style='margin-bottom:-10px;'>Middle East,</p>
            <p class='manlope700 country'>Africa</p>
            <p class='pretendard italic name'>Ismahene</p>
            <a class='text-decoration-none pretendard' href='mailto:middleeast@beautynetkorea.com' target='_blank'>middleeast@beautynetkorea.com</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</main>