<?=$this->extend($config->viewLayout)?>
<?=$this->section('main')?>

<section class='register_section'>
  <?php if ( session()->has('validation') ) var_dump(session('validation')->getErrors()) ?>
  <h1><?=lang('Auth.registration')?></h1>
  <?=view('Auth\Views\_notifications')?>
  <form method="POST" action="<?=route_to('register'); ?>" accept-charset="UTF-8" onSubmit="return registerCheck(this);" enctype="multipart/form-data" id="registerForm">
  <!-- <form method="POST" action="<?=route_to('register'); ?>" accept-charset="UTF-8" enctype="multipart/form-data" id="registerForm"> -->
    <?=csrf_field()?>
    <div>
      <p>
        <input type="text" name="buyerName" value="<?=set_value('buyerName')?>" placeholder="Beautynetkorea Co.,"/>
        <label class='required'><?=lang("Auth.vendorName")?></label>
        <?php if ( session()->has('validation') ) { ?>
        <span class='guide-msg color-red'>
          <i></i>
          <?php
            echo "1";
            if ( session('validation')->hasError('buyerName') ) {
              echo "2";
              echo session('validation')->getError('buyerName');
            }
          ?>
        </span>
        <?php } ?>
      </p>
      <p>
        <input type="text" name="businessNumber" value="<?=set_value('businessNumber')?>" placeholder="123-45-67890"/>
        <label><?=lang("Auth.businessNumber")?></label>
        <span class='guide-msg color-red'>
          <i></i>
          <?php
            if ( session()->has('validation') ) {
              if ( session('validation')->hasError('businessNumber') ) {
                echo session('validation')->getError('businessNumber');
              }
            } else {
              echo lang('Auth.disallowedCharacters');
            }
          ?>
        </span>
      </p>
      <p>
        <select name='buyerRegion'>
          <option value><?=lang('Auth.countrySelect')?></option>
          <?php foreach ( $countries as $country ) : ?>
          <option value='<?=$country['id']?>' data-country-no='<?=$country['country_no']?>'><?=$country['name_en']?></option>
          <?php endforeach ?>
        </select>
        <label class='required'>Country/Region</label>
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
          <input type="text" name="buyerPhone" value="<?=set_value('buyerPhone')?>" placeholder="1234567890"/>
        </div>
        <label class='required'><?= lang('Auth.phone') ?></label>
        <span class="guide-msg color-red"><i></i><?=lang('Auth.allowedOnlyNumber')?></span>
      </div>
      <div>
        <div>
          <input type='text' name="buyerAddress1" value="<?=set_value('buyerAddress1')?>" placeholder="" style='margin-bottom: 0.25rem;'>
          <input type='text' name="buyerAddress2" value="<?=set_value('buyerAddress2')?>" placeholder="This field is optional." >
        </div>
        <label class='required'><?=lang('Auth.address')?></label>
      </div>
      <p>
        <input type='text' name='zipcode' value='<?=set_value('zipcode')?>'>
        <label>Post Code</label>
      </p>
      <p>
        <input type="file" name="certificateBusiness" />
        <label><?=lang('Auth.certificateBusiness').' / '.lang('Auth.businessCard') ?></label>
        <span class="guide-msg color-red"><i></i><?=lang('Auth.allowedFilesGuide')?></span>
      </p>
      <p>
        <input type="text" name="buyerWeb" value="<?=set_value('buyerWeb')?>" placeholder="https://koreacosmeticmall.com" ></textarea>
        <label><?=lang('Auth.buyerWeb')?></label>
      </p>
    </div>
    <div>
      <p id="requiredmsg"><?=lang('Auth.requiredmsg')?></p>
      <p>
        <input minlength="2" type="text" name="name" value="<?=set_value('name')?>" />
        <label class='required'><?=lang('Auth.contactName') ?></label>
      </p>
      <div>
        <div>
          <input type='hidden' name='verified' value='<?=set_value('verified')?>' data-checked>
          <div class='email-rang'>
            <input type="email" name="email" value="<?=set_value('email')?>" />
            <div class='btn email-verify-check'>Verify</div>
          </div>
          <span class='guide-msg color-red'></span>
        </div>
        <label class='required'><?=lang('Auth.email') ?></label>
      </div>
      <p>
        <input type="password" name="password" />
        <span class='eye eye-slash'></span>
        <label class='required'><?=lang('Auth.password')?></label>
        <span class='guide-msg color-red'><i></i><?=lang('Auth.pwmsg')?></span>
      </p>
      <p>
        <input minlength="5" type="password" name="password_confirm" />
        <label class='required'><?=lang('Auth.passwordConfirm')?></label>
        <span class='guide-msg color-red'></span>
      </p>
      <div>
        <div class="checkbox-group">
          <?php foreach ( $regions as $region ) : ?>
            <label>
              <input type="checkbox" value="<?=$region['id']?>" name="region[]"
                <?php if(!empty(set_value('region'))) :
                        foreach(set_value('region') as $checked) {
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