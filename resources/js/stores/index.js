import Vue from 'vue';
import Vuex from 'vuex';
import budget from "./modules/budget";
import involvement from "./modules/involvement";

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    budget,
    involvement
  },
  state: {
    requestPath: ''
    // requestPath: 'http://lkstops.loc'
  }
});
