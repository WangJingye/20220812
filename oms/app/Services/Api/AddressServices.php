<?php namespace App\Services\Api;

use App\Repositories\AddressRepository;

class AddressServices
{
    /**
     * 根据用户ID获取地址簿
     * @param $uid
     * @return array|mixed
     */
    public function getAddressListByUid($uid){
        //从缓存中获取
        $list = AddressRepository::getAddressListCache($uid);
        if(empty($list)){
            //缓存中没有则查DB
            $list = AddressRepository::getAddressList($uid);
            $list = array_reduce($list,function($result,$item){
                $isDefault = ($item->default_shipping_id==$item->entity_id)?1:0;
                $line = [
                    'id'=>$item->entity_id,
                    'username'=>$item->firstname,
                    'mobile'=>$item->telephone,
                    'sex'=>$item->sex,
                    'province'=>$item->region,
                    'city'=>$item->city,
                    'county'=>$item->area,
                    'detail'=>$item->street,
                    'postcode'=>$item->postcode,
                    'isDefault'=>$isDefault,
                ];
                if($isDefault){
                    //默认地址放第一位
                    array_unshift($result,$line);
                }else{
                    array_push($result,$line);
                }
                return $result;
            },[]);
            if($list){
                //序列化并保存到Redis
                $listCache = array_reduce($list,function($result,$item){
                    array_unshift($result,serialize($item));
                    return $result;
                },[]);
                AddressRepository::setAddressCache($uid,$listCache);
            }
        }else{
            //缓存中有数据则将缓存数据反序列化
            $list = array_reduce($list,function($result,$item){
                $result[] = unserialize($item);
                return $result;
            });
        }
        return $list;
    }
}
