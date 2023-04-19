$(document).ready(function() {
  //serializeArray
  //serializeObject();

  $(".brand-keyword-search").on("keyup", function() {
    let result, search, $this, searchList, resultList = "";
    $this = $(this);
    search = $this.val();
    searchList = $(".brand-keyword-search-result");
    
    if ( search.length > 0 ) {
      searchList.addClass('show');
      result = getData('/api/getLikeBrandName', {'brand_name': search});

      if ( result != '' ) {
        $.each(result, (idx, key) => {
          resultList = 
            resultList +
            `<li class='dropdown-item test' data-name='${key['brand_name']}' data-id='${key['brand_id']}'>
              ${key['brand_name']}
            </li>`
        });
      } else {
        resultList = `<li class='dropdown-item'>No Data</li>`;
      }
      appendData(searchList, resultList, true);
    } else {
      searchList.removeClass('show');
      searchList.empty();
      searchList.append("<li class='dropdown-header'>Search for brands</li>");
    }
  });
  
  $(document).on('click', '.test', function() {
    let sorted = $(this).data('name');
    let brand_id = new Array($('form [name="brand_id"]').val());

    brand_id.push($(this).data('id'));

    $(".brand-list-group").find("." + sorted).addClass('active');
    $(".brand-keyword-search").val("");
    $(".brand-keyword-search-result").empty().append("<li class='dropdown-header'>Search for brands</li>");
    $('form [name="brand_id"]').val(brand_id);

    productResult = getData('/api/getProducts', $('form').serializeArray());
  });

  $(".brand-list-group .brand-item").on("click", function() {
    let brand_id = $(this).data('id');
    let form_brand_id = $('form [name="brand_id"]').val() != "" ? $('form [name="brand_id"]').val().split(",") : Array();
    // let form_brand_id = $('form [name="brand_id"]').val() != "" ? $('form [name="brand_id"]').val() : "";
    // let searchList = $(".product-search-result").append('<div data-idx=' + brand_id + '></div>');
    let run = false;
    
    if ( $(this).hasClass('active') ) {
      if ( $(this).index() > 0 ) {
        $(this).removeClass("active");
        form_brand_id.splice(form_brand_id.findIndex((e) => {return e == brand_id}), 1);
        if ( $(".brand-item.active").length == 0 ) {
          $(".brand-item").first().addClass("active");
        }
       
        run = true;
      } else run = false;

    } else {
      if ( $(this).index() > 0 ) {
        if ( $(".brand-list-group .brand-item").first().hasClass('active') ) {
          $(".brand-list-group .brand-item").first().removeClass('active');
        }
        $(this).addClass("active");
        // form_brand_id = brand_id;
        if ( form_brand_id.indexOf(brand_id) < 0 ) {
          form_brand_id.push(brand_id);
          run = true;
        }
      } else {
        if ( form_brand_id != brand_id ) {
          run = true;
          $(".brand-item").removeClass("active");
          $(".brand-item").first().addClass("active");
          form_brand_id = "";
        } else run = false;
      }
    }
    $('form [name="brand_id"]').val(form_brand_id);
    if ( run ) {
      productResult = getData('/api/getProducts', $('form').serializeArray());
      appendData($(".product-search-result"), productResult, true);
    }
  });

  $("#productSearch").on('keyup', function() {
    let $this = $(this);
    let opts = $(".productSearchOpts option:selected").val();
    let search = $this.val();
    let arr = new Object();
    let run = false;
    
    arr = $("form").serializeArray();

    if ( search.length > 1 ) {
      if ( opts == '' ) {
        $(".productSearchOpts").addClass("bg-danger text-white");
        return;
      } else {
        $(".productSearchOpts").removeClass("bg-danger text-white");
        run = true;
      }
    }

    if ( run ) {
      arr.push({'name': opts, 'value': search});
      console.log(arr);
      result = getData('/api/getProducts', arr);
      console.log("result ", result);
      appendData($(".product-search-result"), result, true);
    }
  });

  $(".productSearchOpts").on("change", function() {
    let run = false;
    $("form #keyword_").attr('name', $(this).val()).val($("#productSearch").val());
    
    arr = $("form").serializeArray();

    if( $(this).hasClass('bg-danger') ) {
      $(this).removeClass('bg-danger text-white');
    }

    if ( $(this).val() != "" ) {
      if ( $("#productSearch").val() != "" ) {
        // arr.push({"name": $(this).val(), "value": });
        run = true;
      }
    }

    if ( run ) {
      result = getData('/api/getProducts', arr);
      appendData($(".product-search-result"), result, true);
    }
  });

  $(document).on("click",".product-search-result .product-item", function() {
    activeToggle($(this));
  });
});