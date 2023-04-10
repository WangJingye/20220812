<template v-if="element.tag=='video'">
    <div style="text-align: center;line-height: 200px;
                                             background-position-x: 50%;
                                             background-position-y: 50%;
                                             background-repeat: no-repeat;
                                             background-size: 100% 100%;"
         :style="bgImg(element.src)"
    >

        <i class="el-icon-video-play" style="color:#007aff;font-size: 50px;vertical-align: bottom;" v-if="element.video==''"></i>
        <video ref="video_tag" muted style="position: absolute;left:0;top:0;"    :src="element.video"     loop autoplay   v-if="element.video">
            your browser does not support the video tag
        </video>
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
                    backgroundImage:'url('+url+')',
                    height:(this.element.height / 2) + "px",
                }
            },
        },
        mounted: function () {

        },

    }
</script>

