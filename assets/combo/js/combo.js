/**
 * Combo registration / login form script file
 *
 */
(function($) {
	$(document).ready(function(){
		
		function clearErrors() {
			$('form .error').html('');
			$('form .form-group').removeClass('has-error');
		}
		
		// Radio button click listener
		$('#account-forms input:radio').change(function(){
			clearErrors();
			if (this.checked === true) {
				var $this = $(this);
				$('#register-form, #login-form').hide();
				$('#'+$this.val()+'-form').show();
			}
		});
	});
	
})(jQuery);
