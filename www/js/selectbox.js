;(function($) {
  // $.fn.selectboxPlugin = function( opts ) {
  $.selectboxPlugin = function( opts ) {
    let option = $.extend({

    }, opts);

    let dataList = this;
    let items = this.children();

    console.log("selectBox plugin ", this);

    console.log("datalist ", dataList, ' options ', options);

    dataList.on("change keyup", function() {
      console.log(e);
    });

    // this.on('keypress', function(e) {
    //   console.log("key down");
    //   // this.contents().unwrap().wrap("datalist");
    //   console.log(this);
    // });

    // this.on('dbclick', function(e) {
    //   // e.preventDefault();

    //   // console.log("double click");
    // });
  };  

  // $.fn.selectboxPlugin.define( function () {
  //   var KEYS = {
  //     BACKSPACE: 8,
  //     TAB: 9,
  //     ENTER: 13,
  //     SHIFT: 16,
  //     CTRL: 17,
  //     ALT: 18,
  //     ESC: 27,
  //     SPACE: 32,
  //     PAGE_UP: 33,
  //     PAGE_DOWN: 34,
  //     END: 35,
  //     HOME: 36,
  //     LEFT: 37,
  //     UP: 38,
  //     RIGHT: 39,
  //     DOWN: 40,
  //     DELETE: 46
  //   };
  
  //   return KEYS;
  // ));
})(jQuery);