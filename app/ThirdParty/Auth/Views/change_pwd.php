<?= $this->extend($config->viewLayout) ?>
<?= $this->section('main') ?>

<section class='account_section'>
<h2><?= lang('Auth.changePassword') ?></h2>
<?= view('Auth\Views\_notifications') ?>

<!-- CHANGE PASSWORD -->
<form method="POST" action="<?= site_url('change-password'); ?>" accept-charset="UTF-8"
	onsubmit="changePassword.disabled = true; return true;">
	<?= csrf_field() ?>
	<p>
		<label><?= lang('Auth.currentPassword') ?></label><br />
		<input required type="password" minlength="5" name="password" value="" />
	</p>
	<p>
		<label><?= lang('Auth.newPassword') ?></label><br />
		<input required type="password" minlength="5" name="new_password" value="" />
	</p>
	<p>
		<label><?= lang('Auth.newPasswordAgain') ?></label><br />
		<input required type="password" minlength="5" name="new_password_confirm" value="" />
	</p>
    <p>
        <button name="changePassword" type="submit"><?= lang('Auth.update') ?></button>
    </p>
</form>
<?= $this->endSection() ?>