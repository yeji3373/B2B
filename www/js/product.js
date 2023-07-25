$(document).ready(function() {
  // $('.thumbnail').hover(function() {
  //   if( $(this).parent().find('.thumbnail-zoom').filter(':not(:animated)') ) {
  //     $(this).parent().find('.thumbnail-zoom').removeClass('d-none');
  //   }
  // }, function() {
  //   // setTimeout(() => {
  //   //   console.log("나옴");
  //   // }, 1000);
  //   $(this).parent().find('.thumbnail-zoom').addClass('d-none');
  // });
}).on('keydown', function(e) {
  // console.log(e.keyCode);
  if ( e.keyCode === 13 ) e.preventDefault();
}).on('keyup', '.brand-keyword-search', function(e) {
  let result, search, $this, searchList, resultList = "";
  $this = $(this);
  search = $this.val();
  searchList = $(".brand-keyword-search-result");
  
  if ( search.length > 0 ) {
    searchList.addClass('show');
    result = getData('/api/getLikeBrandName', {'brand_name': search});

    if ( result != '' ) {
      $.each(result, (idx, key) => {
        resultList = 
          resultList +
          `<li class='dropdown-item test' data-name='${key['brand_name']}' data-id='${key['brand_id']}'>
            ${(key['brand_name']).toUpperCase()}
          </li>`
      });
    } else {
      resultList = `<li class='dropdown-item'>No Data</li>`;
    }
    appendData(searchList, resultList, true);
  } else {
    searchList.removeClass('show');
    searchList.empty();
    searchList.append("<li class='dropdown-header'>Search for brands</li>");
  }
}).on('click', '.brand-keyword-search-result .dropdown-item', function() {
  // const $brand = $('form [name="brand_name"]');
  let sorted = $(this).data('name');
  // let brand_id = $('form [name="brand_id"]').val() != "" ? $('form [name="brand_id"]').val().split(",") : Array();
  // let run = false;

  location.replace('/order?'+$.urlParam({'brand_name': sorted}));

  // if ( $(".brand-list-group li").first().hasClass('active') ) {
  //   $(".brand-list-group li").first().removeClass("active");
  // }

  // // if ( $brand.val() == "" ) {
  //   $brand.val(sorted);
  //   productResult = getData('/order/productList', dataInit());
  //   appendData($(".product-search-result"), productResult, true);
    
  //   // $brand.val('');
  //   $(".brand-keyword-search").val(sorted.toUpperCase());
  //   $(".brand-keyword-search-result").empty().append("<li class='dropdown-header'>Search for brands</li>");
  // // }

  // if ( brand_id.indexOf(String($(this).data('id'))) < 0 ) {
    // brand_id.push($(this).data('id'));

    // $(".brand-list-group").find("." + sorted).addClass('active');
    // console.log("brand id ", brand_id);
    // console.log($.urlParam({'brand_id': $(this).data('id')}));
    // $('form [name="brand_id"]').val(brand_id);

    // productResult = getData('/order/productList', dataInit());
    // appendData($(".product-search-result"), productResult, true);
  // }
  // $(".brand-keyword-search").val("");
  // $(".brand-keyword-search-result").empty().append("<li class='dropdown-header'>Search for brands</li>");
}).on('click', '.brand-list-group .brand-item', function() {
  let sorted = $(this).data('name');

  if ( sorted == '' ) {
    location.replace('/order?' + $.removeUrlParam('brand_name'));
  } else location.replace('/order?'+$.urlParam({'brand_name': sorted}));
  // let brand_id = $(this).data('id');
  // let form_brand_id = $('form [name="brand_id"]').val();
  // let run = false;
  
  // if ( $(this).index() > 0 ) {
  //   $(".brand-item.active").removeClass('active');
  //   $(this).addClass("active");
  // } else {
  //   $(".brand-item").removeClass("active");
  //   $(".brand-item").first().addClass("active");
  //   form_brand_id = "";
  // }
  
  // if ( form_brand_id != brand_id ) {
  //   form_brand_id = brand_id;
  //   run = true;
  // } else run = false;

  // $('form [name="brand_id"]').val(form_brand_id);
  // if ( run ) {
  //   productResult = getData('/order/productList', dataInit());
  //   appendData($(".product-search-result"), productResult, true);
  // }
}).on('keyup', '#productSearch', function(e) {
  let opts = $(".productSearchOpts option:selected").val();
  let search = $(this).val();
  let selOpts = [];
  $.each($(".productSearchOpts option"), (i, v) => {
    if ( $(v).val() != '' ) {
      if ( $(".productSearchOpts option:selected").val() == $(v).val() ) {
        selOpts.push({[$(v).val()]: search});
      } else selOpts.push({[$(v).val()]: ''});
    }
  });
  // console.log(selOpts);
  console.log("keycode ", e.keyCode);
  console.log("opts ", opts);
//   // $.urlParam(selOpts);
  if ( e.keyCode == 13 ) {
    if ( opts == '' ) {
      $(".productSearchOpts").addClass("bg-danger text-white");
      return;
    }
    if ( search == '') return;
    
    location.replace('/order?' + $.urlParam(selOpts));
  }
//   if ( e.keyCode == 32 || search.length > 2 ) { 
//     setTimeout(() => {
//       location.replace('/order?' + $.urlParam(selOpts));
//     }, 3000);
//   }
//   // dataInit();
//   // let $this = $(this);
//   // let opts = $(".productSearchOpts option:selected").val();
//   // let search = $this.val();
//   // let run = false;

//   // if ( search.length > 1 ) {
//   //   if ( opts == '' ) {
//   //     $(".productSearchOpts").addClass("bg-danger text-white");
//   //     return;
//   //   } else {
//   //     $(".productSearchOpts").removeClass("bg-danger text-white");
//   //     if ( e.keyCode != 32 ) run = true;
//   //   }
//   // } else if ( search.length == 1 ) {
//   //   if ( e.keyCode == 32 ) $this.val($.trim(search)); return;
//   // } else if ( search.length == 0 ) run = true;

//   // if ( run ) {
//   //   $('form .key[name="'+ opts + '"]').val(search);
//   //   result= getData('/order/productList', dataInit());
//   //   appendData($(".product-search-result"), result, true);
//   // }
}).on('click', '.search-btn', function() {
  let opts = $(".productSearchOpts option:selected").val();
  let search = $("#productSearch").val();
  let selOpts = [];
  $.each($(".productSearchOpts option"), (i, v) => {
    if ( $(v).val() != '' ) {
      if ( $(".productSearchOpts option:selected").val() == $(v).val() ) {
        selOpts.push({[$(v).val()]: search});
      } else selOpts.push({[$(v).val()]: ''});
    }
  });
  
  if ( opts == '' ) return;
  if ( search == '' ) return;

  location.replace('/order?' + $.urlParam(selOpts));
}).on('change', '.productSearchOpts', function() {
  // let run = false;
  // $('form .key[name!="' + $(this).val() + '"]').val("");
  // $('form .key[name="' + $(this).val() + '"]').val($("#productSearch").val());

  if( $(this).hasClass('bg-danger') ) {
    $(this).removeClass('bg-danger text-white');
  }

  if ( $(this).val() != '' ) {
    if ( $('#productSearch').val() != '' ) {
      // location.replace('/order?' + $.urlParam({[$(this).val()]: $('#productSearch').val()}));
      let selOpts = [];
      $.each($(".productSearchOpts option"), (i, v) => {
        if ( $(v).val() != '' ) {
          if ( $(".productSearchOpts option:selected").val() == $(v).val() ) {
            selOpts.push({[$(v).val()]: $('#productSearch').val()});
          } else selOpts.push({[$(v).val()]: ''});
        }
      });
      location.replace('/order?' + $.urlParam(selOpts));
    }
  } else {
    let selOpts = [];
    $.each($(".productSearchOpts option"), (i, v) => {
      if ( $(v).val() != '' ) selOpts.push($(v).val());
    });
    location.replace('/order?' + $.removeUrlParam(selOpts));
  }

  // if ( $(this).val() != "" ) {
  //   if ( $("#productSearch").val() != "" ) {
  //     run = true;
  //   }
  // }

  // if ( run ) {
  //   result = getData('/order/productList', dataInit());
  //   appendData($(".product-search-result"), result, true);
  // }
// }).on('click', '#sampleCheck', function() {
//   if ( $("#sampleCheck").is(":checked") ) {
//     $("form [name='sample']").val(1);
//   } else $("form [name='sample']").val("");

//   result = getData('/order/productList', dataInit());
//   appendData($(".product-search-result"), result, true);
}).on("click", '.order-req, .stock-order-req, .stock-req', function(e) {
  e.preventDefault();
  // let data = $(this).parent().find("form").serializeArray();
  let data = $(this).closest('.list-group-item').find('form').serializeArray();

  result = getData("/order/addCartList", data, true);
  if ( result['Code'] != "200" ) {
    // alert(result['Msg']);
    console.log(result['Msg']);
    return;
  } else {
    getCartList();
    setSubTotalPrice();
    // let btnText = $(".product-search-result .bsk-del-btn:first").text() != '' ? $(".product-search-result .bsk-del-btn:first").text() : '선택 취소';
    let btnText = $(this).data('btn');
    $(this).removeClass('order-req');
    $(this).addClass('bsk-del-btn');
    $(this).text(btnText);
    $(this).before("<input type='hidden' class='cart_idx' value='" + result['Msg'] + "'>");
  }
}).on("click", '.increase-btn, .decrease-btn', function(e) {
  e.preventDefault();
  let $parents = $(this).closest('.cart-qty-form');
  let $parent = $(this).parent();
  let cartId = $parents.children('[name=cart_idx]').val();
  let opVal = $parents.children("[name=op-val]").val();
  let opCode = $parents.children('[name=op-code]').val();
  let prdPrice = parseFloat($parents.children('[name=prd_price]').val());
  let calcSpq = $parent.children('.qty-spq').val();
  let tempPrdTotPrice = parseFloat($parents.find('[name=prd-total-price]').val());
  let maxSpq = $parents.children('[name=qty-maximum-val]').val();
  let standSpq = $parents.children('[name=order_qty]').val();

  calcCode = $(this).data('calc');

  if ( opCode == '' || opCode == 0 ) {
    if ( standSpq == '' ) standSpq = 10;
  }

  if ( calcCode == '-' ) {
    // if ( opCode == 1 ) calcCode = '/';
    if ( calcSpq == standSpq ) {
      alert("This is the minimum quantity");
      return;
    }

    if ( $parents.find('.stock-req-cancel').length > 0 ) {
      let stockCancel = confirm('Inventory is requested. Are you sure you want to cancel your reconsideration request?');
      if ( stockCancel ) {
        $parents.find('.stock-req-cancel').click();
      } else {
        console.log("수량을 빼기?");
      }
      return;
    }
  } else if ( calcCode == '+' ) {
    // if ( opCode == 1 ) calcCode = '*';
    if ( calcSpq == maxSpq ) {
      if ( $parents.find('.stock-req-cancel').length > 0 ) {
        alert("The stock request has already been completed.");
        return;
      } else {
        let stockReq = confirm("This is the maximum quantity. Would you like to proceed with the stock request?");
        if ( stockReq ) {
          $parents.find('.stock-req').click();
        }
      }
      return;
    }
  } else return;
  // calcSpq = eval(calcSpq + calcCode + opVal); 
  calcSpq = eval(calcSpq + calcCode + standSpq);
  $parent.find('.qty-spq').val(calcSpq);

  compare = compareMinMax(calcSpq, maxSpq, standSpq);
  if ( compare['code'] != 200 ) { 
    $parent.find('.qty-spq').val(compare['data']);
    calcSpq = $parent.find('.qty-spq').val();
  }    
  
  tempPrdTotPrice = parseFloat(calcSpq * prdPrice);

  result = setCartSpq(cartId, calcSpq, tempPrdTotPrice, true);

  if ( result['Code'] == 200 ) { 
    getCartList();
    setSubTotalPrice();
    return;
  } else {
    return;
  }
}).on("click", '.bsk-del-btn, .stock-req-cancel', function(e) {
  e.preventDefault();
  let query = [];
  $parents = $(this).parent();
  cartId = $parents.find('[name=cart_idx]').val();
  if ( typeof(cartId) == 'undefined' ) {
    cartId = $parents.find('.cart_idx').val();
  }

  // case 0: 부모, 자식 삭제, 1: 자식 삭제 및 부모의 stock id 값 초기화
  if ( $(this).hasClass('stock-req-cancel') ) {
    query = [
      {name: 'cart_idx', value: $(this).data('childId') },
      {name: 'oper', value: 'del'},
      {name: 'case', value: 1},
      {name: 'stock_req_parent', value: cartId}
    ];
  } else {
    if ( $parents.find('.stock-req-cancel').length > 0 ){
      let stockReqMultiCancel = confirm('Inventory requested. Are you sure you want to delete the inventory requested order as well?');

      if (stockReqMultiCancel) {
        query = [
          {name: 'cart_idx', value: cartId },
          {name: 'oper', value: 'del'},
          {name: 'case', value: 0 },
          {name: 'stock_req_parent', value: $parents.find('.stock-req-cancel').data('childId') }
        ];
      } else {
        console.log("그냥 안지워지게 했음");
        return;
      //   query = [
      //     {name: 'cart_idx', value: cartId },
      //     {name: 'oper', value: 'del'}
      //   ];
      }
    } else {
      if ( $parents.find('.parentId').length > 0 &&  $parents.find('.parentId').val() != '') {
        query = [
          {name: 'cart_idx', value: cartId },
          {name: 'oper', value: 'del'},
          {name: 'case', value: 1 },
          {name: 'stock_req_parent', value: $parents.find('.parentId').val()}
        ];    
      } else {
        query = [
          {name: 'cart_idx', value: cartId },
          {name: 'oper', value: 'del'}
        ];
      }
    }      
  }
  
  if ( query.length > 0 ) {
    result = getData('/order/editCartList', query, true);
    if ( result['Code'] == 200 ) {
      getCartList();
      setSubTotalPrice();
      
      productResult = getData('/order/productList', dataInit());
      appendData($(".product-search-result"), productResult, true);
    } else return;
  }
}).on('keyup', '.qty-spq', function(e) {
  $parent = $(this).closest('.cart-qty-form');
  calcSpq = $(this).val();
  standSpq = $parent.find('[name=order_qty]').val();
  maxSpq = $parent.find('[name=qty-maximum-val]').val();
  operateVal = $parent.find('[name=op-val]').val();
  cartId = $parent.find('[name=cart_idx]').val();
  productPrice = parseFloat($parent.find('[name=prd_price]').val());

  if ( maxSpq == '' ) { maxSpq = 1000; }
  if ( e.keyCode === 13 ) {
    if ( parseInt(calcSpq) > maxSpq || parseInt(calcSpq) < standSpq ) {
      compare = compareMinMax(calcSpq, maxSpq, standSpq);
      if ( compare['code'] != 200 ) {
        alert("Changed to a(an) " + compare['msg'] + "\n" + compare['data']);
        $parent.find('.qty-spq').val(compare['data']);
        calcSpq = compare['data'];
      }
    }
  
    result = setCartSpq(cartId, calcSpq, parseFloat(productPrice * calcSpq), true);
    
    if ( result['Code'] == 200 ) { 
      getCartList();
      setSubTotalPrice();
      return;
    } else {
      return;
    }
  }
}).on('click', '.qty-change-btn', function() {
  $parent = $(this).closest('.cart-qty-form');
  standSpq = $parent.find('[name=order_qty]').val();
  maxSpq = $parent.find('[name=qty-maximum-val]').val();
  operateVal = $parent.find('[name=op-val]').val();
  cartId = $parent.find('[name=cart_idx]').val();
  productPrice = parseFloat($parent.find('[name=prd_price]').val());
  calcSpq = $parent.find('.qty-spq').val();

  if ( maxSpq == '' ) { maxSpq = 1000; }
  if ( parseInt(calcSpq) > maxSpq || parseInt(calcSpq) < standSpq ) {
    compare = compareMinMax(calcSpq, maxSpq, standSpq);
    if ( compare['code'] != 200 ) {
      alert("Changed to a(an) " + compare['msg'] + "\n" + compare['data']);
      calcSpq = compare['data'];
    }
  }

  result = setCartSpq(cartId, calcSpq, parseFloat(productPrice * calcSpq), true);

  if ( result['Code'] == 200 ) { 
    getCartList();
    setSubTotalPrice();
    return;
  } else return;

// }).on('click', '.pre-order-btn', function() {
//   result = getData('/order/orderForm', [{name: 'margin_level', value: 1}]);

//   if ( result.indexOf('error') >= 0 ) {
//     // result = JSON.parse(result);
//     if ( $.inArray('error', result) ) {
//       alert(result['error']);
//       return;
//     }
//   }

//   appendData($('.pre-order'), result, true);
//   $(".pre-order").addClass('show');
//   $("body").css('overflow', 'hidden');
//   $(".prev-addr-sel:first").click();
}).on('click', '.inventory_check_request-btn', function() {
  let target = $(this).data('bsTarget');
  result = getData('/inventory/requestInventoryCheck');

  if ( result.indexOf('error') >= 0 ) {
    result = JSON.parse(result);
    if ( $.inArray('error', result) ) {
      alert(result['error']);
      return;
    }
  }

  appendData($('.pre-order'), result, true);
  $(target).attr('data-bs-confirm', $(this).attr('aria-confirm')).addClass('show');
  $(".prev-addr-sel:first").click();
  $("body").css('overflow', 'hidden');

}).on('click', '.pre-order', function(e) {
  let confirmMsg = "Do you want to cancel the payment?";
  // console.log( $(e.target).attr('class'), ' ' ,$(this).attr('class'));
  if ( $(e.target).attr('class') == $(this).attr('class') ) {
    if ( $(e.target).data('bsConfirm') != '' ) confirmMsg = $(e.target).data('bsConfirm');

    let cancelPaypal = confirm(confirmMsg);
    if ( cancelPaypal ) {
      $(".pre-order").removeClass('show');
      $("body").css('overflow', 'auto');
    }
    else return;
  }
}).on("click", '.region-list .dropdown-item', function() {
  $(".regionSelected").val($(this).text())
  $("[name=region_id]").val($(this).val());
  $("[name=country_code]").val($(this).data('ccode'));

  $("[name=phone_code]").val($(this).data('cno'));
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
}).on('click', '.checkout-btn', function(e) {
  console.log("안내창 띄우기?");
  /* submit check */
  // validRequiredCheck($("form"));
  if ( $('[name=address_id]').val() == '' ) {
    if ( $('.prev-addr-sel').length > 0 ) {
      if ( $('.prev-addr-sel.selected').length == 0 ) {
      if ( $('#address-prev-body').hasClass('show') ) {
        // $("#address-new-head .accordion-button").click();
        alert('Please select or enter an address');
        return false;
        // e.preventDefault();
      }
      }
    }
  }
  // $(".checkout-btn").prop('disabled', true);
}).on('click', '.accordion-button', function() {
  console.log($(".accordion-button").index($(this)));
//   console.log($(this).parent().siblings('.accordion-collapse').attr('class'));
//   if ( $(".accordion-button").index($(this)) == 0 ) {
//     if ( $(".prev-addr-sel").length ) {
//       $('.prev-addr-sel:first').click();
//     }
//   }
}).on('click', '.prev-addr-sel', function() {
  addressFormInit();
  $('.prev-addr-sel').removeClass('selected');
  $(".pre-order form").find('input[name=address_id]').val($(this).data('id'));
  $(this).addClass('selected');
}).on("click", '.prev-addr-edit', function() {
  $(".new-addr [name=address_id]").val($('.prev-addr-sel.selected').data('id'));
  $('.new-addr [name=consignee]').val($('.prev-addr-sel.selected .consignee').text());
  $('.new-addr [name=region]').val($('.prev-addr-sel.selected .region').text());
  $('.new-addr [name=region_id]').val($('.prev-addr-sel.selected .region').data('id'));
  $('.new-addr [name=country_code]').val($('.prev-addr-sel.selected .region').data('ccode'));
  $('.new-addr [name=streetAddr1]').val($('.prev-addr-sel.selected .streetAddr1').text());
  $('.new-addr [name=streetAddr2]').val($('.prev-addr-sel.selected .streetAddr2').text());
  $('.new-addr [name=city]').val($('.prev-addr-sel.selected .city').text());
  $('.new-addr [name=zipcode]').val($('.prev-addr-sel.selected .zipcode').text());
  $('.new-addr [name=phone_code]').val($('.prev-addr-sel.selected .phone_code').text());
  $('.new-addr [name=phone]').val($('.prev-addr-sel.selected .phone').text());

  $("#address-new-head .accordion-button").click();
}).on('click', '.prev-addr-del', function() {
  let idx = $('.prev-addr-del').index(this);
  result = getData('/address/addressOperate', 
                  [ {name: 'idx', value: $(this).data('id')},
                    {name: 'oper', value: 'del'} ], 
                  true);
                  
  alert(result['Msg']);

  if ( result['code'] == 200 ) {
    $('.prev-addr-sel').eq(idx).remove();
    if ( $('[name=address_id]').val() == $(this).data('id') ) $('[name=address_id]').val('');
    if ( $('.prev-addr-sel').length == 0 ) {
      $("#address-new-head .accordion-button").click();
      $(".prev-addr").addClass('d-none');
    }
  }    
}).on('click', '#address-new-head .accordion-button', function() {
  if ( $(this).attr('aria-expanded') == 'true' ) {
    console.log("열림");
  } else {
    console.log("닫힘");
    $("#address-prev-head .accordion-button").click();
  }
}).on('change', '[name=checkout-currency]', function() {
  console.log("changed");
  let currency = $(this);
  let data =  [ { name: 'exchange', value: currency.data('exchange') },
                { name: 'rId', value: currency.data('rid') }];
  let totalPrice, discountPrice, subTotalPrice, applyDiscount;

  result = getData('/order/checkoutTotalPrice', data, true);
  console.log('result ', result);
  if ( result.code != 500 ) {
    totalPrice = result['order_price_total'];
    discountPrice = result['order_discount_total'];
    subTotalPrice = result['order_subTotal'];
    applyDiscount = result['applyDiscount'];

    $('[name=currency_code]').val(currency.data('code'));
    $('[name=payment_id]').prop('checked', false);

    if ( currency.data('exchange') == 1 ) {        
      $('.currency-kr-tax-choice').show();
      $(".cart-total-price").addClass(currency.data('code'));  
      $('#payment-paypal').prop('checked', false).attr('disabled', true);
      $('#payment-bank1').prop('checked', false).attr('disabled', true);
      $('#payment-bank2').prop('checked', false).attr('disabled', false);
    } else {
      $('.currency-kr-tax-choice').hide();
      $(".cart-total-price").removeClass('KRW');        
      $("[name=taxation]:first").click();
      $("#payment-bank1").prop('checked', false).attr('disabled', false);
      $("#payment-bank2").prop('checked', false).attr('disabled', true);
      $('#payment-paypal').prop('checked', false).attr('disabled', false);
    }   

    // if ( applyDiscount != 1 ) discountPrice = 0; subTotalPrice = totalPrice;

    $('.order-total-price').text($.numberWithCommas(totalPrice));
    $('.order-discount-price').text($.numberWithCommas(discountPrice));
    $('.order-subtotal-price').text($.numberWithCommas(subTotalPrice));
    $('[name=order-total-price]').val(totalPrice);
    $('[name=order-discount-price]').val(discountPrice);
    $('[name=order-subtotal-price]').val(subTotalPrice);
  }
}).on('change', '[name=taxation]', function() {
  let data =  [ { name: 'exchange', value: $('[name=checkout-currency]:checked').data('exchange') },
                { name: 'rId', value: $('[name=checkout-currency]:checked').data('rid') }];
  // // let zeroTax = []; // 영과세 구분 없이 다 주문 가능하게 처리해달라고 요청옴
  // if ( $(this).val() == 2 ) { // 1: 영세 2:과세
  //   // zeroTax = [{ name: 'onlyZeroTax', value: $(this).val() }];
  //   $('#payment-paypal').prop('checked', false).attr('disabled', true);
  //   $('#payment-bank1').prop('checked', false).attr('disabled', true);
  //   // $('#payment-bank2').prop('checked', true).attr('disabled', false);
  //   $('#payment-bank2').attr('disabled', false);
  //   // alert("영세로만 판매되는 제품은 제외됨\n국내전용 계좌로만 입금 가능");
  // } else {
  //   // zeroTax = [];
  //   $('#payment-bank2').prop('checked', false).attr('disabled', true);
  //   $('#payment-bank1').removeAttr('disabled', false);
  // }

  // result = getData('/order/checkoutTotalPrice', $.merge(data, zeroTax), true);
  result = getData('/order/checkoutTotalPrice', data, true);
  let totalPrice = result['order_price_total'];
  let discountPrice = result['order_discount_total'];
  let subTotalPrice = result['order_subTotal'];
  let applyDiscount = result['applyDiscount'];
  let tax = 1.1;

  if ( applyDiscount != 1 ) discountPrice = 0; subTotalPrice = totalPrice;
  
  //  0: 영과세, 1:영세 2:과세
  // if ( $(this).val() == 0 ) {
  if ( $(this).val() == 2 ) {
    $('.only-zero-tax').addClass('bg-secondary bg-opacity-25');
    $('.only-zero-tax .product-name').append('<span class="no-order-msg color-red fw-bold ms-1">주문불가</span>');

    // $('.order-total-price').text($.numberWithCommas(Math.ceil(totalPrice * tax)));
    $('.order-discount-price').text($.numberWithCommas(Math.ceil(discountPrice * tax)));
    $('.order-subtotal-price').text($.numberWithCommas(Math.ceil(subTotalPrice * tax)));
    $('[name=order-total-price]').val(Math.ceil(totalPrice * tax));
    $('[name=order-discount-price]').val(Math.ceil(discountPrice * tax));
    $('[name=order-subtotal-price]').val(Math.ceil(subTotalPrice * tax));
  } else {
    $('.only-zero-tax').removeClass('bg-secondary bg-opacity-25');
    $('.no-order-msg').remove();

    $('.order-total-price').text($.numberWithCommas(Math.ceil(totalPrice)));
    $('.order-discount-price').text($.numberWithCommas(Math.ceil(discountPrice)));
    $('.order-subtotal-price').text($.numberWithCommas(Math.ceil(subTotalPrice)));
    $('[name=order-total-price]').val(totalPrice);
    $('[name=order-discount-price]').val(discountPrice);
    $('[name=order-subtotal-price]').val(subTotalPrice);
  }
// }).click(function(e) {
//   $this = $(e.target);
//   // console.log("e ", $this.closest('.stock_modal'));
//   if ( !$this.hasClass('stock-req') && 
//       !$this.closest('.stock_modal').is('.stock_modal') &&
//       !$this.closest('.stock-request-show').is('.stock-request-show')) {
//     e.preventDefault();
//     hideStockModal();
//   }
}).on('change', '[name=payment_id]', function() {
  if ( $(this).val() == 1 ) {
    if ( $("[name=country_code]").val() == 'KR' ) {
      // 초기화???
      alert("When you pay by PayPal, you cannot set the delivery address to Korea.");
      return;
    }
  }
}).on('mouseenter', '.thumbnail-group', function() {
  $(this).children('.thumbnail-zoom').removeClass('d-none');
}).on('mouseleave', '.thumbnail-group', function() {
  $(this).children('.thumbnail-zoom').addClass('d-none');
});

$(window).ready(function() {
  $(".prev-addr-sel:first").click();
});

function dataInit() {
  let formData = null;
  formData = $("form:first").serializeArray();
  return formData;
}

function getCartList() {
  cartList = getData('/order/cartList');
  appendData($(".product-selected"), cartList, true);
}

function setSubTotalPrice() {
  totalPriceResult = getData('/order/cartTotalPrice');
  totalPriceResult = JSON.parse(totalPriceResult);

  $('.total-price').text(totalPriceResult['order_price_total']);
  $('.discount-price').text(totalPriceResult['order_discount_total']);
  $(".sub-total-price").text(totalPriceResult['order_subTotal']);
}

function setCartSpq(cartId, productQty, productPrice, ref = false) {
  let editData = [{name: 'cart_idx', value: cartId},
                  {name: 'order_qty', value: productQty},
                  {name: 'order_price', value: productPrice}];
  // console.log($.param(editData));
  result = JSON.parse(getData('/order/editCartList', editData));
  // result = JSON.parse(result);
  
  
  if ( !ref ) { // return 안받고 곧바로 처리
    if ( result['Code'] == 200 ) { 
      setSubTotalPrice();
    }
  } else return result;
}

// function compareMinMax(curr, max, min, opt, $this, ret = false) {
function compareMinMax(curr, max, min) {
  let data = curr; msg = '', code = 200;
  if ( parseInt(curr) > parseInt(max)) { // 유효 재고량
    code = 500;
    data = max;
    msg = "The maximum order quantity is " + max;
  }

  if ( parseInt(curr) < parseInt(min)) {
    code = 500;
    data = min;
    msg = "The Minimum order Quantity is " + min;
  }
    
  return {code: code, data: data, msg: msg};
}

function validCheckSpq(curr, opt, min, max) {
  let data = curr, msg = '', code = 200;
  // if ( (curr % opt) > 0 ) {
  //   temp = curr - (curr % opt);
  //   code = 500;
  //   msg = opt + "단위로 주문 가능함";
  //   data = temp;
  //   if ( min > temp ) data = min;
  //   if ( max < temp ) data = max;
  // }
  return {code: code, data: data, msg: msg};
}

// function validRequiredCheck($target, submit = false, action = null) {
function validRequiredCheck($target) {
  if ( $target == null ) return;  
  let _return = false; 
  $target.find('input').each(function() {
    if ( $(this).prop('required') == true ) {
      if ( $(this).val() == '' ) {
        $(this).addClass('text-danger');
        return;
      // } else {
      //   if ( $(this).hasClass('text-danger') ) $(this).removeClass('text-danger');
      }
    }
  });

  // if ( submit ) {
  //   if ( action != null ) {
  //     $target.attr('action', action).submit();
  //   } else return;
  // }
}

function addressFormInit() {
  // $('.prev-addr-sel.selected').removeClass('selected');
  $(".address-new-form input").val('');
  $(".address-new-form select option:selected").prop('selected', false);
}

function hideStockModal() {
  // console.log("hide stock modal");
  let parent;
  if ( $('.stock_modal').closest('.list-group-item').length > 0 ) {
    parent = $('.stock_modal').closest('.list-group-item');
    if (parent.hasClass('stock-request-show')) {
      parent.removeClass('stock-request-show');
    }
  }
  
  // if ( $('.stock_modal').is(':visible') ) {
  //   $('.stock_modal').hide().empty();
  // }
}