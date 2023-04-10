<template>
    <div class="block swiper" tag="swiper" >
        <template v-if="element.nodes===false || element.nodes.length==0">
            <el-carousel height="240px" :autoplay="false">
                <el-carousel-item v-for="item in 4" :key="item">
                    <img src="/cms/upload/demo/18.png" />
                </el-carousel-item>
            </el-carousel>
        </template>
        <template v-else>
            <el-carousel height="150px" :autoplay="false" v-if="false">
                <el-carousel-item v-for="(item,i) in element.nodes" :key="i"
                                  :style="item.src.h5.src"
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
                        <img :src="$root.$data.mediaType=='wechat'?item.src.h5.src:item.src.h5.src " />
                    </swiper-slide>
                    <div class="swiper-pagination" slot="pagination"></div>
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
        name: "swiper-show",
        data:function(){
            return {
                swiperOption: {
                    slidesPerView: 1,
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
            };
        },
        methods:{

        }
    }
</script>

<style scoped>

</style>