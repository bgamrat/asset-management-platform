<template>
<div>
    <button @click="addRow" size="sm" class="float-right add-one-more-row">{{ label }}</button>
</div>
</template>

<script>

import { mapActions } from 'vuex'

export default {
  name: "addrow",
  props: {label: String},
  data() { 
      return { addRow: function () { alert("here") }
    } 
  }
}
</script>