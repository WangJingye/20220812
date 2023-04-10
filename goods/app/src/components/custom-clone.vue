<template>
    <div>
        <div class="row">
            <div class="col-3 element-list-wrap">
                <h3><i class="el-icon-s-grid"></i>文本</h3>
                <draggable
                        class="dragArea list-group element-list"
                        :list="list1"
                        :group="{ name: 'people', pull: 'clone', put: true }"
                        :clone="cloneDog"
                        @change="log1"
                >
                    <div class="list-group-item element" v-for="(element,i) in list1" :key="i">
                        <i :class="[element.class]"></i>
                        {{ element.name }}
                    </div>
                </draggable>

                <h3><i class="el-icon-s-grid"></i>图片</h3>
                <draggable
                        class="dragArea list-group element-list"
                        :list="list2"
                        :group="{ name: 'people', pull: 'clone', put: true }"
                        :clone="cloneDog"
                        @change="log2"
                >
                    <div class="list-group-item element" v-for="(element,i) in list2" :key="i">
                        <i :class="[element.class]"></i>
                        {{ element.name }}
                    </div>
                </draggable>
                <h3><i class="el-icon-s-grid"></i>轮播</h3>
                <draggable
                        class="dragArea list-group element-list"
                        :list="list3"
                        :group="{ name: 'people', pull: 'clone', put: true }"
                        :clone="cloneDog"
                        @change="log3"
                >
                    <div class="list-group-item element" v-for="(element,i) in list3" :key="i">
                        <i :class="[element.class]"></i>
                        {{ element.name }}
                    </div>
                </draggable>
                <template v-if="false">

                <h3><i class="el-icon-s-grid"></i>视频</h3>
                <draggable
                        class="dragArea list-group element-list element-list "
                        :list="list4"
                        :group="{ name: 'people', pull: 'clone', put: true }"
                        :clone="cloneDog"
                        @change="log4"
                >
                    <div class="list-group-item element" v-for="(element,i) in list4" :key="i">
                        <i :class="[element.class]"></i>
                        {{ element.name }}
                    </div>
                </draggable>
                </template>
                <h3><i class="el-icon-s-grid"></i>其他</h3>
                <draggable
                        class="dragArea list-group element-list last-element-list "
                        :list="list5"
                        :group="{ name: 'people', pull: 'clone', put: true }"
                        :clone="cloneDog"
                        @change="log5"
                >
                    <div class="list-group-item element" v-for="(element,i) in list5" :key="i">
                        <i :class="[element.class]"></i>
                        {{ element.name }}
                    </div>
                </draggable>
            </div>

            <div class="col-5 cms-content-wrap">
                <h3><i class="el-icon-view"></i>内容</h3>
                <draggable
                        class="dragArea list-group cms-content"
                        tag="ul"
                        :list="list"
                        group="people"
                        @change="log"
                        v-bind="dragOptions"
                >
                    <transition-group type="transition" name="flip-list">
                        <li class="list-group-item cms-element"
                            v-for="(element,index) in list"
                            :key="index + 1"
                            @dblclick="setCurrentElement(element,index)"
                            :class="[currentElement==element?'current':'']"
                            style="min-height: 50px;padding:0;line-height: 50px; "
                        >
                            <element-show :element="element" :index="index + 1 "></element-show>
                        </li>
                    </transition-group>
                </draggable>
            </div>

            <div class="col-4 edit-wrap">

                <h3><i class="el-icon-setting"></i>属性</h3>
                <form class="form-horizontal" v-if="currentElement">

                    <element-edit :currentElement="currentElement"></element-edit>

                </form>
            </div>
        </div>

        <input type="hidden" name="content" :value="JSON.stringify(list)"/>
        <div class="row">
            <div class="col-12" v-if="false">

                <rawDisplayer class="col-6" :value="list" title="List 2"/>
            </div>
        </div>

    </div>

</template>

<script>
    import draggable from "@/vuedraggable";

    export default {
        props: ['page_content','api_domain'],
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
                list: [],
                drag: false,
                currentElement: false,
            };
        },
        mounted: function () {
            if(this.api_domain!=''){
                this.$root.$data.apiDomain=this.api_domain;
            }
            this.list1 = this.getList1();
            this.list2 = this.getList2();
            this.list3 = this.getList3();
            this.list4 = this.getList4();
            this.list5 = this.getList5();
            this.list = this.page_content;
        },
        methods: {
            getList1: function () {
                return [{
                    tag: 'text',
                    name: "文本",
                    style:false,
                    placeholder: "文本",
                    textIndentCount:'0',
                    url: "",
                    content: "试用小样  NEW"
                }, {
                    tag: 'textarea',
                    name: "段落",
                    style:false,
                    placeholder: "请输入段落",
                    textIndentCount:'2',
                    content: "一个人的生活的状态会不自觉地通过言谈举止表露出来。面对邓女士汪先生夫妇的时候，会很直观地感受到他们的幸福和满足，也能感受到他们在经营这个小家庭时的用心。"
                }];
            },
            getList2() {
                return [{
                    tag: 'image',
                    name: "图片",
                    placeholder: "请上传图片",
                    src: this.$root.$data.apiDomain+"/upload/1.jpg",
                    url: ''
                }];
            },
            getList3() {
                return [
                    {
                        tag: 'swiper',
                        name: "基础",
                        nodes: false,
                    },
                    {
                        tag: 'scrollView',
                        name: "商品滑动",
                        nodes: false
                    }
                ];
            },

            getList4() {
                return [{tag: 'video', name: "视频格式1", class: 'fa-video-camera', placeholder: "请输入视频地址"}, {
                    tag: 'video',
                    name: "视频格式2",
                    class: 'fa-video-camera',
                    placeholder: "请输入视频地址"
                }];
            },
            getList5() {
                return [
                    {tag: 'product', name: "产品",id:"",image:this.$root.$data.apiDomain+'/static/admin/images/p1.png',title:"隔离防晒双保险",content:'雅顿铂粹御肤智慧防护乳SPF50PA ++++ 40ml',price:"460"},
                    {
                        "tag": "author",
                        "name":"达人",
                        "icon":this.$root.$data.apiDomain+"/upload/images/home/2.jpg",
                        "author":"张三",
                        "time":"2019-06-13",
                        "title":"雅顿铂粹御肤智慧防护乳",
                        "label":false,
                    }
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
            log: function (evt) {
                if (typeof evt.added != 'undefined') this.currentElement = evt.added.element;
                if (typeof evt.moved != 'undefined') this.currentElement = evt.moved.element;

            },
            setCurrentElement: function (element) {
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

                return newElement;

            },
            removeAt(idx) {
                this.list.splice(idx, 1);
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


</style>
