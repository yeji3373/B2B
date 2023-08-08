<?= $this->extend($config->viewLayout) ?>
<?= $this->section('main') ?>

<section class='register_section'>
  <h1><?= lang('Auth.registration') ?></h1>
  <?= view('Auth\Views\_notifications') ?>
  <!-- <form method="POST" action="<?//= route_to('register'); ?>" accept-charset="UTF-8" onsubmit="registerButton.disabled = true; return true;"> -->
  <form method="POST" action="<?= route_to('register'); ?>" accept-charset="UTF-8" onsubmit="registerCheck();" enctype="multipart/form-data" class='hasInputAnimate'>
    <?= csrf_field() ?>
    <fieldset>
      <legend><?=lang('Auth.account')?></legend>
      <p>
        <input minlength="2" type="text" name="name" value="<?= old('name') ?>" placeholder=" " required />
        <label><?= lang('Auth.name') ?></label>
      </p>
      <p>
        <input minlength="2" maxlength="" type="text" name="id" value="<?= old('id') ?>" placeholder=" " required />
        <label><?= lang('Auth.id') ?></label>
      </p>
      <p>
        <input type="email" name="email" value="<?= old('email') ?>" placeholder=" " required />
        <label><?= lang('Auth.email') ?></label>
      </p>
      <p>
      <!-- <input minlength="5" type="password" name="password" value="" placeholder=" " required /> -->
        <!-- <input minlength="5" type="password" name="password" pattern='.{5,}' value="" placeholder=" " required /> -->
        <input type="password" name="password" value="" placeholder=" "
              title="최소 하나의 숫자와, 하나의 대문자 및 소문자, 최소 5자 이상의 문자를 포함해야 합니다." required />
        <label><?= lang('Auth.password') ?></label>
      </p>
      <p>
        <input minlength="5" type="password" name="password_confirm" value="" placeholder=" " required />
        <label><?= lang('Auth.passwordConfirm')?></label>
      </p>
    </fieldset>
    <fieldset>
      <legend><?=lang('Auth.vendors')?></legend>
      <p>
        <input type="text" name="buyerName" minlength="2" value="<?=old('buyerName')?>" placeholder=" " required />
        <label><?=lang("Auth.vendorName")?></label>
      </p>
      <p>
        <input type="text" name="businessNumber" value="<?=old('businessNumber')?>" minlength="2" placeholder=" " />
        <label><?=lang("Auth.businessNumber")?></label>
        <span class='guide-msg color-red'><i></i><?=lang('Auth.disallowedCharacters')?></span>
      </p>
      <p>
        <select name='buyerRegion'>
          <option><?=lang('Auth.countrySelect')?></option>
          <?php foreach ( $countries as $country ) : ?>
          <option value='<?=$country['id']?>'><?=$country['name_en']?></option>
          <?php endforeach ?>
        </select>
        <label>Country/Region</label>
      </p>
      <div>
        <div class='buyer-phone-group'>
          <select name='buyerPhoneCode'>
            <option value>-</option>
            <?php if ( !empty($itus)) : 
              foreach($itus as $itu) : ?>
              <option value='<?=$itu['country_no']?>'><?=$itu['country_no']?></option>
            <?php endforeach; 
            endif;?>
          </select>&nbsp;-&nbsp;
          <input type="text" name="buyerPhone" value="<?=old('buyerPhone')?>" placeholder="1234567890" pattern="[0-9].{5, 10}" require />
        </div>
        <label><?= lang('Auth.phone') ?></label>
        <span class="guide-msg color-red"><i></i><?=lang('Auth.allowed', [])?></span>
      </div>
      <div>
        <div>
          <input type='text' minlength="8" name="buyerAddress1" value="<?=old('buyerAddress1')?>" placeholder=" " require style='margin-bottom: 0.25rem;'>
          <input type='text' name="buyerAddress2" value="<?=old('buyerAddress2')?>" placeholder=" " >
        </div>
        <label><?= lang('Auth.address') ?></label>
      </div>
      <p>
        <input type='text' name='zipcode' value='<?=old('zipcode')?>'>
        <label>Post Code</label>
      </p>
      <p>
        <!-- <input type="file" name="certificateBusiness" accept="image/jpeg,image/png,image/gif,application/pdf" required /> -->
        <input type="file" name="certificateBusiness" accept="image/jpeg,image/png,image/gif" required />
        <label><?= lang('Auth.certificateBusiness').' / '.lang('Auth.businessCard') ?></label>
        <span class="guide-msg color-red"><i></i><?=lang('Auth.allowedFilesGuide')?></span>
      </p>
      <p>
        <input minlength="8" type="text" name="buyerWeb" value="<?=old('buyerWeb')?>" placeholder=" " ></textarea>
        <label><?= lang('Auth.buyerWeb') ?></label>
      </p>
      <div>
        <div class="checkbox-group">
          <?php foreach ( $regions as $region ) : ?>
            <label>
              <input type="checkbox" value="<?=$region['id']?>" name="region[]">
              <span><?=trim($region['region_en'])?></span>
            </label>
          <?php endforeach ?>
        </div>
        <label><?=lang('Auth.region')?></label>
      </div>
      <div>
        <div class='checkbox-group countries'></div>
        <label><?= lang('Auth.country') ?></label>
      </div>
    </fieldset>
    <div class='grid-footer'>
      <p>
        <button name="registerButton" type="submit"><?= lang('Auth.register') ?></button>
      </p>
      <p>
        <a href="<?= site_url('login'); ?>" class="float-right"><?= lang('Auth.alreadyRegistered') ?></a>
      </p>
    </div>
  </form>
</section>
<?= $this->endSection() ?>