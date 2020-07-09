export default {
  namespaced: true,
  state: {
    budget: {}
  },
  actions: {
    /* Получение уведомлений по типам:
    *  @param payload smart_stop_id
    *  @param payload notify_location = 1 - для бегущей строки
    *  @param payload notify_location = 2 - для основного блока
    *  @param payload notify_location = void - все уведомления
    */
    async updateBudget({ commit }, payload) {
      let { period, version, isDkre = 0 } = payload;

      const res = await axios.get(this.state.requestPath + '/budget/all?period=' + period + '&version=' + version + '&is_dkre=' + isDkre)
        .catch(err => console.log('In budget/updateBudget -', err));

      return !res.data.errors ? commit('setBudget', res.data) : { errors: Object.values(res.data.errors).map(item => item[0]) };
    }

  },
  mutations: {
    setBudget: (state, array) => state.budget = array,
  },
  getters: {
    getBudget: (state) => state.budget,

  }
};
