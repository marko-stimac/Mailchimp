jQuery(function($) {
	var $subscribeForm = $('#js-mailchimp-form');

	// Reset button after subscription event
	function resetBiForm() {
		$submitBtn
			.removeClass('btn--disabled')
			.attr('disabled', false);
	}

	if ($subscribeForm.length) {
		var $submitBtn = $('#js-mailchimp-btn');
		var submitBtnDefaultValue = $submitBtn.text();

		var $messages = $('#mailchimp-messages');
		var $messageSuccess = $messages.find('.mailchimp__message--success');
		var $messageFail = $messages.find('.mailchimp__message--fail');

		// On submit form
		$subscribeForm.on('submit', function(e) {
			e.preventDefault();

			// First add animation and disable button 
			$submitBtn
				.addClass('btn--disabled')
				.attr('disabled', true);

			// Do AJAX request to backend
			$.ajax({
				data: {
					action: 'subscribe_to_mailchimp',
					'user-email': $subscribeForm
						.find('#js-mailchimp-email')
						.val(),
					nonce: mailchimp.nonce
				},
				type: 'POST',
				url: mailchimp-subscription.url,
				success: function(response_code) {
					console.log();
					// Reset button
					resetBiForm();

					// Show message for success
					if(response_code === 200) {
						$messageSuccess.addClass('mailchimp__message--show');
					}
					// Show message for failure
					else {
						$messageFail.addClass('mailchimp__message--show');
					}

					// Reset form
					$subscribeForm.trigger('reset');
				},
				error: function(error) {
					// Reset button
					resetBiForm();

					// Show message for failure
					$messageFail.addClass('mailchimp__message--show');

					console.log(error);
				}
			});
		});
	}
});
