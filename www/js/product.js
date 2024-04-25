$(document).ready(function() {
  $('.product-search-result .product-list').scroll(function() {
    let scrollPer = Math.round((Math.ceil($(".product-search-result .product-list").scrollTop() + $(".product-search-result .product-list").height()) / $(this).prop('scrollHeight')) * 100);

    if ( scrollPer > 70 ) {
      $('#product-form input[name=request_unit]').val(1);
      $('#product-form input[name=page]').val(parseInt($('#product-form input[name=page]').val()) + 1);

      result = getData('/order/productList'
                    , dataInit("#product-form"));
      
      if ( result != '' ) {
        $(this).append(result);
        
        if ( $(".product-invoice-section .list-group-item").length != $('.product-search-result .cart_idx').length ) {
          Array.from($('.product-search-result .list-group-item')).forEach(element => {
            Array.from($(".product-invoice-section .list-group-item")).forEach(ele => {
              if ($(element).find('[name=prd_id]').val() == $(ele).find('[name=prd_id]').val() ) {
                if ( !$(element).find('form .cart_idx').length ) {
                  let btn = $(element).find('button');
                  let removeClass = btn.attr('data-remove-class');
                  let addClass = btn.attr('data-add-class');
                  let btnText = btn.attr('data-btn');
  
                  btn.attr('data-remove-class', addClass);
                  btn.attr('data-add-class', removeClass);
                  btn.attr('data-btn', $.trim(btn.text()));
                  btn.removeClass(removeClass);
                  btn.addClass(addClass);
                  btn.text($.trim(btnText));

                  $(element).find('form').append($('<input type="hidden" class="cart_idx" value=' + $(ele).find('[name=cart_idx]').val() + '>'));
                }
              }
            });
          });
        }
      }
    }
  });
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
            ${(key['brand_name']).toUpperCase().replace('\\', '')}
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
  $("#product-form input[name=brand_id]").val($(this).data('id'));
  result = getData('/order/productList', dataInit("#product-form"));
  if ( result != '' ) {
    $(".brand-section .brand-item.active").removeClass('active');
    $(".brand-section .brand-keyword-search").val('');
    $(this).parent().empty().append('<li class="dropdown-header">Search for brands</li>');
    $.each($(".brand-section .brand-item"), (i, v) => {
      if ( $(v).data('id') == $(this).data('id') ) $(v).addClass('active');
    });
    $(".brand-keyword-search-result").removeClass('show').attr('style', '');
    appendData($('.product-search-result .product-list'), result, true);
    if ( $('.product-search-result .product-list').scrollTop() > 0 ) {
      $('.product-search-result .product-list').scrollTop(0);
    }
  }
}).on('click', '.brand-list-group .brand-item', function() {
  $('.brand-item.active').removeClass('active');

  $(this).addClass('active');
  $('#product-form input[name=brand_id]').val($(this).data('id'));
  $('#product-form input[name=request_unit]').val(1);
  $('#product-form input[name=page]').val(1);

  result = getData('/order/productList', dataInit("#product-form"));
  if ( result != '' ) {
    appendData($('.product-search-result .product-list'), result, true);
    if ( $('.product-search-result .product-list').scrollTop() > 0 ) {
      $('.product-search-result .product-list').scrollTop(0);
    }
  }
}).on('keyup', '#productSearch', function(e) {
  if ( e.keyCode == 13 ) return;
  if ( $(".productSearchOpts option:selected").val() == '' ) $(".productSearchOpts option[value=name]").attr('selected', true);
  if ( $("#productSearch").val() == '' ) return;

  let opts = $(".productSearchOpts option:selected").val();
  let search = $(this).val();
  
  if ( search.length > 2 ) {
    if ( opts == '' ) opts = 'name';
    formInSearchDateInit();
    if ( $("#product-form input[name=page]").val() > 1 ) $("#product-form input[name=page]").val(1);
    if ( $("#product-form input[name=request_unit]").val() == true ) $("#product-form input[name=request_unit]").val(0);

    if ( $("#product-form input[name=" + opts + "]").length) {
      $("#product-form input[name=" + opts + "]").val(search);
    } else {
      $("#product-form").append("<input type='hidden' name='" + opts + "' value='" + search + "'>");
    }

    result = getData('/order/productList', dataInit("#product-form"));
    if ( result != '' ) {
      appendData($('.product-search-result .product-list'), result, true);
      if ( $('.product-search-result .product-list').scrollTop() > 0 ) {
        $('.product-search-result .product-list').scrollTop(0);
      }
    }
  }
}).on('click', '.search-btn', function() {
  if ( $(".productSearchOpts option:selected").val() == '' ) $(".productSearchOpts option[value=name]").attr('selected', true);
  if ( $("#productSearch").val() == '' ) return;

  let opts = $(".productSearchOpts option:selected").val();
  let search = $("#productSearch").val();

  formInSearchDateInit();
  if ( $("#product-form input[name=page]").val() > 1 ) $("#product-form input[name=page]").val(1);
  if ( $("#product-form input[name=request_unit]").val() == true ) $("#product-form input[name=request_unit]").val(0);

  if ( $("#product-form input[name=" + opts + "]").length) {
    $("#product-form input[name=" + opts + "]").val(search);
  } else {
    $("#product-form").append("<input type='hidden' name='" + opts + "' value='" + search + "'>");
  }
  
  result = getData('/order/productList', dataInit("#product-form"));
  if ( result != '' ) {
    appendData($('.product-search-result .product-list'), result, true);
    if ( $('.product-search-result .product-list').scrollTop() > 0 ) {
      $('.product-search-result .product-list').scrollTop(0);
    }
  }
}).on('change', '.productSearchOpts', function() {
  if ( $(this).val() == '' ) {
    formInSearchDateInit();
    $("#productSearch").val('');
    if ( $("#product-form input[name=page]").val() > 1 ) $("#product-form input[name=page]").val(1);
    if ( $("#product-form input[name=request_unit]").val() == true ) $("#product-form input[name=request_unit]").val(0);

    result = getData('/order/productList', dataInit("#product-form"));
    if ( result != '' ) {
      appendData($('.product-search-result .product-list'), result, true);
      if ( $('.product-search-result .product-list').scrollTop() > 0 ) {
        $('.product-search-result .product-list').scrollTop(0);
      }
    }
  }
// }).on('click', '.product-invoice-section .list-group-item .product-item-info', function() {
//   if ( $(this).parent().hasClass('slideUp') ) {
//     $(this).parent().removeClass('slideUp');
//     $(this).children('.view-more').removeClass('view-more').addClass('hide-more').text('Hide More');
//   } else { 
//     $(this).parent().addClass('slideUp');
//     $(this).children('.hide-more').removeClass('hide-more').addClass('view-more').text('Hide More');

//   }
}).on('click', '.more-btn', function() {
  if ( $(this).hasClass('view-more') ) {
    $(this).closest('.list-group-item').removeClass('slideUp');
    $(this).removeClass('view-more').addClass('hide-more').text('Hide More');
    return;
  }

  if ( $(this).hasClass('hide-more') ) {
    $(this).closest('.list-group-item').addClass('slideUp');
    $(this).removeClass('hide-more').addClass('view-more').text('View More');
    return;
  }
}).on("click", '.order-req', function(e) {
  e.preventDefault();
  let data = $(this).closest('.list-group-item').find('form').serializeArray();

  result = getData("/order/addCartList", data, 'POST', true);
  if ( result['Code'] != "200" ) {
    console.log(result['Msg']);
    return;
  } else {
    if ( result['Msg'] != '' ) {
      $("#product-form").append($("<input type='hidden' name='cart_id'/>").val(result['Msg']));
      
      if ( $(".product-selected .isEmpty").length ) {
        if ( !$(".product-selected .isEmpty").hasClass('d-none') ) {
          $(".product-selected .isEmpty").addClass('d-none');
        }
      }
    }
    let btnText = $(this).data('btn');
    let removeClass = $(this).data('removeClass');
    let addClass = $(this).data('addClass');

    $(this).attr('data-btn', $.trim($(this).text()));
    $(this).attr('data-remove-class', addClass);
    $(this).attr('data-add-class', removeClass);
    $(this).removeClass(removeClass);
    $(this).addClass(addClass);    
    $(this).text(btnText);
    
    if ( $(this).prev('.cart_idx').length ) {
      $(this).prev('.cart_idx').val(result['Msg']);
    } else $(this).before("<input type='hidden' class='cart_idx' value='" + result['Msg'] + "'>");

    getCartList();
    setSubTotalPrice();    
  }
}).on("click", '.increase-btn, .decrease-btn, .qty-change-btn', function(e) {
  e.preventDefault();
  let $parents = $(this).closest('.cart-qty-form');
  let $parent = $(this).parent();
  let cartId = $parents.find('[name=cart_idx]').val();
  let opCode = $parents.find('[name=op_code]').val();
  let prdPrice = parseFloat($parents.children('[name=prd_price]').val());
  let calcSpq = $parent.find('.qty-spq').val();
  let maxSpq = $parents.find('[name=qty-maximum-val]').val();
  let standSpq = $parents.find('[name=order_qty]').val();
  // let spq = $parents.find('[name=spq]').val();

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

    // if ( $parents.find('.stock-req-cancel').length > 0 ) {
    //   let stockCancel = confirm('Inventory is requested. Are you sure you want to cancel your reconsideration request?');
    //   if ( stockCancel ) {
    //     $parents.find('.stock-req-cancel').click();
    //   } else {
    //     console.log("수량을 빼기?");
    //   }
    //   return;
    // }
  } else if ( calcCode == '+' ) {
    // if ( calcSpq == maxSpq ) {
    //   if ( $parents.find('.stock-req-cancel').length > 0 ) {
    //     alert("The stock request has already been completed.");
    //     return;
    //   } else {
      
    //     let stockReq = confirm("This is the maximum quantity. Would you like to proceed with the stock request?");
    //     if ( stockReq ) {
    //       $parents.find('.stock-req').click();
    //     }
    //   }
    //   return;
    // }
  } else {
    if ( calcSpq != '' || calcSpq >= standSpq) {      
    } else return;
  }
  // calcSpq = eval(calcSpq + calcCode + opVal); 
  if ( typeof calcCode != 'undefined' && calcCode != '' ) {
    calcSpq = eval(calcSpq + calcCode + standSpq);
  }
  $parent.find('.qty-spq').val(calcSpq);

  compare = compareMinMax(calcSpq, maxSpq, standSpq);
  if ( compare['code'] != 200 ) { 
    $parent.find('.qty-spq').val(compare['data']);
    calcSpq = $parent.find('.qty-spq').val();
  }    
  
  // tempPrdTotPrice = parseFloat(calcSpq * prdPrice);

  result = setCartSpq(cartId, calcSpq, prdPrice, true);
  if ( result['Code'] == 200 ) { 
    if ( result['Msg'] != '' ) {
      $parents.find('[name=prd_total_price]').val(result['Msg']);
      $parents.find('.prd-item-total-price').text(result['Msg']);
    }
    setSubTotalPrice();
    return;
  } else {
    return;
  }
}).on("click", '.bsk-del-btn', function(e) {
  e.preventDefault();
  console.log("bsk-del-btn");
  let cart_id_element = $(this).parent().find('[name=cart_idx]').length ? 
                        $(this).parent().find('[name=cart_idx]').val() : 
                        $(this).parent().find('.cart_idx').val();
  let checkList = '';
  let cartSection = false;
  let $targetBtn = '';
  let query = [];
  
  if ($(this).closest('.product-section').length) {
    cartSection = false;
    checkList = $(".product-invoice-section .product-selected .list-group-item .cart-qty-form input[name=cart_idx]");
  } else {
    cartSection = true;
    checkList = $(".product-section .product-search-result .product-list .list-group-item form input[class=cart_idx]");
  }

  // if ( checkList.length && !cartSection ) {
  if ( checkList.length ) {
    $.each(checkList, (i, v) => {
      if ( $(v).val() == cart_id_element ) {
        if ( cartSection ) {   
          $targetBtn = $(v).closest('.list-group-item').find('button');
        } else {
          // console.log($(v).closest('.list-group-item'));
          $targetBtn = $(v).closest('.list-group-item');
        }
        return false;
      }
    });
  }

  if ( cart_id_element != '' ){
    query = [
      {name: 'cart_idx', value: cart_id_element },
      {name: 'oper', value: 'del'}
    ];

    let btnText, removeClass, addClass;
    result = getData('/order/editCartList', query, 'POST', true);
    console.log(result);
    console.log($targetBtn);
    if ( result['Code'] == 200 ) {
      if ( cartSection ) {
        if ( $targetBtn != '' ) {
          btnText = $targetBtn.attr('data-btn');
          removeClass = $targetBtn.attr('data-remove-class');
          addClass = $targetBtn.attr('data-add-class');

          $targetBtn.removeClass(removeClass);
          $targetBtn.addClass(addClass);
          $targetBtn.attr('data-remove-class', addClass);
          $targetBtn.attr('data-add-class', removeClass);
          $targetBtn.attr('data-btn', $.trim($targetBtn.text()));
          $targetBtn.text(btnText);
          $targetBtn.prev('.cart_idx').remove();
        }
        $(this).closest('.list-group-item').remove();
      } else {
        if ( $targetBtn.length ) {
          $targetBtn.remove();
        }
        $(this).attr('data-remove-class', 'order-req');
        $(this).attr('data-add-class', 'bsk-del-btn');
        $(this).attr('data-btn', 'Unselect');
        $(this).removeClass('bsk-del-btn');
        $(this).addClass('order-req');
        $(this).text('Select');
      }
    }

    if ( !$('.product-selected .list-group-item').length ) {
      if ( $('.product-selected .isEmpty').length ) {
        if ( $('.product-selected .isEmpty').hasClass('d-none') ) {
          $('.product-selected .isEmpty').removeClass('d-none');
        }
      } else {
        $('.product-selected').append('<div class="isEmpty">Is empty</div>');
      }
    }
    setSubTotalPrice();
  }
}).on('keyup', '.qty-spq', function(e) {
  $parent = $(this).closest('.cart-qty-form');
  calcSpq = $(this).val();
  standSpq = $parent.find('[name=order_qty]').val();
  maxSpq = $parent.find('[name=qty-maximum-val]').val();
  // operateVal = $parent.find('[name=op_val]').val();
  cartId = $parent.find('[name=cart_idx]').val();
  productPrice = parseFloat($parent.find('[name=prd_price]').val());

  if ( maxSpq == '' ) { maxSpq = 1000; }
  if ( e.keyCode === 13 ) {
    if ( calcSpq != '' || calcSpq >= standSpq ) {
      if ( parseInt(calcSpq) > maxSpq || parseInt(calcSpq) < standSpq ) {
        compare = compareMinMax(calcSpq, maxSpq, standSpq);
        if ( compare['code'] != 200 ) {
          alert("Changed to a(an) " + compare['msg'] + "\n" + compare['data']);
          $parent.find('.qty-spq').val(compare['data']);
          calcSpq = compare['data'];
        }
      }
    
      // result = setCartSpq(cartId, calcSpq, parseFloat(productPrice * calcSpq), true);
      result = setCartSpq(cartId, calcSpq, productPrice, true);
      if ( result['Code'] == 200 ) { 
        if ( result['Msg'] != '' ) {
          $(this).closest('.cart-qty-request').find('.prd_total_price').val(result['Msg']);
          $(this).closest('.cart-qty-request').find('.prd-item-total-price').text(result['Msg']);
        }
        // getCartList();
        setSubTotalPrice();
        return;
      } else {
        return;
      }
    } else {
      alert('This value is null');
    }
  }
// }).on('click', '.pre-order-btn', function() {
//   result = getData('/order/orderForm', [{name: 'margin_level', value: 1}]);

//   if ( typeof result.Code != 'undefined' && result.Code == 500 ) {
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
  reqeust = true;
  if ( $('.product-selected .cart-qty-request .qty-spq').length ) {
    Array.from($('.product-selected .cart-qty-request .qty-spq')).forEach((v) => {
      if ( $(v).val().trim() == '' ) {
        $(v).focus();
        alert('This value is null');
        request = false;
        return false;
      }
    });
  }
  if ( reqeust ) {
    let target = $(this).data('bsTarget');
    result = getData('/inventory/request', [], 'GET');
    
    if ( result.indexOf('Code') >= 0 ) {
      if ( typeof JSON.parse(result) == 'object' ) {
        result = JSON.parse(result);
        if ( typeof result.Code != 'undefined' && result.Code == 500 ) {
          if ( $.inArray('error', result) ) {
            alert(result['error']);
            return;
          }
        }
      }
    }
    appendData($('.pre-order'), result, true);
    $(target).attr('data-bs-confirm', $(this).attr('aria-confirm')).addClass('show');
    if ( $('.prev-addr-sel').length ) {
      $(".prev-addr-sel:eq(0)").click();
    } else $('input[name=address_operate]').val(1);

    if ( $('.pre-order form input[name=request-total-price]').length ) {
      $('.pre-order form input[name=request-total-price]').val(parseFloat($('.sub-total-price').text()));
    }
    $("body").css('overflow', 'hidden');
    
    if($("#address-prev-head").length == 0){
      $("#address-accordion input[name='address[address_operate]']").val(1);
    }
  } else return;
}).on('change', '[name=checkout-currency]', function() {
  console.log("changed");
  let currency = $(this);
  let data =  [ { name: 'exchange', value: currency.data('exchange') },
                { name: 'rId', value: currency.data('rid') }];
  let totalPrice, discountPrice, subTotalPrice, applyDiscount;

  result = getData('/order/checkoutTotalPrice', data, 'POST',  true);
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
  result = getData('/order/checkoutTotalPrice', data, 'POST',  true);
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
  if ( typeof $(this).children('.thumbnail-zoom').attr('style') == "undefined" ) {
    $(this).children('.thumbnail-zoom').css('background-image', 'url(' + $(this).find('img').attr('src') + ')');
  }
  $(this).children('.thumbnail-zoom').removeClass('d-none');
}).on('mouseleave', '.thumbnail-group', function() {
  $(this).children('.thumbnail-zoom').addClass('d-none');
});

// $(window).ready(function() {
//   $(".prev-addr-sel:first").click();
// });

function dataInit(target = null) {
  let formData = null;
  if ( target == null ) formData = $("form:first").serializeArray();
  else formData = $('form' + target).serializeArray();
  return formData;
}

function getCartList() {
  cartList = getData('/order/cartList', dataInit('#product-form'));
  if ( cartList != '' ) {
    if ( $("#product-form").find('[name=cart_id]').length ) {
      $("#product-form [name=cart_id]").remove();
    }
  }
  appendData($(".product-selected"), cartList, false);
}

function setSubTotalPrice() {
  totalPriceResult = getData('/order/cartTotalPrice');
  totalPriceResult = JSON.parse(totalPriceResult);
  // console.log(totalPriceResult);
  $('.total-price').text($.numberWithCommas(totalPriceResult['order_price_total']));
  $(".sub-total-price").text($.numberWithCommas(totalPriceResult['order_subTotal']));
}

function setCartSpq(cartId, productQty, productPrice, ref = false) {
  let editData = [{name: 'cart_idx', value: cartId},
                  {name: 'order_qty', value: productQty},
                  {name: 'product_price', value: productPrice}];
  // console.log($.param(editData));
  result = getData('/order/editCartList', editData, 'POST', true);
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

function formInSearchDateInit() {
  $.each($(".productSearchOpts option"), (i, v) => {
    if ( $(v).val() != '' ) {
      if ( $("#product-form input[name=" + $(v).val() + "]").length) {
        $("#product-form input[name=" + $(v).val() + "]").val('');
      }
    }
  });
}