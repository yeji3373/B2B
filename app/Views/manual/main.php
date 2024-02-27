<main>
  <div class='d-flex flex-row' >
    <div class='w-16 border-end position-sticky' id='guide-navbar' style='height: 100vh;'>
      <nav class='nav flex-column flex-nowrap h-100 manual-navigator' style='overflow-y:auto;'>
        <!-- <a class='nav-link active text-dark border-bottom' href="#list-tab">
          Table of contents
        </a> -->
        <div class='nav-item'>
          <a class='nav-link text-dark btn-toggle border-bottom d-flex justify-content-between align-items-center'
            href='#about-tab'>
            Beautynetkorea B2B Site
          </a>
          <nav class='about-menu border-bottom nav flex-column'>
            <a class='nav-link text-secondary ps-4' href='#about-tab-1'>
              Home
            </a>
            <a class='nav-link text-secondary ps-4' href='#about-tab-2'>
              Sing-up
            </a>
            <a class='nav-link text-secondary ps-4' href='#about-tab-3'>
              Login
            </a>
            <a class='nav-link text-secondary ps-4' href='#about-tab-4'>
              Main menu
            </a>
          </nav>
        </div>
        <div class='d-flex flex-column'>
          <a class='nav-link text-dark border-bottom' href='#main-tab'>
            Main
          </a>
        </div>
        <div class='nav-item'>
          <a class='nav-link text-dark btn-toggle border-bottom d-flex justify-content-between align-items-center' 
            href='#product-tab'>
            Product/Checkout
          </a>
          <nav class='product-menu border-bottom nav flex-column'>
            <a class='nav-link text-secondary ps-4' href='#product-tab-1'>
              Brands
            </a>
            <a class='nav-link text-secondary ps-4' href='#product-tab-2'>
              Products
            </a>
            <a class='nav-link text-secondary ps-4' href='#product-tab-3'>
              Products in cart
            </a>
            <a class='nav-link text-secondary ps-4' href='#product-tab-4'>
              Inventory check request
            </a>
          </nav>
        </div>
        <div class='nav-item'>
          <a class='nav-link text-dark btn-toggle border-bottom d-flex justify-content-between align-items-center' 
              href='#order-tab'>
            Order Info
          </a>
          <nav class='order-menu border-bottom nav flex-column'>
            <a class='nav-link text-secondary ps-4' href='#order-tab-1'>
              Dashboard
            </a>
            <a class='nav-link text-secondary ps-4' href='#order-tab-2'>
              Order Detail
            </a>
            <nav class='nav flex-column'>
              <a class='nav-link text-secondary ps-5' href='#order-tab-2-1'>
                inventory request
              </a>
              <a class='nav-link text-secondary ps-5' href='#order-tab-2-2'>
                Checking inventory request
              </a>
              <a class='nav-link text-secondary ps-5' href='#order-tab-2-3'>
                Inventory request confirmation completed
              </a>
              <a class='nav-link text-secondary ps-5' href='#order-tab-2-4'>
                Inventory request result confirmation request
              </a>
              <a class='nav-link text-secondary ps-5' href='#order-tab-2-5'>
                Order Confirmation
              </a>          
              <a class='nav-link text-secondary ps-5' href='#order-tab-2-6'>
                Deposit payment confimed
              </a>
              <a class='nav-link text-secondary ps-5' href='#order-tab-2-7'>
                Wrapping up
              </a>
              <a class='nav-link text-secondary ps-5' href='#order-tab-2-8'>
                Balance payment confirmed
              </a>
              <a class='nav-link text-secondary ps-5' href='#order-tab-2-9'>
                Packaging complete
              </a>
              <a class='nav-link text-secondary ps-5' href='#order-tab-2-10'>
                Waiting for shipment
              </a>
              <a class='nav-link text-secondary ps-5' href='#order-tab-2-11'>
                Shipment completed
              </a>
            </nav>
          </nav>
        </div>
        <div class='nav-item border-bottom'>
          <a class='nav-link text-dark' href='#contact-tab'>
            Contact US
          </a>
        </div>
      </nav>
    </div>
    <?=view('/manual/guide')?>
  </div>
</main>

<script>
$(document).ready(function() {
  let HEADER_OFFSET = $("header").offset().top + $("header").outerHeight();

  $(document).scroll(function() {
    if ( HEADER_OFFSET < $(this).scrollTop()) {
      $("#guide-navbar").addClass('top-0');
    } else {
      $("#guide-navbar").removeClass('top-0');
    }
  });
// }).on('click', '.collapse', function() {
//   console.log($(this).children());
//   if ( $(this).attr('aria-expanded') == 'true' ) {

//   }
// // }).on('click', '.collapse .nav-item', function() {

// //  if ($(this).closest('.collapse').parent().children(":first-child").hasClass('nav-link')) {
// //   console.log($(this).closest('.collapse').parent().children(":first-child"));
// //   // $(this).closest('.collapse').parent().children(":first-child").addClass('active');
// //  }
});
</script>