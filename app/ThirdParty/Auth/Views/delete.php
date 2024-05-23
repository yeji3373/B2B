<?= $this->extend($config->viewLayout) ?>
<?= $this->section('main') ?>

<section class='account_section'>
<h1><?= lang('Auth.deleteAccount') ?></h1>
<?= view('Auth\Views\_notifications') ?>

<!-- DELETE ACCOUNT -->
<!-- <h2><?= lang('Auth.deleteAccount') ?></h2> -->

<form method="POST" action="<?= site_url('delete-account') ?>" accept-charset="UTF-8">
	<?= csrf_field() ?>
	<p><?= lang('Auth.deleteAccountInfo') ?></p>
	<p>
		<label><?= lang('Auth.currentPassword') ?></label><br />
		<input required type="password" name="password" value="" />
	</p>
	<p>
		<button type="submit" onclick="return confirm('<?= lang('Auth.areYouSure') ?>')"><?= lang('Auth.deleteAccount') ?></button>
	</p>
</form>
</section>
<?= $this->endSection() ?>