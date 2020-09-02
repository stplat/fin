import Vue from 'vue';
import Vuex from 'vuex';
import budget from "./modules/budget";
import involvement from "./modules/involvement";
import shipment from "./modules/shipment";
import finance from "./modules/finance";
import application from "./modules/application";

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    budget,
    involvement,
    shipment,
    finance,
    application
  },
  state: {
    requestPath: ''
    // requestPath: 'http://lkstops.loc'
  }
});
