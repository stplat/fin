import { serialize } from '../../mixins/helpers';

export default {
  namespaced: true,
  state: {
    materials: []
  },
  actions: {
    /* Переводим материал в статус неликвида */
    async updateMaterials({ commit }, payload) {
      let { article_id } = payload;

      const res = await axios.post(this.state.requestPath + '/material', payload)
        .catch(err => console.log('In material/updateMaterials -', err));

      if (!res.data.errors) {
        commit('setMaterials', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    },
    /* Переводим материал в статус неликвида */
    async push({ commit }, payload) {
      let { id, value } = payload;

      const res = await axios.post(this.state.requestPath + '/material/push', payload)
        .catch(err => console.log('In material/push -', err));

      if (!res.data.errors) {
        commit('setMaterials', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    },
    /* Оформляем заявку на материал */
    async pull({ commit }, payload) {
      let { id, value } = payload;

      const res = await axios.post(this.state.requestPath + '/material/pull', payload)
        .catch(err => console.log('In material/pull -', err));

      if (!res.data.errors) {
        commit('setMaterials', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    },
  },
  mutations: {
    setMaterials: (state, array) => state.materials = array
  },
  getters: {
    getMaterials: (state) => state.materials

  }
};
