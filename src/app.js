import Vue from 'vue'
import App from './App.vue'
import Carousel3d from 'vue-carousel-3d';

Vue.use(Carousel3d);

new Vue({
  el: '#lastfm',
  render: h => h(App)
})