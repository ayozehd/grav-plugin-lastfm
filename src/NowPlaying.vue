<template>
    <div class="lastfm-nowplaying-eq">
        <div class="nowplaying-eq-col">
            <div class="-nowplaying-eq-1-1"></div>
            <div class="-nowplaying-eq-1-2"></div>
        </div>
        <div class="nowplaying-eq-col">
            <div class="-nowplaying-eq-2-1"></div>
            <div class="-nowplaying-eq-2-2"></div>
        </div>
        <div class="nowplaying-eq-col">
            <div class="-nowplaying-eq-3-1"></div>
            <div class="-nowplaying-eq-3-2"></div>
        </div>
        <div class="nowplaying-eq-col">
            <div class="-nowplaying-eq-4-1"></div>
            <div class="-nowplaying-eq-4-2"></div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        color: {
            type: String,
            default: '#FAFAFA'
        }
    },
}
</script>

<style>
.lastfm-nowplaying-eq {
  align-items: flex-end;
  display: flex;
  position: absolute;
  right: 7px;
  bottom: 7px;
  width: 20px;
  height: 12px;
  overflow: hidden;
  opacity: 0.8;
}

.lastfm-nowplaying-eq .nowplaying-eq-col {
  flex: 1;
  position: relative;
  height: 100%;
  margin-right: 1px;
}

.lastfm-nowplaying-eq .nowplaying-eq-col div {
  animation-name: nowplaying-eq-animation;
  animation-timing-function: linear;
  animation-iteration-count: infinite;
  animation-direction: alternate;
  background-color: #FAFAFA;
  position: absolute;
  width: 100%;
  height: 100%;
  transform: translateY(100%);
  will-change: transform;
}

.-nowplaying-eq-1-1 {
  animation-duration: 0.3s;
}

.-nowplaying-eq-1-2 {
  animation-duration: 0.45s;
}

.-nowplaying-eq-2-1 {
  animation-duration: 0.5s;
}

.-nowplaying-eq-2-2 {
  animation-duration: 0.4s;
}

.-nowplaying-eq-3-1 {
  animation-duration: 0.3s;
}

.-nowplaying-eq-3-2 {
  animation-duration: 0.35s;
}

.-nowplaying-eq-4-1 {
  animation-duration: 0.4s;
}

.-nowplaying-eq-4-2 {
  animation-duration: 0.25s;
}

@keyframes nowplaying-eq-animation {
  0% {
    transform: translateY(100%);
  }
  100% {
    transform: translateY(0);
  }
}
</style>