import { serialize } from '../../mixins/helpers';

export default {
  namespaced: true,
  state: {
    shipments: []
  },
  actions: {
    /* Получаем вовлечение */
    async updateShipments({ commit }, payload) {
      let { regions, periods, version } = payload;
      const req = `?${serialize(regions, 'regions')}&${serialize(periods, 'periods')}&version=${version}`;
      const res = await axios.get(this.state.requestPath + '/shipment/all' + req)
        .catch(err => console.log('In shipment/updateShipments -', err));

      if (!res.data.errors) {
        commit('setShipments', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    },
  },
  mutations: {
    setShipments: (state, array) => state.shipments = array,
  },
  getters: {
    getShipments: (state) => state.shipments,

  }
};
