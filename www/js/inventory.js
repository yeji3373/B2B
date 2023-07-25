$(document).ready(function() {

}).on('change', '.requirements select', function() {
  let target = $(this).attr('aria-target');
  
  if ( $(target).length ) {
    // $(target).append(
    //   $("<span/>").addClass('requirement').text($(this).children('option:selected').text())
    //     .click(function() {
    //       $(this).remove();
    //     })
    //     .append($('<input/>').attr('type', 'hidden').attr('name', '').val($(this).children('option:selected').val()))        
    // );
    $(target).append(
      $("<div/>").addClass('requirement-item')
                .append($('<input/>').attr('type', 'hidden').attr('name', '').val($(this).children('option:selected').val()))
                .append($('<label/>').addClass('w-20').text($(this).children('option:selected').text()))
                .append($('<input/>').addClass('w-80').attr('type', 'text').attr('name', ''))

                
    );
  }
})