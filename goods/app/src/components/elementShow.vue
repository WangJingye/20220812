<template>
    <div>
        <template v-if="element.tag=='text'">
            <p tag="text" style="margin-bottom:0;" :style="getTextStyle(element)">
                {{element.content?element.content:element.placeholder}}
            </p>
        </template>
        <template v-if="element.tag=='textarea'">
            <div  tag="textarea" style="margin-bottom:0;" :style="getTextStyle(element)">
                {{element.content?element.content:element.placeholder}}
            </div>
        </template>
        <template v-if="element.tag=='image'">
            <div tag="image">
                <template v-if="element.src">
                    <img v-if="element.src" :src="element.src" style="width:100%;"/>
                </template>
                <template v-else>
                    {{element.placeholder}}
                </template>

            </div>
        </template>
        <template v-if="element.tag=='swiper'">
            <div class="block" tag="swiper">
                <template v-if="element.nodes===false || element.nodes.length==0">
                    <el-carousel height="150px" :autoplay="false">
                        <el-carousel-item v-for="item in 4" :key="item">
                            <h3 class="small" style="color:#fff;text-align: center;font-size: 20px">{{ item }}</h3>
                        </el-carousel-item>
                    </el-carousel>
                </template>
                <template v-else>
                    <el-carousel height="150px" :autoplay="false">
                        <el-carousel-item v-for="(item,i) in element.nodes" :key="i"
                                          :style="{backgroundImage:'url('+item.src+')'}"
                                          style="background-origin: padding-box;
                                             background-position: 50% 50%;
                                             background-position-x: 50%;
                                             background-position-y: 50%;
                                             background-repeat: no-repeat;
                                             background-size: contain;">
                        </el-carousel-item>
                    </el-carousel>
                </template>

            </div>
        </template>
        <template v-if="element.tag=='scrollView'">
            <div class="block" tag="scrollView">
                <template v-if="element.nodes===false || element.nodes.length==0">
                    <div style="height: 150px;background:#fff no-repeat center center / contain"
                         :style="bgImg('/static/admin/images/scrollView3.png')" > </div>
                </template>
                <template v-else>
                    <el-carousel height="150px" :autoplay="false" indicator-position="none">
                        <el-carousel-item v-for="(item,i) in element.nodes" :key="i"
                                          :style="bgImg('/static/admin/images/scrollView2.png')"
                                          style="
                                             display: flex;justify-content: start;
                                             align-items: center;
                                             background-origin: padding-box;
                                             background-color:#ffffff;
                                             background-position-x: 50%;
                                             background-position-y: 50%;
                                             background-repeat: no-repeat;
                                             background-size: contain;">
                            <div class="left"
                                 :style="{backgroundImage:'url('+item.src+')'}"
                                 style="
                                             width:90px;
                                             height: 90px;
                                             margin-left: 70px;
                                             background-origin: padding-box;
                                             background-color:#ffffff;
                                             background-position-x: right;
                                             background-position-y: 50%;
                                             background-repeat: no-repeat;
                                             background-size: 90px 90px;"
                            ></div>
                            <div class="right" style="width: 250px;height: 150px;">
                                <div style="line-height: 14px;font-size: 14px;margin-top: 25px;">{{item.title}}</div>
                                <div style="line-height: 14px;font-size: 14px;margin-top: 14px;color:#e8e8e8">{{item.content}}</div>
                                <div style="color:#df6972;">￥{{item.price}}</div>
                            </div>
                        </el-carousel-item>
                    </el-carousel>
                </template>
            </div>
        </template>

        <template v-if="element.tag=='product'">
            <div tag="product" style="display: flex;justify-content: start">
                    <div class="left" style="flex: 3">
                        <img :src="element.image" style="width:100%;"/>
                    </div>
                    <div class="right" style="flex:7">
                        <div class="title" style="font-size: 24px;">{{element.title}}</div>
                        <div class="content" style="font-size: 18px;line-height: 18px;">{{element.content}}</div>
                        <div class="p_time" style="line-height: 16px;color:#fd862c">7X23小时后结束</div>
                        <div class="price" style="line-height: 40px;color:#fd862c;">
                            <span>价格：￥</span><span >{{element.price}}</span> + <span>{{element.point}}积分</span>
                            <el-button type="warning" round style="background:#fd862c;color:#fff;float: right;margin-right: 60px;">免费申领</el-button>
                        </div>
                    </div>

            </div>
        </template>

        <template v-if="element.tag=='author'">
            <div tag="author" style="display: flex;justify-content: start;height: 90px;">
                <div class="icon" style="flex:2;margin:0;">
                    <img :src="element.icon" style="width:90px;height: 90px;"/>
                </div>
                <div class="desc" style="flex:8;margin:0;">
                    <p style="margin: 0;">{{element.author}}</p>
                    <p style="color:#e8e8e8">发布时间：{{element.time}}</p>
                </div>
            </div>
            <div class="title">{{element.title}}</div>
            <ul class="labels" style="display: flex;justify-content: start;">
                <li v-for="(item,i) in element.label" :key="i" style="margin-right:5px;"> <el-button size="small" round style="color:red;border:1px solid red;">{{item}}</el-button></li>
            </ul>
        </template>
    </div>
</template>

<script>
    export default {
        props:['element','index'],
        name: "element-show",
        components: {
        },
        data() {
            return {

            };
        },
        mounted: function () {

        },


        methods: {
            getTextStyle: function (element) {
                return {
                    fontSize: element.style.fontSize + 'px',
                    color: element.style.fontColor,
                    textAlign: element.style.align,
                    fontWeight: element.style.fontWeight,
                    lineHeight:element.style.lineHeight + 'px',
                //    backgroundColor:element.style.backgroundColor,
                    textIndent:element.style.textIndent + 'px',
                    marginTop:element.style.marginTop + 'px'
                }
            },
            bgImg:function (url){
                return {
                    backgroundImage:'url('+this.$root.$data.apiDomain+url+')'
                }
            }

        }
    };
</script>
<style scoped>
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


</style>
