// $(document).ready(function() {
  $(document).on('keyup', '[name=order_number]', function() {
    let orderNumber;
    if ( $(this).val().length > 1 ) {
      orderNumber = $(this).val();
      $(".orders-list").empty();
    }
    result = getData('/orders/getOrderList', [{name: 'order_number', value: orderNumber}]);
    result = JSON.parse(result);
    
    if ( result.length > 0 ) {
      $.each(result, (i, v) => {
        $(".orders-list").append("<div class='order-item'>\
                                    <span class='order-number'>" + v['order_number'] + "</span>\
                                    <span class='parenthesis small'>" + v['created_at'].split(" ")[0] + "</span>\
                                  <div/>");
      });
    } else $(".orders-list").html('<div class="order-item">is Empty</div>');
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
  });
// });