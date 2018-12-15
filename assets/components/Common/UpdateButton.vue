<template>
<div class="text-center">
    <b-button v-on:click="$emit('update')" size="sm">{{ $t('update') }}</b-button>
</div>
</template>

<script>
export default {
    name: 'UpdateButton'
};
</script>