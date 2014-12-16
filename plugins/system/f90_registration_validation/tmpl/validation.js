(function($){
	$(document).ready(function(){
		var password1 = $('input[type="password"][id^="jform_password"]:first').attr('id');
		var password2 = $('input[type="password"][id^="jform_password"]:eq(1)').attr('id');		

		function validate_passowrd_confirm()
		{
			$('#'+password2+'_err').remove();
			if($('#'+password1).val() == $('#'+password2).val()){
				$('#'+password2+'-lbl, #'+password2).removeClass('invalid');
				return true;
			}
			e
			
			{
				$('#'+password2+'-lbl, #'+password2).addClass('invalid');
				$('<div id="'+password2+'_err" class="invalid">'+Joomla.JText._('PLG_SYSTEM_F90_REGISTRATION_VALIDATION_MSG_REGISTER_PASSWORD1_MESSAGE')+'</div>').insertAfter('#'+password2);
				return false;
			}
		}

		function validate_email_confirm()
		{
			$('#jform_email2_err').remove();
			if($('#jform_email1').val() == $('#jform_email2').val()){
				$('#jform_email2-lbl, #jform_email2').removeClass('invalid');
				return true;
			}
			else{
				$('#jform_email2-lbl, #jform_email2').addClass('invalid');
				$('<div id="jform_email2_err" class="invalid">'+Joomla.JText._('PLG_SYSTEM_F90_REGISTRATION_VALIDATION_MSG_REGISTER_EMAIL2_MESSAGE')+'</div>').insertAfter('#jform_email2');
				return false;
			}
		}
		
		function validate_password(value){
			var return_value = true;
			$.ajax({
				type: 'POST',
				async: false,
				url: "index.php?plg=f90_registration_validation&task=validate_password",
				data: { password : value},
			}).done(function(data) {
				data = $.parseJSON(data);
				$('#'+password1+'_err').remove();
				if(data.error == false){		
					return_value = true;			
					$('#'+password1+'lbl, #'+password1).removeClass('invalid');
					return true;
				}
				else{
					return_value = false;
					$('#'+password1+'-lbl, #'+password1).addClass('invalid');
					$('<div id="'+password1+'_err" class="invalid">'+data.msg+'</div>').insertAfter('#'+password1);
					return false;
				}
				
			});
			return return_value;
		}

		function validate_username(value){
			var return_value = true;
			$.ajax({
				type: 'POST',
				async: false,
				url: "index.php?plg=f90_registration_validation&task=validate_username",
				data: { username : value},
			}).done(function(data) {
				data = $.parseJSON(data);
				
				if(data.error == false){
					return_value = true;
					$('#jform_username_err').remove();					
					$('#jform_username-lbl, #jform_username').removeClass('invalid');
					return true;
				}
				else{
					$('#jform_username_err').remove();
					$('#jform_username-lbl, #jform_username').addClass('invalid');
					$('<div id="jform_username_err" class="invalid">'+data.msg+'</div>').insertAfter('#jform_username');
					return_value= false;
					return false;
				}
				
			});
			return return_value;
		}

		function validate_email(value){
			 if (value.indexOf("@lse") == -1) {
				var msg = "LSE email address is required."

				$('#jform_email1_err').remove();
				$('#jform_email1-lbl, #jform_email1').addClass('invalid');
				$('<div id="jform_email1_err" class="invalid">' + msg + '</div>').insertAfter('#jform_email1');
				return_value = false;
				return false;
			}
			var return_value = true;
			$.ajax({
				type: 'POST',
				async: false,
				url: "index.php?plg=f90_registration_validation&task=validate_email",
				data: { email : value},
			}).done(function(data) {
				data = $.parseJSON(data);

				$('#jform_email1_err').remove();	
				if(data.error == false){
					return_value = true;					
					$('#jform_email1-lbl, #jform_email1').removeClass('invalid');
					return true;
				}
				else{
					$('#jform_email1-lbl, #jform_email1').addClass('invalid');
					$('<div id="jform_email1_err" class="invalid">'+data.msg+'</div>').insertAfter('#jform_email1');
					return_value= false;
					return false;
				}
				
			});
			return return_value;
		}
		
		$('#'+password1).blur(function(){
			validate_password($(this).val());
		});

		$('#'+password2).blur(function(){
			validate_passowrd_confirm();
		});
	
		$('#jform_username').blur(function(){
			validate_username($(this).val());
		});

		$('#jform_email1').blur(function(){
			validate_email($(this).val());
		});
		
		$('#jform_email2').blur(function(){
			validate_email_confirm();
		});
		
		$('button.validate').click(function(e){
			var return_value = true;
			if(validate_username($('#jform_username').val()) == false){
				return_value = false;
			}

			if(validate_password($('#'+password1).val()) == false){
				return_value = false;
			}
				
			if(validate_passowrd_confirm() == false){
				return_value = false;
			}
			
			if(validate_email($('#jform_email1').val()) == false){
				return_value = false;
			}
			
			if(validate_email_confirm() == false){
				return_value = false;
			}
			return return_value;
		});
	});
	
})(jQuery);
