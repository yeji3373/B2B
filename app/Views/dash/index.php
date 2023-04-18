<main>
<div class='w-100 bg-warning' style='height: 30rem;'></div>
<div class='w-100'>
  <div class='w-70 mx-auto my-5'>
    <h2 class='fw-bold text-center mb-4'>We has 200+ cosmetics brands</h2>
    <ul class='d-flex flex-wrap'>
      <?php for ( $i = 0; $i < 20; $i++ ) : ?>
      <li 
        class='w-19 py-3 text-center bg-secondary bg-opacity-10 d-block rounded-1 fs-5
              <?=($i % 5) < 5 ? 'me-2' : ''?>
              <?=($i > 4 ) ? 'mt-3': ''?>' >
        BRAND A
      </li>
      <?php endfor; ?>
    </ul>
  </div>
</div>
<div class='bg-secondary bg-opacity-10 py-4'>
  <div class='d-flex flex-row justify-content-center w-75 mx-auto' style='height: 25rem;'>
    <div class='border border-dark w-30 d-flex flex-column me-4'>
      <div class='w-100' style='height: 65%;'>IMG</div>
      <div>TEXT</div>
    </div>
    <div class='border border-dark w-30 d-flex flex-column me-4'>
      <div class='w-100' style='height: 65%;'>IMG</div>
      <div>TEXT</div>
    </div>
    <div class='border border-dark w-30 d-flex flex-column'>
      <div class='w-100' style='height: 65%;'>IMG</div>
      <div>TEXT</div>
    </div>
  </div>
</div>
<div>
  <div class='w-75 mx-auto'>
    <h2 class='fw-bold text-center'>Join us</h2>
    <div style='height: 25rem;'></div>
  </div>
</div>
</main>