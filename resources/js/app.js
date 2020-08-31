require('./assets/bootstrap');
require('./assets/modernizr');
require('./assets/aside-slide');
require('./assets/login-dropdown');

import Vue from 'vue';
import store from './stores';

/* Mixins */
Vue.mixin(require('./mixins/variables').default);
Vue.mixin(require('./mixins/helpers').default);

/* Plugins */
import Union from './plugins/Unicon';

/* Components */
Vue.component('alert', require('./components/Alert').default);
Vue.component('budget', require('./components/Budget').default);
Vue.component('involvement', require('./components/Involvement').default);
Vue.component('preloader', require('./components/Preloader').default);
Vue.component('shipment', require('./components/Shipment').default);

const app = new Vue({
  el: '#app',
  Union,
  store
});
