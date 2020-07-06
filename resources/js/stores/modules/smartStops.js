export default {
  namespaced: true,
  state: {
    smartStop: {},
    schedule: []
  },
  actions: {
    /* Получение свойств умной остановки
    *  @param payload smart_stop_api_id
    */
    async show({ commit }, payload) {
      let { api_id } = payload;
      const res = await axios.get(this.state.requestPath + '/api/smart-stops/show/' + api_id)
        .catch(err => console.log('In smartStops/show -', err));

      if (!res.data.errors) {
        commit('setSmartStop', res.data);
        return res.data;
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    },

    /* Получение графика движения транспорта на остановке
    *  @param payload smart_stop_api_id
    */
    async schedule({ commit }, payload) {
      let { api_id } = payload;
      const res = await axios.get(this.state.requestPath + '/api/smart-stops/schedule/' + api_id)
        .catch(err => console.log('In smartStops/schedule -', err));

      return !res.data.errors ? commit('setSchedule', res.data) : { errors: Object.values(res.data.errors).map(item => item[0]) };
    }
  },
  mutations: {
    setSmartStop: (state, obj) => state.smartStop = obj,
    setSchedule: (state, array) => state.schedule = array
  },
  getters: {
    getSmartStop: (state) => state.smartStop,
    getSchedule: (state) => state.schedule
  }
};
