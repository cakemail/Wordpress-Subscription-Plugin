/**
*
*  This code controls the Newsletter subscription beaviors
*
**/

// This variable will be used during the subscribing process to keep the button text while it is changed to "Loading"
var original_button_text = "";

/*
 *  This function is used to change input elements style to show them
 *  as disabled
 *
 *  @id -> The id of the block
 */
var disable_inputs = function( form ) {
  form.find('input').each(function(){
    jQuery(this).attr('disabled', true);
  });

  var button = form.find('input[type="submit"]');
  original_button_text = button.val()
  button.val('Loading');
}

/*
 *  This function is used to change input elements style to show them
 *  as enabled
 *
 *  @id -> The id of the block
 */
var enable_inputs = function( form ) {
  form.find('input').each(function(){
    jQuery(this).attr('disabled', false);
  });

  form.find('input[type="submit"]').val(original_button_text);
}

/*
 *  This function is used to change email element style to show it
 *  as error
 *
 *  @id -> The id of the block
 */
var error_mode_inputs = function( form, mess ) {
  var error_box = form.parent().find('.error-box');
  error_box.html(mess);
  error_box.show(0);
}

/*
 *  This function is used to change email element style to show it
 *  as no error
 *
 *  @id -> The id of the block
 */
var normal_mode_inputs = function( id ) {
  //jQuery( '#opt_email_' + id ).removeClass('error');
}

/*
 *  After a success subscription this function should be called to switch
 *  the view to show the confirmation message
 *
 *  @id -> The id of the block
 */
var show_confirmation = function( id ) {
  var container = id.parent().parent();
  container.find(".newsletter_subscription_form").hide(0);
  container.find(".newsletter_confirmation_message").show(0);
}

/*
 *  Submit click event handler
 *  Call the API proxy to subscribe the email
 */
jQuery('.submit_newsletter_subscribe').on('click', function(e) {
  var form = jQuery(this).parent();

  disable_inputs( form );

  var data = {};
  form.children('input').each(function(){
    data[this.name.substring(this.name.lastIndexOf('[')+1, this.name.lastIndexOf(']'))] = this.value;
  });

  jQuery.ajax({
    url : "/wp-content/plugins/newsletter-subscription-form/newsletter-subscription-form-subscribe.php",
    method : 'post',
    data : data,
    success : function ( jqXHR, status_text ){
      if( jqXHR != 1 ) {
        var reg = /message \'(.*?)\'/;
        var mess = reg.exec(jqXHR);

        enable_inputs( form );
        error_mode_inputs( form, mess[1] );
      } else {
        show_confirmation( form );
      }
    },
    error : function ( jqXHR, status_text ) {
      enable_inputs( form );
      error_mode_inputs( form );
    }
  });
});
