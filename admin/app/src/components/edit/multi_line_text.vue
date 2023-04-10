<template>
    <div class="multi_line_text">
        <div class="form-group">
            <el-form-item label="title" >
                <div class="">
                    <el-input v-model="currentElement.title"  label="标题" ></el-input>
                </div>
            </el-form-item>
            <el-form-item label="sub_title" >
                <div class="">
                    <el-input v-model="currentElement.sub_title"  label="子标题" ></el-input>
                </div>
            </el-form-item>
        </div>
        <div class="lines">
            <el-form-item label="新增" >
                <el-input  v-model="newLine" placeholder="new line" style="width:50%;"/>
                <el-button style type="primary" @click="addLine(currentElement)">新增</el-button>
            </el-form-item>

            <template v-if="currentElement.lines.length > 0">
                <draggable
                        class="dragArea list-group "
                        :list="currentElement.lines"
                        @change="sort"
                        handle=".handle"
                >
                    <div class="list-group-item " v-for="(item,i) in currentElement.lines" :key="i" style="position: relative;cursor: default;">
                        <i class="el-icon-document handle" style="position: absolute;left: 0px;top:0px;cursor: move"></i>
                        <i class="el-icon-delete" style="position: absolute;right: 0px;top:0px;" @click="removeAt(currentElement,i)"></i>
                        <el-input   type="textarea" :rows="2" v-model="item.text" />
                    </div>
                </draggable>
            </template>




        </div>
        <div class="form-group">
            <el-form-item label="button" >
                <div class="">
                    <el-input v-model="currentElement.button"  label="按钮" ></el-input>
                </div>
            </el-form-item>
        </div>
    </div>
</template>

<script>
    export default {
        props:['currentElement'],
        name: "multi-line-text",
        data:function (){
            return {
                newLine:""
            };
        },
        methods:{
            sort(evt){
                let _this=this;
                let lines = [...this.currentElement.lines];
                this.currentElement.lines=[];
                lines.forEach(function (line){
                    _this.currentElement.lines.push(line);
                });
            },
            removeAt(element,idx) {
                element.lines.splice(idx, 1);

            },
            addLine:function (currentElement){
                if(this.newLine){
                    currentElement.lines.push({"text":this.newLine});
                    this.newLine="";
                }
            }

        }
    }
</script>

<style scoped>

</style>