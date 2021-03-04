require('./assets/bootstrap');
require('./assets/modernizr');
require('./assets/aside-slide');
require('./assets/login-dropdown');
require('./assets/menu-dropdown');

import Vue from 'vue';
import store from './stores';

/* Mixins */
Vue.mixin(require('./mixins/variables').default);
Vue.mixin(require('./mixins/helpers').default);

/* Plugins */
import Union from './plugins/Unicon';
import './plugins/VtTables';

/* Components */
Vue.component('alert', require('./components/Alert').default);
Vue.component('application', require('./components/Application').default);
Vue.component('budget', require('./components/Budget').default);
Vue.component('finance', require('./components/Finance').default);
Vue.component('involvement', require('./components/Involvement').default);
Vue.component('order-material', require('./components/OrderMaterial').default);
Vue.component('popup', require('./components/Popup').default);
Vue.component('preloader', require('./components/Preloader').default);
Vue.component('shipment', require('./components/Shipment').default);
Vue.component('v-table', require('./components/VTable').default);
Vue.component('warehouse', require('./components/Warehouse').default);
Vue.component('unused', require('./components/Unused').default);

const app = new Vue({
  el: '#app',
  Union,
  store
});
