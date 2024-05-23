<?=$this->extend($config->viewLayout) ?>
<?=$this->section('main')?>

<section class='login_section'>
  <h1><?= lang('Auth.login') ?></h1>
  <?= view('Auth\Views\_notifications') ?>
  <form method="POST" action="<?= site_url('login'); ?>" accept-charset="UTF-8" class='hasInputAnimate'>
    <p>
      <input required minlength="2" type="text" name="email" value="<?= old('email') ?>" placeholder=" " />
      <label><?= lang('Auth.email') ?></label>
    </p>
    <p>
      <input required minlength="5" type="password" name="password" value="" placeholder=" " />
      <label><?= lang('Auth.password') ?></label>
    </p>
    <p>
      <?= csrf_field() ?>
      <button type="submit"><?= lang('Auth.login') ?></button>
    </p>
    <p>
      <a href="<?= site_url('forgot-password'); ?>" class="float-right"><?= lang('Auth.forgotYourPassword') ?></a>
      <a href="<?= site_url('register'); ?>" class="float-right"><?= lang('Auth.register') ?></a>
    </p>
  </form>
</section>

<?= $this->endSection() ?>