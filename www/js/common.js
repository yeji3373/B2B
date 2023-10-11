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
        console.log("error XMLHttpRequest", XMLHttpRequest);
        // val = {'Code': XMLHttpRequest.status, 'Msg': XMLHttpRequest.responseText};
        val = {'Code': XMLHttpRequest.status, 'Msg': textStatus};
        return val;
      }
    });
  } catch(e) {
    val = e;
  } finally {  
    if ( !errors ) {
      if (parse) val = JSON.parse(val);

      if ( typeof val.Code != 'undefined' ) {
        if ( val.Code == 401 ) {
          if ( val.Msg == '' ) {
            val.Msg = '로그인 필요';
          }
          alert(val.Msg);
          location.replace('/login');
        }
      }
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

$(document).ready(function(){

  $("#infoPrev").on("click", function(){
    $("#infoCarousel").carousel('prev');
  });

  $("#infoNext").on("click", function(){
    $("#infoCarousel").carousel('next');
  });

  $("#bannerPrev").on("click", function(){
    $("#bannerCarousel").carousel('prev');
  });

  $("#bannerNext").on("click", function(){
    $("#bannerCarousel").carousel('next');
  });

  $("#brandPrev").on("click", function(){
    $("#brandCarousel").carousel('prev');
  });

  $("#brandNext").on("click", function(){
    $("#brandCarousel").carousel('next');
  });
  
});
