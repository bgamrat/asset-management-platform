<script>
export default {
    name: 'Logout',
    mounted() {
        fetch('/logout')
                .then(res => {
            this.$store.dispatch('common_navigation/refreshNavItems')
            this.$store.dispatch('common_dialog/disableDialog')
            this.$router.push({path:'home', name:'home'})
        })
    }
}
</script>