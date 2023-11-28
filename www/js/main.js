$(document).ready(function(){
}).on('click', '#infoPrev', function(){
  $('#infoCarousel').carousel('prev');
}).on('click', '#infoNext', function(){
  $('#infoCarousel').carousel('next');
}).on('click', '#bannerPrev', function(){
  $('#bannerCarousel').carousel('prev');
}).on('click', '#bannerNext', function(){
  $('#bannerCarousel').carousel('next');
}).on('click', '#brandPrev', function(){
  $('#brandCarousel').carousel('prev');
}).on('click', '#brandNext', function(){
  $('#brandCarousel').carousel('next');
// }).on('click', '.carousel-control-prev', function() {
//   $target = $(this).attr('href');
//   $($target).carousel('prev');
}).on('click', '.subscribe', function() {
  let regex = new RegExp('[a-z0-9]+@[a-z]+\.[a-z]{2,3}');
  var email = $('input[name="email-address"]').val();
  var name = $('input[name="full-name"]').val();
  if(email == ''){
    alert('Type your e-mail address.');
    return false;
  }
  if(regex.test(email) == false){
    alert('The email field must contain a valid email address.');
    return false;
  }
  if(name == ''){
    alert('Type your full name.');
    return false;
  }

  let result = getData('/Home/subscribe', {'email-address' : email, 'full-name' : name});
  
  if(result.length > 0){
    result = JSON.parse(result);
    alert(result.msg);
  }
});


