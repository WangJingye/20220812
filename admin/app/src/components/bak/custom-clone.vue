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
                    <el-tab-pane label="分类导航" v-if="showTab(list6)">
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
                    <el-tab-pane label="促销模块" v-if="showTab(list10)">
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
                    <div class="phone" style="background:url('/static/admin/images/phone.png') no-repeat 0 0/cover;width:380px;height:58px;">
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

        <input type="hidden" :name="'content['+type+']'" :value="JSON.stringify($root.$data.nodes[type])" v-if="use_tags.length==0"/>

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
                // list4: [],
                list5: [],
                list6:[],
                list10:[],
                list: [],
                drag: false,
                currentElement: false,
            };
        },
        mounted: function () {
            if(this.api_domain!=''){
                this.$root.$data.apiDomain=this.api_domain;
            }
            if(this.oss_domain!=''){
                this.$root.$data.ossDomain=this.oss_domain;
            }
            this.list1 = this.getList1();
            this.list2 = this.getList2();
            this.list3 = this.getList3();
            // this.list4 = this.getList4();
            this.list5 = this.getList5();
            this.list6 = this.getList6();
            this.list10 = this.getList10();
            //this.list = this.$root.$data.nodes[this.type];

            // console.log(this.use_tags);


        },
        methods: {
            showTab:function (list){
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
                    //     tag: 'text',
                    //     name: "文本",
                    //     style:false,
                    //     placeholder: "文本",
                    //     textIndentCount:'0',
                    //     action: false,
                    //     content: "试用小样  NEW"
                    // }, {
                    //     tag: 'textarea',
                    //     name: "段落",
                    //     style:false,
                    //     placeholder: "请输入段落",
                    //     textIndentCount:'2',
                    //     content: "一个人的生活的状态会不自觉地通过言谈举止表露出来。面对邓女士汪先生夫妇的时候，会很直观地感受到他们的幸福和满足，也能感受到他们在经营这个小家庭时的用心。"
                    // }
                    {
                        tag: 'title_line',
                        name: "标题有装饰线",
                        style:false,
                        enTitle:'CATEGORIES',
                        cnTitle:'产品类别',


                    },
                    {
                        tag: 'title_no_line',
                        name: "标题无装饰线",
                        style:false,
                        title:'灵感源自V&A博物馆',
                        description:'重新演绎珠宝藏品',
                    },
                    {
                        tag: 'button',
                        name: "按钮",
                        text:'立即查看',
                        action: false,
                    },
                    {
                        tag: 'blank_space',
                        name: "空白组件",
                        height:2
                    },
                    {
                        tag: 'icon_title',
                        name: "图标标题",
                        title:'LES INCONTOURNABLES',
                    },
                    {
                        tag: 'multi_line_text',
                        name: "多行文本",
                        title:"Sisley Paris",
                        lines:[
                            "SISLEY, L’EXPERTISE DU VÉGÉTAL AU SERVICE DE LA BEAUTÉ",
                            "Depuis 1976, SISLEY met à l’honneur la phyto-cosmétologie à travers des soins uniques.",
                        ],
                        button:"Voir plus"

                    }
                ];
            },
            getList2() {
                return [{
                    tag: 'image',
                    name: "单图组件",
                    src:{
                        pc:"/upload/demo/1.jpg",
                        h5:"/upload/demo/2.jpg",
                    },
                    action: false,
                },

                    // {
                    //     tag: 'multi_image',
                    //     name: "多图",
                    //     nodes: false,
                    //     columns:2,
                    // },
                    {
                        tag: 'image_map',
                        name: "一图片多链接",
                        src: this.$root.$data.ossDomain + "/cms/upload/demo/1.jpg",
                        rows:1,
                        columns:2,
                        height:200,
                        actions:false
                    },
                    {
                        tag: 'column_2_title',
                        name: "双宝贝",
                        nodes: false,
                        columns:2,
                    },
                    {
                        tag: 'column_2_btn',
                        name: "双列带按钮",
                        nodes: false,
                        height:'',
                        columns:{
                            pc:4,
                            h5:2
                        },
                    },
                    {
                        tag: 'multi_image_product',
                        name: "多图（商品）",
                        nodes: false,
                        height:'',
                        columns:{
                            pc:3,
                            h5:1
                        },
                    },
                    {
                        tag: 'multi_image_icon',
                        name: "多图（底部图标）",
                        nodes: false,
                        height:'',
                        columns:{
                            pc:6,
                            h5:2
                        },
                    },
                    {
                        tag: 'image_tab',
                        name: "单图带TAB切换",
                        nodes: false,
                        height:0,
                        productCount:2,

                    },
                    {
                        tag: 'video',
                        name: "视频组件",
                        src: this.$root.$data.ossDomain + "/cms/upload/demo/1.jpg",
                        action: false,
                    },
                    {
                        tag: 'image_prod_default',
                        name: "产品展示",
                        src: this.$root.$data.ossDomain + "/miniStore/pdt-detail/prod_default.jpg",
                        action: false,
                    },

                    {
                        tag: 'image_model_default',
                        name: "模特展示",
                        src: this.$root.$data.ossDomain + "/miniStore/pdt-detail/model_default.jpg",
                        action: false,
                    },
                    {
                        tag: 'product_list',
                        name: "产品展示",
                        placeholder: this.$root.$data.ossDomain + "/cms/upload/demo/prod_list.jpg",
                        cateogry_id:"",
                        cateogry_name:"",
                        show_count:6,
                    },
                    {
                        tag: 'image_flex',
                        name: "图片块布局",
                        placeholder: this.$root.$data.ossDomain + "/upload/demo/image_flex.png",
                        nodes: false,
                    },

                ];
            },
            getList3() {
                return [
                    {
                        tag: 'swiper',
                        name: "轮播组件",
                        nodes: false,
                        height:0,
                    },
                    {
                        tag: 'swiper_title',
                        name: "图片轮播",
                        nodes: false,
                        height:0
                    },
                    {
                        tag: 'swiper_product',
                        name: "宝贝轮播",
                        nodes: false,
                    },
                    {
                        tag: 'swiper_horizontal',
                        name: "左右轮播",
                        nodes: false,
                        height:0
                    },

                ];
            },

            getList4() {
                return [{tag: 'video', name: "视频",src:'',screenshot:'',url:""}];
            },
            getList5() {
                return [{
                    tag: 'product',
                    name: "商品",
                    src: this.$root.$data.apiDomain+"upload/demo/1.jpg",
                    title:'demo-01',
                    content:'测试01',
                    price:'￥19.99',
                    image:"https://cdn2.chowsangsang.com/cneshop/images/p/r/68119r/EPCM68119GDR_3fc39937-f9e3-4a26-ab86-abe091288dd6_350.jpg",
                    url: ''
                },{
                    tag: 'coupon',
                    name: "优惠劵",
                    nodes: false,
                    columns:2,
                }];
            },
            getList6() {
                return [{
                    tag: 'category_menu',
                    name: "分类导航",
                    nodes: false,
                    newCategoryName:'',
                    newSubCategoryName:''
                }];
            },
            getList10() {
                return [{
                    tag: 'coupon_single',
                    name: "单优惠劵",
                    src: this.$root.$data.ossDomain + "/cms/upload/demo/coupon1.png",
                    coupon:{
                        coupon_id: "",
                        coupon_name:"",
                        require:"",
                        price:'',
                    }

                }, {
                    tag: 'coupon_multi',
                    name: "多优惠劵",
                    src: this.$root.$data.ossDomain + "/cms/upload/demo/coupon2.png",
                    rows:2,
                    columns:2,
                    height:false,
                    actions:false
                }];
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
            log: function (evt) {
                if (typeof evt.added != 'undefined') this.currentElement = evt.added.element;
                if (typeof evt.moved != 'undefined') this.currentElement = evt.moved.element;

            },
            setCurrentElement: function (element,index) {
                this.currentElement = element;
            },

            cloneDog(element) {
                let newElement = Object.assign({}, element);
                if(newElement.tag=='text'){
                    newElement.style={
                        fontSize: "12",
                        fontWeight: 'normal',
                        fontColor: '#000000',
                        // backgroundColor: '#ffffff',
                        textIndent: '0',
                        marginTop: '0',
                        align: "left"
                    };
                    newElement.action={
                        data:{}
                    };
                }
                if(newElement.tag=='textarea'){
                    newElement.style={
                        fontSize: "12",
                        fontWeight: 'normal',
                        fontColor: '#000000',
                        //    backgroundColor: '#ffffff',
                        marginTop: '0',
                        lineHeight: '12',
                        textIndent: '0',
                        align: "left",
                    };
                }
                if(newElement.tag=='image' || newElement.tag=='button'){
                    newElement.action={
                        data:{},
                        type:'none',
                        route:false
                    };
                }
                if(newElement.tag=='image_map'){
                    newElement.actions=[[
                        {
                            'action':{
                                data:{},
                                type:'none',
                                route:false
                            }
                        },
                        {
                            'action':{
                                data:{},
                                type:'none',
                                route:false
                            }
                        }
                    ]];
                }
                if(newElement.tag=='column_2_title'){
                    newElement.nodes=[
                        {
                            src:'',
                            name:'',
                            id:'',
                        },
                        {
                            src:'',
                            name:'',
                            id:'',
                        }
                    ];

                }
                if(newElement.tag=='swiper_product'){
                    newElement.nodes=[
                    ];

                }
                if(newElement.tag=='coupon_multi'){
                    newElement.actions=[
                        [{'coupon_id':"",'coupon_name':""},{'coupon_id':"",'coupon_name':""}],
                        [{'coupon_id':"",'coupon_name':""},{'coupon_id':"",'coupon_name':""}],
                    ];
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
