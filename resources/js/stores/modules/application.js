import { serialize } from '../../mixins/helpers';

export default {
  namespaced: true,
  state: {
    applications: []
  },
  actions: {
    /* Получаем бюджетные параметры */
    async updateApplications({ commit }, payload) {
      let { periods, article, version_budget, version_involvement, version_f22, version_shipment } = payload;
      const req = `?${ serialize(periods, 'periods') }&article=${ article }&version_budget=${ version_budget }
      &version_involvement=${ version_involvement }&version_f22=${ version_f22 }&version_shipment=${ version_shipment }`;

      const res = await axios.get(this.state.requestPath + '/application/all' + req)
        .catch(err => console.log('In finance/updateApplications -', err));

      if (!res.data.errors) {
        commit('setApplications', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    },
    /* Редактируем вовлечение */
    async editApplication({ commit }, payload) {
      let { periods, article, versionBudget, versionInvolvement, versionF22, versionShipment, param, value } = payload;
      const res = await axios.put(this.state.requestPath + '/application', payload)
        .catch(err => console.log('In application/editApplication -', err));

      if (!res.data.errors) {
        commit('setApplications', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    },
    /* Загружаем данные из файла */
    async uploadApplication({ commit }, payload) {
      const formData = new FormData();
      for (let param in payload) {
        if (payload.hasOwnProperty(param)) {
          if (param === 'periods') {
            for (let period in payload['periods']) {
              formData.append('periods[' + period + ']', payload[param][period]);
            }
          } else {
            formData.append(param, payload[param]);
          }
        }
      }

      const res = await axios.post(this.state.requestPath + '/application/upload', formData)
        .catch(err => console.log('In application/uploadApplication -', err));

      if (!res.data.errors) {
        commit('setApplications', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    },
    /* Редактируем вовлечение */
    async consolidateApplication({ commit }, payload) {
      let { periods, article, versionBudget, versionInvolvement, versionF22, versionShipment, param, value } = payload;
      const res = await axios.post(this.state.requestPath + '/application/consolidate', payload)
        .catch(err => console.log('In application/consolidateApplication -', err));

      if (!res.data.errors) {
        commit('setApplications', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    },
    /* Экспорт */
    async exportApplications({ commit }, payload) {
      let { period } = payload;
      const res = await axios.post(this.state.requestPath + '/application/export', payload)
        .catch(err => console.log('In application/exportApplication -', err));

      if (!res.data.errors) {
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    }
  },
  mutations: {
    setApplications: (state, array) => state.applications = array
  },
  getters: {
    getApplications: (state) => state.applications

  }
};
