<template>
    <div>
        <div tag="icon_title" class="icon_title">
            {{element.title}}
        </div>
    </div>
</template>

<script>
    import driver from './driver';
    export default {
        extends: driver,
        props:['element'],
        name: "icon-title-show"
    }
</script>

<style scoped>
    .icon_title{
        width: 80px;
        height: 60px;
        line-height: 60px;
        background: url('/static/admin/images/icon_flower.png') no-repeat 0 0 /100% 100%;
        margin: 0 auto;
        font-size: 12px;
        white-space: nowrap;
        text-align: center;
        overflow: hidden;
    }
</style>