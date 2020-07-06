require('./assets/modernizr');
require('./assets/bootstrap');

import Vue from 'vue';
import store from './stores';

/* Mixins */
// Vue.mixin(require('./mixins/helpers').default);
// Vue.mixin(require('./mixins/localization').default);

/* Filters */

/* Views (pages) */
// Vue.component('index', require('./views/Index').default);
// Vue.component('v-header', require('./views/layouts/Header').default);
// Vue.component('v-menu', require('./views/layouts/Menu').default);

/* Components */
// Vue.component('schedule', require('./components/Schedule/index').default);
// Vue.component('ticker', require('./components/Ticker').default);
// Vue.component('ad-block', require('./components/AdBlock').default);
// Vue.component('yandex-map', require('./components/YandexMap').default);
// Vue.component('preloader', require('./components/Preloader').default);
// Vue.component('locale', require('./components/Locale').default);

new Vue({
  name: 'App',
  el: '#app',
  store
});
