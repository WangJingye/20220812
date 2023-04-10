<?php

namespace App\Http\Controllers\Backend;

use App\Lib\Http;
use App\Model\Author;
use App\Model\ConfigOss;
use App\Model\OssConfig;
use App\Model\Taxonomy;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe;
use Illuminate\Contracts\Routing\UrlGenerator;
use App\Model\Pages;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{

    protected $appid='';
    protected $secret='';

    const SEARCH_KEY='search:redirect';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
    }



    /**
     * 草稿
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    }

    /**
     * 已发布
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function published()
    {
        $list=Pages::from('page as p')
            //->select('p.*','pub.page_id')
            //->leftJoin('element_published as pub','pub.page_id','=','p.id')
            // ->where('pub.type','h5')
            ->orderBy('p.sort','asc')
            ->orderBy('p.id','desc');
        if(request('name')){
            $list=$list->where('p.name','like','%'.request('name').'%');
        }

        if(request('status')==='1' || request('status')==='0'){
            $list=$list->where('p.status',request('status','0'));
        }

        $list = $list->whereNotIn('p.key',['navication','navication1']);


//        dd($list->get()->toArray());
        $list=$list->paginate($this->pageSize)->withPath('/'.request()->route()->uri);
        return view('backend.page.published',['list'=>$list]);
    }



    function toTree($list=null, $pk='id',$pid = 'pid',$child = 'children',$root=0)
    {
        // 创建Tree
        $tree = array();
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }
    protected function scan_dir($dir,&$root=[],$pid=null){
        if(!is_dir($dir))return false;
        $files = array_diff(scandir($dir), array('.', '..'));
        if(is_array($files)){
            foreach($files as $key=>$value){
                if(is_dir($dir . '/' . $value)){
                    $i=count($root)+1;
                    $item=['label'=>$value,'id'=>$i,'pid'=>$pid];
                    $root[$i]=$item;
                    $this->scan_dir($dir . '/' . $value,$root,$i);
                }

            }
        }
        return $root;
    }
    public function detail($type){

        $id=request('id');
        if($type=='published'){
            $pageContent = DB::table('element_published')->where('page_id',$id)->pluck('content','type')->toArray();
        }

        $items = DB::table('element_published')->where('page_id',$id)->get()->keyBy('type')->toArray();

        if($id){
            $page=new Pages();
            $page=$page->where('id',$id)->first();
        }else{
            $page=new Pages();
        }


        if(!empty($pageContent)){
            $pageContent=array_map(function ($item){
                return  json_decode($item,true);
            },$pageContent);
        } else{
            $pageContent=['wechat'=>[],'h5'=>[]];
        }
        if(!isset($pageContent['wechat'])){
            $pageContent['wechat']=[];
        }
        if(!isset($pageContent['h5'])){
            $pageContent['h5']=[];
        }
        return view('backend.page.page-detail',['page'=>$page,'pageContent'=>json_encode($pageContent),'type'=>$type,'items'=>$items,'action'=>$this]);
    }


    public function files(){
        $label=request('label');
        $dir=base_path('public/upload/'.$label);
        $files=glob($dir.'/*.{jpg,png,gif}',GLOB_BRACE);
        $files = array_map(function ($file) {
            return [
                'path' => strtr($file, [base_path('public') => '']),
                'name' => pathinfo($file)['basename'],
            ];
            //return strtr($file,[ROOT_PATH.'public'=>""]);
        }, $files);

        $data = ['files' => $files];
        return $data;
    }








    protected function addPageContent($pageId,$content,$type){
        $saveType=request('save_type');
        if($type=='published'){
            foreach($content as $key=>$val){
                if($saveType==$key){
                    DB::table('element_published')->where('page_id',$pageId)->where('type',$saveType)->delete();
                    $val = $this->replaceOssDomain($val);
                    $data=['page_id' => $pageId,'type'=>$key,'content'=>$val,'status'=>0,'published'=>request($saveType)['published'],'offline'=>request($saveType)['offline']];
                    $saveType=request('save_type');
                    $data=$data + request($saveType,[]);
                    DB::table('element_published')->insert($data);
                }
            }
        }

        if($type=='published_and_online'){
            foreach($content as $key=>$val){
                if($saveType==$key) {
                    DB::table('element_published')->where('page_id',$pageId)->where('type',$saveType)->delete();
                    $val = $this->replaceOssDomain($val);
                    $data=['page_id' => $pageId,'type'=>$key,'content'=>$val,'status'=>1,'published'=>request($saveType)['published'],'offline'=>request($saveType)['offline']];
                    $saveType=request('save_type');
                    $data=$data + request($saveType,[]);
                    DB::table('element_published')->insert($data);
                }
            }
        }
        if($type=='offline'){
            foreach($content as $key=>$val){
                if($saveType==$key) {
                    DB::table('element_published')->where('page_id',$pageId)->where('type',$saveType)->delete();
                    $val = $this->replaceOssDomain($val);
                    $data=['page_id' => $pageId,'type'=>$key,'content'=>$val,'status'=>0,'published'=>request($saveType)['published'],'offline'=>request($saveType)['offline']];
                    $saveType=request('save_type');
                    $data=$data + request($saveType,[]);
                    DB::table('element_published')->insert($data);
                }
            }
        }



    }

    protected function replaceOssDomain($content){
        $ossDomainReplace = env('OSS_DOMAIN_REPLACE');
        $ossDomain = env('OSS_DOMAIN');
        if($ossDomainReplace && $ossDomain && $ossDomainReplace!==$ossDomain){
            $content = strtr($content,[$ossDomainReplace=>$ossDomain]);
        }
        return $content;
    }



    protected function uploadImg($file){
        $type=$file['type'];
        if($type && !in_array($type, ['image/gif','image/jpeg','image/pjpeg','image/png'])){
            return [
                'status'=>false,
                'message'=>'图片只支持gif,jpg,png'
            ];
        }

        $error=$file['error'];
        if($error> 0  && $error !=4){
            return [
                'status'=>false,
                'message'=>'上传错误'
            ];
        }
        $sizes=$file['size'];
        if($error> 1000000){
            return [
                'status'=>false,
                'message'=>'图片尺寸不能大于1M'
            ];
        }
        $new=false;
        if($file['name']){
            $ext=pathinfo($file['name']);
            $string=uniqid();
            $dir=substr($string, -1);
            $new='/upload/'. $dir.'/' .$string.'.'.$ext['extension'];
            if(!is_dir(base_path('public/upload/'.$dir))){
                mkdir(base_path('public/upload/'.$dir), 0777, true);
            }
            move_uploaded_file($file['tmp_name'], '.'.$new);
            if(env('OSS_ENABLE')){
                $ossClient=new \App\Lib\Oss();
                $prefix='cms';
                $remotePath= $prefix.$new;
                $filePath=realpath('.'.$new);
                $ossClient->upload($remotePath,$filePath);
            }
        }

        if(ConfigOss::checkWork() && $new){
            return [
                'status'=>true,
                'file'=>get_oss_path().trim($new,'/')
            ];
        }

        return [
            'status'=>true,
            'file'=>$new
        ];



    }


    protected function upload($file,$index){
        if(isset($file['type'])){
            $types=$file['type'];
            foreach($types as $type){
                if($type && !in_array($type, ['image/gif','image/jpeg','image/pjpeg','image/png'])){
                    return [
                        'status'=>false,
                        'message'=>'图片只支持gif,jpg,png'
                    ];
                }
            }
        }

        if(isset($file['error'])){
            $errors=$file['error'];
            foreach($errors as $error){
                if($error> 0  && $error !=4){
                    return [
                        'status'=>false,
                        'message'=>'上传错误'
                    ];
                }
            }
        }

        if(isset($file['size'])){
            $sizes=$file['size'];
            foreach($sizes as $size){
                if($error> 2000000){
                    return [
                        'status'=>false,
                        'message'=>'图片尺寸不能大于2M'
                    ];
                }
            }
        }

        if(isset($file['tmp_name'])){
            $files=$file['tmp_name'];
            $i = 0;
            $newFiles=[];
            foreach($files as $tmp){
                if($tmp){
                    $ext=pathinfo($file['name'][$i]);
                    $string=uniqid();
                    $dir=substr($string, -1);
                    $new='/upload/'. $dir.'/' .$string.'.'.$ext['extension'];
                    if(!is_dir(base_path('public/upload/'.$dir))){
                        mkdir(base_path('public/upload/'.$dir), 0777, true);
                    }
                    move_uploaded_file($tmp, '.'.$new);
                    $i++;
                    $newFiles[]=$new;
                }
            }
        }


        return [
            'status'=>true,
            'files'=>$newFiles,
            'index'=>$index
        ];

    }


    protected function addPageItems($pageId,$items){

        DB::table('element')->where('page_id',$pageId)->delete();
        if($items){
            foreach($items as $item){
                $type=$item[0];
                $content=$item[1];
                DB::table('element')->insert(['page_id' => $pageId, 'type' =>$type,'content'=>$content]);
            }
        }
    }




    public function del($type){
        $id=request('id');
        $page = DB::table('page')->find($id);
        $this->delKeywords($page->keyword);
        DB::table('page')->where('id',$id)->delete();

        request()->session()->flash('success', '删除成功!');
        return redirect('admin/page/published');
    }

    public function status(){
        $id=request('id');
        $result=Pages::find($id)->update(['status'=>request('status')]);
        return json_encode(['status'=>'success','result'=>$result]);
    }

    public function ajaxUpload(){
        $pageId = request('pageId',false);
//        if(empty($pageId)){
//            return [
//                'status'=>false,
//                'message'=>'pageId没有生成'
//            ];
//        }
        $file=$_FILES['file'];
        if(isset($file['type'])){
            $type=$file['type'];
            if($type && !in_array($type, ["video/mp4",'image/gif','image/jpeg','image/pjpeg','image/png','image/webp'])){
                return [
                    'status'=>false,
                    'message'=>'图片只支持gif,jpg,png,不支持'.$type
                ];
            }
        }



        if(isset($file['error'])){
            $error=$file['error'];
            if($error> 0  && $error !=4){
                $errorMessage=[
                    0,
                    '文件大小比php.ini中upload_max_filesize指定值要大',
                    '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
                    '文件只有部分被上传',
                    '没有文件被上传',
                    '上传文件大小为',
                ];
                return [
                    'status'=>false,
                    'message'=>'错误代码：'.$errorMessage[$error]
                ];
            }
        }


        if(isset($file['tmp_name'])){
            if($file['tmp_name']){
                $ext=pathinfo($file['name']);
                $string=uniqid();
                //$dir=date('Ymd').'/'.substr($string, -1);
                $dir=$pageId;
                $new='/upload/'. $dir.'/' .$string.'.'.$ext['extension'];
                if(!is_dir(base_path('public/upload/'.$dir))){
                    mkdir(base_path('public/upload/'.$dir), 0777, true);
                }
                move_uploaded_file($file['tmp_name'], '.'.$new);
                if (env('OSS_ENABLE')) {
                    $ossClient = new \App\Lib\Oss();
                    $prefix = 'cms';
                    $remotePath = $prefix . $new;
                    $filePath = realpath('.' . $new);
                    $ossClient->upload($remotePath, $filePath);
                }
            }
        }


        //截图
//        $config = [
//            'ffmpeg.binaries'  => '/usr/local/ffmpeg/bin/ffmpeg',
//            'ffprobe.binaries' =>  '/usr/local/ffmpeg/bin/ffprobe',
//          ];
//        $path = storage_path('upload/300104/5fcf6ca1d04af.mp4');
//        $ffprobe  = FFMpeg::create($config);
//        $video    = $ffprobe->open($path);
//        $fileName = date("Y-m-d H_i_s") . uniqid() . '.jpg';
//        $img_dir  = public_path($fileName);
//        $video->frame(TimeCode::fromSeconds(1))->save($img_dir);

        if(explode('/',$file['type'])[0]=='video'){
            $config = [
                'ffmpeg.binaries'  => env('ffmpeg'),
                'ffprobe.binaries' =>  env('ffprobe'),
            ];
            $path = realpath('.' . $new);
            $ffprobe = FFProbe::create($config);
            $info=$ffprobe
                ->streams($path)
                ->videos()
                ->first();
            $width=$info->get('width');
            $height=$info->get('height');

            $ffmpeg = FFMpeg::create($config);
            $mp4=$ffmpeg->open($path);
            $screenshot='/upload/'. $dir.'/' .$string.'_screenshot.jpg';
            $mp4->frame(TimeCode::fromSeconds(1))->save(public_path($screenshot));
            if(env('OSS_ENABLE')){
                $ossClient=new \App\Lib\Oss();
                $ossClient->upload($screenshot,public_path($screenshot));
            }
        }else{
            $info=getimagesize('.'.$new);
            $width=$info[0];
            $height=$info[1];
        }


        if(isset($prefix)){
            $new = "/{$prefix}{$new}";
        }
        $data= [
            'status'=>true,
            'file'=>$new,
            'type'=>explode('/',$file['type']),
            'info'=>[$width,$height],
            'pageId'=>$pageId
        ];
        if($data['type'][0]=='video'){
            $data['screenshot']=$screenshot;
        }

        return $data;
    }


    public function ajaxAuthor(){
        $author=new Author();
        $data=$author->getJsonData();

        return ['data'=>$data];
    }

    public function pageList(){
        $list=Pages::from('page as p')
            ->select('p.id','p.key','p.name')
            ->orderBy('p.id','asc')
            ->get();
        return $this->success($list);

    }

    public function productList(){

        $pageSize = request('page_size',5);
        $currentPage = request('current_page',1);

        //  product collection
        $type=request('type','product');
        $product_id=request('product_id');
        $name=request('name');


        $params=['page'=>$currentPage,'limit'=>$pageSize];
        if($type){
            $params['type']=$type;
        }
        if($product_id){
            $params['product_id']=$product_id;
        }
        if($name){
            $params['product_name']=$name;
        }
        $productList=$this->curl('goods/spu/getProdOrCollList',$params);
        $items=[];
        $count=0;

        if($productList['code']==1){
            if(isset($productList['data']['pageData']) && count($productList['data']['pageData'])>0){
                foreach($productList['data']['pageData'] as $product){
                    $item=[];
                    $item['id']=$product['unique_id'];
                    $item['image']=$product['kv_image'];
                    $item['sku']=$product['product_id'];
                    $item['name']=$product['product_name'];
                    $item['display_status']=$product['status'];

                    $item['desc']=$product['short_product_desc'];
                    $item['price']=$product['lowest_ori_price'];
                    $items[]=$item;
                }
                $count = $productList['data']['count'];
            }
        }

        $data=[
            'items'=>$items,
            'count'=>$count
        ];
        return $this->success($data);
        /*
        sleep(1);
        $pageSize = request('page_size',5);
        $currentPage = request('current_page',1);

        $id=request('id');
        $sku=request('sku');
        $name=request('name');


        $data=[
            ['id'=>1,'image'=>'https://cdn2.chowsangsang.com/cneshop/images/p/b/18625b/EPCM18625GDB_41a0df32-1c0e-4302-8fba-ccf824da08b5_250.jpg','sku'=>'demo_01','name'=>'测试商品01'],
            ['id'=>2,'image'=>'https://cdn2.chowsangsang.com/cneshop/images/p/k/90233k/EPCM90233KGK_22089843-e191-4040-9a2b-0a4417b4348b_350.jpg','sku'=>'demo_02','name'=>'测试商品02'],
            ['id'=>3,'image'=>'https://cdn2.chowsangsang.com/cneshop/images/p/k/23929k/EPCM23929GDK_b513e897-6f0b-40af-b473-15cec1f6df1a_350.jpg','sku'=>'demo_03','name'=>'测试商品03'],
            ['id'=>4,'image'=>'https://cdn2.chowsangsang.com/cneshop/images/p/k/89956k/EPCM89956DIK_ca802c62-52e2-4811-9930-d20deede6e25_350.jpg','sku'=>'demo_04','name'=>'测试商品04'],
            ['id'=>5,'image'=>'https://cdn2.chowsangsang.com/cneshop/images/p/k/89953k/EPCM89953DIK_86a7787e-c53e-47e9-b354-21a2c9f1eb0d_350.jpg','sku'=>'demo_05','name'=>'测试商品05'],
            ['id'=>6,'image'=>'https://cdn2.chowsangsang.com/cneshop/images/p/k/09218k/EPCM09218GDK_cb21e579-8a97-4278-bece-0f6bc2aeb042_350.jpg','sku'=>'demo_06','name'=>'测试商品06'],
            ['id'=>7,'image'=>'https://cdn2.chowsangsang.com/cneshop/images/p/k/15812k/EPCM15812GDK_9a163ad7-870a-4156-b44e-63d4173e62bb_350.jpg','sku'=>'demo_07','name'=>'测试商品07'],
            ['id'=>7,'image'=>'https://cdn2.chowsangsang.com/cneshop/images/p/k/88005k/EPCM88005GDK_dc53696d-8df5-4417-9d4e-d11c6f0dcd3c_350.jpg','sku'=>'demo_08','name'=>'测试商品08'],
            ['id'=>8,'image'=>'https://cdn2.chowsangsang.com/cneshop/images/p/k/90008k/EPCM90008DIK_e09649cd-6b0d-492b-816b-c11648c9c1c3_350.jpg','sku'=>'demo_09','name'=>'测试商品09'],
            ['id'=>10,'image'=>'https://cdn2.chowsangsang.com/cneshop/images/p/k/90343k/EPCM90343GDK_f06ec6e0-1335-4cd0-8d6c-b6c157039bed_350.jpg','sku'=>'demo_10','name'=>'测试商品10'],
            ['id'=>11,'image'=>'https://cdn2.chowsangsang.com/cneshop/images/p/k/76554k/EPCM76554GDK_1cf94dc7-8146-471a-82e4-a9a5da9d637d_350.jpg','sku'=>'demo_11','name'=>'测试商品11'],
            ['id'=>12,'image'=>'https://cdn2.chowsangsang.com/cneshop/images/p/k/78862k/EPCM78862GDK_0c03e296-9299-4721-9167-9b506e50ba07_350.jpg','sku'=>'demo_12','name'=>'测试商品12'],
            ['id'=>13,'image'=>'https://cdn2.chowsangsang.com/cneshop/images/p/k/90190k/EPCM90190GDK_38cb7884-b9d1-4358-bebd-3df5ae19936b_350.jpg','sku'=>'demo_13','name'=>'测试商品13'],
            ['id'=>14,'image'=>'https://cdn2.chowsangsang.com/cneshop/images/p/k/84831k/EPCM84831GDK_931d4413-fcee-4856-9833-5432270f65e2_350.jpg','sku'=>'demo_14','name'=>'测试商品14'],
            ['id'=>15,'image'=>'https://cdn2.chowsangsang.com/cneshop/images/p/k/78860k/EPCM78860GDK_3d255c20-9c04-46fa-a971-27f96ef8e871_350.jpg','sku'=>'demo_15','name'=>'测试商品15'],
        ];

        if($id){
            foreach($data as $k=>$item){
                if($item['id'] != $id){
                    unset($data[$k]);
                }
            }
        }

        if($sku){
            foreach($data as $k=>$item){
                if(stripos($item['sku'],$sku) === false){
                    unset($data[$k]);
                }
            }
        }

        if($name){
            foreach($data as $k=>$item){
                if(stripos($item['name'],$name) === false){
                    unset($data[$k]);
                }
            }
        }



        $data=[
            'items'=>array_slice($data,$pageSize*($currentPage-1),$pageSize),
            'count'=>15
        ];
        return $this->success($data);
        */
    }


    public function couponList()
    {

        $pageSize = request('page_size', 5);
        $currentPage = request('current_page', 1);

        $name = request('name');


        $params = ['page' => $currentPage, 'limit' => $pageSize];

        if ($name) {
            $params['name'] = $name;
        }

        $couponList = $this->curl('promotion/coupon/activeList', $params);
        $items = [];
        $count = 0;

        if ($couponList['code'] == 0) {
            if (isset($couponList['data']) && count($couponList['data']) > 0) {
                foreach ($couponList['data'] as $coupon) {
                    $item = [];
                    $item['id'] = $coupon['id'];
                    $item['name'] = $coupon['name'];
                    $item['total_amount'] = $coupon['total_amount'];
                    $item['total_discount'] = $coupon['total_discount'];
                    $items[] = $item;
                }
                $count = $couponList['count'];
            }
        }

        $data = [
            'items' => $items,
            'count' => $count
        ];
        return $this->success($data);
    }

    public function tree(){
        $categoryTree=$this->curl('goods/product/getCategoryTree');
        $data=['tree'=>$categoryTree['data']];
        return $data;

        /*
        $dir=base_path('public/upload');
        $files=glob($dir.'/*.{jpg,png,gif}',GLOB_BRACE);
        $list=$this->scan_dir($dir);
        $tree=$this->toTree($list);

        $files = array_map(function ($file) {
            return [
                'path' => strtr($file, [base_path('public') => '']),
                'name' => pathinfo($file)['basename'],
            ];
            //return strtr($file,[ROOT_PATH.'public'=>""]);
        }, $files);

        $data=['tree'=>$tree,'files'=>$files];
        return $data;
        */
    }

    public function post($type){

        try{
            $data=$_POST;
            $keyword=request('keyword',"");
            if($type=='draft'){
                $data['status']=null;
            }elseif($type=='published'){
                $data['status']=0;
            }elseif($type=='published_and_online'){
                $data['status']=1;
            }elseif($type=='offline'){
                $data['status']=0;
            }
            if(!isset($data['show_header_footer'])){
                $data['show_header_footer']=0;
            }

            if($data['id']){
                $page=Pages::find($data['id']);
                $this->delKeywords($page->keyword);
                $page->name=$data['name'];
                $page->key=$data['key'];
                $page->desciption=$data['desciption'];
                $page->keyword=$keyword;
                $page->title=$data['title'];
                $page->share_title=$data['share_title'];
                $page->breadcrumbs=$data['breadcrumbs'];
                if(!is_null($data['status'])){
                    $page->status=$data['status'];
                }



                $page->show_header_footer=$data['show_header_footer'];
                if($data['share_image']){
                    $page->share_image=$data['share_image'];
                }
                $page->bg_big_image=$data['bg_big_image']??"";
                $page->bg_small_image=$data['bg_small_image']??"";
                $page->save();
                $pageId=$data['id'];
            }else{
                $page=new Pages();
                if(Pages::where('key',$data['key'])->first()){
                    // return $this->redirect(request('back_page').toQuery(['id'=>""]),'error',sprintf('key：%s已经存在,请更换一个值',$data['key']));
                    return [
                        'status'=>0,
                        'message'=>sprintf('key：%s已经存在,请更换一个值',$data['key'])
                    ];
                };

                $itemData= [
                    'name'=>$data['name'],
                    'title'=>$data['title'],
                    'key'=>$data['key'],
                    'desciption'=>$data['desciption'],
                    'keyword'=>$keyword,
                    'share_image'=>$data['share_image']?:'',
                    'share_title'=>$data['share_title']?:'',
                    'bg_big_image'=>$data['bg_big_image']?:'',
                    'bg_small_image'=>$data['bg_small_image']?:'',
                    'share_title'=>$data['share_title'],
                    'show_header_footer'=>$data['show_header_footer'],
                    'breadcrumbs'=>$data['breadcrumbs']
                ];
                if(!is_null($data['status'])){
                    $itemData['status']=$data['status'];
                }

                $pageId=DB::table('page')->insertGetId(
                    $itemData
                );
            }

            $this->addPageContent($pageId,$data['content'],$type);
            //$this->createAPI($pageId,$type);
            $this->createJSON($pageId,$type);
            $this->saveKeywords($keyword,$pageId);
            if(isset($data['status']) &&  $data['status']===0){
                $this->delKeywords($keyword);
            }

            return [
                'status'=>1,
                'message'=>sprintf('编辑成功'),
                'data'=>$data,
                'pageId'=>$pageId
            ];
            //return $this->redirect(request('back_page').toQuery(['id'=>$pageId]),'success','编辑成功');

        }catch (\Exception $e){
            // echo $e->getMessage();
            return [
                'status'=>0,
                'message'=>$e->getMessage()
            ];
        }



    }

    protected function delKeywords($keyword){
        $redis = app('redis.connection');
        $redis->hdel(config('app.name').':'.self::SEARCH_KEY,$keyword);
    }
    protected function saveKeywords($keyword,$pageId){
        $keyword = trim($keyword);
        if($keyword){
            $redis = app('redis.connection');
            $redis->hset(config('app.name').':'.self::SEARCH_KEY,$keyword,$pageId);
        }
    }

    protected function createJSON($pageId,$type){


        $saveType=request('save_type');
        if($saveType ==='wechat') {
            $ossFileName='wechat';
        }elseif($saveType=='h5'){
            $ossFileName='h5';
        }else{
            throw new \Exception('保存错误！');
        }


        if( $type=='published_and_online' || $type=='offline'){
            $ossDomain=env('OSS_DOMAIN');
            $sql = "select page.id,`key`,keyword,desciption,`page`.`type`,breadcrumbs,title,if(LENGTH(share_image)>0,concat('".$ossDomain."',share_image),'') as share_image,
            if(LENGTH(bg_big_image)>0,concat('".$ossDomain."',bg_big_image),'') as bg_big_image,
            if(LENGTH(bg_small_image)>0,concat('".$ossDomain."',bg_small_image),'') as bg_small_image,
            share_title,element_published.status,show_header_footer,element_published.create_time,element_published.update_time from page join element_published on page.id=element_published.page_id  where page.id = ? and element_published.type = ?";
            $page = DB::select($sql,[$pageId,$saveType]);

            $page=array_first($page);

            $path= 'cms/page/'.$page->key;
            if(!is_dir(public_path($path))){
                mkdir(public_path($path), 0777, true);
            }
            $fullPath = $path.'/'.$ossFileName.'.json';

            $data = DB::table('element_published')->where('page_id',$pageId)->where('type',request('save_type'))->value('content');
            $nodes=json_decode($data,true);
            $page->nodes=$nodes;
            $content = json_encode($page);
            //$result=$ossClient->putObject($path,$content);
            file_put_contents(public_path($fullPath),$content);
            if(env('OSS_ENABLE')){
                $ossClient=new \App\Lib\Oss();
                $ossClient->upload($fullPath,public_path($fullPath));
            }

        }
    }
    protected function createAPI($pageId,$type){


        $saveType=request('save_type');
        if($saveType ==='h5') {
            $ossFileName='os';
        }elseif($saveType=='wechat'){
            $ossFileName='wechat';
        }else{
            throw new \Exception('oss保存错误！');
        }


        if( $type=='published_and_online' || $type=='offline'){
            $ossDomain=env('OSS_DOMAIN').'/cms';
            $sql = "select page.id,`key`,keyword,desciption,`page`.`type`,breadcrumbs,title,if(LENGTH(share_image)>0,concat('".$ossDomain."',share_image),'') as share_image,
            if(LENGTH(bg_big_image)>0,concat('".$ossDomain."',bg_big_image),'') as bg_big_image,
            if(LENGTH(bg_small_image)>0,concat('".$ossDomain."',bg_small_image),'') as bg_small_image,
            share_title,element_published.status,show_header_footer,element_published.create_time,element_published.update_time from page join element_published on page.id=element_published.page_id  where page.id = ? and element_published.type = ?";
            $page = DB::select($sql,[$pageId,$saveType]);

            $page=array_first($page);

            $ossClient = new \App\Lib\Oss();
            $path= 'cms/page/'.$page->key.'/'.$ossFileName.'.json';

            $data = DB::table('element_published')->where('page_id',$pageId)->where('type',request('save_type'))->value('content');
            $nodes=json_decode($data,true);
            $page->nodes=$nodes;
            $content = json_encode($page);
            $result=$ossClient->putObject($path,$content);

            $cdn = new \App\Lib\CDN();
            $cdn->refreshFile(env('OSS_DOMAIN').'/'.$path); //TODO
        }
    }

    function getOssPath($source,$key,$media){
        $domain = url('');
        $path= 'cms/page/'.$key.'/'.$media.'.json';
        return $domain.'/'.$path;
    }

    protected function _imageTag(&$tag){
        //$tag['src']=getImageUrl($tag['src']);

    }

    protected function  _categoryMenu(&$tag){
        if(isset($tag['nodes']) && count($tag['nodes'])> 0){
            $tag['nodes']=array_map(function ($mainCate){
                $banner=array_shift($mainCate['list']);
                $mainCate['banner']=$banner;
                return $mainCate;
            },$tag['nodes']);
        }

    }

    public function iframe(){
        $validator = \Validator::make(request()->all(), [
                'name' => 'required',
                'tags' => 'required'
            ]
        );
        if($validator->fails()){
            return $validator->errors()->first();
        }

        $data=[
            'name'=>request('name'),
            'tags'=>json_encode(explode('|',request('tags',''))),
        ];


        return view('backend.page.iframe',$data);
    }

    public function kewrordCheck(){
        return false;
    }

    public function keyCheck(){
        return false;
    }


    public function getToken(){
        if($token = app('redis')->get('wx_token')){
            return $token;
        }else{
            $http = new Http();
            $url = 'https://api.weixin.qq.com/cgi-bin/token';
            $params=[
                'grant_type'=>'client_credential',
                'appid'=>$this->appid,
                'secret'=>$this->secret
            ];
            $result = $http->get($url,$params);
            $token = json_decode($result,true)['access_token'];
            $redis = app('redis');
            $redis->setex('wx_token',7200,$token);
            return $token;
        }
    }

    public function getUnlimited ($key){
        $http= new http();
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=".$this->getToken();
        $params=[
            "scene"=>$key,
            // "page"=>"pages/service/service",
            "width"=>280,
            "is_hyaline"=>true
        ];
        $result = $http->post($url,$params);
        if (isset(json_decode($result, true)['errcode'])) {
            return $result;
        } else {
            header('Content-type: image/png');
            echo $result;
            exit;
        }
    }

    public function ajaxKey(){
        $name = request('name');
        $key = request('key');
        $page=new Pages();
        if(Pages::where('key',$key)->first()){
            return [
                'status'=>0,
                'message'=>sprintf('key：%s已经存在,请更换一个值',$key)
            ];
        }else{
            try{
                $pageId=DB::table('page')->insertGetId(
                    ['name'=>$name,'key'=>$key]
                );
                return [
                    'status'=>1,
                    'pageId'=>$pageId
                ];
            }catch (\Exception $e ){
                return [
                    'status'=>0,
                    'message'=>$e->getMessage()
                ];
            }

        }
    }

    public function navication(){

        $type='published';
        $page=new Pages();
        $page=$page->where('key','navication')->first();
        if(empty($page)){
            $pageId=DB::table('page')->insertGetId(
                ['name'=>"导航",'key'=>"navication"]
            );
            $page=Pages::where('key','navication')->first();
        }else{
            $pageId=$page->id;
        }
        $pageContent = DB::table('element_published')->where('page_id',$pageId)->pluck('content','type')->toArray();

        if(!empty($pageContent)){
            $pageContent=array_map(function ($item){
                return  json_decode($item,true);
            },$pageContent);
        } else{
            $data = json_decode('[{"tag":"navication_menu","placeholder_pc":"/upload/demo/navication_pc.png","placeholder_h5":"/upload/demo/navication_h5.png","name":"导航组件","nodes":[]}]',true);
            $pageContent=['wechat'=>$data,'h5'=>$data];
        }
        return view('backend.page.navication',['page'=>$page,'pageContent'=>json_encode($pageContent),'type'=>$type]);
    }

    public function navication1(){

        $type='published';
        $page=new Pages();
        $page=$page->where('key','navication1')->first();
        if(empty($page)){
            $pageId=DB::table('page')->insertGetId(
                ['name'=>"导航",'key'=>"navication1"]
            );
            $page=Pages::where('key','navication1')->first();
        }else{
            $pageId=$page->id;
        }
        $pageContent = DB::table('element_published')->where('page_id',$pageId)->pluck('content','type')->toArray();

        if(!empty($pageContent)){
            $pageContent=array_map(function ($item){
                return  json_decode($item,true);
            },$pageContent);
        } else{
            $data = json_decode('[{"tag":"navication_menu","placeholder_pc":"/upload/demo/navication_pc.png","placeholder_h5":"/upload/demo/navication_h5.png","name":"导航组件","nodes":[]}]',true);
            $pageContent=['wechat'=>$data,'h5'=>$data];
        }
        return view('backend.page.navication',['page'=>$page,'pageContent'=>json_encode($pageContent),'type'=>$type]);
    }

    public function navicationSave($type){

        try{
            $data=$_POST;
            if($type=='draft'){
                $data['status']=null;
            }elseif($type=='published'){
                $data['status']=0;
            }elseif($type=='published_and_online'){
                $data['status']=1;
            }elseif($type=='offline'){
                $data['status']=0;
            }
            if(!isset($data['show_header_footer'])){
                $data['show_header_footer']=0;
            }

            if($page=Pages::find($data['id'])){
                if(!is_null($data['status'])){
                    $page->status=$data['status'];
                }
                $page->save();
                $pageId=$data['id'];
            }else{
                return [
                    'status'=>0,
                    'message'=>"导航数据出错",
                    'data'=>$data,
                ];
            }

            $this->addPageContent($pageId,$data['content'],$type);
            $this->createAPI($pageId,$type);
            return [
                'status'=>1,
                'message'=>sprintf('%s导航编辑成功',request('save_type')),
                'data'=>$data,
                'pageId'=>$pageId
            ];

        }catch (\Exception $e){
            // echo $e->getMessage();
            return [
                'status'=>0,
                'message'=>$e->getMessage()
            ];
        }



    }

    public function ajaxUploadShareImage(){
        $file=$_FILES['file'];
        if(isset($file['type'])){
            $type=$file['type'];
            if($type && !in_array($type, ['image/gif','image/jpeg','image/pjpeg','image/png','image/webp'])){
                return [
                    'status'=>false,
                    'message'=>'图片只支持gif,jpg,png,不支持'.$type
                ];
            }
        }

        if(isset($file['error'])){
            $error=$file['error'];
            if($error> 0  && $error !=4){
                $errorMessage=[
                    0,
                    '文件大小比php.ini中upload_max_filesize指定值要大',
                    '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
                    '文件只有部分被上传',
                    '没有文件被上传',
                    '上传文件大小为',
                ];
                return [
                    'status'=>false,
                    'message'=>'错误代码：'.$errorMessage[$error]
                ];
            }
        }

        if(isset($file['tmp_name'])){
            if($file['tmp_name']){
                $ext=pathinfo($file['name']);
                $string=uniqid();
                $dir=substr($string, -1);
                $new='/upload/'. $dir.'/' .$string.'.'.$ext['extension'];
                if(!is_dir(base_path('public/upload/'.$dir))){
                    mkdir(base_path('public/upload/'.$dir), 0777, true);
                }
                move_uploaded_file($file['tmp_name'], '.'.$new);
                if(env('OSS_ENABLE')){
                    $ossClient=new \App\Lib\Oss();
                    $prefix='cms';
                    $remotePath= $prefix.$new;
                    $filePath=realpath('.'.$new);
                    $ossClient->upload($remotePath,$filePath);
                    $domain = env('OSS_DOMAIN');
                }
            }
        }

        if(isset($prefix)){
            $new = "/{$prefix}{$new}";
        }
        $image_full_url = env('OSS_DOMAIN').$new;
        return [
            'status'=>true,
            'domain'=>$domain??'',
            'file'=>$new,
            'info'=>getimagesize($image_full_url)
        ];
    }

    public function ajaxUploadProduct(){
        $id = request('id','');
        $spu = request('spu',$id);
        if(empty($spu)){
            return [
                'status'=>false,
                'message'=>'$spu没有生成'
            ];
        }
        $file=$_FILES['file'];
        if(isset($file['type'])){
            $type=$file['type'];
            if($type && !in_array($type, ['video/mp4','image/gif','image/jpeg','image/pjpeg','image/png','image/webp'])){
                return [
                    'status'=>false,
                    'message'=>'图片只支持gif,jpg,png,不支持'.$type
                ];
            }
        }




        if(isset($file['error'])){
            $error=$file['error'];
            if($error> 0  && $error !=4){
                $errorMessage=[
                    0,
                    '文件大小比php.ini中upload_max_filesize指定值要大',
                    '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值',
                    '文件只有部分被上传',
                    '没有文件被上传',
                    '上传文件大小为',
                ];
                return [
                    'status'=>false,
                    'message'=>'错误代码：'.$errorMessage[$error]
                ];
            }
        }


        if(isset($file['tmp_name'])){
            if($file['tmp_name']){
                $ext=pathinfo($file['name']);
                $string=uniqid();
                //$dir=date('Ymd').'/'.substr($string, -1);
                $dir='spu_images/'.$spu;
                $new='/upload/'. $dir.'/' .$string.'.'.$ext['extension'];
                if(!is_dir(base_path('public/upload/'.$dir))){
                    mkdir(base_path('public/upload/'.$dir), 0777, true);
                }
                move_uploaded_file($file['tmp_name'], '.'.$new);
                if(env('OSS_ENABLE')){
                    $ossClient=new \App\Lib\Oss();
                    $prefix='cms';
                    $remotePath= $prefix.$new;
                    $filePath=realpath('.'.$new);
                    $ossClient->upload($remotePath,$filePath);
                }
            }
        }
        if(explode('/',$file['type'])[0]=='video'){
            $config = [
                'ffmpeg.binaries'  => env('ffmpeg'),
                'ffprobe.binaries' =>  env('ffprobe'),
            ];
            $path = realpath('.'.$new);
            $ffprobe = FFProbe::create($config);
            $info=$ffprobe
                ->streams($path)
                ->videos()
                ->first();
            $width=$info->get('width');
            $height=$info->get('height');

            $ffmpeg = FFMpeg::create($config);
            $mp4=$ffmpeg->open($path);
            $screenshot='/upload/'. $dir.'/' .$string.'_screenshot.jpg';
            $mp4->frame(TimeCode::fromSeconds(1))->save(public_path($screenshot));
            if(env('OSS_ENABLE')){
                $ossClient=new \App\Lib\Oss();
                $ossClient->upload($screenshot,public_path($screenshot));
            }
        }else{
            $info=getimagesize('.'.$new);
            $width=$info[0];
            $height=$info[1];
        }

        if(isset($prefix)){
            $new = "/{$prefix}{$new}";
        }
        $data= [
            'status'=>true,
            'file'=>$new,
            'type'=>explode('/',$file['type']),
            'info'=>[$width,$height],
            'pageId'=>$spu
        ];
        if($data['type'][0]=='video'){
            $data['screenshot']=$screenshot;
        }


        return $data;
    }

}
