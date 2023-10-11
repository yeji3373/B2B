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
        $(".orders-list div").append("  <a class='list-group-item order-item fw-bold'>\
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
    // console.log($("[name=receipt_id]").val());
    $form = $(this).closest('form');
    result = getData('/orders/getOrderData', $form.serializeArray());

    appendData($('.pi-viewer > div') , result, true);
    $('.pi-viewer').show();
    $('body').addClass('overflow-hidden');
  }).on('click', '.pi-viewer .btnClose', function(e) {
    e.preventDefault();
    
    $('.pi-viewer').hide();
    $('body').removeClass('overflow-hidden');
    
  //개별상품별 주문option 결정
  }).on("click", ".btn-small", function() {
    let formData = $(this).closest('form').serializeArray();
    console.log(formData);
    result = getData('/orders/setOrderOption', formData);
    console.log(result);
    return false;
  // packaging_status -> 6 (고객확인완료) 로 변경
  }).on('click', '.order-check', function(e) {
    e.preventDefault()
    let data = $(this).closest('form').serializeArray();

    if ( $('.requirmentOptForm').length ) {
      if ($('.requirmentOptForm [type=radio]:checked').length < $('.requirmentOptForm').length ) {
        if ( confirm('재고요청 상태가 선택되지 않은 조건이 있습니다. 다시 선택하겠습니까?') ) {
          return;
        }
      }
      data = $.merge(data, $('.requirmentOptForm').serializeArray());   
    }

    result = getData('/orders/orderFixed', data, true);
    console.log(result);

    if (result['Code'] == 200 ) {
      // if ( typeof $(this).data('nextName') != 'undefined' ) {
      //   $(this).text($(this).data('nextName'));
      // }
      // $(this).removeClass('order-check').addClass('order-request');
      location.reload(true);
    }
    return;
    // result = getData('/order')
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
    formData = $(this).closest('.requirmentOptForm').serializeArray();
    result = getData('/orders/setOrderOption', formData, true);
    
    if ( $.inArray('error', result) === true ) {
      console.log("inarray");
      alert(result['error']);
      return;
    }
    return;
    // if(confirm('order confirmation')){
    //   let formData;
    //   let selectedOption = $(this).closest('form').find('input[name="requirement_id"]').val();
    //   if(selectedOption == 1){
    //     formData = $(this).closest('#requirmentOptForm').serializeArray();
    //   }else{
    //     formData = $(this).closest('#requirmentOptForm').serializeArray();
    //   }
    //   console.log(formData);
    //   result = getData('/orders/setOrderOption', formData);
    //   console.log(result);
    //   return true;
    // }else{
    //   return false;
    // }
    // // return false;
  });
