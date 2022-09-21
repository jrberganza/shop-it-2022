import Vue from 'vue';
import VueRouter from 'vue-router';
import Home from '../views/Home.vue';
import Search from '../views/Search.vue';
import Login from '../views/login/Login.vue';
import ForgotPassword from '../views/login/ForgotPassword.vue';
import Register from '../views/login/Register.vue';
import ProductPage from '../views/product/ProductPage.vue';
import ShopPage from '../views/shop/ShopPage.vue';
import NotFound from '../views/NotFound.vue';

Vue.use(VueRouter);

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
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
    path: '/product/:id',
    name: 'ProductPage',
    component: ProductPage,
  },
  {
    path: '/shop/:id',
    name: 'ShopPage',
    component: ShopPage,
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
