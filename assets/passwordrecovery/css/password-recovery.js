/**
 * Signup 
 *
 */
(function($) {
	$(document).ready(function(){
		function clearErrors() {
			$('form .error').html('');
			$('form .form-group').removeClass('has-error');
		}
		
		// Add ajax listener with errors
		$("#password-recovery").on('submit', function(){
			console.log('xfngr');
			var $this = $(this);
			$this.request('onSubmit', {
				success: function(data){
					console.log(data);
					if (data.errors) {
						clearErrors();
						$.each(data.errors, function(k, v){
							$this.find('input[name="'+k+'"]').parent().addClass('has-error');
							$this.find('.error.'+k).html(this[0]);
						});
					}
				},
				loading: function(data) {
					$this.find('input[type="submit"]').attr('disabled', 'disabled');
				}, 
				complete: function(data) {
					$this.find('input[type="submit"]').removeAttr('disabled');
				}
			});
			return false;
		});
	});
});
