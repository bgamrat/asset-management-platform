<template>
    <div>
        <admin-asset-status :label="label"></admin-asset-status>
    </div>
</template>

<script>
import AdminAssetStatus from '../../components/Admin/Asset/AssetStatus';

export default {
    name: 'AdminAssetStatusView',
    props: {label:{  type: String, default: 'Add Row'}},
    components:{'admin-asset-status':AdminAssetStatus},

};
</script>

<style scoped>

</style>


