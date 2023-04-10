<template v-if="element.tag=='video'">
    <div style="text-align: center;font-size:50px;height: 160px;line-height: 160px;
                                             background-position-x: 50%;
                                             background-position-y: 50%;
                                             background-repeat: no-repeat;
                                             background-size: 100% 100%;"
         :style="bgImg(element.src)"
    >
        <i class="el-icon-video-play" style="color:#999"></i>
    </div>

</template>

<script>
    import driver from './driver';
    export default {
        extends: driver,
        props:['element'],
        name: "video-show",
        methods:{
            bgImg:function (url){
                return {
                    backgroundImage:'url('+url+')'
                }
            },
        }
    }
</script>

