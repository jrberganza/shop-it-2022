import Vue from 'vue';
import App from './App.vue';
import router from './router';
import vuetify from './plugins/vuetify';
import store from './store'

Vue.config.productionTip = false;

router.beforeEach(function (to, from, next) {
  store.dispatch('fetchSession');
  next();
});

new Vue({
  router,
  vuetify,
  store,
  render(h) { return h(App); }
}).$mount('#app');
