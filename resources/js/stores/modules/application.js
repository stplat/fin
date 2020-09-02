import { serialize } from '../../mixins/helpers';

export default {
  namespaced: true,
  state: {
    applications: []
  },
  actions: {
    /* Получаем бюджетные параметры */
    async updateApplications({ commit }, payload) {
      let { periods, article, version, version_budget, version_involvement, version_f22, version_shipment } = payload;
      const req = `?${serialize(periods, 'periods')}&article=${article}&version=${version}&version_budget=${version_budget}
      &version_involvement=${version_involvement}&version_f22=${version_f22}&version_shipment=${version_shipment}`;

      const res = await axios.get(this.state.requestPath + '/application/all' + req)
        .catch(err => console.log('In finance/updateApplications -', err));
      console.log(res)

      if (!res.data.errors) {
        commit('setApplications', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    }

  },
  mutations: {
    setApplications: (state, array) => state.applications = array,
  },
  getters: {
    getApplications: (state) => state.applications,

  }
};
