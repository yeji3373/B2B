$(document).ready(function() {

}).on('change', '.requirements select', function() {
  let target = $(this).attr('aria-target');
  
  if ( $(target).length ) {
    if ( !$(this).children('option:selected').attr('aria-appended') ) {
      $(this).children('option:selected').attr('aria-appended', true);

      $(target).append(
        $("<div/>").addClass('requirement-item')
                  .append($('<input/>').attr('type', 'hidden').attr('name', '').val($(this).children('option:selected').val()))
                  .append($('<label/>').addClass('w-20').text($(this).children('option:selected').text()))
                  .append($('<textarea/>').addClass('w-80').attr('name', '').attr('placeholder', $(this).children('option:selected').data('placeholder')))

                  
      );
    }
  }
})