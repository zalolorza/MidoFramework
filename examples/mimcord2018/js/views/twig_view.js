'use strict';

var $ = require('jquery');
var Backbone = require('backbone');
var EventsMap = require('../mixins/events_map');
var Twig = require('twig');
var twig = Twig.twig;


module.exports = Backbone.View.extend({

     twig: null,

     el: null,

     model: null,

     initialize: function (options) {

            

            EventsMap.enqueue(['this.fetchModel','this.loadTwig'],this.render,'renderQueue',this);
            this.fetchModel().loadTwig();

     },

     fetchModel: function () {

          var self = this;

          this.model = new this.model();

          this.model.fetch({

                          processData: true,

                          success: function(model, response, options) {

                              if(!response) {
                                  self.model.attributes = response;
                              }
                              
                              self.trigger('fetchModel');
                           
                         },

                         error: function(model, response, options){

                            console.log(response.responseText);

                         }
                });

          return this;
     },

     loadTwig: function(){

        var self = this;

        twig({

                href: THEME_URI+this.twig,
                async: true,

                load: function(template) {

                    self.twigTemplate = template;
                    self.trigger('loadTwig');

                 }
        });

        return this;
     },

     render: function(){


        var data = this.model.toJSON();


        if(data) {
            var postsHTML = this.twigTemplate.render(data);
            this.$el.html(postsHTML);
            this.callback();
        };

     },

     callback: function(){

     }
});
