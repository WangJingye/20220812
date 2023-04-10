<template>
    <div class="form-group">
        <el-form-item label="button" >
            <div class="button">
                <el-input v-model="currentElement.button" label="按钮" ></el-input>
            </div>
        </el-form-item>
        <el-form-item label="appid" >
            <div class="appid">
                <el-input v-model="currentElement.appid"  label="APP ID" ></el-input>
            </div>
        </el-form-item>
        <el-form-item label="path" >
            <div class="path">
                <el-input v-model="currentElement.path"  label="PATH" ></el-input>
            </div>
        </el-form-item>
    </div>
</template>

<script>
    export default {
        props:["currentElement"],
    }
</script>

<style scoped>

</style>