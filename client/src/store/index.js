import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    session: null,
    searchRequest: {
      query: '',
      shopCategories: [],
      productCategories: [],
      department: '',
      municipality: '',
      zone: '',
    },
  },
  mutations: {
    updateSession(state, session) {
      state.session = session;
    },
    updateSearchRequest(state, searchRequest) {
      state.searchRequest = searchRequest;
    },
  },
  actions: {
    async fetchSession({ commit }, redirectCallback) {
      const res = await fetch("/api/session/current.php");
      const json = await res.json();

      if (json.success) {
        commit('updateSession', json);
        if (redirectCallback) redirectCallback(json);
      } else {
        commit('updateSession', { role: 'visitor' });
        if (redirectCallback) redirectCallback({ role: 'visitor' });
      }
    }
  },
  modules: {
  }
});
