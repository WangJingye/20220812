<template>
    <div>
        <el-button type="text" @click="dialogVisible = true">媒体库</el-button>
        <el-dialog title="请选择文件" :visible.sync="dialogVisible" width="80%">
            <div style="display: flex;justify-content: flex-start;">
                <div style="height: 400px;overflow-y: auto;flex:1">
                    <p>目录</p>
                    <el-tree :data="data" :props="defaultProps" default-expand-all="true"
                             @node-click="handleNodeClick"></el-tree>
                </div>
                <div style="flex:3;display: flex;">

                    <dl v-for="i in files" @click="upload(i)">
                        <dd>
                            <li
                                :style="{backgroundImage:'url('+i+')'}"
                                style="width:100px;height:70px;padding-right: 10px;background-origin: padding-box;
                                             background-position: 50% 50%;
                                             background-position-x: 50%;
                                             background-position-y: 50%;
                                             background-repeat: no-repeat;
                                             background-size: contain;
                                             border: 1px solid #e8e8e8;margin:0px 10px;">
                            </li>
                        </dd>
                        <dt>
                            {{i.replace(/\/upload\//,'')}}
                        </dt>
                    </dl>

                </div>
            </div>
        </el-dialog>
    </div>
</template>


<script>
    export default {
        props: ['currentElement', 'tree'],
        name: "files",
        components: {},
        data() {
            return {
                dialogVisible: false,
                files: [],
                data: [],
                defaultProps: {
                    children: 'children',
                    label: 'label'
                }
            };
        },
        mounted: function () {
            $.post('/admin/page/tree', (response) => {
                this.data = response.tree;
                this.files = response.files;
            }, 'json');
        },
        watch: {},
        methods: {
            handleNodeClick(data) {
                $.post('/admin/page/files', {label: data.label}, (response) => {
                    this.files = response;
                }, 'json');
            },
            upload(i) {
                this.currentElement.src = i;
                this.currentElement.name = i;
                this.dialogVisible = false;
            }

        }
    };
</script>
<style scoped>


</style>
