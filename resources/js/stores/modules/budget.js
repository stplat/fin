import { serialize } from '../../mixins/helpers';

export default {
  namespaced: true,
  state: {
    budget: []
  },
  actions: {
    /* Получаем бюджетные параметры */
    async updateBudget({ commit }, payload) {
      let { regions, periods, version } = payload;
      const req = `?${serialize(regions, 'regions')}&${serialize(periods, 'periods')}&version=${version}`;
      const res = await axios.get(this.state.requestPath + '/budget/all' + req)
        .catch(err => console.log('In budget/updateBudget -', err));

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
