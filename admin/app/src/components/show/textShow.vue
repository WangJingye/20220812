<template>
    <div class="txt">
        <div class="" style="font-weight:400;line-height: 50px;text-align: center">{{element.text}}</div>
    </div>
</template>

<script>
    import driver from './driver';
    export default {
        extends: driver,
        props:['element'],
        name: "text-show",
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