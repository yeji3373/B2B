$(document).ready(function() {
  var checked = $("input[name='region[]']:checked");
  if(checked.length > 0){
    $("input[name='region[]']:checked").each(function (index, item){
      let group = 'region_' + $(item).val();
      let countries;
      if ( $("[name='region[]']:checked").length < 1 ) {
        $(".countries").empty().hide();
      } else $(".countries").show();
      if ( $(item).is(":checked") ) {

        countries = getData('/api/getCountry', {'region_id': $(item).val()});
        $(".countries").append(`<fieldset class='${group}'>
                                  <legend>
                                    ${$(item).next().text()}
                                  </legend>
                                </fieldset>`);
        
        $.each(countries, (idx, key) => {
          let checked = '';
          if($("input[name=checkedCountries]").length > 0){
            $("input[name=checkedCountries]").each(function (index, item){
              if($(item).val() == key['id']){
                checked = 'checked';
              }
            });
          }
          $(`.${group}`).append(
            `<label>
              <input type='checkbox' value='${key['id']}' name='country[]' ${checked}>
              <span>${key['name_en']}</span>
            </label>`
          );
        });
      } else {
        $(".countries").find(`.${group}`).remove();
      }
    });
  }
}).on("change", "[name='region[]']", function() {
  let group = 'region_' + $(this).val();
  let countries;
  if ( $("[name='region[]']:checked").length < 1 ) {
    $(".countries").empty().hide();
  } else $(".countries").show();
  if ( $(this).is(":checked") ) {

    countries = getData('/api/getCountry', {'region_id': $(this).val()});
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
    // test(group, countries);
  } else {
    $(".countries").find(`.${group}`).remove();
  }
}).on('change', '[name=buyerRegion]', function() {
  console.log($(this).children('option:selected').data('countryNo'));
  if ( typeof $(this).children('option:selected').data('countryNo') != 'undefined' ) {
    $("[name=buyerPhoneCode]").val($(this).children('option:selected').data('countryNo')).prop('selected', true);
  }
});
