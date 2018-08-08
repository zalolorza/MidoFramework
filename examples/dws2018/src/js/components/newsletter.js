'use strict';

var $ = require('jquery');


var MailchimpAjax = {

    isInit : false,

    init: function(){

        $(document).ready( function () {
            
            MailchimpAjax.listen();
     
        });

    },

    listen: function(){

        if(this.isInit) return;
        this.isInit = true;

        var $form = $('#mailchimp-form');

        this.$form = $form;
        var self = this;

        if ( $form.length > 0 ) {
                $form.find('[type="submit"]').bind('click', function ( event ) {
                    if ( event ) event.preventDefault();
                    MailchimpAjax.registerForm($form);
                });

                $form.find('.error, .success').bind('click', function ( event ) {
                    if ( event ) event.preventDefault();
                    clearTimeout(self.removeAlertAuto);
                    $(this).fadeOut();
                    $form.find('input#mc-email').focus();
                });

            }

    },

    registerForm: function($form){

        clearTimeout(this.removeAlertAuto);

        $form.addClass('sending');
        $form.find('.notification').hide();
        $form.find('.sending').fadeIn(200);

        this.register(

                    $form.serialize(),

                    function(err) {
                            $form.find('.sending').fadeOut(); 
                            MailchimpAjax.error("Could not connect to the registration server. Please try again later.");
                        },

                    function(data) {
                                        
                                        if (data.result != "success") {

                                            MailchimpAjax.error(data);
                                           
                                        } else {
                                            MailchimpAjax.success(data);
                                            
                                        }
                                    }
                        );



    },

    registerEmail: function(email){

        this.register('EMAIL='+email);

    },

    register: function(data, error, success){


        if(typeof error != 'function'){
            var error = function() {
              console.log("Maichimp error");
            };
        }

        if(typeof success != 'function'){
            var success = function() {
             
            };
        }


        $.ajax({
                type: 'get',
                url: this.$form.attr('action'),
                data: data,
                cache       : false,
                dataType    : 'json',
                crossDomain : true,
                contentType: "application/json; charset=utf-8",
                error       : error ,
                success     : success
            });


    },

    error: function(data){

        this.$form.find('.sending').fadeOut(100);

        if(typeof data.msg == 'undefined' ){
            data = {
                msg : data
            }
        }



        if(data.msg.indexOf('0 -') !== -1){

            //var $error = this.$form.find('.error-mail');
            var $error = this.$form.find('.error-general').html(data.msg.replace(/0 -/g,''));

        } else if(data.msg.indexOf('is already subscribed') !== -1){

            //var $error = this.$form.find('.error-already-suscribed');

            var $error = this.$form.find('.error-general').html(data.msg);

        } else {

            var $error = this.$form.find('.error-general').html(data.msg);
           
        }

        $error.delay(300).fadeIn();

        this.removeAlertAuto = setTimeout(function(){

            $error.fadeOut();

        },20000);
    },

    success: function(data){


        var $form = this.$form;
        $form.find('.sending').fadeOut(100);

        var $success = this.$form.find('.success').html(data.msg);

        $success.delay(100).fadeIn(function(){
            $form.find('input#mc-email').val("");
        });

        this.removeAlertAuto = setTimeout(function(){

            $success.fadeOut();

        },10000);

    }
};

MailchimpAjax.init();

module.exports = MailchimpAjax;
