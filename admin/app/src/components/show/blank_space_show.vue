<template>
    <div>
        <div tag="blank_space" :style="{lineHeight:element.height<12?'12px':element.height + 'px'}">
            {{element.name}} height:{{element.height}}
        </div>
    </div>
</template>

<script>
    import driver from './driver';
    export default {
        extends: driver,
        props:['element'],
        name: "blank-space-show",
        methods:{

        },
    }
</script>

<style scoped>

</style>