<template>
    <div>
        <div tag="swiper_demo" class="swiper_demo">
            <demo/>
        </div>
    </div>
</template>

<script>
    import demo from '../bak/swiper/08-scrollbar';
    export default {
        props:['element'],
        name: "swiper-demo",
        components:{
            demo
        }
    }
</script>

<style scoped>

</style>