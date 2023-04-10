<template>
    <div>
        <template v-if="element.tag=='blank_space'">
            <blank-space-show :element="element"/>
        </template>

        <template v-if="element.tag=='txt'">
            <text-show :element="element"/>
        </template>
        <template v-if="element.tag=='anchor_point'">
            <anchorPointShow :element="element"/>
        </template>


        <template v-if="element.tag=='trial_by_post_modal'">
            <trialByPostModalShow :element="element"/>
        </template>

        <template v-if="element.tag=='txt_simple'">
            <text-show :element="element"/>
        </template>

        <template v-if="element.tag=='product_detail_multi_part_text'">
            <productDetailMultiPartTextShow :element="element"/>
        </template>
        <template v-if="element.tag=='product_detail_title_desc'">
            <productDetailTitleDescShow :element="element"/>
        </template>
        <template v-if="element.tag=='product_detail_text'">
            <productDetailTextShow :element="element"/>
        </template>

        <template v-if="element.tag=='icon_title'">
            <icon-title-show :element="element"/>
        </template>

        <template v-if="element.tag=='icon_title_yellow'">
            <icon-title1-show :element="element"/>
        </template>

        <template v-if="element.tag=='multi_line_text'">
            <multi-line-text-show :element="element"/>
        </template>

        <template v-if="element.tag=='image'">
            <image-show :element="element"/>
        </template>


        <template v-if="element.tag=='image_map'">
            <image-map-show :element="element"/>
        </template>

        <template v-if="element.tag=='banner_image'">
            <image-show :element="element"/>
        </template>



        <template v-if="element.tag=='image_2text'">
            <image-show2 :element="element"/>
        </template>

        <template v-if="element.tag=='image_flex_simple'">
            <imageFlexSimpleShow :element="element"/>
        </template>

        <template v-if="element.tag=='image_flex_image'">
            <image-flex-image-show :element="element"/>
        </template>

        <template v-if="element.tag=='image_flex_unlimited'">
            <image-flex-show :element="element" />
        </template>

        <template v-if="element.tag=='image_flex'">
            <image-flex-show :element="element" />
        </template>

        <template v-if="element.tag=='image_flex_right'">
        </template>

        <template v-if="element.tag=='image_flex_hair'">
            <imageFlexHairShow :element="element" />
        </template>

        <template v-if="element.tag=='multi_image_product'">
            <multi-image-product-show :element="element" />
        </template>

        <template v-if="element.tag=='multi_image_product_recommend'">
            <multiImageProductRecommendShow :element="element" />
        </template>

        <template v-if="element.tag=='multi_image_product_high'">
            <multi-image-product-show :element="element" />
        </template>

        <template v-if="element.tag=='multi_image'">
            <multi-image-show :element="element" />
        </template>

        <template v-if="element.tag=='product_recommend'">
            <multi-image-show :element="element" />
        </template>

        <template v-if="element.tag=='multi_image_icon'">
            <multi-image-icon-show :element="element"/>
        </template>

        <template v-if="element.tag=='swiper_demo'">
            <swiper-demo :element="element"/>
        </template>

        <template v-if="element.tag=='swiper'">
            <swiper-show :element="element"/>
        </template>
        <template v-if="element.tag=='swiper_fraction'">
            <swiper-fraction-show :element="element"/>
        </template>
        <template v-if="element.tag=='swiper_fraction_v2'">
            <swiper-fraction-v2-show :element="element"/>
        </template>
        <template v-if="element.tag=='x_swiper'">
            <xswiper-show :element="element"/>
        </template>

        <template v-if="element.tag=='ranging_list'">
            <ranging :element="element"/>
        </template>

        <template v-if="element.tag=='multi_image_product_swiper'">
            <multi-image-product-swiper-show :element="element"/>
        </template>

        <template v-if="element.tag=='product_swiper'">
            <product-swiper-show :element="element"/>
        </template>

        <template v-if="element.tag=='navication_menu'">
            <navication-menu-show :element="element"/>
        </template>

        <template v-if="element.tag=='image_on_text_swiper'">
            <image-on-text-swiper-show :element="element"/>
        </template>

        <template v-if="element.tag=='category_list'">
            <categroy-list-show :element="element"/>
        </template>

        <template v-if="element.tag=='in_text_animate'">
            <in-text-animate-show :element="element"/>
        </template>

        <template v-if="element.tag=='in_text_animate_middle'">
            <in-text-animate-show :element="element"/>
        </template>

        <template v-if="element.tag=='in_video_animate'">
            <in-video-animate-show :element="element"/>
        </template>

        <template v-if="element.tag=='video'">
            <video-show :element="element"/>
        </template>

        <template v-if="element.tag=='video_home'">
            <videohome-show :element="element"/>
        </template>

        <template v-if="element.tag=='offline_appointment_pc'">
            <offlineAppointmentPcShow :element="element"/>
        </template>

        <template v-if="element.tag=='offline_appointment_wechat'">
            <offlineAppointmentPcShow :element="element"/>
        </template>






    </div>
</template>

<script>
    export default {
        props:['element','index'],
        name: "elementhome-show",
        components: {
        },
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
                swiper_3: {
                    slidesPerView: 'auto',
                    spaceBetween: 5,
                    freeMode: true,
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true
                    },
                    swipeHandler:'false'
                }
            };
        },
        mounted: function () {

        },


        methods: {
            imageMouseOver:function (src,event){
                //console.log(src,event);
                event.target.src = src;
            },
            imageMouseLeave:function (src,event){
                //console.log(src,event);
                event.target.src = src;
            },
            getDriver:function(){
                var mediaType= this.getMediaType();
                var pc=['pc'];
                var h5=['h5','wechat'];
                var type='h5';
                if (pc.includes(mediaType)) type = 'pc';
                if (h5.includes(mediaType)) type = 'h5';
                return type;
            },

            getMediaType:function (){
                return this.$root.$data.mediaType;
            },
            getImageSrc:function(element){
                var mediaType= this.getMediaType();
                var pc=['pc'];
                var h5=['h5','wechat'];
                var type='pc';
                if (pc.includes(mediaType)) type = 'pc';
                if (h5.includes(mediaType)) type = 'h5';

                return element.src[type];
            },
            getkey:function (){
              return _.uniqueId('uniqueId_');
            },
            getTextStyle: function (element) {
                return {
                    fontSize: element.style.fontSize + 'px',
                    color: element.style.fontColor,
                    textAlign: element.style.align,
                    fontWeight: element.style.fontWeight,
                    lineHeight:element.style.lineHeight + 'px',
                    backgroundColor:element.style.backgroundColor,
                    textIndent:element.style.textIndent + 'px',
                    marginTop:element.style.marginTop + 'px'
                }
            },
            bgImg:function (url){
                return {
                    backgroundImage:'url('+this.$root.$data.apiDomain+url+')'
                }
            },
            tabClick:function (tab, event){
                //console.log(tab, event);
            },
            getFirstTab:function (nodes){
                return nodes[0].tabName;
            }



        }
    };
</script>
<style scoped>
    html{
        font-size:16px;
    }
    .col1{flex-basis: 100% ;flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
    .col2{flex-basis: calc( (100% - 10px) /2 );flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
    .col3{flex-basis: calc( (100% - 10px) /3 );flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
    .col4{flex-basis: calc( (100% - 10px) /4 );flex-grow:0;flex-shrink:0;margin-bottom: 10px;}

    .flex-column{
        display: flex;
        flex-flow: column nowrap;
        text-align: center;
        justify-content: center;
        align-items: center;
        align-content: center;
    }
    .el-carousel__item h3 {
        color: #ffffff;
        font-size: 14px;
        opacity: 0.75;
        line-height: 150px;
        margin: 0;
    }

    .el-carousel__item:nth-child(2n) {
        background-color: #99a9bf;
    }

    .el-carousel__item:nth-child(2n+1) {
        background-color: #d3dce6;
    }
    body {
        background: #eee;
        font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
        font-size: 14px;
        color:#000;
        margin: 0;
        padding: 0;
    }
    .swiper-container {
        width: 100%;
        height: 100%;
    }
    .swiper-slide {
        text-align: center;
        font-size: 18px;
        background: #fff;

        /* Center slide text vertically */
        display: flex;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        -webkit-justify-content: center;
        justify-content: center;
        -webkit-box-align: center;
        -ms-flex-align: center;
        -webkit-align-items: center;
        align-items: center;
        width: 60%;
    }


   div.swiper .swiper-button-prev,div.swiper .swiper-button-next{
        display: none;
   }

   div.swiper:hover .swiper-button-prev,div.swiper:hover .swiper-button-next{
        display: block;
   }


    .swiper_title .el-carousel__item {
       background:#fff;
    }

    .swiper_title .title {
        text-align: center;
        height: 25px;
        font-size: 14px;
        font-weight: 600;
    }
    .swiper_title .desc{
        text-align: center;
        height: 25px;
        line-height: 40px;
        font-size: 12px;
        font-weight: 600;
        color: #999;
    }
    .swiper_title .button{
        text-align: center;
        height: 25px;
        margin-top: 10px;
    }
    .swiper_title .button span{
        font-size: 12px;
        padding: 2px 60px;
        border: 1px solid #000;
        box-shadow: 0px 0px 5px;
    }

    .swiper_horizontal{
        /*height: 150px;*/
    }
    .swiper_horizontal .el-carousel__item {
        background:url('/static/admin/images/swiper_horizontal.png') no-repeat 0 0 /100% 100%;
    }

    .swiper_horizontal .swiper-slide{
        width:30%;
    }

    .first-cate{ margin-bottom: 20px;}
    .sub-cate-wrap {
        display: flex;
        flex-flow: row wrap;
        justify-content: space-between;
    }


    .category_menu .cate-wrap{
        padding-right: 10px;
    }

    .swiper_product .el-carousel__item {
        background: #fff;
    }
    .swiper_product .title{
         text-align: center;
         height: 25px;
         font-size: 14px;
         font-weight: 600;
     }
    .swiper_product .desc{
        text-align: center;
        height: 25px;
        line-height: 40px;
        font-size: 12px;
        font-weight: 600;
        color:#999;
    }
    .swiper_product .button {
        text-align: center;
        height: 25px;
        margin-top: 10px;
    }
    .swiper_product .button span {
        font-size: 12px;
        padding: 2px 60px;
        border: 1px solid
        #000;
        box-shadow: 0px 0px 5px;
    }
    .list-group-item.cms-element.blank_space{
        min-height: auto !important;
        line-height: 12px !important;
    }

    .flex_block{
        display:flex;justify-content: space-between;flex-flow: column wrap
    }
    /* h5 */
    .cms-content-wrap.h5 .flex_block .item_1{
        width: 15.9375rem;
        margin-top: 5rem;
        align-self: flex-end;
    }
    .cms-content-wrap.h5 .flex_block .item_2{
        width: 15.9375rem;
        margin-top: 5rem;
        align-self: flex-start;
    }
    .cms-content-wrap.h5 .flex_block .item_3{
        margin-top: 5rem;
    }
    /* pc */
    .cms-content-wrap.pc .flex_block .item_0{
        width: 50%;
        align-self: self-start;
        margin-top: 0;
    }
    .cms-content-wrap.pc .flex_block .item_1{
        width: 30%;
        align-self: self-end;
        margin-top: -40%;
        margin-right: 10%;
    }
    .cms-content-wrap.pc .flex_block .item_2{
        width: 30%;
        align-self: self-start;
        margin-top: -12%;
    }
    .cms-content-wrap.pc .flex_block .item_3{
        width: 40%;
        align-self: self-end;
        margin-top: -40%;
        margin-right: 10%;
    }

    .image_flex_title{
        text-align: center;
        font-size: 1.5625rem;
        padding: 0;
        margin: 1rem 0 .3125rem;
        font-family: PlayfairDisplay-Regular,arial,sans-serif;
        color: #000;
    }
    .image_flex_desc{
        text-align: center;
        font-size: .8125rem;
        line-height: 1.3125rem;
        color: #575757;
        font-family: HelveticaNeueLTW05-45Light,arial,sans-serif;
        padding-top: 0;
    }
    .image_flex_button{
        color: #000;
        font-family: HelveticaNeueLTW05-75Bold,arial,sans-serif;
        font-size: .75rem;
        text-decoration: underline;
        line-height: inherit;
        cursor: pointer;
    }


</style>
