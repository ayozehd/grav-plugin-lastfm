<template>
    <button :class="['lastfm-reload-button', isPressed ? 'animate-reloading' : '']"
            @click="clickedEvent" :disabled="isDisabled">
        <transition name="fade" mode="out-in" :duration="200">
            <svg v-if="isSuccess" xmlns="http://www.w3.org/2000/svg" :width="width" :height="height" viewBox="0 0 24 24" key="reload">
                <path :fill="color" d="M0 12.116l2.053-1.897c2.401 1.162 3.924 2.045 6.622 3.969 5.073-5.757 8.426-8.678 14.657-12.555l.668 1.536c-5.139 4.484-8.902 9.479-14.321 19.198-3.343-3.936-5.574-6.446-9.679-10.251z"/>
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" :width="width" :height="height" viewBox="0 0 24 24" key="success">
                <path :fill="color" d="M9 12l-4.463 4.969-4.537-4.969h3c0-4.97 4.03-9 9-9 2.395 0 4.565.942 6.179 2.468l-2.004 2.231c-1.081-1.05-2.553-1.699-4.175-1.699-3.309 0-6 2.691-6 6h3zm10.463-4.969l-4.463 4.969h3c0 3.309-2.691 6-6 6-1.623 0-3.094-.65-4.175-1.699l-2.004 2.231c1.613 1.526 3.784 2.468 6.179 2.468 4.97 0 9-4.03 9-9h3l-4.537-4.969z"/>
            </svg>
        </transition>
    </button>
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
            default: 28
        },
        width: {
            type: Number,
            default: 28
        },
        onclick: {
            type: Function
        },
        afterOnClick: {
            type: Function
        },
        errorHandler: {
            type: Function
        }
    },
    methods: {
        toggleIsPressed() {
            this.isPressed = !this.isPressed
            this.isDisabled = (this.isPressed && !this.isSuccess)
        },
        clickedEvent() {
            this.toggleIsPressed()
            new Promise(this.onclick)
                .then(this.afterOnClick)
                .catch(this.errorHandler)
                .then(this.successEvent())
        },
        successEvent() {
            this.isPressed = false
            this.isSuccess = true
            setTimeout(() => { this.isSuccess = false; this.isDisabled = false }, 2000)
        }
    },
    data() {
        return {
            isPressed: false,
            isDisabled: false,
            isSuccess: false,
        }
    }
}
</script>
