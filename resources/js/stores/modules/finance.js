import { serialize } from '../../mixins/helpers';

export default {
  namespaced: true,
  state: {
    finances: []
  },
  actions: {
    /* Получаем бюджетные параметры */
    async updateFinances({ commit }, payload) {
      let { periods, version } = payload;
      const req = `?${serialize(periods, 'periods')}&version=${version}`;
      const res = await axios.get(this.state.requestPath + '/finance/all' + req)
        .catch(err => console.log('In finance/updateFinances -', err));
console.log(res)
      if (!res.data.errors) {
        commit('setFinances', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    }

  },
  mutations: {
    setFinances: (state, array) => state.finances = array,
  },
  getters: {
    getFinances: (state) => state.finances,

  }
};
