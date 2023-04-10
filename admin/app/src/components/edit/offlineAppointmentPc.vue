<template>
    <div class="form-group">
        <el-form-item label="button" >
            <div class="button">
                <el-input v-model="currentElement.button" :min="1" label="按钮" ></el-input>
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