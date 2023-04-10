<template>
    <div class="block swiper page-20120218" tag="swiper-fraction" >
        <template v-if="element.nodes===false || element.nodes.length==0">
            <el-carousel height="620px" :autoplay="false">
                <el-carousel-item v-for="item in 4" :key="item">
                    <img src="/cms/upload/demo/40.png" />
                </el-carousel-item>
            </el-carousel>
        </template>
        <template v-else>
            <el-carousel height="150px" :autoplay="false" v-if="false">
                <el-carousel-item v-for="(item,i) in element.nodes" :key="i"
                                  :style="item.src.pc "
                                  style="background-origin: padding-box;
                                             background-position: 50% 50%;
                                             background-position-x: 50%;
                                             background-position-y: 50%;
                                             background-repeat: no-repeat;
                                             background-size: contain;">
                </el-carousel-item>
            </el-carousel>


                <swiper :options="swiperOption" >
                    <swiper-slide v-for="(item,i) in element.nodes" :key="i">
                        <img :src="$root.$data.mediaType=='wechat'?item.src.h5:item.src.pc " />
                    </swiper-slide>
                    <div class="swiper-pagination" :style="{top:element.page_height + '%'}" slot="pagination"></div>
                    <div class="swiper-button-prev" slot="button-prev"></div>
                    <div class="swiper-button-next" slot="button-next"></div>
                </swiper>

        </template>

    </div>
</template>

<script>
    import driver from './driver';
    export default {
        extends: driver,
        props:["element"],
        name: "swiper-fraction-show",
        data:function(){
            return {
                swiperOption: {
                    slidesPerView: 1,
                    spaceBetween: 30,
                    loop: true,
                    pagination: {
                        el: '.swiper-pagination',
                        type: 'fraction',
                        clickable: true
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev'
                    },
                    swipeHandler:'false'
                },
            };
        },
        methods:{

        }
    }
</script>

<style scoped>
    .swiper-pagination{
        text-align:right !important;
        padding-right: 50px !important;
        line-height:14px !important;
        font-size: 12px;
    }
</style>