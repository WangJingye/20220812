<?php

namespace App\Service\Goods;

use App\Model\Goods\StockLog;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProductQueued as Queued;
use Illuminate\Contracts\Bus\Dispatcher;
use App\Model\Dlc\DlcOmsSyncLog;
use App\Model\Goods\Sku;

class StockService
{

    //库存前缀
    static $stock_prefix = 'dlc_stock_keys';


    public static function getRedis()
    {
        return Redis::connection('goods');
    }


    /**
     * 后台批量查询sku
     * @param $sku_array
     * @return array
     */
    public static function getStockAll($sku_array)
    {
        $redis = self::getRedis();
        $key = self::$stock_prefix;
        $stock_array = [];
        foreach ($sku_array as $v) {
            $stock_key = $key . $v;
            $stock_info = $redis->hGetAll($stock_key);
            $stock_array[$v] = $stock_info;
        }
        return [true, 'success', $stock_array];
    }

    /**
     * 批量获取库存信息
     * @param array $sku_array [109100,]
     * @param int $channel_id
     * @return array
     */
    public static function getBatchStock($sku_array, $channel_id = 0)
    {

        $redis = self::getRedis();
        $stock_key = self::$stock_prefix;
        $stock_array = [];
        foreach ($sku_array as $v) {
            $stock_key = $stock_key . $v;
            $stockinfo = $redis->hGetAll($stock_key);
            if ($stockinfo) {
                $stock['stock'] = $stockinfo['stock'];
                if ($channel_id == 0) {
                    unset($stockinfo['is_share'], $stockinfo['is_secure'], $stockinfo['secureinc'], $stockinfo['secure'], $stockinfo['info']);
                    $stock = $stockinfo;
                } else {
                    if ($stockinfo['is_share'] == 0) {

                        $stock['stock'] = $stockinfo['channel' . $channel_id];
                    }
                }
            } else {
                $stock = ['stock' => 0];
            }

            $stock_array[$v] = $stock;
        }
//        $stock_array = ['stock'=>10,1=>90];
        return [true, 'success', $stock_array];


    }

    /**
     * 获取单个库存
     * @param $sku
     * @param int $channel_id 如果渠道0 返回数组 即所有渠道库存信息
     * @return array
     */
    public static function getStockOne($sku, $channel_id = 0)
    {
        $redis = self::getRedis();
        $stock_key = self::$stock_prefix . $sku;

        $stockinfo = $redis->hGetAll($stock_key);
        if (!$stockinfo) {
            $stock['stock'] = 0;
            return [true, 'success', $stock];
        }
        $stock['stock'] = $stockinfo['stock'];
        if ($channel_id == 0) {
            unset($stockinfo['is_share'], $stockinfo['is_secure'], $stockinfo['secureinc'], $stockinfo['secure'], $stockinfo['info']);
            $stock = $stockinfo;
        } else {
            if ($stockinfo['is_share'] == 0) {
                $stock['stock'] = $stockinfo['channel' . $channel_id];
            }
        }


        return [true, 'success', $stock];

    }


    /**
     * 批量处理 库存
     * @param $update_arr array([10000,2,'orderno123'],[100001,12,'orderno123']) skuid num orderno
     * @param $increment 增 或减 1增 0减
     * @param $channel_id
     * @return array
     */
    public static function batchUpdateStock($update_arr, $increment, $channel_id,$no_lock=0)
    {
        try {
            if (empty($update_arr)) {
                return [false, '失败', []];
            }
            $success_array = [];
            $fail_array = [];
            foreach ($update_arr as $value) {
                list($success,) = self::updateStock($value[0], $channel_id, $value[1], $increment,$no_lock);
                if ($success) {
                    $item = [
                        'sku' => $value[0],
                        'num' => $value[1],
                        'order_sn' => $value[2]??'',

                    ];
                    $success_array[] = $item;
                } else {
                    $item = [
                        'sku' => $value[0],
                        'num' => $value[1],
                        'order_sn' => $value[2]??'',

                    ];
                    $fail_array[] = $item;
                }

            }
            if (!empty($fail_array)) {
                if ($increment == 1) {
                    $incr = 0;
                } else {
                    $incr = 1;
                }
                foreach ($success_array as $value) {
                    self::updateStock($value['sku'], $channel_id, $value['num'], $incr,$no_lock);
                }
                return [false, '', ['fail_array' => $fail_array, 'success_array' => $success_array]];

            }
            //添加记录
            foreach ($update_arr as $value) {
                // 1入库 2下单扣除 3 订单失效返还 4 残次品
                $type = $increment == 1 ? 3 : 2;
                self::addStockLog($value[0], $value[1], $type, $channel_id, '', $value[2]);
            }
            return [true, '', []];
        } catch (\Exception $e) {
            Log::error('stock_service:stock_update_fail:' . $increment . '|' . $channel_id . 'error:' . $e->getMessage() . 'parm:', $update_arr);
            return [false, '失败', []];
        }

    }

    public static function batchUpdateStockForce($update_arr, $increment, $channel_id,$note='')
    {
        try {
            if (empty($update_arr)) {
                return [false, '失败', []];
            }
            $success_array = [];
            $fail_array = [];
            foreach ($update_arr as $value) {
                list($success,) = self::updateStockForce($value[0], $channel_id, $value[1], $increment);
                if ($success) {
                    $item = [
                        'sku' => $value[0],
                        'num' => $value[1],
                        'order_sn' => $value[2]??'',

                    ];
                    $success_array[] = $item;
                } else {
                    $item = [
                        'sku' => $value[0],
                        'num' => $value[1],
                        'order_sn' => $value[2]??'',

                    ];
                    $fail_array[] = $item;
                }

            }
            if (!empty($fail_array)) {
                if ($increment == 1) {
                    $incr = 0;
                } else {
                    $incr = 1;
                }
                foreach ($success_array as $value) {
                    self::updateStockForce($value['sku'], $channel_id, $value['num'], $incr);
                }
                return [false, '', ['fail_array' => $fail_array, 'success_array' => $success_array]];

            }
            //添加记录
            foreach ($update_arr as $value) {
                // 1 后台增加 2 后台减少
                $type = ($increment>0) ? 6 : 7;
                self::addStockLog($value[0], $value[1], $type, $channel_id, '', $value[2],$note);
            }
            return [true, '', []];
        } catch (\Exception $e) {
            Log::error('stock_service:stock_update_fail:' . $increment . '|' . $channel_id . 'error:' . $e->getMessage() . 'parm:', $update_arr);
            return [false, '失败', []];
        }

    }

    /**
     * 初始化redis库存结构
     * @param $skuid
     * @param $channel
     */
    public static function initStock($stock_key)
    {
        $redis = self::getRedis();

        if (!$redis->exists($stock_key)) {
            $channel = config('channel');
            $redis->hmset($stock_key, $channel['config']);
            $redis->hset($stock_key, 'info', json_encode($channel['channel']));
            foreach ($channel['channel'] as $v) {
                $redis->hIncrBy($stock_key, 'channel' . $v['id'], 0);//渠道库存
                $redis->hIncrBy($stock_key, 'lock_channel' . $v['id'], 0);//锁定渠道库存
            }
        }

    }

    /***
     * 批量解除库存锁定
     * @param $sku_array
     * @param int $channel_id
     * @return bool
     */
    public static function batchUnlockSku($sku_array, $channel_id = 0)
    {
        $redis = self::getRedis();
        foreach ($sku_array as $v) {
            $stock_key = self::$stock_prefix . $v[0];
            $redis->hIncrBy($stock_key, 'lock_channel' . $channel_id, -$v[1]);
        }
        return true;
    }

    /**
     * 订单支付后 解除库存锁定
     * @param $skuid
     */
    public static function unlockSku($skuid, $channel_id, $num)
    {
        $stock_key = self::$stock_prefix . $skuid;
        $redis = self::getRedis();
        return $redis->hIncrBy($stock_key, 'lock_channel' . $channel_id, -$num);
    }

    /**
     * 获取某个sku的库存所有信息
     * @param $skuid
     * @return array
     */
    public static function getSkuStock($skuid)
    {

        $stock_key = self::$stock_prefix . $skuid;
        $redis = self::getRedis();
        $stock = $redis->hGetAll($stock_key);
        if (empty($stock)) {
            self::initStock($stock_key);
            $stock = $redis->hGetAll($stock_key);

        }
        $stock['info'] = json_decode($stock['info'] ?? '', true);
        return $stock;

    }

    /**
     *
     * 修改库存 自增或自减
     * @param $skuid
     * @param $channel_id 渠道ID
     * @param $num  数量
     * @param $increment  增 或减 1增 0减
     * @param $no_lock (无需操作lock字段)
     * @return array
     */
    public static function updateStock($skuid, $channel_id, $num, $increment,$no_lock=0)
    {
        $increment = $increment >= 1 ? 1 : 0;
        $stock_key = self::$stock_prefix . $skuid;
        $redis = self::getRedis();
        $skuInfo = $redis->hGetAll($stock_key);
        if (!$skuInfo) {
            return [false, '数据异常'];
        }
        $stock = 'stock';
        if ($skuInfo['is_share'] == 0) {
            $stock = 'channel' . $channel_id;
        }

        if($no_lock==1){
            $lua = <<<LUA
                local stock_key = KEYS[1]
                local stock = KEYS[2]
                local num = tonumber(KEYS[3])
                local increment = tonumber(KEYS[4])
                local channel = tonumber(KEYS[5])
                if increment > 0 then
                    redis.call('HINCRBY', stock_key, stock, num)
                    return 1
                else
                    local nowtotal = redis.call('HGET', stock_key, stock)
                    if nowtotal then
                        nowtotal = tonumber(nowtotal)
                        if nowtotal >= num then
                            redis.call('HINCRBY', stock_key, stock, "-"..num)
                            return 1
                        else
                            return -1
                        end
                    else
                        return -1;
                    end
                end

LUA;
        }else{

            $lua = <<<LUA
                local stock_key = KEYS[1]
                local stock = KEYS[2]
                local num = tonumber(KEYS[3])
                local increment = tonumber(KEYS[4])
                local channel = tonumber(KEYS[5])
                if increment > 0 then
                    redis.call('HINCRBY', stock_key, stock, num)
                    redis.call('HINCRBY', stock_key, "lock_channel"..channel, "-"..num)
                    return 1
                else
                    local nowtotal = redis.call('HGET', stock_key, stock)
                    if nowtotal then
                        nowtotal = tonumber(nowtotal)
                        if nowtotal >= num then
                            redis.call('HINCRBY', stock_key, stock, "-"..num)
                            redis.call('HINCRBY', stock_key, "lock_channel"..channel, num)
                            return 1
                        else
                            return -1
                        end
                    else
                        return -1;
                    end
                end

LUA;
        }

        $result = $redis->eval($lua, 5, $stock_key, $stock, $num, $increment, $channel_id);
        if ($result == 1) {
            return [true, 'success'];
        } else {
            return [false, '数据,请重试'];
        }
    }

    public static function updateStockForce($skuid, $channel_id, $num, $increment)
    {
        $increment = $increment >= 1 ? 1 : 0;
        $stock_key = self::$stock_prefix . $skuid;
        $redis = self::getRedis();
        $skuInfo = $redis->hGetAll($stock_key);
        if (!$skuInfo) {
            return [false, '数据异常'];
        }
        $stock = 'stock';
        if ($skuInfo['is_share'] == 0) {
            $stock = 'channel' . $channel_id;
        }
        $num = abs($num);
        $lua = <<<LUA
                local stock_key = KEYS[1]
                local stock = KEYS[2]
                local num = tonumber(KEYS[3])
                local increment = tonumber(KEYS[4])
                local channel = tonumber(KEYS[5])
                if increment > 0 then
                    redis.call('HINCRBY', stock_key, stock, num)
                    return 1
                else
                    local nowtotal = redis.call('HGET', stock_key, stock)
                    if nowtotal then
                        nowtotal = tonumber(nowtotal)
                        if nowtotal >= num then
                            redis.call('HINCRBY', stock_key, stock, "-"..num)
                            return 1
                        else
                            return -1
                        end
                    else
                        return -1;
                    end
                end

LUA;
        $result = $redis->eval($lua, 5, $stock_key, $stock, $num, $increment, $channel_id);
        if ($result == 1) {
            return [true, 'success'];
        } else {
            return [false, '数据,请重试'];
        }
    }

    /**
     * 将共享库存分配给渠道库存
     * @param $skuid
     * @return bool
     */
    public static function setNotShare($skuid, $channel, $channel_stock)
    {
        $stock_key = self::$stock_prefix . $skuid;
        $redis = self::getRedis();
        $stockinfo = $redis->hgetAll($stock_key);
        $stockinfo['info'] = json_decode($stockinfo['info'], true);
        $total = $stockinfo['stock'];

        if ($stockinfo['is_share'] == 1) {

            $stockinfo['is_share'] = 0;
            if ($stockinfo['stock'] > 0) {
                $stockinfo['info'] = $channel;
                $status = self::dealAssign($redis, $stock_key, $stockinfo, $stockinfo['stock']);
                if ($status) {
                    $redis->hIncrBy($stock_key, 'stock', -$total);
                } else {
                    return [false, '出现异常，请重试'];
                }

            }
            $redis->hset($stock_key, 'info', json_encode($channel));
            $redis->hset($stock_key, 'is_share', 0);

        } else {

            if (!empty($channel_stock)) {
                //新分配值的总和
                $new_total = array_sum($channel_stock);

                //原本分配库存的总和
                $old_total = 0;

                foreach ($channel_stock as $k => $v) {
                    $old_val = $stockinfo[$k];
                    $old_total = $old_val + $old_total;
                }

                //确保分配前和分配后总和相等
                if ($new_total != $new_total) {
                    return [false, '分配数据发生异常，请重试'];
                }
                foreach ($channel_stock as $k => $v) {
                    $redis->hset($stock_key, $k, $v);

                }
            }

            $redis->hset($stock_key, 'info', json_encode($channel));

        }

            return [true, 'success'];
    }


    /**
     * 设置安全库存
     * @param $skuid
     * @param $num 数量
     * @param $type 1增 2减
     * @return array
     */
    public static function setSecure($skuid, $num)
    {

        $stock_key = self::$stock_prefix . $skuid;
        $redis = self::getRedis();


        $stockinfo = $redis->hgetAll($stock_key);

        $stockinfo['info'] = json_decode($stockinfo['info'], true);
        if ($stockinfo['is_secure'] == 1 && $stockinfo['secure'] == $num) {
            return [false, '没有数据更改，不用修改'];
        }

        //如果当前安全库存减小
        if ($stockinfo['is_share']!=1) {
            return [false, '该库存不是共享类型，不可修改安全库存'];
        }
        $num = $num-$stockinfo['secure'];

        if ($num>0) {
            $inc = $stockinfo['stock'] - $num;
            if($inc <= 0){
                return [false, '共享库存不足，不可修改'];
            }
            $redis->hIncrBy($stock_key, 'secure', $num);
            $redis->hIncrBy($stock_key, 'stock', -$num);
        }else{

            $total = -$num;
            $status = self::dealAssign($redis, $stock_key, $stockinfo, $total);
            if($status){
                $redis->hIncrBy($stock_key, 'secure', $num);
            }else{
                return false;
            }
        }

        $redis->hset($stock_key, 'is_secure', 1);
        return [true, 'success'];
    }

    /**
     * 设置库存
     * @param $skuid
     * @param $num 数量
     * @param $type 1增 2减
     * @return array
     */
    public static function setStockIncre($skuid, $num)
    {
        $stock_key = self::$stock_prefix . $skuid;
        $redis = self::getRedis();

        $stockinfo = $redis->hgetAll($stock_key);
        $stockinfo['info'] = json_decode($stockinfo['info'], true);
        if ($stockinfo['is_share'] == 1 && $stockinfo['stock'] == $num) {

            return [true, '没有数据更改，不用修改'];
        }

        if($stockinfo['stock']<0){
            $inc = $num - abs($stockinfo['stock']);
        }else{
            $inc = $num - $stockinfo['stock'];
        }


        if ($inc > 0) {
            $redis->hIncrBy($stock_key, 'stock', $inc);
            $redis->hIncrBy($stock_key, 'stockinc', $inc);
            self::addStockLog($skuid, $inc, 5, 0, '', '', '预设库存');
            return [true, 'success'];
        } else {
            return [false, '出现异常，请重试'];
        }


    }

    /**
     * 处理分配库存
     * @param $redis
     * @param $stock_key
     * @param $stockinfo
     * @param $total
     * @return bool
     */
    public static function dealAssign($redis, $stock_key, $stockinfo, $total)
    {

        if (1 == $stockinfo['is_share']) {
            $redis->hIncrBy($stock_key, 'stock', $total);

        } else {
            $chanenl = $stockinfo['info'];
            $percent_num = array_column($chanenl, 'percent');

            $sum = array_sum($percent_num);
            if (10 != $sum) {
                return false;
            }
            $channel_arr = array_column($chanenl, 'id');

            $percent_arr = self::percent($percent_num, $total);//比例数组

            foreach ($channel_arr as $k => $v) {
                if (0 != $percent_arr[$k]) {

                    $redis->hIncrBy($stock_key, 'channel' . $v, $percent_arr[$k]);
                }
            }

        }
        return true;
    }

    /**
     * 不设置安全库存
     * @param $skuid
     * @param $num 数量
     * @param $type 1增 2减
     * @return array
     */
    public static function setNotSecure($skuid)
    {
        $stock_key = self::$stock_prefix . $skuid;
        $redis = self::getRedis();

        $stockinfo = $redis->hgetAll($stock_key);
        $stockinfo['info'] = json_decode($stockinfo['info'], true);
        if ($stockinfo['is_secure'] == 0) {

            return [true, '当前状态是无安全库存，不用修改'];
        }
        if ($stockinfo['secure'] > 0) {
            $total = $stockinfo['secure'];
        } else {

            return [true, '当前状态是无安全库存，不用修改'];
        }


        if ($total > 0) {
            $status = self::dealAssign($redis, $stock_key, $stockinfo, $total);
            if (!$status) {

                return [false, '出现异常，请重试'];
            }
        }
        $redis->hIncrBy($stock_key, 'secure', -$total);
        $redis->hset($stock_key, 'is_secure', 0);
        return [true, 'success'];


    }

    public static function handelSecure($redis, $skuid, $stocinfo, $total, $batch_no)
    {
        $stock_key = self::$stock_prefix . $skuid;
        if (isset($stocinfo['stockinc']) && $stocinfo['stockinc'] > 0) {
            $stockinc = $stocinfo['stockinc'];
            $total = $total - $stockinc;
            if ($total <= 0) {
                $redis->hIncrBy($stock_key, 'stockinc', -$stockinc);
                self::addStockLog($skuid, $total, 1, 0, $batch_no, '', '分配安全库存');
                return true;

            } else {

                $redis->hIncrBy($stock_key, 'stockinc', -$stockinc);


            }
        }
        $secureinc = $stocinfo['secureinc'];
        $total = $total - $secureinc;
        //如果数量只够安全库存的增量，则只用处理增量，不用分配
        if ($stocinfo['secureinc'] > 0) {
            if (0 >= $total) {
                $redis->hIncrBy($stock_key, 'secureinc', -$secureinc);
                self::addStockLog($skuid, $total, 1, 0, $batch_no, '', '分配安全库存');
                return true;

            } else {

                $redis->hIncrBy($stock_key, 'secureinc', -$secureinc);


            }
        }

        //共享
        if (1 == $stocinfo['is_share']) {
            $redis->hIncrBy($stock_key, 'stock', $total);
            self::addStockLog($skuid, $total, 1, 0, $batch_no, '', '分配安全库存');
            return true;
        }
        //按渠道分配
        if (0 == $stocinfo['is_share']) {
            $chanenl = json_decode($stocinfo['info'], true);
            $percent_num = array_column($chanenl, 'percent');

            $sum = array_sum($percent_num);
            if (10 != $sum) {
                return false;
            }
            $channel_arr = array_column($chanenl, 'id');
//            Log::info('StockService' . $total . json_encode($percent_num));
            $percent_arr = self::percent($percent_num, $total);//比例数组
//            Log::info('StockService' . json_encode($percent_arr));
            foreach ($channel_arr as $k => $v) {
                if (0 != $percent_arr[$k]) {
//                    echo "$stock_key, 'channel:' . $v, $percent_arr[$k]";
                    $redis->hIncrBy($stock_key, 'channel' . $v, $percent_arr[$k]);
                }
            }
            //记录入库
            self::addStockLog($skuid, $total, 1, 0, $batch_no, '', '分配安全库存');
            return true;
        }
    }

    /**
     * 渠道库存汇总到共享库存
     * @param $skuid
     * @return bool
     */
    public static function setShare($skuid)
    {
        $stock_key = self::$stock_prefix . $skuid;
        $redis = self::getRedis();
        $stockinfo = $redis->hgetAll($stock_key);
        if ($stockinfo['is_share'] == 1) {
            return [false, '已是共享，不用修改'];
        }

        $stockinfo['info'] = json_decode($stockinfo['info'], true);
        $total = 0;

        foreach ($stockinfo['info'] as $v) {
            $channel_num = $stockinfo['channel' . $v['id']];
            $total = $total + $channel_num;
            $redis->hIncrBy($stock_key, 'channel' . $v['id'], -$channel_num);
        }
        $redis->hIncrBy($stock_key, 'stock', $total);
        $redis->hset($stock_key, 'is_share', 1);
        return [true, 'success'];

    }


    /**
     * 分配库存 --脚本
     * @param $skuid
     * @param total 可分配的库存
     */
    public static function assignStocks(string $skuid, int $total, string $batch_no = ''): array
    {
        Log::info($skuid . 'num' . $total);
        if (0 >= $total) {
            return [false, '库存暂时没有，不能分配'];
        }
        $actual_total = $total;
        $redis = Redis::connection('goods');

        $stock_key = self::$stock_prefix . $skuid;

        $stocinfo = $redis->hgetAll($stock_key);

        if (!$stocinfo) {
            self::initStock($stock_key);
            $stocinfo = $redis->hgetAll($stock_key);
        }

        if (isset($stocinfo['stockinc']) && $stocinfo['stockinc'] > 0) {
            $stockinc = $stocinfo['stockinc'];
            $total = $total - $stockinc;
            if ($total <= 0) {
                $redis->hIncrBy($stock_key, 'stockinc', -$stockinc);
                self::addStockLog($skuid, $actual_total, 1, 0, $batch_no, '', '111');
                return [true, '设置成功'];

            } else {
                $redis->hIncrBy($stock_key, 'stockinc', -$stockinc);
            }
        }
        $secureinc = $stocinfo['secureinc'];
        $total = $total - $secureinc;
        //如果数量只够安全库存的增量，则只用处理增量，不用分配
        if ($stocinfo['secureinc'] > 0) {
            if (0 >= $total) {
                $redis->hIncrBy($stock_key, 'secureinc', -$secureinc);
                self::addStockLog($skuid, $actual_total, 1, 0, $batch_no);
                return [true, '设置成功，下次同步时执行'];

            } else {

                $redis->hIncrBy($stock_key, 'secureinc', -$secureinc);


            }
        }

        //共享
        if (1 == $stocinfo['is_share']) {
            $redis->hIncrBy($stock_key, 'stock', $total);
            self::addStockLog($skuid, $actual_total, 1, 0, $batch_no);
            return [true, '设置成功'];
        }
        //按渠道分配
        if (0 == $stocinfo['is_share']) {
            $chanenl = json_decode($stocinfo['info'], true);
            $percent_num = array_column($chanenl, 'percent');

            $sum = array_sum($percent_num);
            if (10 != $sum) {
                return [false, '数据异常，分配比例有误'];
            }
            $channel_arr = array_column($chanenl, 'id');
//            Log::info('StockService' . $total . json_encode($percent_num));
            $percent_arr = self::percent($percent_num, $total);//比例数组
//            Log::info('StockService' . json_encode($percent_arr));
            foreach ($channel_arr as $k => $v) {
                if (0 != $percent_arr[$k]) {
//                    echo "$stock_key, 'channel:' . $v, $percent_arr[$k]";
                    $redis->hIncrBy($stock_key, 'channel' . $v, $percent_arr[$k]);
                }
            }
            //记录入库
            self::addStockLog($skuid, $actual_total, 1, 0, $batch_no);
            return [true, 'success'];
        }


    }


    /**
     * 根据百分比分配数据 多余或者不够分的都给比例最大的
     * @param $percent_arr
     * @param $total_number
     * @return mixed
     */
    public static function percent($percent_arr, $total_number)
    {

        $total = array_sum($percent_arr);
        $key = array_search(max($percent_arr), $percent_arr);
        if ($total > $total_number) {

            foreach ($percent_arr as &$v) {
                $v = 0;
            }
            $percent_arr[$key] = $total_number;
            return $percent_arr;
        }
        foreach ($percent_arr as &$value) {
            $value = round($value / 10 * $total_number, 0);

        }
        if ($d = ($total_number - array_sum($percent_arr))) $percent_arr[$key] += $d;
        return $percent_arr;
    }


    /**
     * 库存记录
     * @param $skuid
     * @param $num
     * @param $type 1入库2下单扣除 3 订单失效返还 4 残次品
     * @param string $batch_no 批次
     * @param $order_no  子订单号 订单库存返还时记录
     * @param $ori_num (全量更新时的原始数量)
     * @param string $note 备注
     */
    public static function addStockLog($skuid, $num, $type, $channel_id = 0, $batch_no = '', $order_no = '', $note = '',$ori_num=0)
    {
        // 1入库2下单扣除 3 订单失效返还 4 残次品
        $types_message = [
            1 => '库存入库',
            2 => '用户下单',
            3 => '订单失效返还',
            4 => '残次品',
            5 => '预设库存',
            6 => '后台手动增加',
            7 => '后台手动减少',
        ];
        if (empty($note)) {
            $note = $types_message[$type];
        }
        $date = date('Y-m-d H:i:s');
        $inser_array = [
            'sku_id' => $skuid,
            'num' => $num,
            'channel_id' => $channel_id,
            'type' => $type,
            'batch_no' => $batch_no,
            'order_no' => $order_no,
            'note' => $note,
            'created_at' => $date,
            'updated_at' => $date,
            'ori_num'=>$ori_num,
        ];
        StockLog::insert($inser_array);
    }

    /**
     * 全量更新sku库存(用于OMS全量同步库存)
     * @param $skus
     * @param $restore_skus (本来无库存 同步后有库存的skus)
     * @param $force (强制更新 无论是否点了手动)
     * @return bool
     */
    public static function updateStockFull($skus,&$restore_skus,$force=0)
    {
        try{
            $restore_skus = [];
            if($skus){
                $id = DlcOmsSyncLog::query()->insertGetId([
                    'content'=>json_encode($skus),
                    'type'=>DlcOmsSyncLog::TYPE['stock'],
                ]);
                //过滤掉禁止全量更新的sku
                $sku_keys = array_keys($skus);
                $model = Sku::query()->whereIn('sku_id',$sku_keys);
                if(!$force){
                    $model->where('control_stock',0);
                }
                $allow_skus = $model->pluck('sku_id')->toArray();
                $redis = Redis::connection('goods');
                foreach($skus as $sku=>$stock){
                    if(!in_array($sku,$allow_skus)){//过滤掉禁止全量更新的sku
                        continue;
                    }
                    $stock_key = self::$stock_prefix.$sku;
                    $stocinfo = $redis->hgetAll($stock_key);
                    if (!$stocinfo) {
                        self::initStock($stock_key);
                        $stocinfo = $redis->hgetAll($stock_key);
                    }
                    $lock = intval($stocinfo['lock_channel1']);
                    $secure = $stocinfo['is_secure']?intval($stocinfo['secure']):0;
                    $new_stock = $stock-$lock-$secure;
                    $redis->hset($stock_key,'stock',$new_stock);
                    self::addStockLog($sku, $new_stock, 1, 0, '', '', '全量更新',$stock);
//                    $old_stock = $stocinfo['stock'];
                    if($new_stock>0){
                        //如果库存当前有库存 则返回这些sku
                        $restore_skus[] = $sku;
                    }
                }
                DlcOmsSyncLog::query()->find($id)->update(['status'=>1]);
            }
            return true;
        }catch (\Exception $e){
            return $e->getMessage().',file:'.$e->getFile().',line:'.$e->getLine();
        }
    }
}
