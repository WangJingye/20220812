<template>
    <div>
        <div tag="icon_title" class="categroy_list">
            <div class="block  categroy_list" tag="categroy_list" >
                <img :src="element['placeholder_' + getDriver()] " style="width: 100%;height: 100%;"/>
            </div>
        </div>
    </div>
</template>

<script>
    import driver from './driver';
    export default {
        extends: driver,
        props:['element'],
        name: "categroy-list-show"
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