$(document).ready(function (){
  if ( !$('.pre-order').length ) {
  }
}).on("click", '.region-list .dropdown-item', function() {
  $(".regionSelected").val($(this).text())
  $("[name='address[region_id]']").val($(this).val());
  $("[name='address[country_code]']").val($(this).data('ccode'));

  $("[name='address[phone_code]']").val($(this).data('cno'));
}).on('keyup', '.regionSelected', function() {
  $this = $(this);
  $regionList = $('.region-list > li');

  if ( $this.val().length > 1 ) {
    if ( !$this.hasClass('show') ) {
      $this.addClass('show');
      if ( !$(".region-list").hasClass('show') ) $('.region-list').addClass('show');
    }
  }

  $searchedRegion = $regionList.filter(function(i) {
    return $(this).text().toUpperCase().indexOf($this.val().toUpperCase()) > -1;
  });
  $regionList.hide();
  $searchedRegion.show();
}).on('click', '.prev-addr-sel', function() {
  // addressFormInit();
  $('.prev-addr-sel').removeClass('selected');
  $(".pre-order form").find('input[name="address[idx]"]').val($(this).data('id'));
  $(".pre-order form").find('input[name="address[country_code]"]').val($(this).find('.region').data('ccode'));
  $(this).addClass('selected');
}).on("click", '.prev-addr-edit', function() {
  $selectedAddress = $(this).closest('.registed-address').find('.prev-addr-sel');
  console.log($selectedAddress);
  $('.new-addr [name="address[idx]"]').val($selectedAddress.data('id'));
  $('.new-addr [name="address[consignee]"]').val($selectedAddress.find('.consignee').text());
  $('.new-addr [name="address[region]"]').val($selectedAddress.find('.region').text());
  $('.new-addr [name="address[region_id]"]').val($selectedAddress.find('.region').data('id'));
  $('.new-addr [name="address[country_code]"]').val($selectedAddress.find('.region').data('ccode'));
  $('.new-addr [name="address[streetAddr1]"]').val($selectedAddress.find('.streetAddr1').text());
  $('.new-addr [name="address[streetAddr2]"]').val($selectedAddress.find('.streetAddr2').text());
  $('.new-addr [name="address[city]"]').val($selectedAddress.find('.city').text());
  $('.new-addr [name="address[zipcode]"]').val($selectedAddress.find('.zipcode').text());
  $('.new-addr [name="address[phone_code]"]').val($selectedAddress.find('.phone_code').text());
  $('.new-addr [name="address[phone]"]').val($selectedAddress.find('.phone').text());

  $("#address-accordion input[name='address[address_operate]']").val(1);
  $("#address-accordion .accordion-item.new-addr").addClass('edit-address');
  $("#address-new-head .accordion-button").click();
}).on('click', '.prev-addr-del', function() {
  result = getData('/address/edit', 
                  [ {name: 'idx', value: $(this).data('id')},
                    {name: 'oper', value: 'del'} ], 
                  'POST',
                  true);
                  
  alert(result['Msg']);

  if ( result['code'] == 200 ) {
    // $('.prev-addr-sel').eq(idx).remove();
    $(this).closest('.registed-address').remove();
    // if ( $('[name="address[idx]"]').val() == $(this).data('id') ) $('[name="address[idx]"]').val('');
    if ( $('.prev-addr-sel').length == 0 ) {
      if ( $('[name="address[idx]"]').val() != '' ) $('[name="address[idx]"]').val('');
      $("#address-new-head .accordion-button").click();
      $(".prev-addr").addClass('d-none');
    } else {
      if ( !$('.prev-addr-sel:eq(0)').hasClass('selected') ) $('.prev-addr-sel:eq(0)').click();
    }
  }
}).on('click', '#address-accordion .accordion-button', function() {
  if ( $(this).closest('.accordion-item').hasClass('prev-addr') ) {
    if ( $('#address-accordion').find('.prev-addr-sel').length ) {
      $("#address-accordion input[name='address[address_operate]']").val(0);
      $(".accordion-item.new-addr.edit-address").removeClass('edit-address');
      
      addressFormInit();
      
      if ( !$('.prev-addr-sel.selected').length ) {
        $('.prev-addr-sel:eq(0)').click();
        // $(".pre-order form").find('input[name="address[country_code]"]').val($('prev-addr-sel:eq(0)').find('.region').data('ccode'));
      }      
    }
  } else {
    $('.prev-addr-sel.selected').removeClass('selected');
    if ( !$(".accordion-item.new-addr").hasClass("edit-address") ) {
      $('.new-addr input[name="address[idx]"]').val('');
      $(".new-addr input[name='address[address_operate]']").val(1);
    }
    
    Array.from($("#address-accordion input, #address-accordion select")).forEach(v => {
      if ( typeof $(v).attr('aria-required') != undefined 
          && $(v).attr('aria-required') == 'true' ) {
        $(v).prop('required', true);
      }
    });
  }
});

function addressFormInit() {
  // $('.prev-addr-sel.selected').removeClass('selected');
  $(".address-new-form input").val('');
  $(".address-new-form select option:selected").prop('selected', false);
  $("input, select").prop('required', false);
}
