$(document).ready(function() {
  let DISCOUNT = 1000;

  $(document).on("click", ".pagination a.page-link", function(e) {
    e.preventDefault();
    $("form [name='page']").val(parseInt($(this).data('page')));
    // console.log("page ", $("form [name='page']").val());
    productResult = getData('/order/productList', dataInit());
    appendData($(".product-search-result"), productResult, true);
  });

  $(".brand-keyword-search").on("keyup", function() {
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
              ${key['brand_name']}
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
  });
  
  $(document).on('click', '.brand-keyword-search-result .dropdown-item', function() {
    let sorted = $(this).data('name');
    let brand_id = $('form [name="brand_id"]').val() != "" ? $('form [name="brand_id"]').val().split(",") : Array();
    // let run = false;

    if ( $(".brand-list-group li").first().hasClass('active') ) {
      $(".brand-list-group li").first().removeClass("active");
    }

    if ( brand_id.indexOf(String($(this).data('id'))) < 0 ) {
      brand_id.push($(this).data('id'));

      $(".brand-list-group").find("." + sorted).addClass('active');
      $('form [name="brand_id"]').val(brand_id);
  
      productResult = getData('/order/productList', dataInit());
      appendData($(".product-search-result"), productResult, true);
    }
    $(".brand-keyword-search").val("");
    $(".brand-keyword-search-result").empty().append("<li class='dropdown-header'>Search for brands</li>");
  });

  $(".brand-list-group .brand-item").on("click", function() {
    let brand_id = $(this).data('id');
    let form_brand_id = $('form [name="brand_id"]').val();
    let run = false;
    
    if ( $(this).index() > 0 ) {
      $(".brand-item.active").removeClass('active');
      $(this).addClass("active");
      if ( form_brand_id.indexOf(brand_id) < 0 ) {
        form_brand_id = brand_id;
        run = true;
      }
    } else {
      if ( form_brand_id != brand_id ) {
        run = true;
        $(".brand-item").removeClass("active");
        $(".brand-item").first().addClass("active");
        form_brand_id = "";
      } else run = false;
    }

    $('form [name="brand_id"]').val(form_brand_id);
    if ( run ) {
      console.log(dataInit());
      productResult = getData('/order/productList', dataInit());
      console.log(productResult);
      appendData($(".product-search-result"), productResult, true);
    }
  });

  $("#productSearch").on('keyup', function() {
    dataInit();
    let $this = $(this);
    let opts = $(".productSearchOpts option:selected").val();
    let search = $this.val();
    let run = false;

    if ( search.length > 1 ) {
      if ( opts == '' ) {
        $(".productSearchOpts").addClass("bg-danger text-white");
        return;
      } else {
        $(".productSearchOpts").removeClass("bg-danger text-white");
        run = true;
      }
    }

    if ( search.length == 0 ) run = true;

    if ( run ) {
      $('form [name="'+ opts + '"]').val(search);
      result= getData('/order/productList', dataInit());
      appendData($(".product-search-result"), result, true);
    }
  });

  $(".productSearchOpts").on("change", function() {
    let run = false;
    $('form .key[name!="' + $(this).val() + '"]').val("");
    $('form .key[name="' + $(this).val() + '"]').val($("#productSearch").val());

    if( $(this).hasClass('bg-danger') ) {
      $(this).removeClass('bg-danger text-white');
    }

    if ( $(this).val() != "" ) {
      if ( $("#productSearch").val() != "" ) {
        run = true;
      }
    }

    if ( run ) {
      result = getData('/order/productList', dataInit());
      appendData($(".product-search-result"), result, true);
    }
  });

  $("#sampleCheck").on("click", function() {
    if ( $("#sampleCheck").is(":checked") ) {
      $("form [name='sample']").val(1);
    } else $("form [name='sample']").val("");

    result = getData('/order/productList', dataInit());
    appendData($(".product-search-result"), result, true);
  });

  $(document).on("click", ".order-req", function() {
    let data = $(this).parent().find("form").serializeArray();
    console.log("data ", data);

    result = getData("/order/addCartList", data);
    _result = JSON.parse(result);
    if ( _result['Code'] != "200" ) {
      alert(_result['Msg']);
    } else {
      getCartList();
      setSubTotalPrice();

      let btnText = $(".product-search-result .bsk-del-btn:first").text() != '' ? $(".product-search-result .bsk-del-btn:first").text() : '선택 취소';
      $(this).removeClass('order-req');
      $(this).addClass('bsk-del-btn');
      $(this).text(btnText);
      $(this).before("<input type='hidden' class='cart_idx' value='" + _result['Msg'] + "'>");
    }
  });

  // $(document).on("click", ".qty-group button", function() {
  $(document).on("click", ".increase-btn, .decrease-btn", function() {
    $parents = $(this).parent().parent();
    $parent = $(this).parent();
    cartId = $parents.children('.cart_idx').val();
    opVal = $parents.children(".op-val").val();
    prdPrice = parseFloat($parents.children('.prd-price-val').val());
    calcSpq = $parent.children('.qty-spq').val();
    // tempPrdTotPrice = parseFloat($parents.find('.prd-item-total-price').text());
    tempPrdTotPrice = parseFloat($parents.find('prd-total-price').val());
    maxSpq = $parents.children('.qty-maximum-val').val();
    standSpq = $parents.children('.qty-stand-val').val();

    if ( $(this).data('calc') == '-' ) {
      if ( calcSpq == standSpq ) {
        alert("최소 수량입니다.");
        return;
      }
    } else if ( $(this).data('calc') == '+' ) {
      if ( calcSpq == maxSpq ) {
        alert("최대 수량입니다.");
        return;
      }
    } else return;
    calcSpq = eval(calcSpq + $(this).data('calc') + opVal);    
    $parent.find('.qty-spq').val(calcSpq);

    compare = compareMinMax(calcSpq, maxSpq, standSpq);
    if ( compare['code'] != 200 ) { 
      $parent.find('.qty-spq').val(compare['data']);
      calcSpq = $parent.find('.qty-spq').val();
    }    
    
    tempPrdTotPrice = parseFloat(calcSpq * prdPrice);
    // console.log("tempPrdTotPrice ", tempPrdTotPrice);
    // console.log("calcSpq ", calcSpq);
    // console.log("prdPrice ", prdPrice);

    result = setCartSpq(cartId, calcSpq, tempPrdTotPrice, true);
    console.log(result);
    if ( result['Code'] == 200 ) { 
      getCartList();
      setSubTotalPrice();
    } else {
      alert(result['Msg']);
      return;
    }
  });

  $(document).on("click", '.bsk-del-btn', function() {
    $parents = $(this).parent();
    cartId = $parents.children('.cart_idx').val();

    result = getData('/order/editCartList', [{name: 'cart_idx', value: cartId}, {name: 'oper', value: 'del'}]);
    result = JSON.parse(result);
    if ( result['Code'] == 200 ) {
      getCartList();
      setSubTotalPrice();

      if ( $(this).data('order-req') != 'undefined' ) {
        let btnText = $(".product-search-result .order-req:first").text() != '' ? $(".product-search-result .order-req:first").text() : 'Choose';
        console.log('undefined');
        $(this).removeClass('bsk-del-btn');
        $(this).addClass('order-req');
        $(this).text(btnText);
        $parents.children('.cart_idx').remove();
      }
    } else { 
      alert(result['Msg']);
      return;
    }
  });

  $(document).on('blur', '.qty-spq', function(e) {
    e.preventDefault();
    $parent = $(this).parent().parent();
    tempSpq = $(this).val();
    standSpq = $parent.find('.qty-stand-val').val();
    maxSpq = $parent.find('.qty-maximum-val').val();
    operateVal = $parent.find('.op-val').val();
    cartId = $parent.find('.cart_idx').val();
    productPrice = $parent.find('.prd-price-val').val();

    validChk = validCheckSpq(tempSpq, operateVal, standSpq, maxSpq);
    if ( validChk['code'] != 200 ) {
      // alert(validChk['msg'] + "\n" + validChk['data'] + " 변경됨");
      alert(validChk['msg'] + "\n" + validChk['data']);
      // The quantity will be changed to 10.
      $(this).val(validChk['data']);
      // setCartSpq(cartId, $(this).val(), (productPrice * $(this).val()));
      return;
    }

    compare = compareMinMax(tempSpq, maxSpq, standSpq);
    if ( compare['code'] != 200 ) {
      alert(compare['msg'] + "\n" + compare['data'] + " 변경됨");
      $(this).val(compare['data']);
      // setCartSpq(cartId, $(this).val(), (productPrice * $(this).val()));
      return;
    }
    setCartSpq(cartId, $(this).val(), (productPrice * $(this).val()));
  }).on('keyup', '.qty-spq', function(e) {
    e.preventDefault();
    $parent = $(this).parent().parent();
    tempSpq = $(this).val();
    standSpq = $parent.find('.qty-stand-val').val();
    maxSpq = $parent.find('.qty-maximum-val').val();
    operateVal = $parent.find('.op-val').val();
    cartId = $parent.find('.cart_idx').val();

    if ( tempSpq.length > maxSpq.length || e.keyCode == 13 ) {    
      compare = compareMinMax(tempSpq, maxSpq, standSpq);
      if ( compare['code'] != 200 ) {
        alert(compare['msg']);
        return;
      }
      setCartSpq(cartId, $(this).val(), (productPrice * $(this).val()));
    }
  });

  $(".pre-order-btn").on("click", function() {
    result = getData('/order/orderForm');

    if ( result.indexOf('error') >= 0 ) {
      result = JSON.parse(result);
      if ( $.inArray('error', result) ) {
        alert(result['error']);
        return;
      }
    }

    appendData($('.pre-order'), result, true);
    $(".pre-order").show();
    $("body").css('overflow', 'hidden');
    $(".prev-addr-sel:first").click();
  });

  $(".pre-order").on("click", function(e) {
    // console.log('target ',e.target);
    // console.log('$target ', $(e.target));
    // console.log('this ', $(this));
    // console.log('A ', $(this) == e.target);
    // console.log('B ',$(this) === $(e.target));
    if ( $(e.target).attr('class') == $(this).attr('class') ) {
      // console.log( $(this).children('#pre-order') );
      let cancelPaypal = confirm("결제 취소하시겠습니까?");
      // if ( $(this).hasClass(e.target.classList.value) )
      if ( cancelPaypal ) {
        $(".pre-order").toggle();
        $("body").css('overflow', 'auto');
      }
      else return;
    }
  });

  $(document).on("click", ".region-list .dropdown-item", function() {
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
          alert('주소를 선택하거나 입력해주세요');
          return false;
        }
       }
      }
    }

    if ( $("[name=payment_id]:checked").length <= 0 ) {
      alert("Payment method 선택해주세요.");
      return false;
    }

    $(".checkout-btn").prop('disabled', true);
  }).on('click', '#address-new-head .accordion-button', function() {
    if ( $('[name=address_id]').val() != '' && $('.prev-addr-sel.selected').length > 0 ) {
      $('[name=address_id]').val('');
      addressFormInit();
    }
  // }).on('click', '.prev-addr-sel', function() {
  //   if ( $('.prev-addr-sel.selected').length > 0 ) {
  //     $('[name=address_id]').val();
  //     console.log("lengakdafd");
  //     addressFormInit();
  //     $('[name=address_id]').val()
  //   }
  //   $(this).addClass('selected');
  //   console.log($(this).data('id'));
  //   $(".new-addr input[name=address_id]").val($(this).data('id'));
  }).on("click", '.prev-addr-edit, .prev-addr-sel', function(e) {
    console.log($(this));
    addressFormInit();
    // console.log("e target ", $(e.target));
    // console.log($(this).attr("class") == $(e.target).attr("class"));
    // console.log("aaaaaaaa ", $('.prev-addr-sel .phone_code').text());
    if ( $(this).hasClass('prev-addr-edit') ) {
      $("#address-new-head .accordion-button").click();
    }

    if ( $(this).hasClass('prev-addr-sel') ) {
      if ( $('.prev-addr-sel.selected').length > 0 ) {
        $('.prev-addr-sel.selected').removeClass('selected');
      }
      $(this).addClass('selected');
    }
    $(".new-addr [name=address_id]").val($(this).data('id'));
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
  }).on('click', '.prev-addr-del', function() {
    result = getData('/order/addressOperate', 
                    [ {name: 'idx', value: $(this).data('id')},
                      {name: 'oper', value: 'del'} ]);
    result = JSON.parse(result);

    alert(result['Msg']);

    if ( result['code'] == 200 ) {
      if ( $('[name=address_id]').val() == $(this).data('id') ) $('[name=address_id]').val('');
      $('.prev-addr-sel').eq($(this).index()).remove();
      if ( $('.prev-addr-sel').length == 0 ) {
        $("#address-new-head .accordion-button").click();
      }
    }

  }).on('click', '#address-new-head .accordion-button', function() {
    if ( $(this).attr('aria-expanded') == 'true' ) {
      console.log("열림");
    } else {
      console.log("닫힘");
      $("#address-prev-head .accordion-button").click();
    }
  }).on('click', '[name=checkout-currency]', function() {
    let data =  [ { name: 'exchange', value: $(this).data('exchange') },
                  { name: 'rId', value: $(this).data('rid') }];
    result = getData('/order/checkoutTotalPrice', data);
    result = JSON.parse(result);
    let totalPrice = result['order_price_total'];
    let discountPrice = result['order_discount_total'];
    let subTotalPrice = result['order_subTotal'];
    let applyDiscount = result['applyDiscount'];

    if ( $(this).data('exchange') == 1 ) {
      $(".cart-total-price").addClass('KRW');
      $('.currency-kr-tax-choice').show();
      
      if ( $('#payment-paypal').prop('checked') ) alert("페이팔 안됨");

      $('#payment-paypal').prop('checked', false).attr('disabled', true);
    } else {
      $(".cart-total-price").removeClass('KRW');
      $('.currency-kr-tax-choice').hide();
      $("[name=taxation]:first").click();
      $("#payment-bank2").prop('checked', false).attr('disabled', true);
      $('#payment-paypal').removeAttr('disabled');
    }
    $('[name=currency_code]').val($(this).data('code'));

    if ( applyDiscount != 1 ) discountPrice = 0; subTotalPrice = totalPrice;

    $('.order-total-price').text($.numberWithCommas(totalPrice));    
    $('.order-discount-price').text($.numberWithCommas(discountPrice));
    $('.order-subtotal-price').text($.numberWithCommas(subTotalPrice));
    $('[name=order-total-price]').val(totalPrice);
    $('[name=order-discount-price]').val(discountPrice);
    $('[name=order-subtotal-price]').val(subTotalPrice);
  }).on('click', '[name=taxation]', function() {
    let data =  [ { name: 'exchange', value: $('[name=checkout-currency]:checked').data('exchange') },
                  { name: 'rId', value: $('[name=checkout-currency]:checked').data('rid') }];
    let zeroTax;
    if ( $(this).val() == 0 ) {
      zeroTax = [{ name: 'onlyZeroTax', value: $(this).val() }];
      $('#payment-paypal').prop('checked', false).attr('disabled', true);
      $('#payment-bank1').prop('checked', false).attr('disabled', true);
      $('#payment-bank2').prop('checked', true).attr('disabled', false);
      // alert("영세로만 판매되는 제품은 제외됨\n국내전용 계좌로만 입금 가능");
      alert("The products that are only available with Zero tax rates are removed.\nThe payment can only be accepted by our Korean bank account.");
    } else {
      zeroTax = [];
      $('#payment-bank1').removeAttr('disabled', false);
    }
    // console.log($.merge(data, zeroTax));
    result = getData('/order/checkoutTotalPrice', $.merge(data, zeroTax));
    // console.log("result ", result);
    result = JSON.parse(result);
    let totalPrice = result['order_price_total'];
    let discountPrice = result['order_discount_total'];
    let subTotalPrice = result['order_subTotal'];
    let applyDiscount = result['applyDiscount'];
    let tax = 1.1;

    if ( applyDiscount != 1 ) discountPrice = 0; subTotalPrice = totalPrice;
    
    if ( $(this).val() == 0 ) {
      $('.only-zero-tax').addClass('bg-secondary bg-opacity-25');
      $('.only-zero-tax .product-name').append('<span class="no-order-msg color-red fw-bold ms-1">주문불가</span>');

      $('.order-total-price').text($.numberWithCommas(Math.ceil(totalPrice * tax)));
      $('.order-discount-price').text($.numberWithCommas(Math.ceil(discountPrice * tax)));
      $('.order-subtotal-price').text($.numberWithCommas(Math.ceil(subTotalPrice * tax)));
      $('[name=order-total-price]').val(Math.ceil(totalPrice * tax));
      $('[name=order-discount-price]').val(Math.ceil(discountPrice * tax));
      $('[name=order-subtotal-price]').val(Math.ceil(subTotalPrice * tax));
    } else {
      $('.only-zero-tax').removeClass('bg-secondary bg-opacity-25');
      $('.no-order-msg').remove();

      $('.order-total-price').text($.numberWithCommas(totalPrice));
      $('.order-discount-price').text($.numberWithCommas(discountPrice));
      $('.order-subtotal-price').text($.numberWithCommas(subTotalPrice));
      $('[name=order-total-price]').val(totalPrice);
      $('[name=order-discount-price]').val(discountPrice);
      $('[name=order-subtotal-price]').val(subTotalPrice);
    }
  });
});

$(window).ready(function() {
  $(".prev-addr-sel:first").click();
});

function dataInit() {
  let formData = null;
  formData = $("form").serializeArray();
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
  result = getData('/order/editCartList', editData);
  result = JSON.parse(result);

  if ( !ref ) {
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
    // msg = "주문 가능한 최대 수량은 " + max + "입니다";
    msg = "주문 가능한 최대 수량은 " + max;
  }

  if ( parseInt(curr) < parseInt(min)) {
    code = 500;
    data = min;
    // msg = "최소 주문 수량은 " + min + "입니다";
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
  $('.prev-addr-sel.selected').removeClass('selected');
  $(".address-new-form input").val('');
  $(".address-new-form select option:selected").prop('selected', false);
}