<?=$this->extend($config->viewLayout)?>
<?=$this->section('main')?>

<section class='register_section'>
  <h1><?=lang('Auth.registration')?></h1>
  <?=view('Auth\Views\_notifications')?>
  <form method="POST" action="<?=route_to('register'); ?>" accept-charset="UTF-8" onSubmit="return registerCheck(this);" enctype="multipart/form-data" id="registerForm">
  <!-- <form method="POST" action="<?=route_to('register'); ?>" accept-charset="UTF-8" enctype="multipart/form-data" id="registerForm"> -->
    <?=csrf_field()?>
    <div>
      <p>
        <input type="text" name="buyerName" value="<?=set_value('buyerName')?>" placeholder="Beautynetkorea Co.," />
        <label class='required'><?=lang("Auth.vendorName")?></label>
        <?=view('Auth\Views\_validations', ['col' => 'buyerName', 'default' => null])?>
      </p>
      <p>
        <input type="text" name="businessNumber" value="<?=set_value('businessNumber')?>" placeholder="123-45-67890"/>
        <label><?=lang("Auth.businessNumber")?></label>
        <?=view('Auth\Views\_validations', ['col' => 'businessNumber', 'default' => null]);?>
      </p>
      <p>
        <select name='buyerRegion'>
          <option value><?=lang('Auth.countrySelect')?></option>
          <?php foreach ( $countries as $country ) : ?>
          <option value='<?=$country['id']?>' 
              data-country-no='<?=$country['country_no']?>'
              <?=set_value('buyerRegion') != $country['id'] ? : 'selected'?>><?=$country['name_en']?></option>
          <?php endforeach ?>
        </select>
        <label class='required'>Country/Region</label>
        <?=view('Auth\Views\_validations', ['col' => 'buyerRegion', 'default' => null]);?>
      </p>
      <div>
        <div class='buyer-phone-group'>
          <select name='buyerPhoneCode'>
            <option value>-</option>
            <?php if ( !empty($itus)) : 
              foreach($itus as $itu) : ?>
              <option value='<?=$itu['country_no']?>'
                <?=set_value('buyerPhoneCode') != $itu['country_no'] ? : 'selected'?>><?=$itu['country_no']?></option>
            <?php endforeach; 
            endif;?>
          </select>&nbsp;-&nbsp;
          <input type="text" name="buyerPhone" value="<?=set_value('buyerPhone')?>" placeholder="1234567890"/>
        </div>
        <label class='required'><?= lang('Auth.phone') ?></label>
        <?=view('Auth\Views\_validations', ['col' => 'buyerPhone', 'default' => lang('Auth.allowedOnlyNumber')])?>
      </div>
      <div>
        <div>
          <input type='text' name="buyerAddress1" value="<?=set_value('buyerAddress1')?>" placeholder="" style='margin-bottom: 0.25rem;'>
          <input type='text' name="buyerAddress2" value="<?=set_value('buyerAddress2')?>" placeholder="This field is optional." >
        </div>
        <label class='required'><?=lang('Auth.address')?></label>
        <?=view('Auth\Views\_validations', ['col' => 'buyerAddress1', 'default' => null]);?>
      </div>
      <p>
        <input type='text' name='zipcode' value='<?=set_value('zipcode')?>'>
        <label>Post Code</label>
        <?=view('Auth\Views\_validations', ['col' => 'zipcode', 'default' => null]);?>
      </p>
      <p>
        <input type="file" name="certificateBusiness" />
        <label><?=lang('Auth.certificateBusiness').' / '.lang('Auth.businessCard') ?></label>
        <?=view('Auth\Views\_validations', ['col' => 'businessCard', 'default' => lang('Auth.allowedFilesGuide')]);?>
      </p>
      <p>
        <input type="text" name="buyerWeb" value="<?=set_value('buyerWeb')?>" placeholder="https://koreacosmeticmall.com" ></textarea>
        <label><?=lang('Auth.buyerWeb')?></label>
        <?=view('Auth\Views\_validations', ['col' => 'buyerWeb', 'default' => null]);?>
      </p>
    </div>
    <div>
      <p id="requiredmsg"><?=lang('Auth.requiredmsg')?></p>
      <p>
        <input minlength="2" type="text" name="name" value="<?=set_value('name')?>" />
        <label class='required'><?=lang('Auth.contactName') ?></label>
        <?=view('Auth\Views\_validations', ['col' => 'name', 'default' => null]);?>
      </p>
      <div>
        <input type="email" name="email" value="<?=set_value('email')?>" />
        <label class='required'><?=lang('Auth.email') ?></label>
        <?=view('Auth\Views\_validations', ['col' => 'email', 'default' => null]);?>
      </div>
      <p>
        <input type="password" name="password" />
        <span class='eye eye-slash'></span>
        <label class='required'><?=lang('Auth.password')?></label>
        <?=view('Auth\Views\_validations', ['col' => 'password', 'default' => lang('Auth.pwmsg')]);?>
      </p>
      <p>
        <input minlength="5" type="password" name="password_confirm" />
        <label class='required'><?=lang('Auth.passwordConfirm')?></label>
        <?=view('Auth\Views\_validations', ['col' => 'password_confirm', 'default' => null]);?>
      </p>
      <div>
        <div class="checkbox-group">
          <?php foreach ( $regions as $region ) : ?>
            <label>
              <input type="checkbox" value="<?=$region['id']?>" name="region[]"
                <?php if(!empty(set_value('region'))) :
                        foreach(set_value('region') as $checked) {
                          if($checked == $region['id']){ echo " checked"; }
                        }
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
        <?php if(!empty(set_value('country'))) { 
                foreach(set_value('country') as $checked2) {
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