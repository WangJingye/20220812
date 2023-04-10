<template>
    <div>
        <template v-if="currentElement.tag=='text'">
            <div class="form-group">
                <label class="control-label">字体大小:</label>
                <div class="">
                    <el-input-number v-model="currentElement.style.fontSize" :min="12" :max="48"
                                     label="字体大小" size="mini"></el-input-number>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">字体粗细:</label>
                <div class="">
                    <el-radio-group v-model="currentElement.style.fontWeight" size="mini">
                        <el-radio-button value="normal" label="normal">Normal</el-radio-button>
                        <el-radio-button value="bold" label="bold">Bold</el-radio-button>
                    </el-radio-group>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label">字体颜色:</label>
                <div class="">
                    <input type="color" class="form-control" v-model="currentElement.style.fontColor"
                           placeholder="字体颜色">
                </div>
            </div>
            <div class="form-group" v-if="false">
                <label class="control-label">背景颜色:</label>
                <div class="">
                    <input type="color" class="form-control" v-model="currentElement.style.backgroundColor"
                           placeholder="背景颜色">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">首行缩进:</label>
                <div class="">
                    <el-input-number v-model="currentElement.textIndentCount" :min="0" :max="100"
                                     label="首行缩进" size="mini"></el-input-number>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">上外边距:</label>
                <div class="">
                    <el-input-number v-model="currentElement.style.marginTop" :min="0" :max="100"
                                     label="上外边距" size="mini"></el-input-number>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">对齐方式:</label>
                <div class="">
                    <el-radio-group v-model="currentElement.style.align" size="mini">
                        <el-radio-button value="left" label="left">靠左</el-radio-button>
                        <el-radio-button value="center" label="center">居中</el-radio-button>
                        <el-radio-button value="right" label="right">靠右</el-radio-button>
                    </el-radio-group>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">链接:</label>
                <div class="">
                    <input class="form-control" v-model="currentElement.url" placeholder="链接">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">内容:</label>
                <div class="">
                    <input class="form-control" v-model="currentElement.content" placeholder="内容">
                </div>
            </div>

        </template>

        <template v-if="currentElement.tag=='textarea'">
            <div class="form-group">
                <label class="control-label">字体大小:</label>
                <div class="">
                    <el-input-number v-model="currentElement.style.fontSize" :min="10" :max="24"
                                     label="字体大小" size="mini"></el-input-number>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">字体粗细:</label>
                <div class="">
                    <el-radio-group v-model="currentElement.style.fontWeight" size="mini">
                        <el-radio-button value="normal" label="normal">Normal</el-radio-button>
                        <el-radio-button value="bold" label="bold">Bold</el-radio-button>
                    </el-radio-group>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">字体颜色:</label>
                <div class="">
                    <input type="color" class="form-control" v-model="currentElement.style.fontColor" id="fontColor"
                           placeholder="字体颜色">
                </div>
            </div>

            <div class="form-group" v-if="false">
                <label class="control-label">背景颜色:</label>
                <div class="">
                    <input type="color" class="form-control" v-model="currentElement.style.backgroundColor" id="backgroundColor"
                           placeholder="背景颜色">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">上外边距:</label>
                <div class="">
                    <el-input-number v-model="currentElement.style.marginTop" :min="0" :max="100"
                                     label="上外边距" size="mini"></el-input-number>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">首行缩进:</label>
                <div class="">
                    <el-input-number v-model="currentElement.textIndentCount" :min="0" :max="100"
                                     label="首行缩进" size="mini"></el-input-number>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">对齐方式: </label>

                <div class="">
                    <el-radio-group v-model="currentElement.style.align" size="mini">
                        <el-radio-button value="left" label="left">靠左</el-radio-button>
                        <el-radio-button value="center" label="center">居中</el-radio-button>
                        <el-radio-button value="right" label="right">靠右</el-radio-button>
                    </el-radio-group>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">行间距:</label>
                <div class="">
                    <el-input-number v-model="currentElement.style.lineHeight" :min="12" :max="48" :step="1"
                                     label="行间距" size="mini"></el-input-number>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">内容:</label>
                <div class="">
                    <textarea class="form-control" v-model="currentElement.content" placeholder="内容"></textarea>
                </div>
            </div>

        </template>


        <template v-if="currentElement.tag=='image'">
            <div class="form-group">
                <label class="control-label">图片地址:</label>
                <div class="">
                    <files :currentElement="currentElement" v-if="false" />
                    <el-upload
                            ref="upload"
                            class="upload-image"
                            :action="ajaxUpload"
                            :file-list="fileList"
                            :on-success="handleSuccess"
                            :on-error="handleError"
                            list-type="picture"
                            :show-file-list="false"
                            >
                        <el-button size="small" type="primary">上传</el-button>
                        <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
                    </el-upload>
                    <div class="list-group-item "  style="cursor: default;">
                        <img :src="currentElement.src" style="width:100px;" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">链接:</label>
                    <div class="">
                        <input class="form-control" v-model="currentElement.url" placeholder="链接">
                    </div>
                </div>
            </div>

        </template>

        <template v-if="currentElement.tag=='swiper'">
            <div class="form-group">
                <label class="control-label">轮播图片地址:</label>
                <div class="">

                    <el-upload
                            ref="uploadSwiper"
                            class="upload-image"
                            :action="ajaxUpload"
                            :on-success="swiperSuccess"
                            :on-error="handleError"
                            list-type="picture"
                            :multiple="true"
                            :show-file-list="false"
                    >
                        <el-button size="small" type="primary">上传</el-button>
                        <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
                    </el-upload>

                    <template v-if="currentElement.nodes!==false">
                    <draggable
                            class="dragArea list-group "
                            :list="currentElement.nodes"
                            @change="sort"
                    >
                        <div class="list-group-item " v-for="(item,i) in currentElement.nodes" :key="i" style="position: relative">
                            <i class="el-icon-delete" style="position: absolute;right: 0px;top:0px;" @click="removeAt(currentElement.nodes,i)"></i>
                            <img :src="item.src" style="width:100px;" />
                            <input v-model="item.url"  placeholder="链接"/>
                        </div>
                    </draggable>
                    </template>
                </div>
            </div>
        </template>

        <template v-if="currentElement.tag=='scrollView'">
            <div class="form-group">
                <label class="control-label">上传商品图片:</label>
                <div class="">

                    <el-upload
                            ref="uploadSwiper"
                            class="upload-image"
                            :action="ajaxUpload"
                            :on-success="swiperSuccess"
                            :on-error="handleError"
                            list-type="picture"
                            :multiple="true"
                            :show-file-list="false"
                    >
                        <el-button size="small" type="primary">上传</el-button>
                        <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
                    </el-upload>

                    <template v-if="currentElement.nodes!==false">
                        <draggable
                                class="dragArea list-group "
                                :list="currentElement.nodes"
                                @change="sort"
                                handle=".handle"
                        >
                            <div class="list-group-item " v-for="(item,i) in currentElement.nodes" :key="i" style="position: relative;cursor: default">
                                <i class="el-icon-thumb handle" style="position: absolute;left: 0px;top:0px;cursor: move"></i>
                                <i class="el-icon-delete" style="position: absolute;right: 0px;top:0px;" @click="removeAt(currentElement.nodes,i)"></i>
                                <img :src="item.src" style="width:100px;" /><br/>
                                <input style="width:100%;" v-model="item.url"  placeholder="链接"/><br/>
                                <input style="width:100%;" v-model="item.title"  placeholder="标题"/><br/>
                                <input style="width:100%;" v-model="item.content"  placeholder="内容"/><br/>
                                <input style="width:100%;" v-model="item.price"  placeholder="价格"/><br/>
                            </div>
                        </draggable>
                    </template>
                </div>
            </div>
        </template>

        <template v-if="currentElement.tag=='video'">
            <div class="form-group">
                <label class="control-label">视频地址:</label>
                <div class="">
                    <input class="form-control" v-model="currentElement.content" placeholder="视频地址">
                </div>
            </div>
        </template>

        <template v-if="currentElement.tag=='product'">
            <div class="form-group">
                <label class="control-label">产品:</label>
                <div class="">
                    <el-upload
                            ref="upload"
                            class="upload-image"
                            :action="ajaxUpload"
                            :file-list="fileList"
                            :on-success="handleProductSuccess"
                            :on-error="handleError"
                            list-type="picture"
                            :show-file-list="false"
                    >
                        <el-button size="small" type="primary">上传</el-button>
                        <div slot="tip" class="el-upload__tip">只能上传jpg/png文件，且不超过500kb</div>
                    </el-upload>
                    <div class="list-group-item "  style="cursor: default;" v-if="currentElement.image">
                        <img :src="currentElement.image" style="width:100px;" />
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">链接:</label>
                    <div class="">
                        <input class="form-control" v-model="currentElement.url" placeholder="链接">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">商品ID:</label>
                    <div class="">
                        <input class="form-control" v-model="currentElement.id" placeholder="商品ID">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">标题:</label>
                    <div class="">
                        <input class="form-control" v-model="currentElement.title" placeholder="标题">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">内容:</label>
                    <div class="">
                        <input class="form-control" v-model="currentElement.content" placeholder="内容">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">价格:</label>
                    <div class="">
                        <input class="form-control" v-model="currentElement.price" placeholder="价格">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">积分:</label>
                    <div class="">
                        <input class="form-control" v-model="currentElement.point" placeholder="积分">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label">时间:</label>
                    <div class="">
                        <el-date-picker
                                value-format="timestamp"
                                v-model="currentElement.time"
                                type="datetime"
                                placeholder="选择日期时间">
                        </el-date-picker>
                    </div>
                </div>
            </div>

        </template>

        <template v-if="currentElement.tag=='author'">

            <div class="form-group">
                <label class="control-label">名称:</label>
                <div class="">
                    <el-select v-model="currentElement.author" placeholder="请选择">
                        <el-option
                                v-for="item in authorOptions"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value">
                        </el-option>
                    </el-select>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">图标:</label>
                <div class="">
                   <img :src="currentElement.icon" style="width:100px;"/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">时间:</label>
                <div class="">
                    <el-date-picker
                            value-format="yyyy-MM-dd"
                            format="yyyy-MM-dd"
                            v-model="currentElement.time"
                            type="date"
                            placeholder="选择日期">
                    </el-date-picker>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">标题:</label>
                <div class="">
                    <input class="form-control" v-model="currentElement.title" placeholder="标题">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label">标签:</label>
                <div class="">
                    <input ref="labelItem" class="form-control"  placeholder="标签" style="width:60%;display: inline;">
                    <el-button type="primary" size="mini" style="display: inline" @click="addLabel">添加</el-button>
                    <ul>
                        <li v-for="(item,i) in currentElement.label" :key="i" style="border:1px solid #e8e8e8;padding:3px;margin-top: 5px;">
                            {{item}}
                            <i class="el-icon-delete" style="position: absolute;right: 0px;" @click="removeLabel(currentElement.label,i)"></i>
                        </li>
                    </ul>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import draggable from "@/vuedraggable";
    export default {
        props:['currentElement'],
        name: "element-edit",
        components: {
            draggable
        },
        data() {
            return {
                fileList: [{
                    name: "",
                    url: this.currentElement.tag=='image'?this.currentElement.src:""
                }],
                authorOptions: [],
                apiDomain:this.$root.$data.apiDomain,
                ajaxUpload:this.$root.$data.apiDomain+'/admin/page/ajaxUpload'
            };
        },
        mounted: function () {
            $.post(this.apiDomain+'/admin/page/ajaxAuthor',(response)=>{
                this.authorOptions=response.data;
            });
        },
        watch:{
            currentElement:function (currentElement){
               this.fileList=[{
                   name: "",
                   url: this.currentElement.tag=='image'?currentElement.src:""
               }];
            },
            'currentElement.src':function (val, oldVal){
                this.fileList=[{
                    name: '',
                    url: this.currentElement.tag=='image'?val:""
                }];
            },
            'currentElement.author':function (val, oldVal){
                let item=_.find(this.authorOptions, function(o) { return o.value == val; });
                if(this.currentElement.tag=='author'){
                    this.currentElement.icon=item.icon;
                }

            },
            'currentElement.textIndentCount':function (val, oldVal){
                if(this.currentElement.tag=='text' || this.currentElement.tag=='textarea'){
                    this.currentElement.style.textIndent=val * this.currentElement.style.fontSize;
                }
            },
        },
        methods: {
            handleSuccess(response, file, fileList){
                if(response.status==true){
                    this.currentElement.src=response.file;
                }else{
                    this.$refs.upload.clearFiles()
                    this.$alert(response.message,'提示');
                }

            },
            handleProductSuccess(response, file, fileList){
                if(response.status==true){
                    this.currentElement.image=response.file;
                }else{
                    this.$refs.upload.clearFiles()
                    this.$alert(response.message,'提示');
                }
            },
            handleError(err, file, fileList){
               console.log('err',err);
                this.$alert('上传错误','提示');
            },
            swiperSuccess(response, file, fileList){
                if(this.currentElement.nodes===false){
                    this.currentElement.nodes=[];
                }

                if(this.currentElement.tag=='swiper'){
                    this.currentElement.nodes.push( {
                        tag:'image',
                        src:response.file,
                        url:''
                    });
                }
                if(this.currentElement.tag=='scrollView'){
                    this.currentElement.nodes.push( {
                        src:response.file,
                        url:'',
                        title:'',
                        content:'',
                        price:''
                    });
                }



            },
            removeAt(list,idx) {
                list.splice(idx, 1);
            },
            removeLabel(list,idx){
                list.splice(idx, 1);
            },
            sort(evt){
                let _this=this;
                let files = [...this.currentElement.nodes];
                this.currentElement.nodes=[];
                files.forEach(function (file){
                    _this.currentElement.nodes.push(file);
                });
            },
            addLabel(){

                let label = this.$refs.labelItem.value;
                if(this.currentElement.label===false){
                    this.currentElement.label = [];
                }
                this.currentElement.label.push(label);
                this.$refs.labelItem.value="";
            }


        }
    };
</script>
<style scoped>
    .list-group-item{
        cursor: move;
    }
    .list-group-item>i.el-icon-delete{
        cursor: default;
    }
</style>
