<?php

namespace Tests\Feature;

use App\Service\Dlc\UsersService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Support\Token;

class UserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
//        $open_id = 'oZAUq48Qu5zB0pZR5bUX_iaGdQiM1';
//        $phone = '15021165981';
//        \App\Service\Dlc\UsersService::userRegister($open_id,$phone);
//        \App\Service\Dlc\UsersService::getUserInfo(1058230);
//        print_r($this->signIn());
//        print_r($this->wxPhoneLogin());
        app('ApiRequestInner')->request('orderPosIdUpdate','POST',[
            'Uid'=>'1058288',
            'MemberCode'=>'11223344',
        ]);
        $this->assertTrue(true);
    }

    private function signIn(){
            $open_id = 'omBzf4qwFLjOOl7tYsXuis_0-uok';
            $avatar_url = 'https://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJxoV3C6H94w0MYgZEYhPsCVdUbKN96hsQT4Pic9ETuScXNU45Bibu6whD23gt8otA0thECF3YrD3Bw/132';
            $uid = UsersService::userUpdateOrInsertByOpenId(compact('open_id'),compact('open_id','avatar_url'));
            $token = Token::createTokenByOpenId($uid,$open_id);
            return compact('open_id','token');
    }

    private function wxPhoneLogin(){
        $open_id = 'test001openid';
        $phone = '13122515416';
        return UsersService::userRegister($open_id,$phone);
    }
}
