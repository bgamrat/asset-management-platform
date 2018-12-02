<template>
    <div>
        <b-row>
            <b-col>
                <img id="logo" class="mx-auto" width="500" height="auto" src="images/logo.jpg" alt="logo" title="Moving Pieces" />
            </b-col>
        </b-row>
    </div>
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

<style scoped>
#logo {
    width: 500px;
    display: block;
}
</style>