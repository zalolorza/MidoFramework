'use strict';

//polyfills ES5 to ES6
require('./mixins/polyfills');

require('./mixins/scroll_throttled');
require('./mixins/resize_throttled');

//dynamic styles
require('./mixins/dynamic_styles');

//Mailchimp
//require('./mixins/mailchimp_ajax');

//Google Maps
require('./mixins/google_maps');


//Cookies
require('./mixins/cookies');

//scroll
//require('./mixins/scroll_controllers');

//Menu
require('./components/menu');

//Paralaxx
require('./components/scroll_parallax');

//Header
require('./components/header');

//UI
require('./components/ui');
