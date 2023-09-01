<header class='p-3 py-2 text-bg-dark'>
  <nav>
    <div class="d-flex flex-wrap align-items-center justify-content-center">
      <!-- <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
        <svg class="bi me-2" width="40" height="32" role="img" aria-label="Bootstrap"><use xlink:href="#bootstrap"></use></svg>
      </a> -->

      <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
        <li><a href="/" class="nav-link px-2 text-secondary">HOME</a></li>
        <li><a href='<?=site_url('/main')?>' class="nav-link px-2 text-white">Main</a></li>
        <li><a href='<?=site_url('/order')?>' class="nav-link px-2 text-white">Product/Checkout</a></li>
        <li><a href='<?=site_url('/orders')?>' class="nav-link px-2 text-white">Order Info</a></li>
        <!-- <li><a href="#" class="nav-link px-2 text-white">Pricing</a></li>
        <li><a href="#" class="nav-link px-2 text-white">FAQs</a></li>
        <li><a href="#" class="nav-link px-2 text-white">About</a></li> -->
      </ul>

      <div class="text-end">
        <?php echo view('layout/loggedIn'); ?>
      </div>
    </div>
  </nav>
</header>