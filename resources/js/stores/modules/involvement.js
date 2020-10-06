import { serialize } from '../../mixins/helpers';

export default {
  namespaced: true,
  state: {
    involvement: []
  },
  actions: {
    /* Получаем вовлечение */
    async updateInvolvement({ commit }, payload) {
      let { regions, periods, version } = payload;
      const req = `?${serialize(regions, 'regions')}&${serialize(periods, 'periods')}&version=${version}`;
      const res = await axios.get(this.state.requestPath + '/involvement/all' + req)
        .catch(err => console.log('In involvement/updateInvolvement -', err));

      if (!res.data.errors) {
        commit('setInvolvement', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    },
    /* Редактируем вовлечение */
    async editInvolvement({ commit }, payload) {
      let { period, version, region, activity, article, param, value } = payload;
      const res = await axios.put(this.state.requestPath + '/involvement', payload)
        .catch(err => console.log('In involvement/editInvolvement -', err));

      if (!res.data.errors) {
        commit('setInvolvement', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    },
    /* Загружаем данные из файла */
    async uploadInvolvement({ commit }, payload) {
      const formData = new FormData();
      for (let param in payload) {
        if (payload.hasOwnProperty(param)) {
          if (param === 'periods') {
            for (let period in payload['periods']) {
              formData.append('periods[' + period + ']', payload[param][period]);
            }
          }
          if (param === 'regions') {
            for (let region in payload['regions']) {
              formData.append('regions[' + region + ']', payload[param][region]);
            }
          } else {
            formData.append(param, payload[param]);
          }
        }
      }

      const res = await axios.post(this.state.requestPath + '/involvement/upload', formData)
        .catch(err => console.log('In budget/uploadInvolvement -', err));

      if (!res.data.errors) {
        commit('setInvolvement', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    }
  },
  mutations: {
    setInvolvement: (state, array) => state.involvement = array,
  },
  getters: {
    getInvolvement: (state) => state.involvement,

  }
};
