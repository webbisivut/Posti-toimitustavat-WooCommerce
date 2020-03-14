jQuery( document ).ready( function ($) {
	$('.loading-img-smartpost-posti').hide();

	$('.js-ajax-php-json-posti-button').click(function(){
		$('.loading-img-smartpost-posti').show();

		var data = {
			'action': 'smartpost_posti_noutopisteet',
			'datedata': $('.smartpost_noutopiste_posti').val()
		};

		data = $(this).serialize() + '&' + $.param(data);
		$.ajax({
			type: 'POST',
			url: smartPostAjax.ajaxurl,
			data: data,
			success: function(data) {
				var noutopiste = data;
				var noutopiste = noutopiste.substr(0, noutopiste.length-1);

				$('.smartpost-posti-return').html(noutopiste);
				$('.loading-img-smartpost-posti').hide();
			}
		});
		return false;
	});

	$('.js-ajax-php-json-posti-button-sz').click(function(){
		$('.loading-img-smartpost-posti').show();

		var data = {
			'action': 'smartpost_posti_noutopisteet_sz',
			'datedata': $('.smartpost_noutopiste_posti-sz').val()
		};

		data = $(this).serialize() + '&' + $.param(data);
		$.ajax({
			type: 'POST',
			url: smartPostAjax.ajaxurl,
			data: data,
			success: function(data) {
				var noutopiste = data;
				var noutopiste = noutopiste.substr(0, noutopiste.length-1);

				$('.smartpost-posti-return-sz').html(noutopiste);
				$('.loading-img-smartpost-posti').hide();
			}
		});
		return false;
	});

	$('#smartpost_noutopiste_posti-sz').keypress(function(e){
        if(e.which == 13){ 
            $('.js-ajax-php-json-posti-button-sz').click(); 
        }
    });
});
