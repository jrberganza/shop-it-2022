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
    async fetchSession({ commit }) {
      const res = await fetch("/api/session/current.php");
      const json = await res.json();

      if (json.success) {
        commit('updateSession', json);
      } else {
        commit('updateSession', { role: 'visitor' });
      }
    }
  },
  modules: {
  }
});
