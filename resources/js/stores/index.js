import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
  },
  state: {
    requestPath: 'http://lkstops3.optimagp66.ru'
    // requestPath: 'http://lkstops.loc'
  }
});
