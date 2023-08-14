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
        $(".orders-list div").append("<a class='list-group-item order-item fw-bold'>\
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
    appendData($('.pi-viewer > div') 
                , getData('/orders/getOrderData', $form.serializeArray())
                , true);
    $('.pi-viewer').show();
    $('body').addClass('overflow-hidden');
  }).on('click', '.pi-viewer .btnClose', function(e) {
    e.preventDefault();
    
    $('.pi-viewer').hide();
    $('body').removeClass('overflow-hidden');
  }).on("click", ".detail_id_check .btn", function() {
    let formData = $(this).closest('.detail_id_check').find('form').serializeArray();
    result = getData('', formData);
    
    return false;
  });
