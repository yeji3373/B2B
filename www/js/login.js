// $(document).ready(function() {
  $(document).on("change", "[name='region[]']", function() {
    let group = 'region_' + $(this).val();
    let countries;
    if ( $("[name='region[]']:checked").length < 1 ) {
      $(".countries").empty().hide();
    } else $(".countries").show();
    if ( $(this).is(":checked") ) {

      countries = getData('/api/getCountry', {'region_id': $(this).val()});
      // $(".countries").append(`<div class='${group}'></div>`);
      $(".countries").append(`<fieldset class='${group}'>
                                <legend>
                                  ${$(this).next().text()}
                                </legend>
                              </fieldset>`);

      $.each(countries, (idx, key) => {
        $(`.${group}`).append(
          `<label>
            <input type='checkbox' value='${key['id']}' name='country[]'>
            <span>${key['name_en']}</span>
          </label>`
        );
      });
    } else {
      $(".countries").find(`.${group}`).remove();
    }
  // }).on('keyup','input[name=businessNumber]', function() {
  //   // let businessNoExp = /[0-9a-zA-Z\-{1}]/g;
  //   let notAllowed = /[\{\}\[\]\/?.,;:|\)*~`!^_+┼<>@\#$%&\'\"\\\(\=]/g;
  //   let temp = [];

  //   if ( notAllowed.test($(this).val()) ) {
  //     temp = $(this).val().substring(0, $(this).val().length - 1);
  //   } else temp = $(this).val();
    
  //   // if ( $(this).val().match(businessNoExp) != null ) {
  //   //   temp = $(this).val().match(businessNoExp).join().replace(/,/g, "");
  //   // } else temp = "";

  //   $(this).val(temp); 
  });
// });