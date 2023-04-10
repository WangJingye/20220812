<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DemoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function productList(){
            $ossClient=new \App\Lib\Oss();
            $prefix='cms';
            $file='/upload/demo/1.jpg';
            $remotePath= $prefix.$file;
            $filePath=realpath('.'.$file);
            $ossClient->upload($remotePath,$filePath);
    }

    public function doc(){
        //return $this->contact();
        //return $this->ok($this->getDetailById());
        //return $this->ok($this->getAllTags());
        return $this->ok($this->getListById());
        return $this->ok($this->getListByTag());
        return $this->ok($this->getCustomAllMenu());
        return $this->ok($this->homeMenu());
        return $this->ok($this->login());
    }

    protected function contact(){
        return $this->ok([],'留言成功');
    }

    protected function getDetailById(){
        return [
            'image'=>['src'=>'http://img.alicdn.com/bao/uploaded/i2/TB1XwrQSXXXXXb_apXXntC69XXX_035351.jpg','width'=>750,'height'=>500,],
            'title'=>'标题',
            'description'=>'文章的描述',
            'category'=>'Increase Conversion',
            'tag'=>['3D/4D全息','AI大数据学习'],
            'created_at'=>'2019-10-23 10:23:11',
            'is_favorites'=>1,
            'content'=>[

            ]


        ];
    }


    protected function getListById(){
        return [
            'list'=>[
                [
                    'image'=>'http://img.alicdn.com/bao/uploaded/i2/TB1XwrQSXXXXXb_apXXntC69XXX_035351.jpg',
                    'title'=>'PREFUME BOX',
                    'description'=>'标题的描述标题的描述标题的描述标题的描述标题的描述标题的描述标题的描述标题的描述标题的描述',
                    'category'=>'Increase Traffic',
                    'article_id'=>1,
                    'tag'=>['3D/4D/全息','3D/4D/全息','3D/4D/全息','3D/4D/全息']
                ],
                [
                    'image'=>'http://img.alicdn.com/bao/uploaded/i2/TB1XwrQSXXXXXb_apXXntC69XXX_035351.jpg',
                    'title'=>'PREFUME BOX',
                    'description'=>'标题的描述标题的描述标题的描述标题的描述标题的描述标题的描述标题的描述标题的描述标题的描述',
                    'category'=>'Increase Traffic',
					'article_id'=>1,
                    'tag'=>['3D/4D/全息','3D/4D/全息','3D/4D/全息','3D/4D/全息']
                ],
                [
                    'image'=>'http://img.alicdn.com/bao/uploaded/i2/TB1XwrQSXXXXXb_apXXntC69XXX_035351.jpg',
                    'title'=>'PREFUME BOX',
                    'description'=>'标题的描述标题的描述标题的描述标题的描述标题的描述标题的描述标题的描述标题的描述标题的描述',
                    'category'=>'Increase Traffic',
					'article_id'=>1,
                    'tag'=>['3D/4D/全息','3D/4D/全息','3D/4D/全息','3D/4D/全息']
                ],
                [
                    'image'=>'http://img.alicdn.com/bao/uploaded/i2/TB1XwrQSXXXXXb_apXXntC69XXX_035351.jpg',
                    'title'=>'PREFUME BOX',
                    'description'=>'标题的描述标题的描述标题的描述标题的描述标题的描述标题的描述标题的描述标题的描述标题的描述',
                    'category'=>'Increase Traffic',
					'article_id'=>1,
                    'tag'=>['3D/4D/全息','3D/4D/全息','3D/4D/全息','3D/4D/全息']
                ],
            ]
        ];
    }

    protected function getListByTag(){
        return $this->getListById();
    }

    protected function getAllTags(){
        return [
            'tags'=>[
                ['id'=>1,'name'=>'3D/4D全息'],
                ['id'=>2,'name'=>'AI大数据学习'],
                ['id'=>3,'name'=>'3D/4D全息'],
                ['id'=>4,'name'=>'AI大数据学习'],
                ['id'=>5,'name'=>'3D/4D全息'],
                ['id'=>6,'name'=>'3D/4D全息'],
                ['id'=>7,'name'=>'3D/4D全息'],
                ['id'=>8,'name'=>'3D/4D全息'],
                ['id'=>9,'name'=>'3D/4D全息'],
                ['id'=>10,'name'=>'3D/4D全息']
            ]
        ];
    }

    protected function getCustomAllMenu(){
        return [
            'menus'=>[
                [
                    'name'=>'Increase Traffic'
                ],
                [
                    'name'=>'Increase Follows'
                ],
                [
                    'name'=>'Increase Conversion'
                ],
                [
                    'name'=>'Branding'
                ],


            ]
        ];
    }

    protected function homeMenu(){
        return [
            'menus'=>[
                [
                    'category_id'=>1,
                    'is_custom_category'=>true,
                    'image'=>'http://img.alicdn.com/bao/uploaded/i2/TB1XwrQSXXXXXb_apXXntC69XXX_035351.jpg',
                    'title'=>'标题',
                    'description'=>'描述',
                ],
                [
                    'category_id'=>1,
                    'is_custom_category'=>false,
                    'image'=>'http://img.alicdn.com/bao/uploaded/i2/TB1XwrQSXXXXXb_apXXntC69XXX_035351.jpg',
                    'title'=>'标题',
                    'description'=>'描述',
                ],
                [
                    'category_id'=>1,
                    'is_custom_category'=>false,
                    'image'=>'http://img.alicdn.com/bao/uploaded/i2/TB1XwrQSXXXXXb_apXXntC69XXX_035351.jpg',
                    'title'=>'标题',
                    'description'=>'描述',
                ],
                [
                    'category_id'=>1,
                    'is_custom_category'=>false,
                    'image'=>'http://img.alicdn.com/bao/uploaded/i2/TB1XwrQSXXXXXb_apXXntC69XXX_035351.jpg',
                    'title'=>'标题',
                    'description'=>'描述',
                ],
                [
                    'category_id'=>1,
                    'is_custom_category'=>false,
                    'image'=>'http://img.alicdn.com/bao/uploaded/i2/TB1XwrQSXXXXXb_apXXntC69XXX_035351.jpg',
                    'title'=>'标题',
                    'description'=>'描述',
                ]

            ]
        ];
    }

    protected function login(){
        return [
            'open_id'=>1234567890
        ];
    }
}
