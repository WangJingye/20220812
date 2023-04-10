<?php namespace App\Http\Controllers\Api;

use App\Repositories\AddressRepository;
use Illuminate\Http\Request;
use App\Http\Requests\{DelRequest};
use App\Exceptions\ApiPlaintextException;

class AddressController extends ApiController
{
    /**
     * @var \App\Services\Api\AddressServices
     */
    public $addressServices;
    public function __construct(){
        $this->addressServices = new \App\Services\Api\AddressServices;
    }

    /**
     * 保存地址(任何修改都会删除该用户的所有配送地址的缓存)
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function saveAddress(Request $request){
        $uid = $this->getUid();
        $addressData = array_filter([
            'id'=>$request->get('id'),
            'parent_id'=>$uid,
            'firstname'=>$request->get('username'),
            'lastname'=>$request->get('username'),
            'sex'=>$request->get('sex'),
            'region'=>$request->get('province'),
            'postcode'=>$request->get('postcode'),
            'city'=>$request->get('city'),
            'area'=>$request->get('county'),
            'street'=>$request->get('detail'),
            'telephone'=>$request->get('mobile'),
            'is_default'=>$request->get('isDefault')?1:0,
        ]);
        $api = app('ApiRequestMagento');
        $resp = $api->exec(['url'=>'V1/connext/addresses','method'=>'POST'],compact('addressData'));
        $resp = json_decode($resp);
        if($resp->code == 1){
            //删除Redis缓存
            AddressRepository::delAddressListCache($uid);
            return $this->success(['id'=>$resp->id]);
        }elseif($resp->code == 0){
            throw new \Exception($resp->message);
        }throw new ApiPlaintextException('地址保存失败');
    }

    /**
     * 删除地址
     * @param DelRequest $request
     * @return array
     * @throws \Exception
     */
    public function delAddress(DelRequest $request){
        $uid = $this->getUid();
        $address_id = $request->get('id');
        $api = app('ApiRequestMagento');
        $resp = $api->exec(['url'=>"V1/connext/addresses/{$address_id}",'method'=>'DELETE']);
        $resp = json_decode($resp);
        if($resp->code == 0){
            throw new \Exception($resp->message);
        }
        //删除Redis缓存
        AddressRepository::delAddressListCache($uid);
        return $this->success();
    }

    /**
     * 获取地址簿
     * @return mixed
     * @throws \Exception
     */
    public function addressList(){
        $uid = $this->getUid();
        $list = $this->addressServices->getAddressListByUid($uid);
        return $this->success(compact('list'));
    }

    /**
     * 设置默认地址
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function setDefAddress(Request $request){
        $uid = $this->getUid();
        $address_id = $request->get('id');
        if($address_id){
            $api = app('ApiRequestMagento');
            $resp = $api->exec(['url'=>"V1/connext/addresses/{$address_id}/default",'method'=>'PUT']);
            $resp = json_decode($resp);
            if($resp->code == 0){
                throw new \Exception($resp->message);
            }
            //删除Redis缓存
            AddressRepository::delAddressListCache($uid);
        }return $this->success();
    }

}
