<template>

</template>
<script>
export default {
    name: 'Logout',
    mounted(){
        this.$store.dispatch('common_dialog/disableDialog');
        this.$router.push({path:'home', name:'home'})
    },
    created() {
        fetch('/logout');
    },
}
</script>