<template>
    <div tag="image">
        <img  :src="getImageSrc(element)" style="width:100%;" title=""/>
    </div>
</template>

<script>
    import driver from './driver';
    export default {
        extends: driver,
        props:['element'],
        name: "image-show",
        data:function(){
            return {};
        },
        methods:{
            imageMouseOver:function (src,event){
                //console.log(src,event);
                event.target.src = src;
            },
            imageMouseLeave:function (src,event){
                //console.log(src,event);
                event.target.src = src;
            },
        }
    }
</script>

<style scoped>

</style>