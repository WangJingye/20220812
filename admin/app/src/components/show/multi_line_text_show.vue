<template>
    <div class="multi_line_text">
        <div class="title">{{element.title}}</div>
        <div class="sub_title">{{element.sub_title}}</div>
        <div class="lines">
            <p v-for="(line,index) in element.lines" :key="index">{{line.text}}</p>
        </div>
        <div class="button"><span>{{element.button}}</span></div>
    </div>
</template>

<script>
    import driver from './driver';
    export default {
        extends: driver,
        props:['element'],
        name: "multi-line-text-show",
        methods:{

        },
    }
</script>

<style scoped>
    .multi_line_text .title{
        text-align: center;
        font-size: 16px;
        font-weight: 800;
        font-family: HelveticaNeueLTW05-45Light,arial,sans-serif;
    }

    .multi_line_text .sub_title{
        text-align: center;
        font-size: 14px;
        font-weight: 400;
        font-family: HelveticaNeueLTW05-45Light,arial,sans-serif;
    }
    .multi_line_text .lines p{
        text-align: center;
        font-size: 12px;
        line-height: 12px;
        font-weight: 400;
        font-family: HelveticaNeueLTW05-45Light,arial,sans-serif;
    }
    .multi_line_text .button{
        text-align: center;
        font-size: 12px;
        font-weight: 400;
        font-family: HelveticaNeueLTW05-75Bold,arial,sans-serif;
        text-decoration: underline;
    }
</style>