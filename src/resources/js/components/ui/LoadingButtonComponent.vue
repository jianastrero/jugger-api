<template>
    <button :type="buttonType" :class="[button, {'wobble': loadingChanged, 'rubberBand': loadingIndicate}]" class="btn animated">
        <span v-if="loading"><i :class="[iconLoading]" class="fas fa-spin"></i> <span v-if="showTextWhileLoading">{{ buttonText }}</span></span>
        <span v-else><i :class="[icon]" class="fas"></i> <span>{{ buttonText }}</span></span>
    </button>
</template>

<script>
    export default {
        name: 'LoadingButtonComponent',
        props: [
            'button',
            'buttonType',
            'buttonText',
            'showTextWhileLoading',
            'icon',
            'iconLoading',
            'loading'
        ],
        data() {
            return {
                name: 'LoadingButtonComponent',
                loadingChanged: false,
                loadingIndicate: false
            }
        },
        methods: {
            resetLoadingChanged() {
                this.loadingChanged = false;
            },
            loadingIndicateTimer() {
                if (this.loading) {
                    this.loadingIndicate = !this.loadingIndicate;
                    setTimeout(this.loadingIndicateTimer, 1500);
                } else {
                    this.loadingIndicate = false;
                }
            }
        },
        watch: {
            loading: function (val) {
                this.loadingChanged = true;
                setTimeout(this.resetLoadingChanged, 600);
                setTimeout(this.loadingIndicateTimer, 1500);
            }
        }
    }
</script>

<style scoped>
</style>