<?php namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class AddressRepository
{
    /**
     * 根据用户ID获取地址列表
     * @param $uid
     * @return array
     */
    public static function getAddressList($uid){
        $sql = "select address.entity_id,address.firstname,address.telephone,address.sex,address.region,address.city,address.area,address.street,address.postcode,customer.default_shipping as default_shipping_id
from customer_address_entity as address
LEFT JOIN customer_entity as customer on address.parent_id=customer.entity_id
where customer.entity_id = {$uid}";
        return DB::select($sql);
    }

    /**
     * 根据ID查询地址
     * @param $address_id
     * @param $uid
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|object|null
     */
    public static function getAddress($address_id,$uid){
        return DB::table('customer_address_entity')
            ->where('entity_id',$address_id)
            ->where('parent_id',$uid)
            ->first();
    }

    const ADDRESS = '_AddressList';

    /**
     * 保存用户配送地址
     * @param $uid
     * @param $addressList
     * @return mixed
     */
    public static function setAddressCache($uid,$addressList){
        $key = env('APP_NAME').self::ADDRESS.':'.$uid;
        $result = Redis::HMSET($key,$addressList);
        //有效期为7天
        $expireTime = strtotime(date("Y-m-d H:i:s"))+3600*24*7;
        Redis::EXPIREAT($key, $expireTime);
        return $result;
    }

    /**
     * 获取用户配送地址
     * @param $uid
     * @return mixed
     */
    public static function getAddressListCache($uid){
        $key = env('APP_NAME').self::ADDRESS.':'.$uid;
        return Redis::HGETALL($key);
    }

    /**
     * 删除用户配送地址
     * @param $uid
     * @return mixed
     */
    public static function delAddressListCache($uid){
        $key = env('APP_NAME').self::ADDRESS.':'.$uid;
        return Redis::DEL($key);
    }

}
