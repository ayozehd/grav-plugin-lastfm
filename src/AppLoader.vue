<template>
    <div class="grav-plugin-lastfm-app-loader" :style="{'height': height}"><div></div><div></div><div></div></div>
</template>

<script>
export default {
  props: {
    color: {
      type: String,
      default: '#333'
    },
    height: {
      type: Number,
      default: 280
    }
  }
}
</script>

<style>
.grav-plugin-lastfm-app-loader {
  display: inline-block;
  position: relative;
  width: 80px;
  height: 80px;
}

.grav-plugin-lastfm-app-loader div {
  display: inline-block;
  position: absolute;
  left: 8px;
  width: 16px;
  background: #333;
  animation: lds-facebook 1.2s cubic-bezier(0, 0.5, 0.5, 1) infinite;
}

.grav-plugin-lastfm-app-loader div:nth-child(1) {
  left: 8px;
  animation-delay: -0.24s;
}

.grav-plugin-lastfm-app-loader div:nth-child(2) {
  left: 32px;
  animation-delay: -0.12s;
}

.grav-plugin-lastfm-app-loader div:nth-child(3) {
  left: 56px;
  animation-delay: 0;
}

@keyframes lds-facebook {
  0% {
    top: 8px;
    height: 64px;
  }
  50%, 100% {
    top: 24px;
    height: 32px;
  }
}
</style>