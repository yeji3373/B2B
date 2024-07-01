$(document).ready(function() {
  if ( $(".detail_id_check").length ) {
    Array.from($(".detail_id_check")).forEach((v) => {
      if ( typeof $(v).data('detailId') != 'undefined' ) {

      }
    });
  }
}).on('keyup', '[name=order_number]', function() {
  let orderNumber;
  if ( $(this).val().length > 2 ) {
    orderNumber = $(this).val();
    $(".orders-list div").empty();
  
    result = getData('/orders/getOrderList', [{name: 'order_number', value: orderNumber}, {name: 'page',  value: 1}]);
    result = JSON.parse(result);
       
    if ( result.length > 0 ) {
      $.each(result, (i, v) => {
        $(".orders-list div").append("  <a class='list-group-item order-item fw-bold' href='/orders?order_number=" + v['order_number'] + "'>\
                                          <span class='order-number'>" + v['order_number'] + "</span>\
                                          <span class='parenthesis small'>" + v['created_at'].split(" ")[0] + "</span>\
                                        <a/>");
      });
    } else $(".orders-list div").html('<div class="order-item">is Empty</div>');
  }
  // }).on('click', '.order-item', function() {
  //   let orderNumber = $.trim($(this).children('.order-number').text());
  //   result = getData('/orders/getOrder', [{name: 'order_number', value: orderNumber}]);
  //   appendData($(".order-detail-container"), result, true);

  //   if ( $('.order-item.active') != $(this) ) {
  //     if ( $('.order-item.active').length > 0 ) {
  //       $('.order-item.active').removeClass('active');
  //     }
  //   }
  //   $(this).addClass('active');
  }).on('click', '.pi-view', function() {
    $form = $(this).closest('form');
    result = getData('/orders/getOrderData', $form.serializeArray());

    appendData($('.pi-viewer > div') , result, true);
    $('.pi-viewer').show();
    $('body').addClass('overflow-hidden');
  }).on('click', '.pi-viewer .btnClose', function(e) {
    e.preventDefault();
    
    $('.pi-viewer').hide();
    $('body').removeClass('overflow-hidden');
  }).on('click', '.order-check', function(e) {
    e.preventDefault();
    let data = $(this).closest('form').serializeArray();
    let msg = '재고요청 상태가 선택되지 않은 조건이 있습니다. 다시 선택하겠습니까?';
  
    if ( $('.requirmentOptForm').length ) {
      if ($('.requirmentOptForm [type=radio]:checked').length < $('.requirmentOptForm').length ) {
        if ( typeof $(this).data('confirmMsg') != 'undefined' ) {
          msg = $(this).data('confirmMsg');
        }
      
        if ( confirm(msg) ) {
          return;
        } else {
          console.log('???');
          Array.from($('.requirmentOptForm')).forEach(element => {
            if ( typeof $(element).data('type') != 'undefined' && typeof $(element).data('name') != 'undefined' ) {
              if ( $(element).data('type') == '0' ) {
                tempName = $(element).attr('name');
                $(element).attr('name', $(element).data('name'));
                $(element).data('name', tempName);
                $(element).data('type', 1);
              } 
            }
          });
        }
      }
      data = $.merge(data, $('.requirmentOptForm').serializeArray());
    }

    result = getData('/orders/orderFixed', data, 'POST', true);
    if (result['Code'] == 200 ) {
      location.reload(true);
    }
    return;
  }).on('click', '.order-request', function(e) {
    e.preventDefault();
    result = getData('/order/orderForm', $(this).closest('form').serializeArray());

    if ( typeof result.Code != 'undefined' && result.Code == 500 ) {
      result = JSON.parse(result);
      if ( $.inArray('error', result) ) {
        alert(result['error']);
        return;
      }
      return;
    }
    
    appendData($('.pre-order'), result, true);
    $("body").css('overflow', 'hidden');
    $('.pre-order').addClass('show');
    if ( $('.prev-addr-sel').length ) {
      $(".prev-addr-sel:eq(0)").click();
    }
  }).on('click', '.confirmbtn', function(e) {
    e.preventDefault();
    if ( $(this).closest('.requirmentOptForm').length == 0 ) return;
    let requirmentOptForm = $(this).closest('.requirmentOptForm'); 
    let formData = [];

    Array.from(requirmentOptForm.find('input')).forEach(element => {
      if ( typeof $(element).data('type') != "undefined" || typeof $(element).data('name') != 'undefined' ) {
        if ( $(element).data('type') == '1' ) {
          tempName = $(element).attr('name');
          $(element).attr('name', $(element).data('name'));
          $(element).data('name', tempName);
          $(element).data('type', 0);
        }
      } else return;
    });
    
    formData = requirmentOptForm.serializeArray();
    let result = getData('/orders/setOrderOption', formData, 'POST', true);
    if ( $.inArray('error', result) === true ) {
      console.log("inarray");
      alert(result['error']);
      return;
    }
    return;
  });
