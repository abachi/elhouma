require('./bootstrap');

window.Vue = require('vue');

import App from './components/App.vue';
import ElHoumaMap from './components/ElHoumaMap.vue';
import { LMap, LTileLayer, LMarker } from 'vue2-leaflet';

Vue.component('l-map', LMap);
Vue.component('l-tile-layer', LTileLayer);
Vue.component('l-marker', LMarker);
Vue.component('elhouma-map', ElHoumaMap);
import 'leaflet/dist/leaflet.css';

const app = new Vue({
  el: '#app',
  components: {
    App,
    LMap,
    LTileLayer,
    LMarker
  },
  render: h => h(App)
});
