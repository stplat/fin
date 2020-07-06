import Vue from 'vue';
import Vuex from 'vuex';
import locale from '../mixins/localization/store/locales';
import notifies from './modules/notifies';
import smartStops from './modules/smartStops';

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    locale,
    notifies,
    smartStops
  },
  state: {
    requestPath: 'http://lkstops3.optimagp66.ru'
    // requestPath: 'http://lkstops.loc'
  }
});
