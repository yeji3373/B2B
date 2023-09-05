$(document).ready(function() {

}).on('change', '.requirements select', function() {
  let target = $(this).attr('aria-target');
  let idx = 0;

  if ( $(target).length ) {
    if ( $(target).find('.requirement-item').length ) {
      console.log($(target).find('.requirement-item').length);
      // idx = $(target).find('.requirement-item').data('idx') + 1;
      idx = $(target).find('.requirement-item').length;
    }

    if ( !$(this).children('option:selected').attr('aria-appended') ) {
      $(this).children('option:selected').attr('aria-appended', true);

      $(target).append(
        $("<div/>").addClass('requirement-item').attr('data-idx', idx)
                  .append($('<input/>').attr('type', 'hidden').attr('name', 'requirement[' + idx + '][requirement_id]').val($(this).children('option:selected').val()))
                  .append($('<label/>').text($(this).children('option:selected').text()))
                  .append($('<textarea/>').attr('name', 'requirement[' + idx + '][requirement_detail]').attr('placeholder', $(this).children('option:selected').data('placeholder')))
      );
    }
  }
})