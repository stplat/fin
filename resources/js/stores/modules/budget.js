import { serialize } from '../../mixins/helpers';

export default {
  namespaced: true,
  state: {
    budget: []
  },
  actions: {
    /* Получаем бюджетные параметры */
    async updateBudget({ commit }, payload) {
      let { regions, periods, version, version_involvement } = payload;
      const req = `?${serialize(regions, 'regions')}&${serialize(periods, 'periods')}
      &version=${version}&version_involvement=${version_involvement}`;

      const res = await axios.get(this.state.requestPath + '/budget/all' + req)
        .catch(err => console.log('In budget/updateBudget -', err));

      if (!res.data.errors) {
        commit('setBudget', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    },
    /* Редактируем вовлечение */
    async editBudget({ commit }, payload) {
      const res = await axios.put(this.state.requestPath + '/budget', payload)
        .catch(err => console.log('In budget/editBudget -', err));
console.log(res);
      if (!res.data.errors) {
        commit('setBudget', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    },
    /* Загружаем данные из файла */
    async uploadBudget({ commit }, payload) {
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

      const res = await axios.post(this.state.requestPath + '/budget/upload', formData)
        .catch(err => console.log('In budget/uploadBudget -', err));

      if (!res.data.errors) {
        commit('setBudget', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    }
  },
  mutations: {
    setBudget: (state, array) => state.budget = array,
  },
  getters: {
    getBudget: (state) => state.budget,

  }
};
