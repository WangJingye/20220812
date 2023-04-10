<template>
    <div class="block ranging" tag="ranging_list" style="">
        <template v-if="element.nodes===false || element.nodes.length==0">
            <div>
                <img src="/static/admin/images/x_swiper.png" style="width: 100%;height: 100%;"/>
            </div>
        </template>
        <template v-else>
            <el-tabs tab-position="top" @tab-click="reload">
                <el-tab-pane :key="k" v-for=" (tab,k) in element.nodes" :label="tab.name"  >
                    <div style="width:100%;position: relative">
                        <img :src="tab.top.bg" style="width:100%;"/>
                        <video muted style="position: absolute;object-fit:fill" :src="tab.top.middle.src" :style="getStyle(tab)"  controls="controls"   loop autoplay   v-if="tab.top.middle.src && tab.top.middle.type=='video'">
                            your browser does not support the video tag
                        </video>
                        <img  style="position: absolute;left:0;top:0;width:100%;" :src="tab.top.product" />
                    </div>
                </el-tab-pane>
            </el-tabs>
        </template>

    </div>
</template>

<script>
    import driver from './driver';
    export default {
        extends: driver,
        props: ['element'],
        data() {
            return {
                swiperOption: {
                    observer: true,
                    observerParents: true,
                    observeSlideChildren:true,
                    slidesPerView: 3,
                    spaceBetween: 30,
                    loop: true,
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev'
                    },
                    swipeHandler:'false'
                },
                swiperAuto: {
                    // speed:300,
                    // autoplay : {
                    //     delay:3000
                    // },
                    // loop:true,
                    initialSlide :0,
                    slidesPerView: 'auto',
                    observer: true,
                    observerParents: true,
                    observeSlideChildren:true,
                    slidesPerView: 3,
                    spaceBetween: 5,
                    centeredSlides:true,
                    centeredSlidesBounds:true,
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true
                    },
                    swipeHandler:'false'
                }
            };
        },
        methods:{
            reload:function (e) {
                //console.log(this.$refs.jackSwiper);
                this.$refs.jackSwiper.forEach(function (swiper){
                    swiper.update();
                });
                //
            },
            getStyle:function(tab){
                let data ={
                        width:this.getWidth(tab),
                        height:this.getHeight(tab)
                }
                if(tab.top.middle.position=='left_top'){
                    data.left=0;
                    data.top=0;
                }else if(tab.top.middle.position=='right_top'){
                    data.right=0;
                    data.top=0;
                }else if(tab.top.middle.position=='left_bottom'){
                    data.bottom=0;
                    data.left=0;
                }else if(tab.top.middle.position=='right_bottom'){
                    data.bottom=0;
                    data.right=0;
                }else if(tab.top.middle.position=="center"){
                    data.left="50%";
                    data.top="50%";
                    data.transform='translate(-50%, -50%)';
                }else{
                    data.left=0;
                    data.top=0;
                }
                return data;
            },
            getWidth:function(tab){

                return tab.top.middle.width / 2 + "px";
            },
            getHeight:function(tab){
                if(tab.top.middle.height > 0){
                    return tab.top.middle.height / 2 + "px";
                }else{
                    return "";
                }
            }
        }
    }
</script>

<style scoped>
    .swiper_horizontal{
        /*height: 150px;*/
    }
    .cms-content-wrap.h5 .swiper_horizontal .swiper-slide{
        width:50%;
    }
    .cms-content-wrap.wechat .swiper_horizontal .swiper-slide{
        width:50%;
    }

    .cms-content-wrap.pc  .swiper_horizontal .swiper-slide{
        width:33%;
    }
    .product_name{
        color: #000;
        text-decoration: none;
        font-family: HelveticaNeueLTW05-45Light,arial,sans-serif;
        font-size: .8125rem;
        line-height: 1.3;
        margin-bottom: 0;
    }
    .product_desc{
        font-size: .75rem;
        height: 2.25rem;
        overflow: hidden;
        color: #575757;
        font-family: HelveticaNeueLTW05-45Light,arial,sans-serif;
    }
    .product_price{
        box-sizing:border-box;
        color:rgb(87, 87, 87);
        display:inline;
        font-family:HelveticaNeueLTW05-75Bold, arial, sans-serif;
        font-size:12px;
        font-weight:700;
        height:auto;
        line-height:19.2px;
        text-align:left;
        text-rendering:optimizelegibility;
        text-size-adjust:100%;
        user-select:none;
        white-space:nowrap;
        width:auto;
        -webkit-font-smoothing:
    }
</style>