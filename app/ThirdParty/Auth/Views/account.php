<?= $this->extend($config->viewLayout) ?>
<?= $this->section('main') ?>

<section class='account_section'>
<h1><?= lang('Auth.accountSettings') ?></h1>
<?= view('Auth\Views\_notifications') ?>

<form method="POST" action="<?= site_url('account'); ?>" accept-charset="UTF-8">
	<?= csrf_field() ?>
	<p>
		<label><?= lang('Auth.name') ?></label><br />
		<input disabled type="text" name="name" value="<?= $userData['name']; ?>"/>
	</p>
	<p>
		<label><?= lang('Auth.email') ?></label><br />
		<input disabled type="text" value="<?= $userData['email']; ?>"/>
	</p>
	<p class='text-lowercase'><a href="<?=site_url('/change-password')?>" class='float-right'><?=lang('Auth.changePassword')?></a></p>
	<p class='text-lowercase'><a href='<?=site_url('/delete')?>'><?=lang('Auth.deleteAccount')?></a></p>
</form>


<!-- CHANGE EMAIL -->
<!-- <h2><?= lang('Auth.changeEmail') ?></h2>
<p><?= lang('Auth.changeEmailInfo') ?></p>

<form method="POST" action="<?= site_url('change-email'); ?>" accept-charset="UTF-8"
	onsubmit="changeEmail.disabled = true; return true;">
	<?= csrf_field() ?>
	<p>
		<label><?= lang('Auth.newEmail') ?></label><br />
		<input required type="email" name="new_email" value="<?= old('new_email') ?>" />
	</p>
	<p>
		<label><?= lang('Auth.currentPassword') ?></label><br />
		<input required type="password" name="password" value="" />
	</p>
    <p>
        <button name="changeEmail" type="submit"><?= lang('Auth.update') ?></button>
    </p>
</form> -->
<?= $this->endSection() ?>