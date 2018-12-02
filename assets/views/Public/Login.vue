<template>
    <div>
        <form action="" method="post">
            <b-row>
                <b-col>
                    <input type="hidden" name="_csrf_token" :value="csrf_token" />
                    <b-form-group id="username-group"
                                  :label="$t('username')"
                                  label-for="username">
                        <b-form-input id="username"
                                      type="text"
                                      name="_username"
                                      :value="username"
                                      v-model.trim="username" 
                                      required>
                        </b-form-input>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-form-group id="password-group"
                                  :label="$t('password')"
                                  label-for="password">
                        <b-form-input id="password"
                                      type="password"
                                      name="_password"
                                      :value="password"
                                      v-model="password" 
                                      required>
                        </b-form-input>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-form-checkbox id="remember_me"
                                     name="_remember_me"
                                     v-model="remember_me" 
                                     value="on">
                        {{ $t('rememberme') }}
                    </b-form-checkbox>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-button type="button" v-on:click="doSubmit" variant="primary">{{ $t('submit') }}</b-button>
                    <input type="hidden" id="_submit" name="_submit" value="submit">
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-link href="/#/password-reset">
                        {{ $t('passwordreset')}}
                    </b-link>
                </b-col>
            </b-row>
        </form>
    </div>
</template>
<script>
import { mapState, mapActions } from 'vuex'

export default {
    name: 'Login',
    data() {
        return {
             username: '', password: '', remember_me: false 
        }
    },
    mounted(){
        this.$store.dispatch('common_dialog/setDialog', {'icon':'fa fa-question','title':'I win!', 'content':'Yay!'})
    },
    created(){
        this.refreshCsrfToken();
    },
    methods: {
        doSubmit() {
            var formData = new FormData(), rm = document.getElementById("remember_me");
            formData.append('_csrf_token',this.csrf_token);
            formData.append('_username',this.username);
            formData.append('_password',this.password);
            if (rm.checked === true) {
                formData.append('remember_me','on');
            }
            formData.append('_submit','Log in');
            fetch('/login_check', { method: 'POST', credentials: 'same-origin',  body: formData})
                .then(res => {
                    if (/login$/.test(res.url)) {
                        res.text().then(text => {
                            var err = JSON.parse(text);
                            // error message i18n is handled on the server side by FOS User Bundle
                            this.$store.dispatch('common_message/setMessage',{'visible':true,'variant':'warning','message':err.message});
                        });
                    } else {
                        // Authentication succeeded, go to the home page and refresh the navigation
                        this.$store.dispatch('common_navigation/refreshNavItems');
                        this.$router.push({path:'home', name:'home'})
                    }
                })
        },
        refreshCsrfToken() {
                this.$store.dispatch('common_user/refreshCsrfToken').then(() => {
            });
        }
    },
    computed:
        mapState({
            csrf_token: state => state.common_user.csrf_token
        })
}
</script>

<style scoped>

</style>