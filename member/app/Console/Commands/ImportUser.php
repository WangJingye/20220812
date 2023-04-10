<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\SocialUsers;
use App\Model\Users;
use App\Model\Address;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
class ImportUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import-user:{num}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test';

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
     *
     * @return mixed
     */
    public function handle()
    {
            $num = $this->argument('num');


            setlocale(LC_ALL, 'zh_CN');

            $excel_file_path = 'C:\Users\Dora.Chang\Desktop\customer\dlc_customer_bunch' . $num . '.csv';

            $content = file_get_contents($excel_file_path);


            $encode = mb_detect_encoding($content, array("ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5'));
            $content = mb_convert_encoding($content, 'UTF-8', $encode);
            $content = str_replace('"', '', $content);
            $content_data = explode("\n", $content);

            unset($content_data[0]);
            $row = [];
            foreach ($content_data as $v) {
                $array = explode(',', $v);
                if (!empty($array) && count($array) > 1) {
                    $row[] = $array;
                }
            }

            $user = new Users;


            foreach ($row as $k => $item) {

                $arr = [
                    'pos_id' => trim($item[0], '"'),
                    'phone' => trim($item[1], '"'),
                    'email' => trim($item[2], '"'),
                    'password' => trim($item[4], '"'),
                    'respect_type' => trim($item[5], '"'),
                    'nickname' => trim($item[6], '"'),
                    'name' => trim($item[7], '"'),
                    'birth' => date('Y-m-d', strtotime(trim($item[8], '"'))),


                ];
                $item[0] = trim($item[0], '"');
                $item[3] = trim($item[3], '"');
                if(!$item[0]){
                    $user = $user->UpdateOrCreate(['email' => $item[0]], $arr);
                }else{
                    Log::info('num'.$k);
                    continue;
//                    $user = $user->UpdateOrCreate(['pos_id' => $item[0]], $arr);
                }

                $uid = $user->id;

                if ($item[3]) {

                    SocialUsers::FirstOrCreate(['open_id' => $item[3]], [
                        'user_id' => $uid,
                        'open_id' => $item[3],
                        'social_type' => 'weibo'
                    ]);
                }
                if ($item[16]) {
                    $data = [
                        'user_id' => $uid,
                        'zip_code' => trim($item[17], '"') ?? 0,
                        'sex' => trim($item[11], '"') == 'Madam' ? 1 : 2,
                        'name' => trim($item[10], '"'),
                        'mobile' => trim($item[12], '"'),
                        'city' => trim($item[14], '"'),
                        'province' => trim($item[13], '"'),
                        'area' => trim($item[15], '"') ?? '',
                        'address' => trim($item[16], '"'),
                        'is_default' => trim($item[18], '"') ?? 0,
                    ];
                    $id = Address::UpdateOrCreate(['user_id' => $uid], $data);
                }

            }


    }
}