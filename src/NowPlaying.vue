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
