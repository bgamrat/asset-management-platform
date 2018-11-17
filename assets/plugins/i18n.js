import Vue from 'vue'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n);

export var i18n = new VueI18n({
    locale: 'en',
    fallbackLocale: 'en',
    messages: {
        'en': {
            'last_username': 'Last username',
            'password': 'Password',
            'passwordreset': 'Password reset',
            'rememberme': 'Remember me',
            'submit': 'Submit',
            'username': 'Username'}
    }
})

