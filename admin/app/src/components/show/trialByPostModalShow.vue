<template>
    <div class="txt">
       <img src="/static/admin/images/1.png" />
    </div>
</template>

<script>
    import driver from './driver';
    export default {
        extends: driver,
        props:['element'],
        name: "text-show",
        methods:{

        },
    }
</script>

<style scoped>

</style>