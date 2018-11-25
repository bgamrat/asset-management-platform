<template>

</template>
<script>
export default {
    name: 'Logout',
    mounted(){
        this.$store.dispatch('common_dialog/disableDialog');
    },
    created() {
        fetch('/logout');
        this.$store.dispatch('common_message/hideMessage');
    },
}
</script>