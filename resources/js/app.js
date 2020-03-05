require('./bootstrap');

window.Vue = require('vue');

import App from './views/App.vue';
import Home from './views/Home.vue';
import CreateIssue from './views/issues/Create.vue';
import ElHoumaMap from './components/ElHoumaMap.vue';
import { LMap, LTileLayer, LMarker } from 'vue2-leaflet';
import VueRouter from 'vue-router'

Vue.use(VueRouter)

Vue.component('l-map', LMap);
Vue.component('l-tile-layer', LTileLayer);
Vue.component('l-marker', LMarker);
Vue.component('elhouma-map', ElHoumaMap);
import 'leaflet/dist/leaflet.css';

const router = new VueRouter({
    mode: 'history',
    routes: [
        {
            path: '/',
            name: 'home',
            component: Home
        },
        {
            path: '/issues/create',
            name: 'issues.create',
            component: CreateIssue,
        },
    ],
});

const app = new Vue({
  el: '#app',
  components: { App },
  router,
});
