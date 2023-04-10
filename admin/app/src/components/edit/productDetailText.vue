<template>
    <div class="textEdit">
        <div class="form-group">
            <el-form-item label="添加" >
                <div class="">
                    <el-button  type="primary" size="mini" @click="addLine()">添加一行文本</el-button>
                </div>
            </el-form-item>
        </div>

        <template v-if="currentElement.lines != false">
            <draggable
                    class="dragArea list-group "
                    :list="currentElement.lines"
                    @change="sort"
                    handle=".handle"
            >
                <div class="list-group-item " v-for="(item,i) in currentElement.lines" :key="i" style="position: relative;cursor: default;">
                    <i class="el-icon-document handle" style="position: absolute;left: 0px;top:0px;cursor: move"></i>
                    <i class="el-icon-delete" style="position: absolute;right: 0px;top:0px;" @click="removeAt(i)"></i>
                    <el-input    v-model="item.txt" />
                </div>
            </draggable>
        </template>




    </div>
</template>

<script>
    export default {
        props:['currentElement'],
        data:function (){
            return {
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
            removeAt(idx) {
                this.currentElement.lines.splice(idx, 1);

            },
            addLine:function (){
                   this.currentElement.lines.push({"txt":""});
            }
        }

    }
</script>

<style scoped>

</style>