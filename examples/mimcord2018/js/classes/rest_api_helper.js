'use strict';

var $ = require('jquery');


module.exports = {

	hydrate : function(data, dataContainer){


		var return_data = {};
		return_data[dataContainer] = {};


		if(data.acf){

			if(data.custom_acf){
				data.acf = Object.assign({}, data.acf, data.custom_acf);
				delete data.custom_acf;
			}

			return_data[dataContainer] = Object.assign({}, return_data[dataContainer], data.acf);
			delete data.acf;

		}
		
	

		if(data[dataContainer]){

			if(data[dataContainer]['featured_media']){
				delete data.featured_media;
			}

			return_data[dataContainer] = Object.assign({}, return_data[dataContainer], data[dataContainer]);
			delete data[dataContainer];
		} 


		if(data.title){

			return_data[dataContainer]['title']= data.title.rendered; 
			delete data.title;

		}

		if(data.content){
			return_data[dataContainer]['post_content']= data.content.rendered; 
			return_data[dataContainer]['content'] = data.content.rendered; 
			delete data.content;
		}

		if(data.browser) {

			return_data['browser'] = data.browser;
			delete data.browser;

		}
	    	

		return_data[dataContainer] = Object.assign({}, return_data[dataContainer], data);

		return return_data;

	}

};