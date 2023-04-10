<template>
    <div>
        <div class="container">
            <el-button type="primary"></el-button>
            <el-button type="success " circle="true">circle</el-button>
            <el-button type="warning " round="true">round</el-button>
            <el-button type="danger " plain="true">plain</el-button>
            <el-button type="info " disabled="true">disabled</el-button>
            <el-button type="text" loading="true">loading</el-button>
        </div>
        <div class="container">
            <el-row>
                <el-col :span="24">
                    <div class="grid-content bg-purple-dark"></div>
                </el-col>
            </el-row>
            <el-row :gutter="20">
                <el-col :span="6">
                    <div class="grid-content bg-purple"></div>
                </el-col>
                <el-col :span="6">
                    <div class="grid-content bg-purple-light"></div>
                </el-col>
                <el-col :span="6">
                    <div class="grid-content bg-purple"></div>
                </el-col>
                <el-col :span="6">
                    <div class="grid-content bg-purple-light"></div>
                </el-col>
            </el-row>

        </div>

        <div class="container">

            <el-button @click="show = !show">Click Me</el-button>

            <div style="display: flex; margin-top: 20px; height: 100px;">


                <transition name="el-fade-in">
                    <div v-show="show" class="transition-box">.el-fade-in</div>
                </transition>

                <transition name="el-zoom-in-center">
                    <div v-show="show" class="transition-box">.el-zoom-in-center</div>
                </transition>

                <transition name="el-zoom-in-top">
                    <div v-show="show" class="transition-box">.el-zoom-in-top</div>
                </transition>

                <transition name="el-zoom-in-bottom">
                    <div v-show="show" class="transition-box">.el-zoom-in-bottom</div>
                </transition>




            </div>

        </div>


    </div>
</template>

<script>
    export default {
        name: "el_demo",
        props: ['title', 'user'],
        data() {
            return {
                show: true
            };
        },
        mounted: function () {
        },
        methods: {}
    };
</script>
<style scoped>
    .transition-box {
        margin-bottom: 10px;
        width: 200px;
        height: 100px;
        border-radius: 4px;
        background-color: #409EFF;
        text-align: center;
        color: #fff;
        padding: 40px 20px;
        box-sizing: border-box;
        margin-right: 20px;
    }

    .el-row {
        margin-bottom: 20px;
    }

    .el-row:last-child {
        margin-bottom: 0;
    }

    .el-col {
        border-radius: 4px;
    }

    .bg-purple-dark {
        background: #99a9bf;
    }

    .bg-purple {
        background: #d3dce6;
    }

    .bg-purple-light {
        background: #e5e9f2;
    }

    .grid-content {
        border-radius: 4px;
        min-height: 36px;
    }

    .row-bg {
        padding: 10px 0;
        background-color: #f9fafc;
    }
</style>
