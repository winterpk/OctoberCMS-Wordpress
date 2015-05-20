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
		
		// Add ajax listener with errors
		$("form.ajax-form").on('submit', function(){
			var $this = $(this);
			$this.request($this.attr('data-request'), {
				success: function(data){
					if (data.errors) {
						$this.addClass('has-errors');
						clearErrors();
						$.each(data.errors, function(k, v){
							$this.find('input[name="'+k+'"], select[name="'+k+'"], textarea[name="'+k+'"]').closest('.form-group').addClass('has-error');
							$this.find('.error.'+k).html(this[0]);
						});
					} else {
						$this.addClass('success');
						this.success(data);	
					}
				},
				loading: function(data) {
					$this.find('input[type="submit"]').attr('disabled', 'disabled');
				}, 
				complete: function(data) {
					$this.find('input[type="submit"]').removeAttr('disabled');
					this.complete(data);
				},
				beforeUpdate: function(data) {
					for (var i in this.options.update) {
						var $updateEle = $(this.options.update[i]);
						$updateEle.hide();
						$updateEle.fadeIn('slow');
					}
				},
			});
			return false;
		});
		
	});
})(jQuery);
