<template>
    <div class="block multi_image_product" tag="multi_image_product" style="display: flex;justify-content: space-between;flex-flow: row wrap;">
        <template v-if="element.nodes===false || element.nodes.length==0">
            <div>
                <img src="/static/admin/images/multi_image_product.png" style="width: 100%;height: 100%;"/>
            </div>
        </template>
        <template v-else>
            <div v-for="(item,i) in element.nodes"
                 :key="i"
                 :class=" 'col' + element.columns[getDriver()] "
                 style="background: #fff;margin-bottom: 0px;"
            >
                <div class="flex-column">
                    <div style="width:100%;height: 200px;">
                        <img :src="item.src.pc"  style="width:100%;height:100%;padding:0;"/>
                    </div>
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
    .multi_image_product .col1{flex-basis: 100% ;flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
    .multi_image_product .col2{flex-basis: calc( (100% - 10px) /2 );flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
    .multi_image_product .col3{flex-basis: calc( (100% - 10px) /3 );flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
    .multi_image_product .col4{flex-basis: calc( (100% - 10px) /4 );flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
    .multi_image_product .col5{flex-basis: calc( (100% - 10px) /5 );flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
    .multi_image_product .col6{flex-basis: calc( (100% - 10px) /6 );flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
    .multi_image_product .col7{flex-basis: calc( (100% - 10px) /7 );flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
    .multi_image_product .col8{flex-basis: calc( (100% - 10px) /8 );flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
    .multi_image_product .col9{flex-basis: calc( (100% - 10px) /9 );flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
    .multi_image_product .col10{flex-basis: calc( (100% - 10px) /10 );flex-grow:0;flex-shrink:0;margin-bottom: 10px;}
</style>