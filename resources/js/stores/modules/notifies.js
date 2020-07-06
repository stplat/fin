export default {
  namespaced: true,
  state: {
    notifiesTicker: [],
    notifiesBlock: [],
    notifies: []
  },
  actions: {
    /* Получение уведомлений по типам:
    *  @param payload smart_stop_id
    *  @param payload notify_location = 1 - для бегущей строки
    *  @param payload notify_location = 2 - для основного блока
    *  @param payload notify_location = void - все уведомления
    */
    async index({ commit }, payload) {
      let { smart_stop_id, notify_location = false } = payload;
      const res = await axios.get(this.state.requestPath + '/api/notifies?smart_stop_id=' + smart_stop_id + ( notify_location ? '&notify_location=' + notify_location : '' ))
        .catch(err => console.log('In notifies/index -', err));

      if (!res.data.errors) {
        switch (notify_location) {
          case '1':
            commit('setNotifiesTicker', res.data);
            break;
          case '2':
            commit('setNotifiesBlock', res.data);
            break;
          default:
            commit('setNotifies', res.data);
            break;
        }
      } else {
        return { errors: Object.values(res.data.errors).map(item => item[0]) };
      }
    }

  },
  mutations: {
    setNotifiesTicker: (state, array) => state.notifiesTicker = array,
    setNotifiesBlock: (state, array) => state.notifiesBlock = array,
    setNotifies: (state, array) => state.notifies = array
  },
  getters: {
    getNotifiesTicker: (state) => state.notifiesTicker,
    getNotifiesBlock: (state) => state.notifiesBlock,
    getNotifies: (state) => state.notifies
  }
};
