<template>
        <div class="form-group">
            <el-form-item label="高度" >
                <div class="">
                    <el-input-number v-model="currentElement.height" :min="1"
                                     label="高度" size="mini"></el-input-number>
                </div>
            </el-form-item>
        </div>
</template>

<script>
    export default {
        props:["currentElement"],
        name: "blank-space",
        methods:{

        }

    }
</script>

<style scoped>

</style>