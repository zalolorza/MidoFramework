'use strict';

var Backbone = require('backbone');


module.exports = Backbone.Model.extend({

  endpoint: '',

	urlRoot: SITE_URL+'/ajax/',

	url : function(){
    var url = this.urlRoot+this.endpoint;
    url = url + "/?lan=" + ICL_LANGUAGE_CODE;
    return url;
  }

});
