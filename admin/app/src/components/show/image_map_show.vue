<template>
    <div tag="image_map">
        <template v-if="element.src">
            <div style="position: relative">
                <img :src="element.src" style="width:100%;"/>
                <div style="position: absolute;top:0;left: 0;width:100%;height: 100%;">
                    <table class="table table-bordered" style="height: 100%;">
                        <tr v-for="row in element.rows">

                            <td  v-for="column in element.columns" style="padding:0;color:#fff;text-align: center;text-shadow: rgb(0, 0, 0) 1px 1px 0px;">链接{{ ( row -1 ) * element.columns  + column }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </template>
    </div>
</template>

<script>
    import driver from './driver';
    export default {
        extends: driver,
        props:['element'],
        name: "image-map-show",
        data:function(){
            return {};
        },
        methods:{

        }
    }
</script>

<style scoped>

</style>