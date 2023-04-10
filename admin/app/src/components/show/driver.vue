<script>
    export default {
        props:['element'],
        name: "driver",
        methods:{
            getDriver:function(){
                var mediaType= this.getMediaType();
                var pc=['pc'];
                var h5=['h5','wechat'];
                var type='h5';
                if (pc.includes(mediaType)) type = 'pc';
                if (h5.includes(mediaType)) type = 'h5';
                return type;
            },
            getMediaType:function (){
                return this.$root.$data.mediaType;
            },
            getImageSrc:function(element){
                var mediaType= this.getMediaType();
                var pc=['pc'];
                var h5=['h5','wechat'];
                var type='pc';
                if (pc.includes(mediaType)) type = 'pc';
                if (h5.includes(mediaType)) type = 'h5';

                return element.src[type];
            },
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

