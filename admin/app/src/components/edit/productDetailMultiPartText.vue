<template>
    <div class="textEdit">
        <div class="form-group" v-for="(part,index) in  currentElement.nodes" :key="index">
            <el-form-item :label="index + 1 " >
                <div class="">
                    <el-input v-model="part.title" placeholder="标题"/>
                    <el-input type="textarea" v-model="part.desc" placeholder="描述"/>
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