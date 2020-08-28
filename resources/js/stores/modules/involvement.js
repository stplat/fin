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
      let { period, version, region, activity, article } = payload;
      const res = await axios.put(this.state.requestPath + '/involvement', { period, version, region, activity, article })
        .catch(err => console.log('In involvement/editInvolvement -', err));
console.log(res)
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
