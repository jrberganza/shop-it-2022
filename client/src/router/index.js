import Vue from 'vue';
import VueRouter from 'vue-router';
import Home from '../views/Home.vue';
import NotFound from '../views/NotFound.vue';
import ProductPage from '../views/ProductPage.vue';
import Search from '../views/Search.vue';
import Login from '../views/login/Login.vue';
import Register from '../views/login/Register.vue';
import ForgotPassword from '../views/login/ForgotPassword.vue'

Vue.use(VueRouter);

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
  },
  {
    path: '/product/:id',
    name: 'ProductPage',
    component: ProductPage,
  },
  {
    path: '/search/',
    name: 'Search',
    component: Search,
  },
  {
    path: '/login/',
    name: 'Login',
    component: Login,
  },
  {
    path: '/login/forgot/',
    name: 'ForgotPassword',
    component: ForgotPassword,
  },
  {
    path: '/register/',
    name: 'Register',
    component: Register,
  },
  {
    path: '*',
    name: 'NotFound',
    component: NotFound,
  },
];

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  routes,
});

export default router;
