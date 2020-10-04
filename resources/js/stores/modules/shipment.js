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
    /* Загружаем данные из файла */
    async uploadShipments({ commit }, payload) {
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

      const res = await axios.post(this.state.requestPath + '/shipment/upload', formData)
        .catch(err => console.log('In shipment/uploadShipments -', err));

      console.log(res)
      if (!res.data.errors) {
        commit('setShipments', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    }
  },
  mutations: {
    setShipments: (state, array) => state.shipments = array,
  },
  getters: {
    getShipments: (state) => state.shipments,
  }
};
