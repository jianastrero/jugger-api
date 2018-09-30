<template>
    <div class="align-middle text-center text-light bg-primary jugger-full-screen lineheight-100vh">
        <i class="fas fa-spin fa-cog fa-10x"></i>
    </div>
</template>

<script>
    export default {
        name: 'LogoutComponent',
        props: [
            'rootUrl'
        ],
        data() {
            return {
                name: 'LogoutComponent'
            }
        },
        mounted() {
            this.logout();
        }
    }
</script>

<style scoped>
    .lineheight-100vh {
        line-height: 100vh;
    }
</style>