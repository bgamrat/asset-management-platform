<template>

</template>
<script>
export default {
    name: 'Home',
    mounted(){
        this.$store.dispatch('common_dialog/disableDialog');
    },
    created() {
        this.$store.dispatch('common_message/hideMessage');
    },
}
</script>