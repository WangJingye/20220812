<template>
    <div class="block  in_text_animate" tag="in_text_animate" >
        <div>
            <img :src="element['placeholder_' + getDriver()] " style="width: 100%;height: 100%;"/>
        </div>
    </div>
</template>

<script>
    import driver from './driver';

    export default {
        extends: driver,
        props:['element'],
        name: "in-text-animate-show",
        data() {
            return {
            };
        },
        mounted() {

        },
        methods:{

        }

    }
</script>

<style scoped>

</style>