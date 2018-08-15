jQuery(function ($) {

     var $subscribeForm = $('#js-mailchimp-form');

     // resetiranje gumba na početnu vrijednost
     function resetBiForm() {
          $submitBtn.removeClass('btn--disabled btn--loading').attr("disabled", false);
     }

     if ($subscribeForm.length) {

          var $submitBtn = $('#js-mailchimp-btn');
          var submitBtnDefaultValue = $submitBtn.text();

          var $messages = $('#mailchimp-messages');
          var $messageSuccess = $messages.find('.mailchimp__message--success');
          var $messageFail = $messages.find('.mailchimp__message--fail');


          // Šalji formu
          $subscribeForm.on('submit', function (e) {
               e.preventDefault();

               // stavi neku animaciju na gumb ili  disabled na njega
               $submitBtn.addClass('btn--disabled btn--loading').attr("disabled", true);

               $.ajax({
                    data: {
                         'action': 'bi_subscribe_user',
                         'user-email': $subscribeForm.find('#js-mailchimp-email').val(),
                         nonce: biAjaxSubscribe.nonce
                    },
                    type: 'POST',
                    url: biAjaxSubscribe.url,
                    success: function (data) {

                         // resetiraj gumb na početnu vrijednost
                         resetBiForm();

                         // pokaži poruku da je poslano
                         $messageSuccess.addClass('mailchimp__message--show');

                         // Google Analytics
                         if (typeof dataLayer !== 'undefined')
                              dataLayer.push({
                                   'event': 'subscribe'
                              });

                         // resetiraj formu
                         $subscribeForm.trigger('reset');
                    },
                    error: function (error) {

                         // resetiraj gumb na početnu vrijednost
                         resetBiForm();

                         // pokaži poruku da nije poslano
                         $messageFail.addClass('mailchimp__message--show');

                         console.log(error);
                    }

               });




          });
     }

});