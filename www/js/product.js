$(document).ready(function() {
  // $form = formSubmitTest({}, {'action': '/apiLoggedIn/cartStatsuInsert'});
  // $form.submit();
  // return;
  getProductList();
  getCartList();
  getTotalPrice();
  $('.product-search-result .product-list').bind('scroll', productListPage);
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
    // console.log(result);
    if ( result.Code == 200 ) {
      $.each(result.data, (idx, key) => {
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
  past_id = $(".brand-list-group .brand-item.active").data('id');
  current_id = $(this).data('id');

  if ( past_id != current_id ) {
    $(".brand-list-group .brand-item.active").removeClass('active');
    
    $.each($(".brand-list-group .brand-item"), (i, v) => {
      if ( $(v).data('id') == current_id ) $(v).addClass('active');
    });

    $("#product-form input[name=brand_id]").val(current_id);
    $(".brand-keyword-search-result").removeClass('show');
    result = getData('/apiLoggedIn/products', dataInit("#product-form"));
    // console.log(result);
    if ( result.Code == 200 ) {
      list = getData('/order/productList', {'products' : result.data});
      $(".product-search-result .product-list").html(JSON.parse(list.data));
      $('.product-search-result .product-list').scrollTop(0);      
    }
  }
}).on('click', '.brand-list-group .brand-item', function() {
  $('.brand-item.active').removeClass('active');
  $(this).addClass('active');

  $('#product-form input[name=brand_id]').val($(this).data('id'));
  // $('#product-form input[name=request_unit]').val(1);
  $('#product-form input[name=offset]').val(1);

  result = getData('/apiLoggedIn/products', dataInit("#product-form", true));
  if ( result.Code == 200 ) {
    list = getData('/order/productList', {'products' : result.data});
    $(".product-search-result .product-list").html("").scrollTop(0).append(JSON.parse(list.data));
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
    $(".product-search-result .product-list").scrollTop(0);

    if ( $("#product-form input[name=" + opts + "]").length) {
      $("#product-form input[name=" + opts + "]").val(search);
    } else {
      $("#product-form").append("<input type='hidden' name='" + opts + "' value='" + search + "'>");
    }

    result = getData('/apiLoggedIn/products', dataInit("#product-form", true));
    if ( result.Code == 200 ) {
      if ( result.data.length > 0 ) {
        list = getData('/order/productList', {'products': result.data});
        $(".product-search-result .product-list").html(JSON.parse(list.data));
      } else {
        $('.product-search-result .product-list').html('not found');
      }
    }    
  }
}).on('click', '.search-btn', function() {
  if ( $(".productSearchOpts option:selected").val() == '' ) $(".productSearchOpts option[value=name]").attr('selected', true);
  if ( $("#productSearch").val() == '' ) return;

  let opts = $(".productSearchOpts option:selected").val();
  let search = $("#productSearch").val();

  $(".product-search-result .product-list").scrollTop(0);
  
  if ( $("#product-form input[name=" + opts + "]").length) {
    $("#product-form input[name=" + opts + "]").val(search);
  } else {
    $("#product-form").append("<input type='hidden' name='" + opts + "' value='" + search + "'>");
  }

  result = getData('/apiLoggedIn/products', dataInit("#product-form", true));
  console.log(result);
  if ( result.Code == 200 ) {
    if ( result.data.length > 0 ) {
      list = getData('/order/productList', {'products': result.data});
      appendData($('.product-search-result .product-list'), list.data, true);
    } else {
      $('.product-search-result .product-list').html('not found');
    }
  }
}).on('change', '.productSearchOpts', function() {
  if ( $(this).val() == '' ) {
    formInSearchDateInit();
    $("#productSearch").val('');
    if ( $("#product-form input[name=offset]").val() > 1 ) $("#product-form input[name=offset]").val(1);
    if ( $("#product-form input[name=request_unit]").val() == true ) $("#product-form input[name=request_unit]").val(0);

    result = getData('/order/productList', dataInit("#product-form", true));
    if ( result.Code == 200 ) {
      appendData($('.product-search-result .product-list'), result.data, true);
      if ( $('.product-search-result .product-list').scrollTop() > 0 ) {
        $('.product-search-result .product-list').scrollTop(0);
      }
    }
  }
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
  $this = $(this);
  let prd_id = $(this).data('prdId');
  let data = {'prd_id': prd_id};

  result = getData('/apiLoggedIn/cartItem', data);
  // console.log(result);
  if ( result.Code == 200 ) {
    if ( result.data.cartList == '' || result.data.cartList == null) return;
    if ( result.data.cartList.dataType == 'insert' ) {
      cartResult = getData('/cart/addCartList', data);
      if ( cartResult.Code == 200 ) {
        getCartList();
        getTotalPrice();
      }
    } else {
      alert('This product is already in your shopping cart');
      return;
    }
  }
}).on("click", '.qty-change-btn', function(e) {
  $form = $(this).closest('form[class=cart-qty-form]');
  $form.find("input[name=dataType]").val('update');
  $form.find('.qty-group input[type=text]').attr('name', 'order_qty');
  if ( $form.find('input[name=operator]').length ) {
    $form.find('input[name=operator]').val(2);
  } else $form.append($('<input/>').attr({'type': 'hidden', 'name': 'operator', 'value': 2}));
  // $form.attr('action', '/cart/editCartList');
  // $form.submit();
  let data = $form.serializeArray();
  let qtyResult = getData('/cart/editCartList', data);
  if ( qtyResult.Code == 200 ) {
    getCartList();
    getTotalPrice();
  } else {
    alert(qtyResult.data);
    return;
  }
}).on("click", '.increase-btn, .decrease-btn', function(e) {
  calc_oper = $(this).data('calc');
  $form = $(this).closest('form[class=cart-qty-form]');
  $form.find("input[name=dataType]").val('update');
  if ( $form.find('input[name=operator]').length ) {
    $form.find('input[name=operator]').val(calc_oper);
  } else $form.append($('<input/>').attr({'type': 'hidden', 'name': 'operator', 'value': calc_oper}));

  // $form.attr('action', '/cart/editCartList');
  // $form.submit();
  let data = $form.serializeArray();
  let qtyResult = getData('/cart/editCartList', data);
  if ( qtyResult.Code == 200 ) {
    getCartList();
    getTotalPrice();
  } else {
    alert(qtyResult.data);
    return;
  }
}).on("click", '.bsk-del-btn', function(e) {
  if ( $(this).closest('form[class=cart-qty-form]').find("input[name=dataType]").val() != 'delete') {
    $(this).closest('form[class=cart-qty-form]').find("input[name=dataType]").val('delete');
  }
  let data = $(this).closest('form[class=cart-qty-form]').serializeArray();
  let deleteResult = getData('/cart/editCartList', data);
  if ( deleteResult.Code == 200 ) {
    getCartList();
    getTotalPrice();
  }
}).on('keydown', '.qty-spq', function(e) {
  if ( e.keyCode == 13 ) {
    $form = $(this).closest('form[class=cart-qty-form]');
    $form.find("input[name=dataType]").val('update');
    $form.find('.qty-group input[type=text]').attr('name', 'order_qty');
    if ( $form.find('input[name=operator]').length ) {
      $form.find('input[name=operator]').val(2);
    } else $form.append($('<input/>').attr({'type': 'hidden', 'name': 'operator', 'value': 2}));

    let data = $form.serializeArray();
    let qtyResult = getData('/cart/editCartList', data);
    if ( qtyResult.Code == 200 ) {
      getCartList();
      getTotalPrice();
    } else {
      alert(qtyResult.data);
      return;
    }
  }
}).on('click', '.inventory_check_request-btn', function() {
  let orderMinCheckResult = getData('/cart/checkMinimumAmount');  
  if ( orderMinCheckResult.Code == 400 ) {
    console.log(orderMinCheckResult);
    if ( orderMinCheckResult.data != '' ) {
      alert(orderMinCheckResult.data);
    } else {
      alert("Select products over $1,000");
    }
    return;
  }

  let target = $(this).data('bsTarget');
  console.log(target);
  result = getData('/inventory/request', [], 'GET');
  // console.log(result);
  if ( result.Code == 200 ) {
    let resultData = JSON.parse(result.data);
    $(target).empty();
    $(target).append(resultData.view);
    $(target).attr('data-bs-confirm', $(this).attr('aria-confirm')).addClass('show');
    $("body").css('overflow', 'hidden');

    if ( $(target).find('.prev-addr-sel').length ) {
      $(target).find('.prev-addr-sel').eq(0).click();
    } else {
      $(target).find('input[name="address[address_operate]"]').val(1);
    }   
  }
}).on('click', '.prev-addr-sel' , function() {
  if ( $("input[name='address[idx]']").length ) {
    $("input[name='address[idx]']").val($(this).data('id'));
  }
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

function dataInit(target = null, refresh = false) {
  let formData = null;

  if ( target == null ) target = $("form:first");
  else target = $('form' + target);

  if ( refresh ) {
    if ( target.find('input[name=offset]').length ) target.find('input[name=offset]').val(1);
  }
  
  formData = target.serializeArray();
  console.log(formData);
  return formData;
}

function getCartList() {
  if ( $('.cart-in-product').length ) {
    carts = getData('/apiLoggedIn/cartList', {'returnType': 'html'});
    if ( carts.Code == 200 ) {
      let list = JSON.parse(carts.data.html);
      $('.cart-in-product').html(list);
    }  
  }
}

function getProductList() {
  if ( $(".product-search-result .product-list").length ) {
    products = getData('/apiLoggedIn/products', {'returnType': 'html'});
    if ( products.Code == 200 ) {
      let list = JSON.parse(products.data.html);
      $(".product-search-result .product-list").html(list);
    }
  }
}

function getTotalPrice() { 
  let totalPrice = 0;
  getTotalPriceResult = getData('/cart/cartSumPrice');
  if ( getTotalPriceResult.Code == 200 ) {
    totalPrice = JSON.parse(getTotalPriceResult.data).totalPrice;
      $('.product-total-price .product-price .sub-total-price').text($.numberWithCommas(totalPrice));
  }
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
      getTotalPrice();
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

function productListPage() {
  // scroll up check
  let scrollPer = Math.round((Math.ceil($(this).scrollTop() + $(this).height()) / $(this).prop('scrollHeight')) * 100);
  let page = parseInt($('#product-form input[name=offset]').val());
  let $this = $(this);

  if ( scrollPer > 90 ) {
    console.log($(this).prop('scrollHeight'));
    $this.unbind('scroll');
    $('#product-form input[name=request_unit]').val(1);
    $('#product-form input[name=offset]').val(page + 1);

    result = getData('/apiLoggedIn/products', dataInit("#product-form"));
    if ( result.Code == 200 ) {
      console.log(result.data);
      if ( result.data.length > 0) {
        list = getData('/order/productList', {'products' : result.data});
        $this.append(JSON.parse(list.data));
      }
    }
    $this.bind('scroll', productListPage);
  }
}