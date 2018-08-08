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

        this.$form = $form.eq(0);
        this.$notifications =$form.find('.notification');
        this.$success = $form.find('.success');
        this.$error_mail = $form.find('.error-mail');
        this.$error = $form.find('.error-generic');
        this.$submit = $form.find('input[type="submit"]');
        this.$submit.data('default',this.$submit.val());

        var self = this;

        if ( $form.length > 0 ) {
                $form.find('input[type="submit"]').bind('click', function ( event ) {
                    if ( event ) event.preventDefault();
                    self.registerForm();
                });

                $form.find('.notification').bind('click', function ( event ) {
                    clearTimeout(self.removeAlertAuto);
                    $(this).fadeOut();
                });

            }

    },

    registerForm: function(){


        clearTimeout(this.removeAlertAuto);

        if(!this.$form.validationEngine('validate',{scroll: false})) return false;

        this.$form.addClass('sending');
        this.$notifications.hide();
        this.$submit.val(this.$submit.data('sending')).addClass('sending');

        var self = this;

        this.register(

                    self.$form.serialize(),

                    function(err) {
                            self.$submit.val(self.$submit.data('default')).removeClass('sending');
                            alert("Could not connect to the registration server. Please try again later.");
                        },

                    function(data) {

                                        if (data.result != "success") {

                                            self.error(data);

                                        } else {
                                            self.success();

                                        }
                                    }
                        );



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

        var self = this;

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
            }).always(function(){
                self.$submit.val(self.$submit.data('default')).removeClass('sending');
            });


    },

    error: function(data){

        this.$notifications.hide();

        if(data.msg.indexOf('0 -') !== -1){

            var $error = this.$error_mail;

        } else if(data.result == 'error'){

            var $error = this.$error;
            $error.html(data.msg);

        };

        $error.delay(200).fadeIn();

       this.removeAlertAuto = setTimeout(function(){

            $error.fadeOut();

        },20000);
    },

    success: function(){

        var self = this;

        this.$notifications.hide();

        this.$success.fadeIn(function(){
            self.$form.find('input#mc-email').val("");
            self.$form.find('input#mc-name').val("");
        });

        this.removeAlertAuto = setTimeout(function(){
            self.$success.fadeOut();
        },10000);

    }
};

MailchimpAjax.init();

module.exports = MailchimpAjax;
