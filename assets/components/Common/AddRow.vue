<template>
<div class="text-right">
    <b-button v-on:click="$emit('add-row')" size="sm">{{ $t('add_row') }}</b-button>
</div>
</template>

<script>
export default {
    name: 'AddRow'
};
</script>