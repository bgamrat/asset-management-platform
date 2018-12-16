<template>
<div class="text-center">
    <b-button v-on:click="$emit('save')" size="sm">{{ $t('save') }}</b-button>
</div>
</template>

<script>
export default {
    name: 'SaveButton'
};
</script>