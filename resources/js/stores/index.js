import Vue from 'vue';
import Vuex from 'vuex';
import budget from "./modules/budget";
import involvement from "./modules/involvement";
import shipment from "./modules/shipment";

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    budget,
    involvement,
    shipment
  },
  state: {
    requestPath: ''
    // requestPath: 'http://lkstops.loc'
  }
});
