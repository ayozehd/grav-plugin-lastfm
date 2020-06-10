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
