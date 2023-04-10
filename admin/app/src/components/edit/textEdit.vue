<template>
    <div class="textEdit">
        <div class="form-group">
            <el-form-item label="title" >
                <div class="">
                    <el-input v-model="currentElement.text"  label="文本" ></el-input>
                </div>
            </el-form-item>
        </div>




    </div>
</template>

<script>
    export default {
        props:['currentElement'],
        name: "textEdit",
        data:function (){
            return {
            };
        },

    }
</script>

<style scoped>

</style>