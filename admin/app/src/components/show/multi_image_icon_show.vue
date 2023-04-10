<template>
    <div class="block multi_image_icon" tag="multi_image_icon">
        <template v-if="element.nodes===false || element.nodes.length==0">
            <div>
                <img src="/static/admin/images/multi_image_icon.png" style="width: 100%;height: 100%;"/>
            </div>
        </template>
        <template v-else>
            <div v-for="(item,i) in element.nodes"
                 :key="i"
                 :class=" 'col' + element.columns[getDriver()] "
                 style="background: #fff;margin-bottom: 0px;"
            >
                <div class="flex-item">
                    <div class="image">
                        <img :src="item.src" style="width:100%;"/>
                    </div>
                    <div class="title">{{item.title}}</div>
                </div>
            </div>
        </template>

    </div>
</template>

<script>
    import driver from './driver';
    export default {
        extends: driver,
        props: ['element'],
        name: "multi-image-icon-show",
        methods:{

        }
    }
</script>

<style scoped>
    .multi_image_icon{display: flex;justify-content: space-between;flex-flow: row wrap;}
    .title {text-align: center;font-size: 12px;}
    .multi_image_icon .flex-item{width:70px; margin: 0 auto;}
    .multi_image_icon .col1{flex-basis: 100% ;flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
    .multi_image_icon .col2{flex-basis: calc( (100% - 10px) /2 );flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
    .multi_image_icon .col3{flex-basis: calc( (100% - 10px) /3 );flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
    .multi_image_icon .col4{flex-basis: calc( (100% - 10px) /4 );flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
    .multi_image_icon .col5{flex-basis: calc( (100% - 10px) /5 );flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
    .multi_image_icon .col6{flex-basis: calc( (100% - 10px) /6 );flex-grow:0;flex-shrink:0;margin-bottom: 10px;}

    .cms-content-wrap.h5 .multi_image_icon,.cms-content-wrap.wechat .multi_image_icon{

    }
    .cms-content-wrap.pc{

    }

</style>