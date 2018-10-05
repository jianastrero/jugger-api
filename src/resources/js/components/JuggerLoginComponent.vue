<template>
    <div class="jugger-full-screen jugger-p-3 jugger-p-0 bg-primary">
        <div class="jugger-match-parent d-flex flex-row shadow">
            <div class="flex-grow-1 jugger-overflow-hidden jugger-position-relative">
                <div class="jugger-match-parent animated pulse">
                    <img :src="rootUrl + '/jianastrero/jugger-api/images/jugger-api-logo-large.png'" class="jugger-large-logo jugger-pointer-none"/>
                </div>
            </div>
            <div class="flex-grow-0 col-md-3 col-12 bg-light d-flex flex-column align-content-center justify-content-center">
                <form v-on:submit.prevent="login" class="flex-grow-0">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Username</label>
                        <input v-model="email" :class="{'is-invalid': errorMessage !== '' || emailErrorMessage !== ''}" type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter username">
                        <small v-if="emailErrorMessage !== ''" class="form-text text-danger animated fadeInUp">{{ emailErrorMessage }}</small>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Password</label>
                        <input v-model="password" :class="{'is-invalid': errorMessage !== '' || passwordErrorMessage !== ''}" type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                        <small v-if="passwordErrorMessage !== ''" class="form-text text-danger animated fadeInUp">{{ passwordErrorMessage }}</small>
                    </div>
                    <div>
                        <small v-if="errorMessage !== ''" class="form-text text-danger text-center mb-3 animated fadeInUp">{{ errorMessage }}</small>
                    </div>
                    <div class="d-flex align-content-center justify-content-end">
                        <loading-button-component
                                :button="'btn-success'"
                                :buttonType="'submit'"
                                :buttonText="'Submit'"
                                :showTextWhileLoading="false"
                                :icon="'fa-sign-in-alt'"
                                :iconLoading="'fa-sync'"
                                :loading="loading">
                        </loading-button-component>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import LoadingButtonComponent from "./ui/LoadingButtonComponent.vue";

    export default {
        components: {LoadingButtonComponent},
        name: 'JuggerLoginComponent',
        props: [
            'rootUrl'
        ],
        data() {
            return {
                name: 'JuggerLoginComponent',
                email: '',
                password: '',
                loading: false,
                errorMessage: '',
                emailErrorMessage: '',
                passwordErrorMessage: ''
            }
        },
        methods: {
            login() {
                if (!this.loading) {
                    this.errorMessage = '';
                    this.emailErrorMessage = '';
                    this.passwordErrorMessage = '';
                    this.loading = true;
                    fetch(this.rootUrl + '/jugger-api/jugger-api/login', {
                        mode: 'cors',
                        method: 'post',
                        headers: {
                            "Content-type": "application/x-www-form-urlencoded; charset=UTF-8",
                            "Accept": "application/json"
                        },
                        body: 'username='+this.email+'&password='+this.password
                    })
                        .then(this.json)
                        .then(this.handleJson)
                        .catch(this.error);
                }
            },
            handleJson(json) {
                this.loading = false;
                this.handle(json, this.success, this.handleError);
            },
            success(json) {
                this.$session.start();
                this.$session.set('accessToken', json.token);
                window.location = this.rootUrl + '/jugger-api/home';
            },
            handleError(errors) {
                if (errors.hasOwnProperty('message')) {
                    if (errors.message.hasOwnProperty('username')) {
                        this.emailErrorMessage = errors.message.username[0];
                    }
                    if (errors.message.hasOwnProperty('password')) {
                        this.passwordErrorMessage = errors.message.password[0];
                    }
                    if (!errors.message.hasOwnProperty('username') && !errors.message.hasOwnProperty('password')) {
                        this.errorMessage = errors.message;
                    }
                } else {
                    this.errorMessage = 'Something went wrong, please try again later';
                }
            }
        },
        mounted() {
            if (this.$session.exists()) {
                if (!this.$session.has('accessToken')) {
                    this.logoutSession();
                } else {
                    window.location = this.rootUrl + '/jugger-api/home';
                }
            }
        }
    }
</script>

<style scoped>
</style>