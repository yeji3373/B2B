<?php if ( session()->isLoggedIn ) { ?>
  <div class="dropdown dropstart">
    <a href="#" class="btn d-block link-light text-decoration-none dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
      <?=session()->userData['email']?>
    </a>
    <ul class="dropdown-menu text-small shadow">
      <li><a class="dropdown-item" href="#">Settings</a></li>
      <li><a class="dropdown-item" href="#">Profile</a></li>
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item" href="/logout">Sign out</a></li>
    </ul>
  </div>
  <?php } else { ?>
  <div class='w-auto'>
    <a type="button" class="btn btn-sm btn-outline-secondary px-4 me-2" href='/register'>Sign-up</a>
    <a type="button" class="btn btn-sm btn-dark px-4" href='/login'>Login</a>
  </div>
<?php } ?>
