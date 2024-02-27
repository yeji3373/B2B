<?=$this->extend($config->viewLayout)?>
<?=$this->section('main')?>

<section class='register_section'>
  <h1><?=lang('Auth.registration')?></h1>
  <?=view('Auth\Views\_notifications')?>
  <form method="POST" action="<?=route_to('register'); ?>" accept-charset="UTF-8" onSubmit="return registerCheck(this);" enctype="multipart/form-data" id="registerForm">
    <?=csrf_field()?>
    <div>
      <p>
        <input type="text" name="buyerName" minlength="2" value="<?=old('buyerName')?>" placeholder="Beautynetkorea Co.," required />
        <label class='required'><?=lang("Auth.vendorName")?></label>
      </p>
      <p>
        <input type="text" name="businessNumber" value="<?=old('businessNumber')?>" placeholder="123-45-67890" pattern='^([A-Za-z\d])[A-Za-z\d].{2,}' />
        <label><?=lang("Auth.businessNumber")?></label>
        <span class='guide-msg color-red'><i></i><?=lang('Auth.disallowedCharacters')?></span>
      </p>
      <p>
        <select name='buyerRegion' required>
          <option><?=lang('Auth.countrySelect')?></option>
          <?php foreach ( $countries as $country ) : ?>
          <option value='<?=$country['id']?>' data-country-no='<?=$country['country_no']?>'><?=$country['name_en']?></option>
          <?php endforeach ?>
        </select>
        <label class='required'>Country/Region</label>
      </p>
      <div>
        <div class='buyer-phone-group'>
          <select name='buyerPhoneCode' required>
            <option value>-</option>
            <?php if ( !empty($itus)) : 
              foreach($itus as $itu) : ?>
              <option value='<?=$itu['country_no']?>'><?=$itu['country_no']?></option>
            <?php endforeach; 
            endif;?>
          </select>&nbsp;-&nbsp;
          <input type="text" name="buyerPhone" value="<?=old('buyerPhone')?>" placeholder="1234567890" pattern="[0-9].{5,10}" required />
        </div>
        <label class='required'><?= lang('Auth.phone') ?></label>
        <span class="guide-msg color-red"><i></i><?=lang('Auth.allowedOnlyNumber')?></span>
      </div>
      <div>
        <div>
          <input type='text' minlength="8" name="buyerAddress1" value="<?=old('buyerAddress1')?>" placeholder=" " required style='margin-bottom: 0.25rem;'>
          <input type='text' name="buyerAddress2" value="<?=old('buyerAddress2')?>" placeholder=" " >
        </div>
        <label class='required'><?=lang('Auth.address')?></label>
      </div>
      <p>
        <input type='text' name='zipcode' value='<?=old('zipcode')?>'>
        <label>Post Code</label>
      </p>
      <p>
        <input type="file" name="certificateBusiness" accept="image/jpeg,image/png,image/gif,application/pdf" />
        <label><?=lang('Auth.certificateBusiness').' / '.lang('Auth.businessCard') ?></label>
        <span class="guide-msg color-red"><i></i><?=lang('Auth.allowedFilesGuide')?></span>
      </p>
      <p>
        <input minlength="8" type="text" name="buyerWeb" value="<?=old('buyerWeb')?>" placeholder=" " ></textarea>
        <label><?=lang('Auth.buyerWeb')?></label>
      </p>
    </div>
    <div>
      <p id="requiredmsg"><?=lang('Auth.requiredmsg')?></p>
      <p>
        <input minlength="2" type="text" name="name" value="<?=old('name')?>" placeholder=" " required />
        <label class='required'><?=lang('Auth.contactName') ?></label>
      </p>
      <p>
        <input type="email" name="email" value="<?=old('email')?>" placeholder=" " required />
        <label class='required'><?=lang('Auth.email') ?></label>
      </p>
      <p>
        <input type="password" name="password" pattern="^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{5,}$"
              title="<?=lang('Auth.pwmsg')?>" required />
        <span class='eye eye-slash'></span>
        <label class='required'><?=lang('Auth.password')?></label>
        <span class='guide-msg color-red'><i></i><?=lang('Auth.pwmsg')?></span>
      </p>
      <p>
        <input minlength="5" type="password" name="password_confirm" required />
        <label class='required'><?=lang('Auth.passwordConfirm')?></label>
      </p>
      <div>
        <div class="checkbox-group">
          <?php foreach ( $regions as $region ) : ?>
            <label>
              <input type="checkbox" value="<?=$region['id']?>" name="region[]"
                <?php if(!empty(old('region'))) :
                        foreach(old('region') as $checked) {
                          if($checked == $region['id']){ echo " checked"; }
                        };
                      endif;
                ?>
              >
              <span><?=trim($region['region_en'])?></span>
            </label>
          <?php endforeach ?>
        </div>
        <label><?=lang('Auth.region')?></label>
      </div>
      <div>
        <div class='checkbox-group countries'></div>
        <?php if(!empty(old('country'))) { 
                foreach(old('country') as $checked2) {
                  echo "<input type='hidden' name='checkedCountries' value='{$checked2}'>";
                };
              }
        ?>
        <label><?=lang('Auth.country')?></label>
      </div>
    </div>
    <div class='grid-footer'>
      <p>
        <button name="registerButton" type="submit"><?=lang('Auth.register')?></button>
      </p>
      <p>
        <a href="<?=site_url('login')?>" class="float-right"><?=lang('Auth.alreadyRegistered')?></a>
      </p>
    </div>
  </form>
</section>
<?=$this->endSection()?>
