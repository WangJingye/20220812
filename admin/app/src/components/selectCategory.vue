<template>
    <div style="">
        <span  style="width:100%;" class="layui-btn layui-btn-normal" @click="dialogVisible = true">选择分类</span>
        <el-dialog class="select-category-dialog" title="请选择产品分类" :visible.sync="dialogVisible" :close-on-click-modal="false" width="30%" top="0" append-to-body>
            <div >
                <div >
                    <p>分类</p>
                    <el-input placeholder="输入关键字进行过滤" v-model="filterText" v-if="true"/>
                    <el-tree :data="treeData"
                             :props="defaultProps"
                             :default-expand-all="true"
                             @node-click="handleNodeClick"
                             :filter-node-method="filterNode"
                             ref="tree"></el-tree>

                </div>
            </div>
        </el-dialog>
    </div>
</template>


<script>
    export default {
        props: [],
        name: "select-category",
        components: {},
        data() {
            return {
                filterText:'',
                dialogVisible: false,
                treeData: [],
                defaultProps: {
                    children: 'children',
                    label: 'label'
                },
            };
        },
        watch: {
            filterText(val) {
                this.$refs.tree.filter(val);
            },
            dialogVisible(val){
                if(val === true){
                    $('.edit-wrap').css('position','relative');
                }else{
                    $('.edit-wrap').css('position','fixed');
                }
            }
        },
        created:function (){
        },
        mounted: function () {
            this.treeData=this.$root.$data.ajax.treeData;
        },
        methods: {
            filterNode(value, data) {
                if (!value) return true;
                return data.label.indexOf(value) !== -1;
            },
            handleNodeClick(data) {
                /**
                     id: 118
                     label: "银饰"
                 */
                this.dialogVisible=false
                this.$emit('setCategoryId',data)
            },


        }
    };
</script>
<style >
    .select-category-dialog{
        overflow-x: hidden;
    }
    .select-category-dialog>.el-dialog{
        margin:0 !important;
        animation:myfirst 1s;
        left:70%;
    }
    @keyframes myfirst
    {
        0%   {left:100%;}
        100% {left:70%;}
    }
</style>
