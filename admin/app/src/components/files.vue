<template>
    <div>
        <el-button type="text" @click="dialogVisible = true">媒体库</el-button>
        <el-dialog title="请选择文件" :visible.sync="dialogVisible" width="80%">
            <div style="display: flex;justify-content: flex-start;">
                <div style="height: 400px;overflow-y: auto;flex:1">
                    <p>目录</p>
                    <el-tree :data="data" :props="defaultProps" :default-expand-all="true"
                             @node-click="handleNodeClick"></el-tree>
                </div>
                <div style="flex:3;display: flex;flex-flow: row wrap;">

                    <dl v-for="(i,index) in files"  :key="index + 1">
                        <dd>
                            <li
                                 style="width:100px;height:70px;padding-right: 10px;background-origin: padding-box;
                                             background-position: 50% 50%;
                                             background-position-x: 50%;
                                             background-position-y: 50%;
                                             background-repeat: no-repeat;
                                             background-size: contain;
                                             border: 1px solid #e8e8e8;margin:0px 10px;">
                                <img :src="i.path" style="width:100px;height:70px;" @click="upload(i,$event)"/>
                            </li>
                        </dd>
                        <dt>
                            {{i.name}}
                        </dt>
                    </dl>

                </div>
            </div>
        </el-dialog>
    </div>
</template>


<script>
    import {EXIF} from "exif-js";
    export default {
        props: ['currentElement', 'attribute'],
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
                },
                uploadFlag: false
            };
        },
        mounted: function () {
            let url = this.$root.$data.apiDomain + 'admin/page/tree';
            $.post(url, (response) => {
                this.data = response.tree;
                this.files = response.files;
            }, 'json');
        },
        watch: {},
        methods: {
            handleNodeClick(data) {
                let url = this.$root.$data.apiDomain + 'admin/page/files';
                $.post(url, {label: data.label}, (response) => {
                    this.files = response.files;
                }, 'json');
            },
            upload(i,event) {
                if (this.uploadFlag) return;
                if (timer) clearTimeout(timer);
                this.uploadFlag = true;
                let attribute = this.attribute?this.attribute:"src";
                if(this.currentElement.tag=='image'){
                    this.currentElement.src = i.path;
                }else if(this.currentElement.tag=='swiper'){
                    if(this.currentElement.nodes==false){
                        this.currentElement.nodes=[];
                    }



                    EXIF.getData(event.target, function() {
                         var res=EXIF.getTag(this, 'Orientation');
                        console.log(res);
                    });



                    this.currentElement.nodes.push({
                        'tag':'image',
                        'src':i.path,
                        'url':'',
                        'action':{
                            data:{},
                            type:'none',
                            route:false
                        },
                    });
                }else if(this.currentElement.tag=='multi_image'){
                    if(this.currentElement.nodes==false){
                        this.currentElement.nodes=[];
                    }

                    this.currentElement.nodes.push({
                        'tag':'image',
                        'src':i.path,
                        'url':'',
                        'action':{
                            data:{},
                            type:'none',
                            route:false
                        },
                    });

                }else if(this.currentElement.tag=='video'){
                    this.currentElement[attribute] = i.path;
                }else{
                    this.currentElement[attribute] = i.path;
                }
                var timer = setTimeout(() => {
                    this.uploadFlag = false;
                }, 2000)

                this.dialogVisible = false;
            }

        }
    };
</script>
<style scoped>
li{
    list-style: none
}

</style>
