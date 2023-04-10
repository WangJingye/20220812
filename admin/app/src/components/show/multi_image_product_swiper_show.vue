<template>
    <div class="block  multi_image_product_swiper page-20120218" tag="multi_image_product_swiper" >
        <template v-if="element.nodes===false || element.nodes.length==0">
            <div class="block  multi_image_product_swiper" tag="multi_image_product_swiper" >
                <img :src="element['placeholder_' + getDriver()] " style="width: 100%;height: 100%;"/>
            </div>
        </template>
        <template v-else>
            <div class="swiper_horizontal" @click="reload">
                <swiper  ref="jackSwiper" :options="swiperOption"   :auto-update="true" :auto-destroy="true"
                         >
                    <swiper-slide  v-for="(item,i) in element.nodes" :key="i">
                        <img :src="item.src.active"/>
                        <div class="product_name">{{item.product_name}}</div>
                        <div class="product_desc">{{item.product_desc}}</div>
                        <div class="product_price">{{item.product_price}}</div>
                    </swiper-slide>
                    <div class="swiper-pagination" slot="pagination"></div>
                    <div class="swiper-button-prev" slot="button-prev"></div>
                    <div class="swiper-button-next" slot="button-next"></div>
                </swiper>
            </div>
        </template>
    </div>
</template>

<script>
    import driver from './driver';
    export default {
        extends: driver,
        props:['element'],
        name: "multi-image-product-swiper-show",
        data() {
            return {
                swiperOption: {
                    observer: true,
                    observerParents: true,
                    observeSlideChildren:true,
                    slidesPerView: 2,
                    spaceBetween: 5,
                    slidesPerGroup: 2,
                    loop: false,
                    loopFillGroupWithBlank: false,
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev'
                    }
                },
                swiperAuto: {
                    // speed:300,
                    // autoplay : {
                    //     delay:3000
                    // },
                    // loop:true,
                    slidesPerView: 'auto',
                    observer: true,
                    observerParents: true,
                    observeSlideChildren:true,
                    initialSlide :1,
                    spaceBetween: 5,
                    centeredSlides:false,
                    centeredSlidesBounds:false,
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true
                    },
                    swipeHandler:'false'
                }
            };
        },
        mounted() {

        },
        methods:{
            reload:function (e) {
                this.$refs.jackSwiper.swiper.update();
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
    }
</style>