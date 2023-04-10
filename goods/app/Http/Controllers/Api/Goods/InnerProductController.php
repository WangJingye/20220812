<?php

namespace App\Http\Controllers\Api\Goods;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use App\Model\Goods\Spu;
use App\Model\Goods\ProductHelp;
use App\Model\Goods\Tree;
use App\Model\Goods\Category;
use App\Model\Goods\VirtualCategory;

class InnerProductController extends Controller
{
    protected $_model = Spu::class;

    /**
     * 获取产品列表.
     */
    public function getProdInfoByIds(Request $request)
    {
        $prodIdStr = $request->prodIdStr;
        $prodIds = json_decode($prodIdStr, true);
        $prodIds = array_unique($prodIds);

        $ProductHelp = new ProductHelp();

        $enProdsList = $ProductHelp->redisModel->_hmget(config('redis.prodList'), $prodIds);

        if (!$enProdsList) {
            return $this->error(2006, 'goods.product.get_product.not_found');
        }

        $list = [];
        foreach ($enProdsList as $prodId => $enProdInfo) {
            if (false !== $enProdInfo) {
                $deProdInfo = json_decode($enProdInfo, true);
                $list[] = $deProdInfo['pdt'];
            }
        }

        if (empty($list)) {
            return $this->error(2006, 'goods.product.get_product.not_found');
        }

        return $this->success(['list' => $list]);
    }

    /**
     * 获取分类列表.
     */
    public function getCategoryTree(Request $request)
    {
        $cateList = Category::select('id', 'p_cate_id as pid', 'category_id as cateId', 'category_name as label')->get()->toArray();

        $Tree = new Tree();
        $tree = $Tree->getTreeData($cateList, 'level', 'label', 'id', 'pid');

        return $this->success($tree);
    }

    /**
     * 获取Sku信息.
     */
    public function getInfoBySkuIds(Request $request)
    {
        $skuIdStr = $request->skuIdStr;
        $skuIds = json_decode($skuIdStr, true);

        $ProductHelp = new ProductHelp();
        $pipe = $ProductHelp->redisModel->redis->multi(\Redis::PIPELINE);
        $pipe->hmget(config('redis.skuList'), $skuIds);
        $pipe->get(config('redis.goodsGoldenPriceInfo'));
        $redisBack = $pipe->exec();
        unset($pipe);
        if (empty($redisBack)) {
            return $this->error(0, 'server.redis.error');
        } else {
            $skusInfo = $redisBack[0] ?: [];
            $enGoldenPrice = $redisBack[1];
        }

        $deGoldenPrice = json_decode($enGoldenPrice, true);

        $list = [];
        $prodInventorys = [];
        foreach ($skusInfo as $skuId => $skuInfo) {
            if ($skuInfo === false) {
                continue;
            }
            $deSkuInfo = json_decode($skuInfo, true);
            $skuList = $deSkuInfo;
            if (!isset($prodInventorys[$deSkuInfo['spu.product_id']])) {
                $spuInventoryBack = $ProductHelp->checkSpuStock($deSkuInfo['spu.product_id']);
                $prodInventorys[$deSkuInfo['spu.product_id']] = $spuInventoryBack;
            } else {
                $spuInventoryBack = $prodInventorys[$deSkuInfo['spu.product_id']];
            }

            //如果是brandsite商品，都不给库存
            if (isset($deSkuInfo['spu.is_brandSite']) && $deSkuInfo['spu.is_brandSite'] === 1) {
                $skuList['sku.inventory'] = 0;
                $skuList['spu.inventory'] = false;
            } else {
                $skuList['sku.inventory'] = isset($spuInventoryBack['skuStocks'][$skuId]) && false !== $spuInventoryBack['skuStocks'][$skuId] ? (int)$spuInventoryBack['skuStocks'][$skuId] : 0;
                $skuList['spu.inventory'] = $spuInventoryBack['status'];
            }

            $list[] = $skuList;
            unset($skuList);
        }

        $return['list'] = $list;
        $return['goldRates'] = $deGoldenPrice['goldRates'];

        return $this->success($return);
    }

    /**
     * 统计SKU销量.
     */
    public function collectSales(Request $request)
    {
        $skuStockList = $request->skuStockList;
        $deSkuStockList = json_decode($skuStockList, true);

        $ProductHelp = new ProductHelp();
        $skuIds = array_column($deSkuStockList, 'skuId');
        $prodsInfo = $ProductHelp->redisModel->_hmget(config('redis.mappingSkuProd'), $skuIds);
        $incrArr = [];
        foreach ($deSkuStockList as $skuStockInfo) {
            $skuId = $skuStockInfo['skuId'];
            $qty = $skuStockInfo['qty'];
            if (isset($prodsInfo[$skuId]) && false !== $prodsInfo[$skuId]) {
                $prodId = $prodsInfo[$skuId];
                if (!isset($incrArr[$prodId])) {
                    $incrArr[$prodId] = $qty;
                } else {
                    $incrArr[$prodId] += $qty;
                }
            }
        }
        $prodIds = array_keys($incrArr);
        $prodsInfo = $ProductHelp->redisModel->_hmget(config('redis.prodLevelInfo'), $prodIds);

        if (!empty($incrArr)) {
            $salesTable = config('redis.prodSales');
            $salesByUsageTable = config('redis.prodSalesByUsage');
            foreach ($incrArr as $pid => $pqty) {
                $ProductHelp->redisModel->_hincrBy($salesTable, $pid, $pqty);
                if (isset($prodsInfo[$pid]) && false !== $prodsInfo[$pid]) {
                    $deProdInfo = json_decode($prodsInfo[$pid], true);
                    $usage = $deProdInfo['usage_code'];
                    $key = $salesByUsageTable . '###' . $usage;
                    $ProductHelp->redisModel->_zincrby($key, $pqty, $pid);
                }
            }
        }

        return $this->success([]);
    }

    /**
     * 校验Sku库存信息.
     */
    public function checkSkusStock(Request $request)
    {
        $skuIdStr = $request->skuIdStr;
        $skuIds = json_decode($skuIdStr, true);

        $ProductHelp = new ProductHelp();
        $stockInfo = $ProductHelp->redisModel->_hmget(config('redis.store'), $skuIds);
        $skusDisplayStatus = $ProductHelp->redisModel->_hmget(config('redis.skuDisplay'), $skuIds);

        $return = [];
        foreach ($stockInfo as $skuId => $stock) {
            $tmpReturnSku = [];
            $tmpReturnSku['sku_id'] = $skuId;
            if ('1' !== $skusDisplayStatus[$skuId]) {
                $tmpReturnSku['inventory'] = 0;
            } else {
                $tmpReturnSku['inventory'] = false !== $stock ? (int)$stock : 0;
            }
            $return[] = $tmpReturnSku;
        }

        return $this->success($return);
    }

    /**
     * 搜索产品.
     */
    public function getSearchProdsByIds(Request $request)
    {
        $prodIdStr = $request->prodIdStr;
        $prodIds = json_decode($prodIdStr, true);
        $prodIds = array_unique($prodIds);
        $sortKey = $request->sortKey ?: '';
        $sort = $request->sort ?: 1;
        $fillterCategory = $request->fillterCategory ? explode('/', $request->fillterCategory) : ['all'];
        $fillterMetal = $request->fillterMetal ? json_decode($request->fillterMetal, true) : [];
        $fillterJewel = $request->fillterJewel ? json_decode($request->fillterJewel, true) : [];
        $fillterMinPrice = null !== $request->fillterMinPrice && $request->fillterMinPrice >= 0 ? $request->fillterMinPrice : 0;
        $fillterMaxPrice = null !== $request->fillterMaxPrice && $request->fillterMaxPrice >= 0 ? $request->fillterMaxPrice : 'N/A';
        if ('N/A' !== $fillterMaxPrice && $fillterMinPrice > $fillterMaxPrice) {
            return $this->error(0, 'goods.product.get_category.price_range.error');
        }
        //分页配置
        $curPage = $request->curPage > 0 ? $request->curPage : 1;

        $pageSize = 10;
        $offset = $pageSize;
        $start = ($curPage - 1) * $pageSize;

        $ProductHelp = new ProductHelp();

        $return = [];
        $list = [];
        $displayProdInfos = [];

        $prodsInfo = $ProductHelp->redisModel->_hmget(config('redis.prodLevelInfo'), $prodIds);
        $prodsSalesInfo = $ProductHelp->redisModel->_hmget(config('redis.prodSales'), $prodIds);
        $hProdsDateInfo = $ProductHelp->redisModel->_hmget(config('redis.hProdDisplayDate'), $prodIds);

        if (!empty($prodsInfo)) {
            $prodsDisplayStatus = $ProductHelp->redisModel->_hmget(config('redis.prodDisplay'), $prodIds);

            $deProdsInfo = [];
            $promotionRequest = [];
            foreach ($prodsInfo as $prodId => $enProdInfo) {
                if (false === $enProdInfo) {
                    continue;
                }
//                if ('1' !== $prodsDisplayStatus[$prodId]) {
//                    continue;
//                }
                $deProdInfo = json_decode($enProdInfo, true);
                $deProdsInfo[$prodId] = $deProdInfo;
                $promotionRequest[] = ['model_id' => $deProdInfo['section'], 'cid' => $deProdInfo['product_type'], 'styleNumber' => $deProdInfo['style_number']];
            }

            if (!empty($deProdsInfo)) {
                $deGoldenPrice = $ProductHelp->fetchGoldenPrice();
                $prosInfo = $ProductHelp->fetchPromotionList($promotionRequest);

                foreach ($deProdsInfo as $prodId => $deProdInfo) {
                    $displayProdInfos[] = $deProdInfo;
                    $filterSpuData = $ProductHelp->filterSpuData($deProdInfo, $fillterCategory, $fillterMetal, $fillterJewel);
                    if (!$filterSpuData) {
                        continue;
                    }
                    $sales = isset($prodsSalesInfo[$prodId]) && false !== $prodsSalesInfo[$prodId] ? (int)$prodsSalesInfo[$prodId] : 0;
                    $new = isset($hProdsDateInfo[$prodId]) && false !== $hProdsDateInfo[$prodId] ? (int)$hProdsDateInfo[$prodId] : 0;
                    $spu = $ProductHelp->setSpuData($deProdInfo, $deGoldenPrice, $prodsDisplayStatus[$prodId], $sales, $new, $prosInfo);
                    $filterSpuDataByPrice = $ProductHelp->filterSpuDataByPrice($spu, $fillterMinPrice, $fillterMaxPrice);
                    if (!$filterSpuDataByPrice) {
                        continue;
                    }
                    $list[] = $spu;
                }
            }
        }
        if ('' !== $sortKey) {
            $list = $ProductHelp->sortSpuList($list, $sortKey, $sort);
        }

        $fillterMetal = [];
        $fillterJewel = [];
        $ProductHelp->getGoldenTypeFilter($displayProdInfos, $fillterMetal, $fillterJewel);

        //取出分页数据
        $return['list'] = array_slice($list, $start, $offset);
        $listCount = count($list);
        $return['totalPage'] = (int)ceil($listCount / $pageSize);
        $return['curPage'] = (int)$curPage;
        if (!empty($fillterMetal)) {
            $return['fillterMetal'] = $fillterMetal;
        }
        if (!empty($fillterJewel)) {
            $return['fillterJewel'] = $fillterJewel;
        }

        return $this->success($return);
    }

    /**
     * 设置虚拟分类.
     */
    public function setVirtualCate(Request $request)
    {
        $prodType = $request->cid ? explode(',', $request->cid) : [];
        $exclude = $request->exclude ? explode(',', $request->exclude) : []; //排除
        $extra = $request->extra ? explode(',', $request->extra) : []; //额外
        $ruleId = $request->rule_id;
        $prodList = [];
        if (empty($prodType) && empty($extra)) {
            $prodList = Spu::select('product_id', 'style_number')->get()->toArray();
        } else {
            if ($prodType) {
                $prodListByProdType = Spu::select('product_id', 'style_number')->whereIn('product_type', $prodType)->get()->toArray();
                $prodList = array_merge($prodList, $prodListByProdType);
            }
            if ($extra) {
                $prodListByExtra = Spu::select('product_id', 'style_number')->whereIn('style_number', $extra)->get()->toArray();
                $prodList = array_merge($prodList, $prodListByExtra);
            }
        }
        $realProdList = [];
        if (!empty($prodList)) {
            if (!empty($exclude)) {
                foreach ($prodList as $prod) {
                    if (!in_array($prod['style_number'], $exclude)) {
                        $realProdList[] = $prod;
                    }
                }
            } else {
                $realProdList = $prodList;
            }
        }
        $returnList = [];
        if (!empty($realProdList)) {
            $returnList = array_column($realProdList, 'product_id');
        }

        $insertVirtualCateInfo = [];
        $insertVirtualCateInfo['rule_id'] = $ruleId;
        $insertVirtualCateInfo['product_type'] = $request->cid ?: null;
        $insertVirtualCateInfo['exclude'] = $request->exclude ?: null;
        $insertVirtualCateInfo['extra'] = $request->extra ?: null;
        $insertVirtualCateInfo['updated_at'] = date('Y-m-d H:i:s');
        VirtualCategory::updateOrCreate(
            ['rule_id' => $ruleId],
            $insertVirtualCateInfo
        );
        $ProductHelp = new ProductHelp();

        $ProductHelp->redisModel->_hset(config('redis.vmappingCateProd'), $ruleId, json_encode($returnList));

        return $this->success([]);
    }

    /**
     * 锁库存.
     */
    public function storeStock(Request $request)
    {
        // $request = json_decode('{"skuStockList":"[{\"skuId\":\"20232407795\",\"qty\":\"3\"},{\"skuId\":\"20224049373\",\"qty\":\"2\"}]"}');
        $skuStockList = $request->skuStockList;
        $deSkuStockList = json_decode($request->skuStockList, true);
        $skus = array_column($deSkuStockList, 'skuId');

        $ProductHelp = new ProductHelp();
        $statusTable = config('redis.skuDisplay');
        $storeTable = config('redis.store');
        $skuDisplayList = $ProductHelp->redisModel->_hmget($statusTable, $skus);
        $skuStockList = $ProductHelp->redisModel->_hmget($storeTable, $skus);

        $lua = <<<EOF
            local statustable = ARGV[1]
            local storetable = ARGV[2]
            local sku = ARGV[3]
            local qty = tonumber(ARGV[4])
            local nowstatus = redis.call('HGET', statustable, sku)
            if nowstatus then
                local nowqty = redis.call('HGET', storetable, sku)
                if nowqty then
                    nowqty = tonumber(nowqty)
                    if nowqty >= qty then
                        redis.call('HINCRBY', storetable, sku, "-"..qty)
                        return 1
                    else
                        return -1
                    end
                else
                    return -2
                end
            else
                return -3
            end
            
EOF;
        //已经成功扣库存的sku
        $passSkus = [];
        $break = false;
        $breakCode = 0;
        $breakSku = '';
        foreach ($deSkuStockList as $skuStockInfo) {
            $skuId = $skuStockInfo['skuId'];
            $qty = $skuStockInfo['qty'];
            //检查sku状态
            if ('1' !== $skuDisplayList[$skuId]) {
                $breakCode = '2003'; //sku已下架
                $breakSku = $skuId;
                $break = true;
                break;
            }
            //检查sku库存
            if (false === $skuStockList[$skuId] || $skuStockList[$skuId] < $qty) {
                $breakCode = '2002'; //库存不足
                $breakSku = $skuId;
                $break = true;
                break;
            }
            try {
                $qtyBack = $ProductHelp->redisModel->redis->eval($lua, ['statustable', 'storetable', 'sku', 'qty', $statusTable, $storeTable, $skuId, $qty], 4);
                switch ($qtyBack) {
                    case -1:
                    case -2:
                        $breakCode = '2002'; //库存不足
                        $breakSku = $skuId;
                        $break = true;
                        break 2;
                    case -3:
                        $breakCode = '2003'; //sku已下架
                        $breakSku = $skuId;
                        $break = true;
                        break 2;
                    default:
                        $passSkus = $skuStockInfo;
                }
            } catch (\Exception $e) {
                $breakCode = '2001'; //库存内部异常
                $breakSku = $skuId;
                $break = true;
                break;
            }
        }

        if ($break && !empty($passSkus)) {
            foreach ($passSkus as $passSkuStockInfo) {
                $ProductHelp->redisModel->_hincrBy($storeTable, $passSkuStockInfo['skuId'], $passSkuStockInfo['qty']);
            }
        }

        if ($break) {
            return $this->error($breakCode, '', ['skuId' => $breakSku]);
        } else {
            return $this->success([]);
        }
    }

    /**
     * 还原库存.
     */
    public function revertStock(Request $request)
    {
        $skuStockList = $request->skuStockList;
        $deSkuStockList = json_decode($request->skuStockList, true);

        $ProductHelp = new ProductHelp();
        $storeTable = config('redis.store');

        foreach ($deSkuStockList as $skuStockInfo) {
            $ProductHelp->redisModel->_hincrBy($storeTable, $skuStockInfo['skuId'], $skuStockInfo['qty']);
        }

        return $this->success([]);
    }

}
