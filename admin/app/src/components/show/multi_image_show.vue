<template>
    <div class="block multi_image" tag="multi_image" style="display: flex;justify-content: space-between;flex-flow: row wrap;">
        <template v-if="element.nodes===false || element.nodes.length==0">
            <div>
                <img src="/static/admin/images/multi_image_product.png" style="width: 100%;height: 100%;"/>
            </div>
        </template>
        <template v-else>
            <div v-for="(item,i) in element.nodes"
                 :key="i"
                 :class=" 'col' + element.columns "
                 style="background: #fff;margin-bottom: 0px;"
            >
                <div class="flex-column">
                    <div style="width:100%;height: 200px;">
                        <img :src="item.src"  style="width:100%;height:100%;padding:0;"/>
                    </div>
                    <div style="font-weight: 600;font-size: 14px;line-height: 30px;margin-top: 10px;text-align: center">{{item.title}}</div>
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
        name: "multi-image-show",
        methods:{
            imageMouseOver:function (src,event){
                //console.log(src,event);
                event.target.src = src;
            },
            imageMouseLeave:function (src,event){
                //console.log(src,event);
                event.target.src = src;
            },
        }
    }
</script>

<style scoped>
    .multi_image .col1{flex-basis: 100% ;flex-grow:0;flex-shrink:0;}
    .multi_image .col2{flex-basis: calc( (100% ) /2 );flex-grow:0;flex-shrink:0}
    .multi_image .col3{flex-basis: calc( (100% ) /3 );flex-grow:0;flex-shrink:0;}
    .multi_image .col4{flex-basis: calc( (100% ) /4 );flex-grow:0;flex-shrink:0;}
    .multi_image .col5{flex-basis: calc( (100% ) /5 );flex-grow:0;flex-shrink:0;}
    .multi_image .col6{flex-basis: calc( (100% ) /6 );flex-grow:0;flex-shrink:0;}
    .multi_image .col7{flex-basis: calc( (100% ) /7 );flex-grow:0;flex-shrink:0;}
    .multi_image .col8{flex-basis: calc( (100% ) /8 );flex-grow:0;flex-shrink:0;}
    .multi_image .col9{flex-basis: calc( (100% ) /9 );flex-grow:0;flex-shrink:0;}
    .multi_image .col10{flex-basis: calc( (100% ) /10 );flex-grow:0;flex-shrink:0;}
</style>