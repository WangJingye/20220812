<template>
    <div tag="image_flex_simple" style="display: flex;" :style="getStyle()">
        <div class="flex-column" style="width:80%;">
            <img :src="element.src"  style="width:100%;padding:0;"/>
            <div class="image_flex_title">{{element.title}}</div>
            <div class="image_flex_desc">{{element.desc}}</div>
            <div class="image_flex_button">{{element.button}}</div>
        </div>
    </div>
</template>

<script>
    import driver from './driver';
    export default {
        extends: driver,
        props:['element'],
        data:function(){
            return {};
        },
        methods:{
            getStyle:function(){
                let align ='center';
                if(this.element.align == 'left'){
                    align = 'flex-start'
                }
                if(this.element.align == 'right'){
                    align = 'flex-end'
                }
                return {
                    justifyContent:align
                }
            }
        }
    }
</script>

<style scoped>
    .image_flex{
        display: flex;
        flex-flow:column;
    }
    .image_flex_title{
        text-align: center;
        font-family: PlayfairDisplay-Regular,arial,sans-serif;
        font-size: 1.5625rem;
    }
    .image_flex_desc{
        text-align: center;
        font-size: .8125rem;
        line-height: 1.3125rem;
        color: #575757;
        font-family: HelveticaNeueLTW05-45Light,arial,sans-serif;
    }
    .image_flex_button{
        color: #000;
        font-family: HelveticaNeueLTW05-75Bold,arial,sans-serif;
        font-size: .75rem;
        text-decoration: underline;
        line-height: inherit;
        cursor: pointer;
        text-align: center;
    }
</style>