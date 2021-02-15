import { serialize } from '../../mixins/helpers';

export default {
  namespaced: true,
  state: {
    materials: []
  },
  actions: {
    /* Меняем значения неликвида у материала */
    async toUnused({ commit }, payload) {
      let { id, value } = payload;

      const res = await axios.post(this.state.requestPath + '/material/to-unused', payload)
        .catch(err => console.log('In material/toUnused -', err));

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
