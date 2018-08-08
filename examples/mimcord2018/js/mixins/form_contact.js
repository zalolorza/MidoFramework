'use strict';

var $ = require('jquery');
var MailchimpAjax = require('../mixins/mailchimp_ajax');
require('bootstrap');
require('bootstrap-select');


var ContactForm = {

    isInit : false,

    init: function(selector){

			alert('hail');

    		var self = this;

    		this.$form = $(selector);
    		this.$form.data('sending',false);
        this.$success = this.$form.find('.form-success');
        this.$error = this.$form.find('.form-error');
    		this.input = {

    			'newsletter' : this.$form.find('#newsletterCheckbox'),
    			'submit': this.$form.find('input[type="submit"]'),
    			'submitValue':  this.$form.find('input[type="submit"]').val()
    		}

    		this.$form.submit(function(e){

    			e.preventDefault();
    			self.sumbitForm();

    		});

    		this.input.submit.click(function(e){

    			if($(this).hasClass('success')){
    				e.preventDefault();
    				self.resetSubmit();
    			}

    		});

        this.$form.find('.selectpicker').selectpicker();

    },


    sumbitForm: function(){

	    	var self = this;

	    	if(this.$form.data('sending')) return false;

	    	if(!this.$form.validationEngine('validate',{scroll: false})) return false;

	    	this.$form.data('sending',true);
	    	this.$form.addClass('sending');
	    	this.input.submit.val(this.input.submit.data('sending')).addClass('sending');

	    	if(this.input.newsletter.is(':checked')) {

	    					MailchimpAjax.registerEmail($('#contact-form input[type="email"]').val());

			  	}

	    	$.ajax({
				type: this.$form.attr('method'),
				data: this.$form.serialize(),
				url: this.$form.attr('action')

		}).done(function(){

			     self.success();

		}).fail(function() {

		    	self.error();

		}).always(function() {

		    	self.$form.data('sending',false);
    			self.$form.removeClass('sending');
          self.input.submit.val(self.input.submitValue).removeClass('sending');

    			setTimeout(function(){

            if(self.$form.data('sending')) return;
            self.$success.fadeOut();
            self.$error.fadeOut();

	    		},10000);

		  });

    },

    error: function(data){

    		var self = this;
        this.$success.hide();
    		this.$error.fadeIn();

    },

    success: function(){

    		var self = this;
        this.$error.hide();
    		this.$success.fadeIn();

     		this.$form[0].reset();

    }
};

$(function(){
    ContactForm.init('#contact-form');
});

module.exports = ContactForm;
