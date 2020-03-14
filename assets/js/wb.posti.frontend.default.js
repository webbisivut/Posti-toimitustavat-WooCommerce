jQuery(document).ready(function($) {
    // Create reusable function to show or hide .smartpost-uf-wrap
    function toggleCustomBoxPosti() {
        // Get id of selected input
        var selectedMethod = $('input:checked', '#shipping_method').attr('id');
        var hiddenOrNot = $('.shipping_method').attr('type');
        var getVal = $('.shipping_method').val();

        var target_method = $("*[id^='shipping_method_0_wb_posti_smartpost_shipping_method']").attr('id');
        var target_getVal = $("*[value^='wb_posti_smartpost_shipping_method']").attr('value');

        var target_method2 = $("*[id^='shipping_method_0_sz_wb_posti_smartpost_shipping_method']").attr('id');
        var target_getVal2 = $("*[value^='sz_wb_posti_smartpost_shipping_method']").attr('value');

        // Toggle .smartpost-uf-wrap depending on the selected input's id
        if (selectedMethod === target_method && typeof target_method != 'undefined' || hiddenOrNot == 'hidden' && getVal == target_getVal && typeof getVal != 'undefined') {
            $('.smartpost-posti-wrap').show();
      			$('#smartpost_noutopiste_posti').val('');
        } else {
            $('.smartpost-posti-wrap').hide();
			      $('#smartpost_noutopiste_posti').val('Ei käytössä');
        };

        if (selectedMethod === target_method2 && typeof target_method2 != 'undefined' || hiddenOrNot == 'hidden' && getVal == target_getVal2 && typeof getVal != 'undefined') {
            $('.smartpost-posti-wrap-sz').show();
            $('#smartpost_noutopiste_posti-sz').val('');
        } else {
            $('.smartpost-posti-wrap-sz').hide();
            $('#smartpost_noutopiste_posti-sz').val('Ei käytössä');
        };
    };
  // Fire our function on page load
  $(document).ready(toggleCustomBoxPosti);

  // Fire our function on radio button change
  $(document).on('change', '#shipping_method input:radio', toggleCustomBoxPosti);

  $(document).on('change', '#billing_country', function() {
        $( document ).ajaxComplete(function() {
          toggleCustomBoxPosti();
        });
  });
  
  //setTimeout( toggleCustomBoxPosti, 2000 );

    setTimeout(function () {
        $(document).ajaxComplete(function () {
            toggleCustomBoxPosti();
        }), 3000 
    });

});
