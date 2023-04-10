<template>
    <div class="textEdit">
        <div class="form-group">
            <el-form-item label="标题" >
                <div class="">
                    <el-input v-model="currentElement.title" placeholder="标题"/>
                </div>
            </el-form-item>
            <el-form-item label="描述" >
                <div class="">
                    <el-input v-model="currentElement.desc" placeholder="描述"/>
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