<template>
    <div tag="image_flex_right">
        <template v-if="element.nodes!=false">
            <div class="image_flex" style="">
                <div
                        v-for="(item,i) in element.nodes"
                        :key="i"
                        class="flex_item"
                        :class=" 'item_' + i "
                        :style="getStyle(item,i)"
                >
                    <div class="flex-column">
                        <img :src="item.src"  style="width:100%;height:100%;padding:0;"/>
                        <div class="image_flex_title">{{item.title}}</div>
                        <div class="image_flex_desc">{{item.desc}}</div>
                        <div class="image_flex_button">{{item.button}}</div>
                    </div>
                </div>
            </div>
        </template>
        <template v-else>
            <img :src="element.placeholder" style="width:100%;" title="image_flex"/>
        </template>
    </div>
</template>

<script>
    import driver from './driver';
    export default {
        extends: driver,
        props:["element"],
        data:function (){
            return {};
        },
        methods:{
            getStyle:function (item,$i){
                if(this.$root.$data.mediaType=='h5'){
                    return this._getH5style(item,$i);
                }
                if(this.$root.$data.mediaType=='pc'){
                    return this._getPcStyle(item,$i);
                }
            },

            _getH5style:function(item,$i){
                var style={};
                if($i==0){
                    style.width='100%';
                    style.alignSelf="center";
                }
                var aspect_ratio = item.aspect_ratio;
                if($i==1){
                    if(item.aspect_ratio > 0.8 ){
                        aspect_ratio= "80%";
                    }else if(item.aspect_ratio > 0.6){
                        aspect_ratio= "60%";
                    }else {
                        aspect_ratio= "50%";
                    }
                    style.width=aspect_ratio;
                    style.alignSelf="flex-end";
                }
                if($i==2){
                    if(item.aspect_ratio > 0.8 ){
                        aspect_ratio= "80%";
                    }else if(item.aspect_ratio > 0.6){
                        aspect_ratio= "60%";
                    }else {
                        aspect_ratio= "50%";
                    }
                    style.width=aspect_ratio;
                    style.alignSelf="flex-start";
                }
                if($i==3){
                    aspect_ratio= "80%";
                    style.width=aspect_ratio;
                    style.alignSelf="center";
                }
                return style;
            },
            _getPcStyle:function (item,$i){
                var style={};
                if($i==0){
                    style.width='45%';
                    style.alignSelf="flex-start";
                }
                var aspect_ratio = item.aspect_ratio;
                if($i==1){
                    aspect_ratio= "40%";
                    style.width=aspect_ratio;
                    style.alignSelf="flex-end";
                    style.marginTop='-40%'
                }
                if($i==2){
                    aspect_ratio= "40%";
                    style.width=aspect_ratio;
                    style.alignSelf="flex-start";
                    style.marginTop='-20%'
                }
                if($i==3){
                    aspect_ratio= "40%";
                    style.width=aspect_ratio;
                    style.alignSelf="flex-end";
                    style.marginTop='-40%'
                }
                return style;
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