<template>
  <div class="grav-plugin-lastfm" :style="{'background': background_color}">
    <div v-if="isLoading" class="lastfm-flex-container">
      <AppLoader :height="height" />
    </div>
    <div class="lastfm-toolbar" v-show="!isLoading">
      <ReloadButton :onclick="fetchData" :width="16" :height="16" :color="yiqBackground" :errorHandler="this.displayError"/>
    </div>
    <carousel-3d v-if="!isLoading" ref="carousel" :count="slides.length" :width="width" :height="height"
                :onMainSlideClick="toggleInfo" @before-slide-change="defaultMainSlide" :display="display" :loop="true">
        <slide v-for="(slide, i) in slides" :index="i" :key="i">
          <template slot-scope="{ isCurrent }">
            <figure class="lastfm-figure">
              <img :src="slide.image">
              <div class="lastfm-info-helper">
                <transition name="fade">
                  <button v-show="!isInfo && isCurrent">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"><path fill="#fefefe" d="M12 2c5.514 0 10 4.486 10 10s-4.486 10-10 10-10-4.486-10-10 4.486-10 10-10zm0-2c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm-.001 5.75c.69 0 1.251.56 1.251 1.25s-.561 1.25-1.251 1.25-1.249-.56-1.249-1.25.559-1.25 1.249-1.25zm2.001 12.25h-4v-1c.484-.179 1-.201 1-.735v-4.467c0-.534-.516-.618-1-.797v-1h3v6.265c0 .535.517.558 1 .735v.999z"/></svg>
                  </button>
                </transition>
              </div>
              <transition name="fade">
                <figcaption class="lastfm-flex-container lastfm-info" v-show="isInfo && isCurrent">
                  <div class="lastfm-flex-box lastfm-info-box">
                    <h3 class="lastfm-info-title">{{ slide.name }}</h3>
                    <p class="lastfm-info-album">{{ slide.album }}</p>
                    <p class="lastfm-info-artist">{{ slide.artist }}</p>
                  </div>
                  <div class="lastfm-flex-box lastfm-links">
                    <a :href="slide.url" target="_blank" alt="Last.fm" aria-label="Last.fm">
                      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#fefefe" d="M10.584 17.209l-.88-2.392s-1.43 1.595-3.573 1.595c-1.897 0-3.244-1.65-3.244-4.289 0-3.381 1.704-4.591 3.382-4.591 2.419 0 3.188 1.567 3.849 3.574l.88 2.75c.879 2.667 2.528 4.811 7.284 4.811 3.409 0 5.719-1.044 5.719-3.793 0-2.227-1.265-3.381-3.629-3.932l-1.76-.385c-1.209-.275-1.566-.77-1.566-1.594 0-.935.742-1.485 1.952-1.485 1.319 0 2.034.495 2.144 1.677l2.749-.33c-.22-2.474-1.924-3.491-4.729-3.491-2.474 0-4.893.935-4.893 3.931 0 1.87.907 3.052 3.188 3.602l1.869.439c1.402.33 1.869.907 1.869 1.705 0 1.017-.989 1.43-2.858 1.43-2.776 0-3.932-1.457-4.591-3.464l-.907-2.749c-1.155-3.574-2.997-4.894-6.653-4.894-4.041-.001-6.186 2.556-6.186 6.899 0 4.179 2.145 6.433 5.993 6.433 3.107.001 4.591-1.457 4.591-1.457z"/></svg>
                    </a>
                  </div>
                </figcaption>
              </transition>
              <NowPlaying v-if="slide.nowplaying === true"/>
            </figure>
          </template>
        </slide>
    </carousel-3d>
    <transition name="fade" mode="out-in">
      <div v-if="isError" class="lastfm-flex-container">
        <div class="lastfm-error">
          {{ getError }}
          <button class="lastfm-error-close" @click="cleanErrorMessage">&#x2715;</button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import AppLoader from './AppLoader.vue';
import ReloadButton from './ReloadButton.vue';
import NowPlaying from './NowPlaying.vue';
import utils from './utils.js';
import './styles.css';

export default {
  components: {
    AppLoader,
    ReloadButton,
    NowPlaying,
  },
  created() {
    this.fetchData()
  },
  methods: {
    goToSlide(index) {
      this.$refs.carousel.goSlide(index)
    },
    defaultMainSlide() {
      this.isInfo = false;
    },
    fetchData() {
      const url = this.appPath || '/_lastfm';
      fetch(url)
        .then(response => {
          if (!response.ok)
            return Promise.reject(response)
          
          return response.json()
        })
        .then(data => this.completeResponse(data))
        .catch(err => this.errorResponse(err));
    },
    completeResponse: function(data) {

      if (data.width)
        this.width = data.width

      if (data.height)
        this.height = data.height

      if (data.display)
        this.display = data.display

      if (data.background_color)
        this.background_color = data.background_color

      /* TODO check diff and add required slides + go auto nowplaying? */
      if (data.slides)
        this.slides = data.slides

      if (data.error) {
        this.error = data.error
        this.isError = true

      } else {
        this.isLoading = false
      }
      
    },
    errorResponse(err) {
      this.isLoading = false;
      this.displayError();
    },
    displayError() {
      this.isError = true;
    },
    cleanErrorMessage() {
      this.isError = false;
      this.error = null
    },
    toggleInfo() {
      this.isInfo = !this.isInfo
    }
  },
  computed: {
    yiqBackground() { return utils.yiq(this.background_color || 'transparent')},
    appPath() {return window.lastfm_path},
    getError() {
      if (!this.error) {
        return 'Unknown error ocurred...'
      }
      return this.error
    },
    isNowPlaying() {
      this.slides.some(x => x.nowplaying == true)
    }
  },
  data() {
    return {
      slides: [],
      isLoading: true,
      isInfo: false,
      isError: false,
      width: 280,
      height: 280,
      display: 7,
      background_color: 'transparent',
      error: null
    }
  }
}
</script>