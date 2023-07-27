function getData(url, data, parse = false, type = 'POST') {
  let val, errors = false;
  try {
    $.ajax({
      type: type,
      getType: 'json',
      url: url,
      async: false,
      data: data,
      success: function(res, status, xhr) {
        // let responseType = xhr.getResponseHeader('content-type') || "";
        // if ( responseType.indexOf('html') > -1 ) {
        //	 console.log(res);
        //   if ( parse == true ) errors = true;
        // }
        // // if ( parse ) val = JSON.parse(res);
        // // else val = res;
        val = res;
      },
      error: function(XMLHttpRequest, textStatus, errorThrow) {
        console.log("error XMLHttpRequest", XMLHttpRequest, ' textStatus ', textStatus);
        val = {'Code': 500, 'Msg': XMLHttpRequest.responseText};
        // return false;
      }
    });
  } catch(e) {
    val = e;
  } finally {  
    if ( !errors ) {
      if (parse) val = JSON.parse(val);
    } else {
      val = {'Code': 500, 'Msg': val};
    }
  }

  return val;
}

function appendData($target, result, init = false) {
  if ( $target == '' || $target == null || typeof $target !== 'object') return;
  
  if ( init ) {
    if ( $target.children().length > 0 || $target.length > 0 ) $target.empty();
  }
  $target.append(result);
}

function convertData(tag) {
  let _return;
  switch (tag) {
    case 'select':
      _return = $("<option>");
      break
    default : 
      _return =  $("<li/>");
      break
  }

  return _return;
}

function activeToggle(target) {
  if ( target.hasClass('active') ) {
    target.removeClass('active');
  } else target.addClass('active');
}


// $(function() {
$.numberWithCommas = function(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

$.withoutCommas = function (x) {
  return x.toString().replace(',', '');
}
// });