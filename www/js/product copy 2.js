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
    let form_brand_id = $('form [name="brand_id"]').val() != "" ? $('form [name="brand_id"]').val().split(",") : Array();
    let run = false;
    
    if ( $(this).hasClass('active') ) {
      if ( $(this).index() > 0 ) {
        $(this).removeClass("active");
        form_brand_id.splice(form_brand_id.findIndex((e) => {return e == brand_id}), 1);
        if ( $(".brand-item.active").length == 0 ) {
          $(".brand-item").first().addClass("active");
        }        
        run = true;
      } else run = false;

    } else {
      if ( $(this).index() > 0 ) {
        if ( $(".brand-list-group .brand-item").first().hasClass('active') ) {
          $(".brand-list-group .brand-item").first().removeClass('active');
        }
        $(this).addClass("active");
        // form_brand_id = brand_id;
        if ( form_brand_id.indexOf(brand_id) < 0 ) {
          form_brand_id.push(brand_id);
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
    }
    $('form [name="brand_id"]').val(form_brand_id);
    if ( run ) {
      productResult = getData('/order/productList', dataInit());
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
      // $("form #keyword_").attr('name', opts).val(search);  
      $('form [name="'+ opts + '"]').val(search);
      // result = getData('/api/getProducts', dataInit());
      // appendData($(".product-search-result"), result, true);
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
      alert(validChk['msg'] + "\n" + validChk['data'] + " 변경됨");
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
    appendData($('.pre-order'), result, true);
    $(".pre-order").show();
    $("body").css('overflow', 'hidden');
    // location.href="/paypal";
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
  }).on('click', '.checkout-btn', function() {
    /* submit check */
    // validRequiredCheck($("form"));
    
  }).on('click', '.prev-addr-sel', function() {
    $(this).addClass('border-2 border-primary');
    console.log($(this).data('id'));
    $(".new-addr input[name=address_id]").val($(this).data('id'));
  }).on("click", '.prev-addr-edit, .prev-addr-sel', function(e) {
    console.log($(this));
    // console.log("e target ", $(e.target));
    // console.log($(this).attr("class") == $(e.target).attr("class"));
    // console.log("aaaaaaaa ", $('.prev-addr-sel .phone_code').text());
    if ( $(this).hasClass('prev-addr-edit') ) {
      $("#address-new-head .accordion-button").click();
    }
    $('.new-addr [name=consignee]').val($('.prev-addr-sel .consignee').text());
    $('.new-addr [name=region]').val($('.prev-addr-sel .region').text());
    $('.new-addr [name=region_id]').val($('.prev-addr-sel .region').data('id'));
    $('.new-addr [name=streetAddr1]').val($('.prev-addr-sel .streetAddr1').text());
    $('.new-addr [name=streetAddr2]').val($('.prev-addr-sel .streetAddr2').text());
    $('.new-addr [name=city]').val($('.prev-addr-sel .city').text());
    $('.new-addr [name=zipcode]').val($('.prev-addr-sel .zipcode').text());
    $('.new-addr [name=phone_code]').val($('.prev-addr-sel .phone_code').text());
    $('.new-addr [name=phone]').val($('.prev-addr-sel .phone').text());
  });

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
  console.log(totalPriceResult);

  if ( totalPriceResult['applyDiscount'] == 1 ) {
    $(".sub-total-price").text(totalPriceResult['order_subTotal']);
  } else $(".sub-total-price").text(totalPriceResult['order_price_total']);
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
    msg = "주문 가능한 최대 수량은 " + max + "입니다";
  }

  if ( parseInt(curr) < parseInt(min)) {
    code = 500;
    data = min;
    msg = "최소 주문 수량은 " + min + "입니다";
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