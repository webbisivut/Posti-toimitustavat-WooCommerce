jQuery(document).ready(function($) {
	function toggleCustomPeTrigger() {
		$( 'body' ).trigger( 'update_checkout' );
	}
	
	$(document).on('change', "*[id^='payment_method']", toggleCustomPeTrigger);

});