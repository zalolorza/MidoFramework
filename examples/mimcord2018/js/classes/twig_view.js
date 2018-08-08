'use strict';

var $ = require('jquery');
var Backbone = require('backbone');
var _ = require('underscore');
var _h = require('../mixins/helper');
Backbone.$ = $;
var Twig = require('twig');
var twig = Twig.twig;


var EventsMap = require('../state/events_map');
var Cache = require('../state/cache');
var Router = require('../state/router');



module.exports = Backbone.View.extend({

     isoRender : false,   //if true means rendered on server

     twig: {
          main: '',       //main twig file
          included: ''    //main twig file
     },

     choreo_default: {                    //this.event means the event is mapped from the current object. if not, the event is mapped from mapFrom (map by default)

          map: EventsMap,                 //the main events are mapped from/to another object
          compile: null,                  //events queue before compiling html+twig (string if single, array if multiple)
          render: 'switchViews'           //events queue before rendering (string if single, array if multiple)
          //kill: 'switchViews',          //events queue that kills the view. Only listens before 'render'. (string if single, array if multiple) 

     },

     init: function(){

     },

     initialize: function (options) {

          this.init(options);

          this.options = options;

          if(typeof options.firstPage != 'undefined') this.isoRender = options.firstPage;

          if(typeof this.modelClass != 'undefined'){

               this.dataType = 'model';

          } else if(typeof this.collectionClass != 'undefined') {

                this.dataType = 'collection';

          }
         
          _.once(this.choreographer());

          
     },

     choreographer: function(){

        var self = this;

        this.choreographerListener();

        this.choreo = _.extend(this.choreo_default,this.choreo);


          if(typeof this.choreo.mapTo == 'undefined') this.choreo.mapTo == this.choreo.map;
          if(typeof this.choreo.mapFrom == 'undefined') this.choreo.mapFrom == this.choreo.map;


          //compile choreography
      
          this.choreo.compile = _h.union(this.choreo.compile,['this.loadTwig','this.fetch']);

          EventsMap.enqueue(this.choreo.compile,this.compile,'viewCompile',this,this.choreo.mapFrom);


          //render choreography

          this.choreo.render = _h.union(this.choreo.render,'this.compile');

          EventsMap.enqueue(this.choreo.render, this.render,'viewRender',this,this.choreo.mapFrom); 


          //start choreography
          if(!this.isoRender) {

               this.loadTwig().fetch();

          }; 

          if(typeof this.choreo.kill == 'undefined' && self.choreo.mapFrom == EventsMap) this.choreo.kill == 'switchViews';
          
          if(this.choreo.kill){

              this.choreo.kill = _h.arrayify(this.choreo.kill);

              this.on('render',function(){

                  EventsMap.enqueue(self.choreo.kill, self.killAll,'viewKill',self,self.choreo.mapFrom);

              });

          }  

     },

     choreographerListener: function(){

        var self = this;

        $.each(this.events, function( key, value ) {

            if($.inArray( key, ['compile','render','kill','fetch']) == -1) return;

            self.on(key,function(){

           
                if(typeof value == 'string'){

                        self[value].call();

                } else {

                        value.call();

                }
                

            });
              
        });
      
     },

     triggerEvent: function(eventName){

          this.trigger(eventName);

          if(this.choreo.mapTo){

               var globalEventName = 'view'+eventName.charAt(0).toUpperCase() + eventName.slice(1);

               this.choreo.mapTo.trigger(globalEventName);

          }

     },


     fetch: function(){

          switch(this.dataType ){

               case 'model':
                    this.fetchModel();
                    break;

               case 'collection':
                    this.fetchCollection();
                    break;

          }

     },

     fetchModel: function () {

          var self = this;


          if(Cache.models[this.options.id]){

                this.model = Cache.models[this.options.id];
                this.trigger('fetch');
                return;

          } else {
                
                this.model = new this.modelClass({id:this.options.id});

                this.model.fetch({

                          processData: true,

                          success: function(model, response, options) {

                              self.model.fetched = true;
                              
                              Cache.models[model.attributes.id] = model;

                              self.trigger('fetch');

                         },

                         error: function(model, response, options){
                           
                            console.log(response.responseText);

                         }
                });
          }  

          

     },

     fetchCollection: function(){

           this.trigger('fetch');

     },

     loadTwig: function(){

          var self = this;

          if(typeof this.twig.included == 'undefined'){

               this.twig.included = [];

          }

          this.twig.includedLength = this.twig.included.length;

          if(this.twig.includedLength == 0) this.loadMainTwig();

          $.each(this.twig.included,function(index, file){

               twig({

                      href: file,
                      async: true,

                      load: function(template) { 

                         self.twig.includedLength--;

                         if(self.twig.includedLength == 0){

                                   self.loadMainTwig();

                         }

                      }
               });

          });

          return this;
     },

     loadMainTwig: function(){

        var self = this;

        twig({

                href: this.twig.main,
                async: true,

                load: function(template) { 

                    self.twigTemplate = template;
                    self.trigger('loadTwig');

                 }
        });

     },

     compile: function () {

          this.trigger('beforeCompile');

           switch(this.dataType ){

               case 'model':
                    this.compileModel();
                    break;

               case 'collection':
                    this.compileCollection();
                    break;

          }

          this.triggerEvent('compile');

     },

     compileModel: function(){

          var data = this.model.returnData();

          this.updateMeta();

          var postsHTML = this.twigTemplate.render(data);

          this.$compiledContent = $(postsHTML); 

     },

     compileCollection: function(){

          //

     },

     updateMeta: function(){

         var data = this.model.returnData();
         if(typeof data.title == 'undefined') return;
         var wp_title = data.title;
         var e = document.createElement('div');
         e.innerHTML = wp_title;
         wp_title = e.childNodes[0].nodeValue;
         document.title = wp_title;

      },


     render: function(){

          if(this.isoRender){

               this.triggerEvent('compile');

          } else {

               this.$el.html(this.$compiledContent);

          }
          
          this.triggerEvent('render');

      },


     killAll: function(){

          this.stopListening();

          this.trigger('kill');

          this.killed = true;

     },

});
