import Vue from 'vue'
import VueI18n from 'vue-i18n'

Vue.use(VueI18n);

export var i18n = new VueI18n({
    locale: 'en',
    fallbackLocale: 'en',
    messages: {
        'en': {
            'add_row': 'Add Row',
            'admin-asset-status': 'Admin Asset Status',
            'app-name': 'Moving Pieces',
            'available': 'Available',
            'comment': 'Comment',
            'default': 'Default',
            'home': 'Home',
            'in_use': 'In Use',
            'last_username': 'Last username',
            'login': 'Login',
            'logout': 'Log out',
            'name': 'Name',
            'password': 'Password',
            'passwordreset': 'Password reset',
            'rememberme': 'Remember me',
            'remove' : 'Remove',
            'submit': 'Submit',
            'success': 'Look, I am awesome',
            'update': 'Update',
            'username': 'Username'}
    }
})

