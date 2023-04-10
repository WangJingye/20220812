<template>
    <div class="block  product_swiper" tag="product_swiper" >

        <template v-if="element.nodes[0].product_id == ''">
            <div>
                <img :src="element['placeholder_' + getDriver()] " style="width: 100%;height: 100%;"/>
            </div>
        </template>
        <template v-else>
            <div class="swiper_horizontal" @click="reload">
                <swiper  ref="jackSwiper" :options="swiperAuto"   :auto-update="true" :auto-destroy="true"
                >
                    <swiper-slide  v-for="(item,i) in element.nodes" :key="i">
                        <img :src="item.src"/>
                        <div class="product_name">{{item.name}}</div>
                        <div class="product_desc">{{item.desc}}</div>
                        <div class="product_price">{{item.price}}</div>
                    </swiper-slide>
                    <div class="swiper-pagination" slot="pagination"></div>
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
        name: "product-swiper-show",
        data() {
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
        -webkit-font-smoothing:
    }
</style>