<template>
    <div>
        <div class="row dnd_wrap" style="display: flex;justify-content: flex-start;flex-flow: row nowrap;width:100%">
            <div class=" element-list-wrap" :style="{width:$root.$data.tagWindow}" style="border-top: 2px solid #f2f2f2;padding-top:20px;">

                <el-tabs tab-position="left" type="border-card"  style="min-height: 500px;">
                    <el-tab-pane label="文本模块" v-if="showTab(list1)">
                        <draggable
                                class="dragArea list-group element-list"
                                :list="list1"
                                :group="{ name: 'people', pull: 'clone', put: true }"
                                :clone="cloneDog"
                                @change="log1"
                        >
                            <div class="list-group-item element " :class="element.tag"  :title="element.name" v-for="(element,i) in list1" :key="i" v-if="showTag(element)">
                                <p class="iconfont" :class="'icon_'+element.tag"></p>
                                <p class="tag-name">{{element.name}}</p>
                            </div>
                        </draggable>
                    </el-tab-pane>
                    <el-tab-pane label="图片模块" v-if="showTab(list2)">
                        <draggable
                                class="dragArea list-group element-list"
                                :list="list2"
                                :group="{ name: 'people', pull: 'clone', put: true }"
                                :clone="cloneDog"
                                @change="log2"
                        >
                            <div class="list-group-item element" :class="element.tag"  :title="element.name" v-for="(element,i) in list2" :key="i" v-if="showTag(element)">
                                <template v-if="element.tag=='image_prod_default' || element.tag=='image_model_default'">
                                    <img :src ="element.src" style="width: 100%;height:100%" />
                                </template>
                                <template v-else>
                                    <p class="iconfont" :class="'icon_'+element.tag"></p>
                                    <p class="tag-name">{{element.name}}</p>
                                </template>

                            </div>
                        </draggable>
                    </el-tab-pane>
                    <el-tab-pane label="商品推荐" v-if="showTab(list40)">
                        <draggable
                                class="dragArea list-group element-list"
                                :list="list40"
                                :group="{ name: 'people', pull: 'clone', put: true }"
                                :clone="cloneDog"
                                @change="log40"
                        >
                            <div class="list-group-item element" :class="element.tag"  :title="element.name" v-for="(element,i) in list40" :key="i" v-if="showTag(element)">
                                <template v-if="element.tag=='image_prod_default' || element.tag=='image_model_default'">
                                    <img :src ="element.src" style="width: 100%;height:100%" />
                                </template>
                                <template v-else>
                                    <p class="iconfont" :class="'icon_'+element.tag"></p>
                                    <p class="tag-name">{{element.name}}</p>
                                </template>

                            </div>
                        </draggable>
                    </el-tab-pane>
                    <el-tab-pane label="弹窗模块" v-if="showTab(list50)">
                        <draggable
                                class="dragArea list-group element-list"
                                :list="list50"
                                :group="{ name: 'people', pull: 'clone', put: true }"
                                :clone="cloneDog"
                                @change="log50"
                        >
                            <div class="list-group-item element" :class="element.tag"  :title="element.name" v-for="(element,i) in list50" :key="i" v-if="showTag(element)">
                                <template v-if="element.tag=='image_prod_default' || element.tag=='image_model_default'">
                                    <img :src ="element.src" style="width: 100%;height:100%" />
                                </template>
                                <template v-else>
                                    <p class="iconfont" :class="'icon_'+element.tag"></p>
                                    <p class="tag-name">{{element.name}}</p>
                                </template>

                            </div>
                        </draggable>
                    </el-tab-pane>
                    <el-tab-pane label="视频模块" v-if="showTab(list30)">
                        <draggable
                                class="dragArea list-group element-list"
                                :list="list30"
                                :group="{ name: 'people', pull: 'clone', put: true }"
                                :clone="cloneDog"
                                @change="log30"
                        >
                            <div class="list-group-item element" :class="element.tag"  :title="element.name" v-for="(element,i) in list30" :key="i" v-if="showTag(element)">
                                <template v-if="element.tag=='image_prod_default' || element.tag=='image_model_default'">
                                    <img :src ="element.src" style="width: 100%;height:100%" />
                                </template>
                                <template v-else>
                                    <p class="iconfont" :class="'icon_'+element.tag"></p>
                                    <p class="tag-name">{{element.name}}</p>
                                </template>

                            </div>
                        </draggable>
                    </el-tab-pane>
                    <el-tab-pane label="图片块布局" v-if="showTab(list20)">
                        <draggable
                                class="dragArea list-group element-list"
                                :list="list20"
                                :group="{ name: 'people', pull: 'clone', put: true }"
                                :clone="cloneDog"
                                @change="log20"
                        >
                            <div class="list-group-item element" :class="element.tag"  :title="element.name" v-for="(element,i) in list20" :key="i" v-if="showTag(element)">
                                <template v-if="element.tag=='image_prod_default' || element.tag=='image_model_default'">
                                    <img :src ="element.src" style="width: 100%;height:100%" />
                                </template>
                                <template v-else>
                                    <p class="iconfont" :class="'icon_'+element.tag"></p>
                                    <p class="tag-name">{{element.name}}</p>
                                </template>

                            </div>
                        </draggable>
                    </el-tab-pane>
                    <el-tab-pane label="轮播模块" v-if="showTab(list3)">
                        <draggable
                                class="dragArea list-group element-list"
                                :list="list3"
                                :group="{ name: 'people', pull: 'clone', put: true }"
                                :clone="cloneDog"
                                @change="log3"
                        >
                            <div class="list-group-item element"  :title="element.name" v-for="(element,i) in list3" :key="i" v-if="showTag(element)">
                                <p class="iconfont" :class="'icon_'+element.tag"></p>
                                <p class="tag-name">{{element.name}}</p>
                            </div>
                        </draggable>
                    </el-tab-pane>
                    <el-tab-pane label="动画模块" v-if="showTab(list4)">
                        <draggable
                                class="dragArea list-group element-list"
                                :list="list4"
                                :group="{ name: 'people', pull: 'clone', put: true }"
                                :clone="cloneDog"
                                @change="log4"
                        >
                            <div class="list-group-item element"  :title="element.name" v-for="(element,i) in list4" :key="i" v-if="showTag(element)">
                                <p class="iconfont" :class="'icon_'+element.tag"></p>
                                <p class="tag-name">{{element.name}}</p>
                            </div>
                        </draggable>
                    </el-tab-pane>
                    <el-tab-pane label="分类导航" v-if="false">
                        <draggable
                                class="dragArea list-group element-list"
                                :list="list6"
                                :group="{ name: 'people', pull: 'clone', put: true }"
                                :clone="cloneDog"
                                @change="log6"
                        >
                            <div class="list-group-item element "  :title="element.name" v-for="(element,i) in list6" :key="i" v-if="showTag(element)">
                                <p class="iconfont" :class="'icon_'+element.tag"></p>
                                <p class="tag-name">{{element.name}}</p>
                            </div>
                        </draggable>
                    </el-tab-pane>
                    <el-tab-pane label="商品及分类" v-if="showTab(list10)">
                        <draggable
                                class="dragArea list-group element-list"
                                :list="list10"
                                :group="{ name: 'people', pull: 'clone', put: true }"
                                :clone="cloneDog"
                                @change="log10"
                        >
                            <div class="list-group-item element "  :title="element.name" v-for="(element,i) in list10" :key="i" v-if="showTag(element)">
                                <p class="iconfont" :class="'icon_'+element.tag"></p>
                                <p class="tag-name">{{element.name}}</p>
                            </div>
                        </draggable>
                    </el-tab-pane>


                </el-tabs>
            </div>

            <div class=" cms-content-wrap" :class="$root.$data.mediaType" :style="{width:$root.$data.showWindow}">
                <div style="display: flex;flex-flow: column nowrap;justify-content: center;align-content: center;align-items: center;background: #f2f2f2;padding-top: 20px;">
                    <div class="phone" style="background:url('/static/admin/images/phone.png') no-repeat 0 0;width:380px;height:58px;">
                        <h5>{{title}}</h5>
                    </div>
                    <div v-if="$root.showHeaderFooter == '1'" class="header" style="text-align: center;background: url('/static/admin/images/header.png') no-repeat 0 0/100% 100%;"></div>
                    <draggable
                            class="dragArea list-group cms-content"
                            tag="ul"
                            :list="$root.$data.nodes[this.type]"
                            group="people"
                            @change="log"
                            v-bind="dragOptions"
                    >
                        <transition-group type="transition" name="flip-list">
                            <li  class="list-group-item cms-element"
                                 :class="[currentElement==element?'current':'',element.tag]"
                                 style="min-height: 50px;padding:0;line-height: 50px; "
                                 @dblclick="setCurrentElement(element,index)"
                                 v-for="(element, index) in $root.$data.nodes[this.type]"
                                 :key="index + 1"
                            >
                                <i class="el-icon-delete" @click="deleteElement($root.$data.nodes[type],index)"></i>
                                <element-show :element="element" :index="index"></element-show>
                            </li>
                        </transition-group>
                    </draggable>
                    <div v-if="$root.showHeaderFooter == '1'" class="footer" style="text-align: center;border:1px solid #ccc;">footer</div>
                </div>
            </div>


            <div class=" edit-wrap"  :style="{width:this.$root.$data.editWindow}" style="padding-left:20px;padding-top:20px;border-top: 2px solid #f2f2f2;">
                <h3><i class="el-icon-setting" style="margin-right: 4px;"></i>{{currentElement.name}}</h3>
                <element-edit :currentElement="currentElement"></element-edit>
            </div>

        </div>

        <input type="hidden" :name="'content['+type+']'" :value="JSON.stringify($root.$data.nodes[type])" />

        <el-dialog class="" title="CMS内容管理" :visible.sync="$root.$data.dialogVisible" :close-on-click-modal="false" :modal="false" width="80%" top="0">
            <rawDisplayer class="col-6" :value="$root.$data.nodes[type]" title="JSON数据"/>
        </el-dialog>

    </div>

</template>


<script>
    import draggable from "@/vuedraggable";

    export default {
        // props: ['api_domain','oss_domain','title','use_tags'],
        props:{
            type:String,
            use_input:String,
            page_source:String,
            upload_url:String,
            api_domain:String,
            oss_domain:String,
            title:String,
            use_tags:{
                type: Array,
                default: function (){
                    return [];
                },
                required: false,
            },
            not_use_tags:{
                type: Array,
                default: function (){
                    return [];
                },
                required: false,
            }
        },
        name: "custom-clone",
        display: "Custom Clone",
        components: {
            draggable
        },
        data() {
            return {
                list1: [],
                list2: [],
                list3: [],
                list4: [],
                list5: [],
                list6:[],
                list10:[],
                list20:[],
                list30:[],
                list40:[],
                list50:[],
                list: [],
                drag: false,
                currentElement: false,
            };
        },
        mounted: function () {
            if(this.page_source){
                this.$root.$data.pageSource=this.page_source;
            }
            if(this.upload_url){
                this.$root.$data.uploadUrl=this.upload_url;
            }
            if(this.api_domain!=''){
                this.$root.$data.apiDomain=this.api_domain;
            }
            if(this.oss_domain!=''){
                this.$root.$data.ossDomain=this.oss_domain;
            }
            if(this.mediaType){
                this.$root.$data.mediaType=this.mediaType;
            }
            this.list1 = this.getList1();
            this.list2 = this.getList2();
            this.list3 = this.getList3();
            this.list4 = this.getList4();
            this.list5 = this.getList5();
            this.list6 = this.getList6();
            this.list10 = this.getList10();
            this.list20 = this.getList20();
            this.list30 = this.getList30();
            this.list40 = this.getList40();
            this.list50 = this.getList50();
            //this.list = this.$root.$data.nodes[this.type];
            // console.log(this.use_tags);


        },
        methods: {
            showTab:function (list){
                if(list.length == 0){
                    return false;
                }
                if(this.use_tags.length==0){
                    return true;
                }else{
                    let result=_.filter(list, (element)=> {
                        return  this.use_tags.includes(element.tag);
                    });
                    if(result.length>0){
                        return true;
                    }else{
                        return false;
                    }
                }



            },
            showTag:function (element){
                if(this.not_use_tags.length>0 && this.not_use_tags.includes(element.tag)){
                    return false;
                }

                if(this.use_tags.length==0){
                    return true;
                }else{
                    return this.use_tags.includes(element.tag);
                }
            },
            getContent:function (){},
            getList1: function () {
                return [
                    // {
                    //     tag: 'blank_space',
                    //     name: "空白组件",
                    //     height:2
                    // },
                    // {
                    //     tag: 'txt',
                    //     name: "文本",
                    //     text:"帕尔玛之水"
                    // },
                ];
            },
            getList2() {
                return [
                    // {
                    //     tag: 'anchor_point',
                    //     name: "锚点",
                    //     text: "",
                    //     action: false,
                    // },
                    {
                        tag: 'image',
                        name: "单图组件",
                        src: false,
                        action: false,
                    },
                    {
                        tag: 'multi_image',
                        name: "多图",
                        nodes: false,
                        height:'',
                        columns:false,
                    },
                    {
                        tag: 'image_map',
                        name: "一图片多链接",
                        src: this.$root.$data.ossDomain + "/cms/upload/demo/p2.png",
                        rows:2,
                        columns:3,
                        height:"",
                        actions:false
                    },
                ];
            },
            getList40() {
                return [

                ];
            },
            getList50() {
                return [
                ];
            },
            getList30() {
                return [
                    {
                    tag: 'video',
                    name: "视频组件",
                    //src: this.$root.$data.ossDomain + "/cms/upload/demo/34.png",
                    src:"",
                    video:"",
                    height:400,
                    isAuto:false,
                    isLoop:false,
                },
                ];
            },
            getList20() {
                return [


                ];
            },
            getList3() {
                return [
                    {
                        tag: 'swiper',
                        name: "轮播",
                        nodes: false,
                        height:'',
                        columns:false,
                        isAuto:false,
                        isLoop:false,
                    },
                    // {
                    //     tag: 'swiper_fraction',
                    //     class:"",
                    //     name: "轮播-页码",
                    //     nodes: false,
                    //     height:'',
                    //     columns:false,
                    //     page_height:66,
                    // },
                    {
                        tag: 'swiper_fraction_v2',
                        class:"",
                        name: "轮播-页码位置",
                        nodes: false,
                        height:'',
                        columns:false,
                        top:80,
                        left:50,
                        isAuto:false,
                        isLoop:false,
                    },
                    {
                        tag: 'multi_image_product_swiper',
                        name: "图片左右滑屏",
                        placeholder_pc: this.$root.$data.ossDomain + "/cms/upload/demo/multi_image_product_swiper_pc.png",
                        placeholder_h5: this.$root.$data.ossDomain + "/cms/upload/demo/30.png",
                        nodes: false,
                        columns:false
                    },
                    // {
                    //     tag: 'image_on_text_swiper',
                    //     name: "图片上有文字左右滑动",
                    //     placeholder_pc: this.$root.$data.ossDomain + "/cms/upload/demo/image_on_text_swiper_pc.png",
                    //     placeholder_h5: this.$root.$data.ossDomain + "/cms/upload/demo/31.png",
                    //     nodes: false,
                    //     columns:false
                    // },
                    // {
                    //     tag: 'ranging_list',
                    //     name: "排行榜",
                    //     nodes:[]
                    // },
                ];
            },

            getList4() {
                return [
                    // {
                    //     tag: 'in_video_animate',
                    //     name: "滑入视频动画",
                    //     placeholder_pc: this.$root.$data.ossDomain + "/cms/upload/demo/in_text_animate_pc.png",
                    //     placeholder_h5: this.$root.$data.ossDomain + "/cms/upload/demo/in_text_animate_h5.png",
                    //     nodes: false,
                    //     type:false
                    // }
                ]
            },
            getList5() {
                return [];
            },
            getList6() {
                return [
                    {
                        tag: 'navication_menu',
                        placeholder_pc: this.$root.$data.ossDomain + "/cms/upload/demo/navication_pc.png",
                        placeholder_h5: this.$root.$data.ossDomain + "/cms/upload/demo/navication_h5.png",
                        name: "导航组件",
                        nodes: false
                    },
                ];
            },
            getList10() {
                return [
                    // {
                    //     tag: 'category_list',
                    //     placeholder_pc: this.$root.$data.ossDomain + "/cms/upload/demo/categroy_list_pc.png",
                    //     placeholder_h5: this.$root.$data.ossDomain + "/cms/upload/demo/categroy_list_h5.png",
                    //     name: "分类商品",
                    //     category_id: "",
                    //     category_name: ""
                    // },
                ];
            },
            log1: function (evt) {
                this.list1 = this.getList1();
            },
            log2: function (evt) {
                this.list2 = this.getList2();
            },
            log3: function (evt) {
                this.list3 = this.getList3();
            },
            log4: function (evt) {
                this.list4 = this.getList4();
            },
            log5: function (evt) {
                this.list5 = this.getList5();
            },
            log6: function (evt) {
                this.list6 = this.getList6();
            },
            log10: function (evt) {
                this.list10 = this.getList10();
            },
            log20: function (evt) {
                this.list20 = this.getList20();
            },
            log30: function (evt) {
                this.list30 = this.getList30();
            },
            log40: function (evt) {
                this.list40 = this.getList40();
            },
            log50: function (evt) {
                this.list50 = this.getList50();
            },
            log: function (evt) {
                if (typeof evt.added != 'undefined') this.currentElement = evt.added.element;
                if (typeof evt.moved != 'undefined') this.currentElement = evt.moved.element;

            },
            setCurrentElement: function (element,index) {
                this.currentElement = element;

            },

            cloneDog(element) {
                let newElement = Object.assign({}, element);

                if(newElement.tag=='product_detail_text' ){
                    newElement.lines=[];

                }

                if(newElement.tag=='image'  || newElement.tag=='banner_image'){
                    newElement.action={
                        data:{},
                        type:'none',
                        route:false
                    };
                    newElement.src={
                        pc: this.$root.$data.ossDomain + "/cms/upload/demo/pc.png",
                        h5: this.$root.$data.ossDomain + "/cms/upload/demo/h5.png",
                    };
                }
                if(newElement.tag=='image_2text' ){
                    newElement.action={
                        data:{},
                        type:'none',
                        route:false
                    };
                    newElement.src={
                        pc: this.$root.$data.ossDomain + "/cms/upload/demo/pc.png",
                        h5: this.$root.$data.ossDomain + "/cms/upload/demo/h5.png",
                    };
                }
                if(newElement.tag=='image_flex_image' ){
                    newElement.action={
                        data:{},
                        type:'none',
                        route:false
                    };
                    newElement.src={
                        pc: this.$root.$data.ossDomain + "/cms/upload/demo/pc.png",
                        h5: this.$root.$data.ossDomain + "/cms/upload/demo/h5.png",
                    };
                }
                if(newElement.tag=='multi_line_text'){
                    newElement.lines=[
                        {text:"SISLEY, L’EXPERTISE DU VÉGÉTAL AU SERVICE DE LA BEAUTÉ"},
                        {text:"Depuis 1976, SISLEY met à l’honneur la phyto-cosmétologie à travers des soins uniques."}
                    ];
                }
                if(newElement.tag=="multi_image_product" || newElement.tag=="multi_image_product_high"){
                    newElement.columns={
                        pc:3,
                        h5:1
                    };
                }

                if(newElement.tag=="multi_image_product_recommend"){
                    newElement.columns={
                        pc:4,
                        h5:2
                    };
                }



                if(newElement.tag=="multi_image_icon"){
                    newElement.columns={
                        pc:6,
                        h5:2
                    };
                }
                if(
                    newElement.tag=='multi_image_product_swiper'
                ){
                    newElement.nodes=[];
                    // newElement.columns={
                    //     pc:3,
                    //     h5:1
                    // };

                }
                if(
                    newElement.tag=='product_swiper'
                ){
                    newElement.nodes=[
                        {product_id:"",sku:"",src:"",name:"",desc:"",price:""},
                        {product_id:"",sku:"",src:"",name:"",desc:"",price:""},
                        {product_id:"",sku:"",src:"",name:"",desc:"",price:""},
                    ];
                    newElement.columns={
                        pc:3,
                        h5:1
                    };

                }
                if(
                    newElement.tag=='image_on_text_swiper'
                ){
                    newElement.nodes=[];
                    newElement.columns={
                        pc:2,
                        h5:1
                    };

                }

                if(
                    newElement.tag=='in_text_animate' || newElement.tag=='in_text_animate_middle'
                ){
                    newElement.nodes={
                        "src":"",
                        "bgColor":"",
                        "title":"",
                        "desc":""
                    };
                }

                if(
                    newElement.tag=='in_video_animate'
                ){
                    newElement.nodes={
                        "video":"",
                        "src":"",
                        "bgColor":"",
                        "title":"",
                        "desc":""
                    };
                }

                if(
                    newElement.tag=='ranking_list'
                ){
                }

                if(
                    newElement.tag=='x_swiper'
                ){
                    newElement.title="附魔香水";
                    // newElement.nodes=[
                    //     {
                    //         "name":"香水",
                    //         "list":[
                    //             {
                    //                 "title": "Eau du Soir",
                    //                 "src": "/upload/demo/EauDeCampagne.png",
                    //                 "alt": "Eau du Soir 30 ml",
                    //                 "desc": "Une eau de parfum raffinée, contrastée"
                    //             },
                    //             {
                    //                 "title": "Eau du Soir",
                    //                 "src": "/upload/demo/EauDeCampagne.png",
                    //                 "alt": "Eau du Soir 30 ml",
                    //                 "desc": "Une eau de parfum raffinée, contrastée"
                    //             },
                    //             {
                    //                 "title": "Eau du Soir",
                    //                 "src": "/upload/demo/EauDeCampagne.png",
                    //                 "alt": "Eau du Soir 30 ml",
                    //                 "desc": "Une eau de parfum raffinée, contrastée"
                    //             },
                    //             {
                    //                 "title": "Eau du Soir",
                    //                 "src": "/upload/demo/EauDeCampagne.png",
                    //                 "alt": "Eau du Soir 30 ml",
                    //                 "desc": "Une eau de parfum raffinée, contrastée"
                    //             },
                    //             {
                    //                 "title": "Eau du Soir",
                    //                 "src": "/upload/demo/EauDeCampagne.png",
                    //                 "alt": "Eau du Soir 30 ml",
                    //                 "desc": "Une eau de parfum raffinée, contrastée"
                    //             },
                    //             {
                    //                 "title": "Eau du Soir",
                    //                 "src": "/upload/demo/EauDeCampagne.png",
                    //                 "alt": "Eau du Soir 30 ml",
                    //                 "desc": "Une eau de parfum raffinée, contrastée"
                    //             }
                    //
                    //            ]
                    //     },
                    //     {
                    //         "name":"水域",
                    //         "list":[
                    //             {
                    //                 "title": "Eau du Soir",
                    //                 "src": "/upload/3.png",
                    //                 "alt": "Eau du Soir 30 ml",
                    //                 "desc": "Une eau de parfum raffinée, contrastée"
                    //             },
                    //             {
                    //                 "title": "Eau du Soir",
                    //                 "src": "/upload/3.png",
                    //                 "alt": "Eau du Soir 30 ml",
                    //                 "desc": "Une eau de parfum raffinée, contrastée"
                    //             },
                    //             {
                    //                 "title": "Eau du Soir",
                    //                 "src": "/upload/3.png",
                    //                 "alt": "Eau du Soir 30 ml",
                    //                 "desc": "Une eau de parfum raffinée, contrastée"
                    //             },
                    //             {
                    //                 "title": "Eau du Soir",
                    //                 "src": "/upload/3.png",
                    //                 "alt": "Eau du Soir 30 ml",
                    //                 "desc": "Une eau de parfum raffinée, contrastée"
                    //             },
                    //             {
                    //                 "title": "Eau du Soir",
                    //                 "src": "/upload/3.png",
                    //                 "alt": "Eau du Soir 30 ml",
                    //                 "desc": "Une eau de parfum raffinée, contrastée"
                    //             },
                    //         ]
                    //     },
                    //     {
                    //         "name":"蜡烛",
                    //         "list":[
                    //             {
                    //                 "title": "Eau du Soir",
                    //                 "src": "/upload/2.png",
                    //                 "alt": "Eau du Soir 30 ml",
                    //                 "desc": "Une eau de parfum raffinée, contrastée"
                    //             },
                    //             {
                    //                 "title": "Eau du Soir",
                    //                 "src": "/upload/2.png",
                    //                 "alt": "Eau du Soir 30 ml",
                    //                 "desc": "Une eau de parfum raffinée, contrastée"
                    //             },
                    //             {
                    //                 "title": "Eau du Soir",
                    //                 "src": "/upload/2.png",
                    //                 "alt": "Eau du Soir 30 ml",
                    //                 "desc": "Une eau de parfum raffinée, contrastée"
                    //             },
                    //             {
                    //                 "title": "Eau du Soir",
                    //                 "src": "/upload/2.png",
                    //                 "alt": "Eau du Soir 30 ml",
                    //                 "desc": "Une eau de parfum raffinée, contrastée"
                    //             },
                    //             {
                    //                 "title": "Eau du Soir",
                    //                 "src": "/upload/2.png",
                    //                 "alt": "Eau du Soir 30 ml",
                    //                 "desc": "Une eau de parfum raffinée, contrastée"
                    //             },
                    //             {
                    //                 "title": "Eau du Soir",
                    //                 "src": "/upload/2.png",
                    //                 "alt": "Eau du Soir 30 ml",
                    //                 "desc": "Une eau de parfum raffinée, contrastée"
                    //             },
                    //         ]
                    //     }
                    // ];
                }

                if(newElement.tag=='image_map'){
                    newElement.actions=[];
                    newElement.actions=[
                        [
                            {
                                "action": {
                                    "data": {},
                                    "type": "none",
                                    "route": {}
                                }
                            },
                            {
                                "action": {
                                    "data": {},
                                    "type": "none",
                                    "route": {}
                                }
                            },
                            {
                                "action": {
                                    "data": {},
                                    "type": "none",
                                    "route": {}
                                }
                            }
                        ],
                        [
                            {
                                "action": {
                                    "data": {},
                                    "type": "none",
                                    "route": {}
                                }
                            },
                            {
                                "action": {
                                    "data": {},
                                    "type": "none",
                                    "route": {}
                                }
                            },
                            {
                                "action": {
                                    "data": {},
                                    "type": "none",
                                    "route": {}
                                }
                            }
                        ]
                    ]
                }
                return newElement;

            },
            removeAt(idx) {
                this.list.splice(idx, 1);
            },
            deleteElement(list,index){
                list.splice(index, 1);
            },

            getCurrent: function () {
                if (this.list) {

                } else {
                    return {};
                }
            }

        },
        computed: {
            dragOptions() {
                return {
                    animation: 200,
                    group: "description",
                    disabled: false,
                    ghostClass: "ghost"
                };
            }
        },
    };
</script>
<style scoped>

    .button {
        margin-top: 35px;
    }

    .handle {
        float: left;
        padding-top: 8px;
        padding-bottom: 8px;
    }

    .close {
        float: right;
        padding-top: 8px;
        padding-bottom: 8px;
    }

    input {
        display: inline-block;
        width: 50%;
    }

    .text {
        margin: 20px;
    }

    .button {
        margin-top: 35px;
    }

    .flip-list-move {
        transition: transform 0.5s;
    }

    .no-move {
        transition: transform 0s;
    }

    .ghost {
        opacity: 0.5;
        background: #c8ebfb;
    }

    .list-group {
        min-height: 20px;
    }

    .list-group-item {
        cursor: move;
    }

    .list-group-item i {
        cursor: pointer;
    }

    input {
        width: 100%;
    }

    /*
    .element{
        background-image:url('/static/admin/images/icon.png');
        background-repeat:no-repeat;
        transform: scale(0.8);
    }

    .element.title_no_line{
        background-position-x: -146px;
        background-position-y: -139px;
    }
    .element.title_line{
        background-position-x: -286px;
        background-position-y: -139px;
    }
    */
    .element.image{
        background-position-x: -143px;
        background-position-y: -420px;
    }
    .element.image_map{
        background-position-x: -287px;
        background-position-y: -420px;
    }
    .element.column_2_title{
        background-position-x: 0px;
        background-position-y: -273px;
    }
    .element.column_2_btn{
        background-position-x: -145px;
        background-position-y: -273px;
    }
    .element.image_tab{
        background-position-x: 0px;
        background-position-y: -415px;
    }
    .element.video{
        background-position-x: -288px;
        background-position-y: -278px;
    }
    .element.swiper{
        background-position-x: 0px;
        background-position-y: 0px;
    }
    .element.swiper_title{
        background-position-x: -145px;
        background-position-y: 0px;
    }
    .element.swiper_horizontal{
        background-position-x: -286px;
        background-position-y: 0px;
    }


    .iconfont::before {
        display: block;
        color:#707e8d;
        text-align: center;
        padding-top:10px;
        font-size: 24px;
    }
    .icon_title_no_line::before{
        font-size:14px;
    }
    .icon_title_line::before{
        font-size:18px;
    }
    p.tag-name{
        text-align: center;
    }


    .phone{
        position: relative;
    }
    .phone h5 {
        position: absolute;
        bottom: 4px;
        width: 100%;
        font-size: 16px;
        text-align: center;
    }


</style>
