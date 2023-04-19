<main>
<div class='w-100'>
  <img src='//beautynetkorea.daouimg.com/2023_b2b/b2b_main.jpg'/>
</div>
<div class='w-100'>
  <div class='w-70 mx-auto my-5'>
    <h2 class='fw-bold text-center mb-4'>We has 200+ cosmetics brands</h2>
    <ul class='d-flex flex-wrap ps-0 m-0 w-100 justify-content-between'>
      <?php for ( $i = 1; $i <= 20; $i++ ) : ?>
      <li class='w-19 text-center border border-1 d-block rounded-1 fs-5
              <?=(($i % 5) > 0 ? 'me-2' : '')?>
              <?=($i > 5 ) ? 'mt-3': ''?>' >
        <!-- BRAND A -->
        <img class='w-85'
            src='//beautynetkorea.daouimg.com/2023_b2b/brand_logo/b2b_logo_<?=($i < 10 ? '0'.$i : $i)?>.jpg' />
      </li>
      <?php endfor; ?>
    </ul>
  </div>
</div>
<div class='bg-secondary bg-opacity-10 py-4 '>
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
</div>
</main>