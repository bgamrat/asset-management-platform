<template>
<div>
    <b-button v-on:click="$emit('add-row')" size="sm" class="float-right add-one-more-row">{{ $t('add_row') }}</b-button> 
</div>
</template>

<script>
export default {
    name: 'AddRow'
};
</script>