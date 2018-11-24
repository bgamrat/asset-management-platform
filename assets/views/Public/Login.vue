<template>
    <div>
        <form action="" method="post">
            <b-row>
                <b-col>
                    <input type="hidden" name="_csrf_token" :value="_csrf_token" />
                    <b-form-group id="username-group"
                                  :label="$t('username')"
                                  label-for="username">
                        <b-form-input id="username"
                                      type="text"
                                      name="_username"
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
                                      required>
                        </b-form-input>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-form-checkbox id="remember_me"
                                     name="_remember_me"
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
        return { }
    },
    mounted(){
        this.$store.dispatch('common_dialog/setDialog', {'title':'I win!', 'content':'Yay!'})
    },
    created(){
        this.refreshCsrfToken();
    },
    methods: {
        doSubmit() {
            this.$store.dispatch('common_message/setMessage',{'visible':true,'variant':'success','message':this.$i18n.t('success')});
        },
        refreshCsrfToken() {
                this.$store.dispatch('common_user/refreshCsrfToken').then(() => {
            });
        }
    },
    computed:
        mapState({
            _csrf_token: state => state.common_user.csrf_token
        })
}
</script>

<style scoped>

</style>