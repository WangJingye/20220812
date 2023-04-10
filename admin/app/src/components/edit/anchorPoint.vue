<template>
    <div class="anchorPoint">
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
        data:function (){
            return {
            };
        },

    }
</script>

<style scoped>

</style>