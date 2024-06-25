function getData(url, data, type = 'POST', parse = false) {
  if ( type == 'GET' ) {
    let query = '';

    if ( data != '' ) {
      if (typeof data == 'string' ) {
        return {'Code': 500, 'error': '형식이 잘못되었습니다.'}
      } else if ( typeof data == 'object' ) {
          Object.entries(data).forEach(([key, value]) => {
            if ( query == '' ) query += '?';
            if ( query != '' ) query += '&';
            query += `${key}=${value}`;
          });
      } else {
        data.forEach(v => {
          console.log(v);
        });
      }
    }
  }

  let val, errors = false;
  try {
    $.ajax({
      type: type,
      getType: 'json',
      url: url,
      async: false,
      data: data,
      success: function(res, status, xhr) {
        // console.log('res ', res, ' status ', status, ' xhr ', xhr);
        val = { 'Msg': status, 'Code' : xhr.status, 'data' : res };
      },
      error: function(XMLHttpRequest, textStatus, errorThrow) {
        errors = true;
        // console.log("error XMLHttpRequest", XMLHttpRequest);
        // console.log('textStatus ', textStatus);
        // console.log('errorThrow ', errorThrow);
        val = {'Code': XMLHttpRequest.status, 'Msg': textStatus, 'data' : errorThrow };
        // return val;
      }
    });
  } catch(e) {
    val = e;
  // } finally {  
  //   if ( !errors ) {
  //     if (parse) val = JSON.parse(val);

  //     if ( typeof val.Code != 'undefined' ) {
  //       if ( val.Code == 401 ) {
  //         if ( val.Msg == '' ) {
  //           val.Msg = '로그인 필요';
  //         }
  //         alert(val.Msg);
  //         location.replace('/login');
  //       }
  //     }
  //   // } else {
  //     // val = {'Code': 500, 'Msg': val};
  //   }
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

$.numberWithCommas = function(x) {
  return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

$.withoutCommas = function (x) {
  return x.toString().replace(',', '');
}

// $.urlParam = function(name) {
//   var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
//   if (results == null ) return null;
//   return decodeURI(results[1]) || 0;
// }

$.removeUrlParam = function(name) {
  console.log(typeof name);
  const urlStr = window.location.href;
  const url = new URL(urlStr);
  let urlParams = new URLSearchParams(url.search);
  var results;

  if ( typeof name == 'string' ) {
    results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if ( results == null ) return;
    urlParams.delete(name);
  } else {
    name.forEach((val) => {
      if ( urlParams.has(val) ) {
        urlParams.delete(val);
      }
    });
  }
  return decodeURI(urlParams);
}

$.urlParam = function(param) {
  console.log(typeof param);
  console.log(param);
  const urlStr = window.location.href;
  const url = new URL(urlStr);
  let urlParams = new URLSearchParams(url.search);

  if ( typeof param == 'object' ) {
    if ( $.isArray(param) === false ) {
      for ( let entry of Object.entries(param) ) {
        let results = urlParams.get(entry[0]);
        if ( results == null ) {
          urlParams.append(entry[0], entry[1]);
        } else {
          urlParams.set(entry[0], entry[1]);
        }
      }
    } else {
      param.forEach((v) => {
        for ( let entry of Object.entries(v) ) {
          let results = urlParams.get(entry[0]);
          if ( results == null ) {
            urlParams.append(entry[0], entry[1]);
          } else {
            urlParams.set(entry[0], entry[1]);
          }
        }
      });
    }
  } else return;

  return decodeURI(urlParams);
}

function formSubmitTest(data = Array(), formOpt = {}, $form = '') {
  if ( $form == '' ) {
    if ( formOpt.action == '' ) formOpt.action ='/api';
    if ( formOpt.name == '' || !formOpt.name ) formOpt.name = 'test';
    if ( formOpt.method == '' || !formOpt.method ) formOpt.method = 'post';
    $form = $("<form/>").attr({'action': formOpt.action, 'name': formOpt.name, 'method': formOpt.method});
    $("body").append($form);
  }
  console.log(data);
  if ( data != '' ) {
    for(key in data) {
      console.log("key " ,key);
      $form.append($("<input/>").attr({"type": "hidden", "name" : key, "value": data[key]}));
    }
  } else return;
  
  return $form;
}

$(document).on('click', '.pre-order', function(e) {
  let confirmMsg = "Do you want to cancel the payment?";

  if ( $(e.target).attr('class') == $(this).attr('class') ) {
    if ( $(e.target).data('bsConfirm') != '' 
        && typeof $(e.target).data('bsConfirm') != 'undefined' ) {
          confirmMsg = $(e.target).data('bsConfirm');
    }

    let cancelPaypal = confirm(confirmMsg);
    if ( cancelPaypal ) {
      $(".pre-order").removeClass('show');
      $("body").css('overflow', 'auto');
    }
    else return;
  }
});