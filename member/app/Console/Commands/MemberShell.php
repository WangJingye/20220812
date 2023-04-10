<?php

namespace App\Console\Commands;

use App\Model\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Model\Users;
use App\Model\MemberMergeRecord;
use App\Service\Dlc\UsersService;
use App\Service\Dlc\Sftp;

class MemberShell extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'member:shell {option} {params?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $option = $this->argument('option');
            $params = $this->argument('params');
            call_user_func_array([__CLASS__, $option], [$params]);
            $this->line('['.date('Y-m-d H:i:s').']Successful');
        } catch (\Exception $e) {
            $this->line($e->getMessage());
        }$this->line('['.date('Y-m-d H:i:s').']End');
    }

    protected function merge($remote_zip_dir){
        $sftp = new Sftp;
        $remote_zip_dir = "/{$remote_zip_dir}";
        $files = $sftp->scan($remote_zip_dir);
        if($files){
            foreach($files as $file){
                $file_info = pathinfo($file);
                if($file_info['extension']=='zip'){
                    //每次只获取一个压缩包
                    $remote_zip_file = "{$remote_zip_dir}/{$file}";
                    //获取对应的md5文件(解压密码)
                    if(in_array("{$file_info['filename']}.md5",$files)){
                        $remote_md5_file = "{$remote_zip_dir}/{$file_info['filename']}.md5";
                        break;
                    }
                }
            }
        }
        if(isset($remote_zip_file)){
            $secret = 'dlc@123';
            //文件统一名称
            $local_filename = date('YmdHis_').rand('100','999');
            //创建相关目录
            //创建zip包保存的目录
            $backup_dir = storage_path('file/merge/backup');
            if(!is_dir($backup_dir)){
                mkdir($backup_dir,0777,true);
            }
            //临时目录
            $tmp_extract_dir = storage_path('file/merge/extract');
            if(!is_dir($tmp_extract_dir)){
                mkdir($tmp_extract_dir,0777,true);
            }
            //存放xml的目录
            $xml_dir = storage_path('file/merge/xml');
            if(!is_dir($xml_dir)){
                mkdir($xml_dir,0777,true);
            }
            //下载zip包
            $local_zip_file = "{$backup_dir}/".$local_filename.'.zip';
            $sftp->download($remote_zip_file,$local_zip_file);
            //下载成功后删除远程zip包
            $sftp->unlink($remote_zip_file);
            $zip = new \ZipArchive;
            if($zip->open($local_zip_file) === TRUE){
                //解压
                //下载md5解压密码(如果有的话)
                if(isset($remote_md5_file)){
                    $local_md5_file = "{$backup_dir}/".$local_filename.'.md5';
                    $sftp->download($remote_md5_file,$local_md5_file);
                    //获取md5文件里的密码
//                    $secret = file_get_contents($local_md5_file);
                    $zip->setPassword($secret);
                    //下载成功后删除远程md5文件
                    $sftp->unlink($remote_md5_file);
                }
                $zip->extractTo($tmp_extract_dir);
                $zip_extract_msg = $zip->getStatusString();
                $zip->close();
                //遍历所有解压的文件 找出对应的xml
                $extract_files = $this->file_list($tmp_extract_dir);
                if(!empty($extract_files)){
                    foreach($extract_files as $extract_file){
                        $extract_file_info = pathinfo($extract_file);
                        if($extract_file_info['extension']=='xml'){
                            //获取第一个xml文件
                            $extract_xml_file = "{$tmp_extract_dir}/{$extract_file}";
                            if(isset($extract_xml_file)){
                                $local_xml_file = "{$xml_dir}/{$extract_file}";
                                //将这个xml文件移动到存放xml的目录中
                                copy($extract_xml_file,$local_xml_file);
                                $local_xml_file = str_replace('\\','/',$local_xml_file);
                                $this->mergeHandle($local_xml_file);
                            }
                        }
                    }
                    //将解压临时目录删除
                    $this->deleteDir($tmp_extract_dir);
                    return ;
                }else{
                    throw new \Exception($zip_extract_msg);
                }
            }else{
                throw new \Exception('解压失败');
            }
        }return;
    }

    /**
     * @param $xml_path
     * @throws \Exception
     */
    protected function mergeHandle($xml_path)
    {
        if(file_exists($xml_path)){
            $xml = file_get_contents($xml_path);
            $xml = simplexml_load_string($xml);
            if(!empty($xml->Row)){
                foreach($xml->Row as $row){
                    $row = (array)$row;
                    $this->mergeHandleRow($row['OldMemberCode'],$row['NewMemberCode'],$xml_path);
                }
            }
        }return ;
    }

    protected function mergeHandleRow($old_member_code,$new_member_code,$xml_path=''){
        $key = md5($old_member_code.$new_member_code);
        $data = [
            'old_member_code'=>$old_member_code,
            'new_member_code'=>$new_member_code,
            'key'=>$key,
            'file_path'=>$xml_path,
        ];
        if(!MemberMergeRecord::query()->where('key',$key)->count()){
            //不存在则添加记录新记录
            $record_id = MemberMergeRecord::query()->insertGetId($data);
            //合并本地用户信息
            $r = $this->memberMerge($old_member_code,$new_member_code);
            if($r){
                //订单合并
                $result = $this->orderMerge($old_member_code,$new_member_code);
                $update_data = [
                    'status'=>$result['code']?1:0,
                    'msg'=>$result['message'],
                ];
            }else{
                //没找到记录 结束
                $update_data = [
                    'status'=>1,
                    'msg'=>'本地没找到旧会员卡号,不做任何处理',
                ];
            }
            $this->line("ID:{$record_id},结果:{$update_data['msg']}");
            MemberMergeRecord::query()->find($record_id)->update($update_data);
        }else{
            //表中已存在该记录则不做处理
            $this->line("已存在记录不做处理,旧会员ID:{$old_member_code},新会员ID:{$new_member_code}");
        }
    }

    private function memberMerge($old_member_code,$new_member_code){
        $old_user_id = Users::query()->where('pos_id',$old_member_code)->value('id');
        if($old_user_id){
            //找到旧会员ID 则更新为新的会员ID
            Users::query()->find($old_user_id)->update(['pos_id'=>$new_member_code]);
            //更新缓存中的会员ID
            UsersService::setPosId($old_user_id,$new_member_code);
            return true;
        }
        //没找到 无需处理
        return false;
    }

    /**
     * @param $old_member_code
     * @param $new_member_code
     * @return mixed
     */
    private function orderMerge($old_member_code,$new_member_code){
        return app('ApiRequestInner')->request('orderMerge','POST',[
            'OldMemberCode'=>$old_member_code,
            'NewMemberCode'=>$new_member_code,
        ]);
    }

    public function mergeretry($id){
        $record = MemberMergeRecord::query()->find($id);
        if($record){
            $old_member_code = $record->old_member_code;
            $new_member_code = $record->new_member_code;
            $this->mergeHandleRow($old_member_code,$new_member_code);
        }throw new \Exception('没有这个记录');
    }

    /**
     * 获取指定目录下的所有文件和文件夹
     * @param $path
     * @return array
     */
    public function file_list($path) {
        $all = [];
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    $all[] = $file;
                }
            }
        }return $all;
    }

    /**
     * 删除指定目录下的所有文件和文件夹
     * @param $path
     */
    public function deleteDir($path) {
        if (is_dir($path)) {
            //扫描一个目录内的所有目录和文件并返回数组
            $dirs = scandir($path);
            foreach ($dirs as $dir) {
                //排除目录中的当前目录(.)和上一级目录(..)
                if ($dir != '.' && $dir != '..') {
                    //如果是目录则递归子目录，继续操作
                    $sonDir = $path.'/'.$dir;
                    if (is_dir($sonDir)) {
                        //递归删除
                        $this->deleteDir($sonDir);
                        //目录内的子目录和文件删除后删除空目录
                        @rmdir($sonDir);
                    } else {
                        //如果是文件直接删除
                        @unlink($sonDir);
                    }
                }
            }
            @rmdir($path);
        }
    }
}
