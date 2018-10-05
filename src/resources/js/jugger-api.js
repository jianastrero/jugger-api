require('./bootstrap');

import Vue from 'vue'
import VueSession from 'vue-session'
import JuggerLoginComponent from './components/JuggerLoginComponent.vue'
import JuggerHomeComponent from './components/JuggerHomeComponent.vue'
import JuggerLogoutComponent from './components/JuggerLogoutComponent.vue'
import LoadingButtonComponent from './components/ui/LoadingButtonComponent.vue'

window.Vue = Vue;

Vue.mixin({
    methods: {
        httpCode(response) {
            if (response.status >= 200 && response.status <= 300) {
                return Promise.resolve(response);
            } else {
                return Promise.reject(new Error(response.status))
            }
        },
        json(response) {
            return response.json();
        },
        error(error) {
            console.log('error is called', error);
        },
        handle(json, successCallback, handledErrorCallback, errorCallback = this.error) {
            console.log('json', json);
            if (json.hasOwnProperty('errors')) {
                let errors = json.errors;
                if (errors instanceof Array) {
                    handledErrorCallback(json.errors);
                } else if(errors instanceof Object) {
                    handledErrorCallback(json.errors);
                } else {
                    errorCallback(json.errors);
                }
            } else {
                successCallback(json);
            }
        },
        text(response) {
            return response.text();
        },
        formatStringDate(stringDate) {
            let options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            let date = new Date(stringDate);
            return date.toLocaleDateString("en-US", options);
        },
        logout() {
            console.log('logging out');
            fetch(this.rootUrl + '/jugger-api/jugger-api/logout', {
                mode: 'cors',
                method: 'post',
                headers: {
                    'Authorization': 'Bearer '  + this.$session.get('accessToken'),
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Accept': 'application/json'
                }
            })
                .then(this.logoutSession)
                .catch(this.logoutSession);
        },
        logoutSession() {
            console.log('logging out session');
            this.$session.clear();
            this.$session.destroy();
            window.location = this.rootUrl + '/jugger-api';
        }
    },
});

Vue.use(VueSession);
Vue.component('jugger-login-component', JuggerLoginComponent);
Vue.component('jugger-home-component', JuggerHomeComponent);
Vue.component('jugger-logout-component', JuggerLogoutComponent);
Vue.component('loading-button-component', LoadingButtonComponent);

const app = new Vue({
    el: '#app',
    components: {
        JuggerLoginComponent,
        JuggerHomeComponent,
        JuggerLogoutComponent
    }
});
