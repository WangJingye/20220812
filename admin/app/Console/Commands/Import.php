<?php

namespace App\Console\Commands;

use function AlibabaCloud\Client\json;
use App\Model\Element;
use App\Model\Pages;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use App\Lib\Http;

class Import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:product_detail {--filename=}';
    protected $_http;
    const  IMPORT_KEY = 'import_product_detail';
    protected $importId;
    protected $errorLines=[];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '批量导入商品图文详情';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_http = new Http();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $csvFile=$this->option('filename');
        if(!$csvFile){
            return $this->error('filename必传');
        }
        $lineCount = $this->getLineCount($csvFile);
        if($lineCount>0){
            $importId=DB::table('import_log')->insertGetId(['count'=>$lineCount,'filename'=>$csvFile]);

            $this->importId = $importId;
            $this->getCsvData($csvFile);
            $data=[
                'finished'=>date("Y-m-d H:i:s")
            ];
            if(count($this->errorLines)>0){
                $data['error_ids'] =join(',',$this->errorLines);
            }
            $this->updateStatus($data);
        }
    }

    public function updateStatus($data){
        dump($data);
        DB::table('import_log')->where('id',$this->importId)->update($data);
    }

    public function getLineCount($csvFile){
        $command ='wc -l '.$csvFile ."|awk '{print $1}'";
        $return = (int) trim(shell_exec($command),"\n");
        return $return - 1;
    }

    protected function getCsvData($file){
        if(!is_file($file)){
            $this->updateStatus(['message'=>'不是文件']);
            dd('不是文件');
        }

        $handle = fopen($file,'r');
        if(!$handle){
            $this->updateStatus(['message'=>'读取文件失败']);
            dd('读取文件失败');
        }


        $i =0;
        while(($data = fgetcsv($handle))!==false){
                if($i == 0 ){
                    if($data[0]!=='SPU' || $data[1]!=='TYPE' || $data[2]!=='MEDIA1' ||$data[3]!=='MEDIA2'||$data[4]!=='MEDIA3'||$data[5]!=='MEDIA4'||$data[6]!=='MEDIA5'||$data[7]!=='MEDIA6'  ){
                        $this->updateStatus(['message'=>'标题不对']);
                        return false;
                    }
                }
                if($i>0){
                    if(count($data)!=8){
                        $this->updateStatus(['message'=>sprintf("第%s行：列数不对",$i + 1)]);
                    }else{
                        $this->importImages($data,$i);
                    }
                }
                $i++;
        }
    }

    protected function importImages($data,$i){
        $sku = $data[0];
        $type = $data[1];
        $imagesTag=false;
        $kvTag=false;
        $imageArray = array_slice($data,2);
        if($type=='detail'){
            $imagesTag=$this->createMultiTag($sku,$imageArray);
            $imagesTag = json_encode($imagesTag);
        }

        if($type=='kv'){
            $kvTag=$this->createImageTag($sku,$imageArray);
            $kvTag = json_encode($kvTag);
        }
        $params=[
            'id'=>$sku,
            'kv_images'=>$kvTag,
        ];

        $this->save($params,$i);

    }


    protected function createImageTag($sku,$imageArray){

        $images=[];
        $dir='/upload/spu_images/';
        foreach($imageArray as $image){
            $image=strtolower(trim($image));

            $dir='/upload/spu_images/';
            $fileFullPath=public_path(".".$dir.$image);
            if(!is_file($fileFullPath)){
                $this->info(sprintf("SPU:%s中的素材不存在：%s",$sku,$fileFullPath));
                continue;
            }
            $finfo = finfo_open(FILEINFO_MIME);
            $mime=finfo_file($finfo,$fileFullPath);
            finfo_close($finfo);

            if(strpos($mime,'video')===0){
                $config = [
                    'ffmpeg.binaries'  => env('ffmpeg'),
                    'ffprobe.binaries' =>  env('ffprobe'),
                ];

//            $ffprobe = FFProbe::create($config);
//            $info=$ffprobe
//                ->streams($path)
//                ->videos()
//                ->first();
//            $width=$info->get('width');
//            $height=$info->get('height');

                $ffmpeg = FFMpeg::create($config);
                $string=uniqid();
                if(!is_dir(public_path($dir."screenshot"))){
                    mkdir(public_path($dir."screenshot"), 0777, true);
                }
                $mp4Path=public_path(".".$dir.$image);
                $mp4=$ffmpeg->open($mp4Path);
                $screenshot= $dir."screenshot/" .$string.'_screenshot.jpg';
                $mp4->frame(TimeCode::fromSeconds(1))->save(public_path(".".$screenshot));
                $imageObj=[
                    "tag"=>"video",
                    "data"=>[
                        "src"=>$this->getOssPath($sku,$screenshot),
                        "video"=>$this->getOssPath($sku,$dir.$image),
                    ]
                ];
            }else{
                $imageObj=[
                    "tag"=>"image",
                    "data"=>[
                        "src"=>$this->getOssPath($sku,  $dir.$image),
                    ]
                ];
            }

            $images [] = $imageObj;
        }
        return $images;
    }


    protected function createMultiTag($sku,$imageArray){

        $nodes = [];
        foreach($imageArray as $image){
            $imageObj=[
                "tag"=>"image",
                "src"=>$this->getOssPath($sku,$image),
                "action"=>[
                    "data"=>"",
                    "type"=>"none",
                    "route"=>"",
                    "height"=>""
                ]
            ];
            $nodes [] = $imageObj;
        }
        $tag =[
            "tag"=>"multi_image",
            "name"=>"多图",
            "nodes"=>$nodes,
            "height"=>"",
            "columns"=>1
        ];
        return [$tag];
    }


    protected function getOssPath($sku,$filename){
        return env('OSS_DOMAIN').$filename;
    }

    public function save($data,$i){
        $result=$this->curl('goods/spu/saveDetail',$data);
        if(isset($result['code']) && $result['code']==1){
            DB::table('import_log')->where('id',$this->importId)->increment('success');
        }else{
            DB::table('import_log')->where('id',$this->importId)->increment('error');
            $this->errorLines[]=$i+1;
        }
        return $result;
    }

    public function curl($apiName, $data = [])
    {
        return $this->_http->curl($apiName, $data);
    }



}
