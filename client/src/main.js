import Vue from 'vue';
import App from './App.vue';
import router from './router';
import vuetify from './plugins/vuetify';
import store from './store'

Vue.config.productionTip = false;

router.beforeEach(function (to, from, next) {
  let privileges = to.meta.privileges;
  if (store.state.session == null) {
    store.dispatch('fetchSession', function (session) {
      if (privileges == "user") {
        if (session.role == "visitor") {
          router.push('/login');
        }
      } else if (privileges == "employee") {
        if (session.role != "employee" && session.role != "admin") {
          router.push('/login');
        }
      } else if (privileges == "admin") {
        if (session.role != "admin") {
          router.push('/login');
        }
      }
    });
  }
  next();
});

new Vue({
  router,
  vuetify,
  store,
  render(h) { return h(App); }
}).$mount('#app');
