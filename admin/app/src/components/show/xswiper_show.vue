<template>
    <div class="block xswiper" tag="xswiper" style="">
        <template v-if="element.nodes===false || element.nodes.length==0">
            <div>
                <img src="/static/admin/images/x_swiper.png" style="width: 100%;height: 100%;"/>
            </div>
        </template>
        <template v-else>
            <div class="title" style="text-align: center;">{{element.title}}</div>
            <el-tabs tab-position="top" @tab-click="reload">
                <el-tab-pane :key="k" v-for=" (list,k) in element.nodes" :label="list.name"  >
                    <swiper  ref="jackSwiper" :options="swiperAuto"   :auto-update="true" :auto-destroy="true"
                    >
                        <swiper-slide  v-for="(it,i) in list.list" :key="i">
                            <img :src="it.src" style="width:50px;"/>
                            <div style="font-size: 12px;">{{it.title}}</div>
                        </swiper-slide>
                        <div class="swiper-pagination" slot="pagination"></div>
                    </swiper>
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
        name: "xswiper-show",
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