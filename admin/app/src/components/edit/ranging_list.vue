<template>
    <div class="ranging_list">

        <div class="">
            <div class="form-group" style="margin-top: 10px;">
                <el-form-item label="新建TAB" >
                    <div style="display: flex">
                        <input class="form-control" v-model="name" placeholder="名称" style="width:200px;">
                        <el-button type="mini" @click="createTab">新建</el-button>
                    </div>
                </el-form-item>
            </div>
            <div class="tabs">
                <draggable
                        v-if="currentElement.nodes.length > 0"
                        class="dragArea list-group "
                        :list="currentElement.nodes"
                        @change="sort"
                        handle=".handle"
                >
                    <div class="list-group-item " :class="'cate-' + i " v-for="(item,i) in currentElement.nodes" :key="i" style="position: relative;cursor: default;overflow: hidden;">
                        <i class="el-icon-document handle" style="position: absolute;left: 0px;top:0px;cursor: move"></i>
                        <i class="el-icon-delete" style="position: absolute;right: 0px;top:0px;" @click="removeAt(currentElement,i)"></i>
                        <div>
                            <div class="form-group">
                                <el-form-item label="tab名称" >
                                    <div style="display:flex">
                                        <el-input v-model="item.name"  placeholder="tab名称" style="width:50%;"/>
                                        <edit-ranking-list-model :tab="item"/>
                                    </div>
                                </el-form-item>
                            </div>
                        </div>
                    </div>
                </draggable>
            </div>
        </div>
    </div>
</template>


<script>
    import EditRankingListModel from "../editRankingListModel";
    export default {
        props: ['currentElement'],
        components: {EditRankingListModel},
        data() {
            return {
                name:"",
            };
        },
        mounted: function () {
            if(this.currentElement.nodes==false){
                this.currentElement.nodes=[];
            }
        },
        watch: {},
        methods: {
            createTab(){
                this.currentElement.nodes.push(
                    {
                        name:this.name,
                        top:{
                            bg:"",
                            middle:{
                                type:"",
                                src:"",
                                position:"left_top",
                                width:300,
                                height:300,
                            },
                            product:""
                        },
                        banner:{},
                        product:[]
                    }
                );
            },
            sort(evt){
                let _this=this;
                let files = [...this.currentElement.nodes];
                this.currentElement.nodes=[];
                files.forEach(function (file){
                    _this.currentElement.nodes.push(file);
                });
            },
            removeAt(element,idx) {
                element.nodes.splice(idx, 1);

            },

        }
    };
</script>
<style scoped>

</style>
