<template>
    <div class="form-group">
        <el-form-item label="title" >
            <div class="">
                <el-input v-model="currentElement.title" :min="1" label="标题" ></el-input>
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