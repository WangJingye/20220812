<?php

namespace App\Http\Controllers\Api\Goods;

use App\Http\Controllers\Api\Controller;
use App\Service\Goods\CategoryService;
use App\Service\Goods\ProductService;
use Illuminate\Http\Request;
use App\Model\Goods\Spu;
use App\Model\Goods\ProductHelp;
use App\Model\Goods\Tree;
use App\Lib\Http;
use App\Model\Goods\Category;

class ProductController extends Controller
{
    protected $_model = Spu::class;

    /**
     * 获取分类.
     */
    public function getCategory(Request $request)
    {
        $categoryId = $request->categoryId;
        $sortKey = $request->sortKey ?: '';
        $sort = $request->sort ?: 1;
        $fillterCategory = $request->fillterCategory ? explode('/', $request->fillterCategory) : ['all'];
        $fillterMetal = $request->fillterMetal ? json_decode($request->fillterMetal, true) : [];
        $fillterJewel = $request->fillterJewel ? json_decode($request->fillterJewel, true) : [];
        $fillterMinPrice = null !== $request->fillterMinPrice && $request->fillterMinPrice >= 0 ? $request->fillterMinPrice : 0;
        $fillterMaxPrice = null !== $request->fillterMaxPrice && $request->fillterMaxPrice >= 0 ? $request->fillterMaxPrice : 'N/A';
        if ('N/A' !== $fillterMaxPrice && $fillterMinPrice > $fillterMaxPrice) {
            return $this->error(2004, 'goods.product.get_category.price_range.error');
        }
        //分页配置
        $curPage = $request->curPage > 0 ? $request->curPage : 1;

        $pageSize = 10;
        $offset = $pageSize;
        $start = ($curPage - 1) * $pageSize;

        $ProductHelp = new ProductHelp();
        $enCateInfo = $ProductHelp->redisModel->_hget(config('redis.cateList'), $categoryId);
        if (!$enCateInfo) {
            return $this->error(2005, 'goods.product.get_category.not_found');
        }
        $deCateInfo = json_decode($enCateInfo, true);

        $list = [];
        $tmpCateList = $deCateInfo['list'];
        if (!empty($tmpCateList)) {
            //全部用途，没有最低最高价格时不做筛选
            if ("['all']" === $fillterCategory && $fillterMetal === [] && $fillterJewel === [] && 0 === $fillterMinPrice && 'N/A' === $fillterMaxPrice) {
                foreach ($tmpCateList as $productInfo) {
                    unset($productInfo['usage_code'], $productInfo['gold_type_code'], $productInfo['product_type']);
                    $list[] = $productInfo;
                }
            } else {
                foreach ($tmpCateList as $productInfo) {
                    $filterSpuData = $ProductHelp->filterSpuData($productInfo, $fillterCategory, $fillterMetal, $fillterJewel);
                    if (!$filterSpuData) {
                        continue;
                    }
                    $filterSpuDataByPrice = $ProductHelp->filterSpuDataByPrice($productInfo, $fillterMinPrice, $fillterMaxPrice);
                    if (!$filterSpuDataByPrice) {
                        continue;
                    }
                    unset($productInfo['usage_code'], $productInfo['gold_type_code'], $productInfo['product_type']);
                    $list[] = $productInfo;
                }
            }
        }

        if ('' !== $sortKey) {
            $list = $ProductHelp->sortSpuList($list, $sortKey, $sort);
        }

        $return = $deCateInfo;
        //取出分页数据
        $return['list'] = array_slice($list, $start, $offset);
        $listCount = count($list);
        $return['totalPage'] = (int)ceil($listCount / $pageSize);
        $return['curPage'] = (int)$curPage;

        return $this->success($return);
    }

    /**
     * 获取产品.
     * depend:金价，促销价.
     */
    public function getProduct(Request $request)
    {
        $code = $request->code;
        $prodIds = json_decode($code, true);
        $prodIds = array_unique($prodIds);
        $ProductHelp = new ProductHelp();

        $enProdsList = $ProductHelp->redisModel->pr(config('redis.prodList'), $prodIds);

        if (!$enProdsList) {
            return $this->error(2006, 'goods.product.get_product.not_found');
        }

        $list = [];
        foreach ($enProdsList as $prodId => $enProdInfo) {
            if (false !== $enProdInfo) {
                $list[] = json_decode($enProdInfo, true);
            }
        }

        if (empty($list)) {
            return $this->error(2006, 'goods.product.get_product.not_found');
        }

        return $this->success(['list' => $list]);
    }

    /**
     * 获取门店产品.
     * depend:金价，促销价.
     */
    public function getDoorProduct(Request $request)
    {
        $code = $request->code;
        $prodIds = json_decode($code, true);
        $prodIds = array_unique($prodIds);
        $ProductHelp = new ProductHelp();

        $enProdsList = $ProductHelp->redisModel->_hmget(config('redis.doorsProdList'), $prodIds);

        if (!$enProdsList) {
            return $this->error(2007, 'goods.product.get_product.not_found');
        }

        $list = [];
        foreach ($enProdsList as $prodId => $enProdInfo) {
            if (false !== $enProdInfo) {
                $list[] = json_decode($enProdInfo, true);
            }
        }
        if (empty($list)) {
            return $this->error(2007, 'goods.product.get_product.not_found');
        }
        return $this->success(['list' => $list]);
    }

    /**
     * 获取分类列表.
     */
    public function getCategoryTree(Request $request)
    {
//        $str = '{"tree":[{"id":1,"pid":0,"cateId":"191227141034565694","label":"\u5168\u90e8\u4ea7\u54c1","level":1},{"id":2,"pid":0,"cateId":"191227141034571393","label":"\u624b\u94fe\/\u624b\u956f\/\u811a\u94fe","level":1,"children":[{"id":3,"pid":2,"cateId":"191227141034579327","label":"K\u91d1","level":2},{"id":8,"pid":2,"cateId":"191227141517962275","label":"\u94bb\u77f3","level":2},{"id":33,"pid":2,"cateId":"191227142742827536","label":"\u5b9d\u77f3","level":2},{"id":48,"pid":2,"cateId":"191227142859215162","label":"\u9ec4\u91d1","level":2},{"id":54,"pid":2,"cateId":"191227143037334712","label":"\u94c2\u91d1","level":2},{"id":115,"pid":2,"cateId":"200117203528923335","label":"\u73cd\u73e0","level":2},{"id":118,"pid":2,"cateId":"200117204504176554","label":"\u94f6\u9970","level":2}]},{"id":4,"pid":0,"cateId":"191227141034585137","label":"\u624b\u94fe","level":1},{"id":5,"pid":0,"cateId":"191227141034590537","label":"K\u91d1","level":1},{"id":6,"pid":0,"cateId":"191227141034598689","label":"\u54c1\u724c\u7cfb\u5217","level":1,"children":[{"id":7,"pid":6,"cateId":"191227141034604130","label":"\u8584\u8377\u7cfb\u5217","level":2},{"id":10,"pid":6,"cateId":"191227141517979843","label":"Promessa","level":2},{"id":21,"pid":6,"cateId":"191227142419619202","label":"Infinity","level":2},{"id":22,"pid":6,"cateId":"191227142420926221","label":"Kashikey","level":2},{"id":23,"pid":6,"cateId":"191227142546773765","label":"\u5fc3\u5f71","level":2},{"id":24,"pid":6,"cateId":"191227142604969655","label":"Daily luxe","level":2},{"id":28,"pid":6,"cateId":"191227142610170446","label":"Happy Floret","level":2},{"id":29,"pid":6,"cateId":"191227142630999391","label":"\u5168\u7231\u94bb","level":2,"children":[{"id":30,"pid":29,"cateId":"191227142631007087","label":"\u5178\u96c5","level":3},{"id":39,"pid":29,"cateId":"191227142816226959","label":"Iconic","level":3},{"id":112,"pid":29,"cateId":"200110104834597300","label":"\u5a5a\u5ac1","level":3}]},{"id":35,"pid":6,"cateId":"191227142747332604","label":"\u70ab\u52a8","level":2},{"id":37,"pid":6,"cateId":"191227142754992253","label":"PetChat","level":2},{"id":38,"pid":6,"cateId":"191227142806706247","label":"Lady Heart","level":2},{"id":46,"pid":6,"cateId":"191227142826117313","label":"\u7d20\u94fe","level":2},{"id":52,"pid":6,"cateId":"191227142903306022","label":"\u5bf9\u6212","level":2},{"id":59,"pid":6,"cateId":"191227143223318217","label":"V&A","level":2,"children":[{"id":60,"pid":59,"cateId":"191227143223324642","label":"Posy Ring","level":3},{"id":78,"pid":59,"cateId":"191227143535028192","label":"Bless","level":3}]},{"id":61,"pid":6,"cateId":"191227143236747005","label":"\u751f\u751f\u6709\u793c","level":2,"children":[{"id":62,"pid":61,"cateId":"191227143236753116","label":"\u65b0\u751f\u7bc7","level":3},{"id":85,"pid":61,"cateId":"191227143606196733","label":"\u8d3a\u5e74\u751f\u8096\u7bc7","level":3},{"id":94,"pid":61,"cateId":"191227143655606356","label":"\u73cd\u85cf\u7bc7","level":3},{"id":95,"pid":61,"cateId":"191227143655931442","label":"\u795d\u5bff\u7bc7","level":3},{"id":130,"pid":61,"cateId":"200402124651462170","label":"\u65b0\u5a5a\u8d3a\u793c\u7bc7","level":3}]},{"id":63,"pid":6,"cateId":"191227143255574942","label":"\u751f\u751f\u6709\u56cd","level":2,"children":[{"id":64,"pid":63,"cateId":"191227143255581054","label":"\u7b80\u7ea6\u7bc7","level":3},{"id":65,"pid":63,"cateId":"191227143404591578","label":"\u9f99\u51e4\u7bc7","level":3},{"id":76,"pid":63,"cateId":"191227143448341061","label":"\u55bb\u610f\u7bc7","level":3},{"id":96,"pid":63,"cateId":"191227143718152204","label":"\u82b1\u5349\u7bc7","level":3}]},{"id":67,"pid":6,"cateId":"191227143428199721","label":"Charme","level":2,"children":[{"id":68,"pid":67,"cateId":"191227143428205609","label":"\u7231\u60c5","level":3},{"id":71,"pid":67,"cateId":"191227143433920616","label":"\u5b57\u6bcd","level":3},{"id":74,"pid":67,"cateId":"191227143444457582","label":"\u7ae5\u8bdd\u7cfb\u5217","level":3},{"id":80,"pid":67,"cateId":"191227143551201745","label":"\u5f69\u8272\u73bb\u7483\u73e0","level":3},{"id":83,"pid":67,"cateId":"191227143604564827","label":"\u6587\u5316\u795d\u798f","level":3},{"id":84,"pid":67,"cateId":"191227143605599670","label":"\u661f\u8fd0\u795e\u8bdd","level":3},{"id":87,"pid":67,"cateId":"191227143608721516","label":"\u9177\u9ed1","level":3},{"id":92,"pid":67,"cateId":"191227143641953332","label":"\u6570\u5b57","level":3},{"id":93,"pid":67,"cateId":"191227143649613538","label":"\u53ef\u7231","level":3}]},{"id":72,"pid":6,"cateId":"191227143434105337","label":"\u6587\u5316\u795d\u798f","level":2,"children":[{"id":73,"pid":72,"cateId":"191227143434111820","label":"\u516d\u5b57\u5927\u660e\u5492","level":3},{"id":82,"pid":72,"cateId":"191227143600844925","label":"\u683c\u6851\u82b1","level":3},{"id":98,"pid":72,"cateId":"191227143734303729","label":"\u4f5b\u6709\u7f18","level":3},{"id":124,"pid":72,"cateId":"200309153143320804","label":"\u4e1c\u65b9\u53e4\u7956","level":3},{"id":126,"pid":72,"cateId":"200312114318391687","label":"\u4f20\u5947","level":3},{"id":131,"pid":72,"cateId":"200403144300392675","label":"\u516b\u745e\u76f8","level":3}]},{"id":75,"pid":6,"cateId":"191227143447340057","label":"\u7231\u60c5\u5bc6\u8bed","level":2},{"id":77,"pid":6,"cateId":"191227143509368805","label":"Lace","level":2},{"id":79,"pid":6,"cateId":"191227143540457733","label":"Finger Play","level":2},{"id":81,"pid":6,"cateId":"191227143556000072","label":"g* \u7cfb\u5217","level":2},{"id":86,"pid":6,"cateId":"191227143606728755","label":"Nior","level":2},{"id":88,"pid":6,"cateId":"191227143628540882","label":"Wrist Play","level":2},{"id":89,"pid":6,"cateId":"191227143632165325","label":"\u706b\u5f71\u5fcd\u8005","level":2},{"id":91,"pid":6,"cateId":"191227143635937849","label":"La pelle","level":2},{"id":97,"pid":6,"cateId":"191227143719672012","label":"\u822a\u6d77\u738b","level":2},{"id":99,"pid":6,"cateId":"191227143735760933","label":"Ear Play","level":2},{"id":113,"pid":6,"cateId":"200117200012167879","label":"Hodel","level":2},{"id":114,"pid":6,"cateId":"200117200024693134","label":"Marco Bicego","level":2},{"id":121,"pid":6,"cateId":"200309153054546906","label":"QQ Family","level":2},{"id":122,"pid":6,"cateId":"200309153127041545","label":"\u9047\u89c1","level":2},{"id":123,"pid":6,"cateId":"200309153129868244","label":"\u9634\u9633\u5e08","level":2},{"id":125,"pid":6,"cateId":"200309153201194768","label":"\u738b\u8005\u8363\u8000","level":2}]},{"id":9,"pid":0,"cateId":"191227141517972510","label":"\u94bb\u77f3","level":1},{"id":11,"pid":0,"cateId":"191227141539172760","label":"\u94c2\u91d1","level":1},{"id":12,"pid":0,"cateId":"191227141558581377","label":"\u6212\u6307","level":1,"children":[{"id":13,"pid":12,"cateId":"191227141558588394","label":"K\u91d1","level":2},{"id":18,"pid":12,"cateId":"191227141808189200","label":"\u94bb\u77f3","level":2},{"id":32,"pid":12,"cateId":"191227142733724859","label":"\u5b9d\u77f3","level":2},{"id":43,"pid":12,"cateId":"191227142822580300","label":"\u73cd\u73e0","level":2},{"id":51,"pid":12,"cateId":"191227142903296649","label":"\u9ec4\u91d1","level":2},{"id":55,"pid":12,"cateId":"191227143040307636","label":"\u94c2\u91d1","level":2},{"id":117,"pid":12,"cateId":"200117204502388378","label":"\u94f6\u9970","level":2}]},{"id":14,"pid":0,"cateId":"191227141600981621","label":"\u8033\u9970","level":1,"children":[{"id":15,"pid":14,"cateId":"191227141600987218","label":"\u94bb\u77f3","level":2},{"id":40,"pid":14,"cateId":"191227142818559603","label":"\u5b9d\u77f3","level":2},{"id":44,"pid":14,"cateId":"191227142823280098","label":"\u73cd\u73e0","level":2},{"id":56,"pid":14,"cateId":"191227143040943643","label":"\u94c2\u91d1","level":2},{"id":58,"pid":14,"cateId":"191227143048952508","label":"\u9ec4\u91d1","level":2},{"id":70,"pid":14,"cateId":"191227143433532428","label":"K\u91d1","level":2},{"id":116,"pid":14,"cateId":"200117204251414395","label":"\u94f6\u9970","level":2}]},{"id":16,"pid":0,"cateId":"191227141702958102","label":"\u639b\u5760","level":1,"children":[{"id":17,"pid":16,"cateId":"191227141702965384","label":"\u94bb\u77f3","level":2},{"id":26,"pid":16,"cateId":"191227142608955470","label":"\u5b9d\u77f3","level":2},{"id":34,"pid":16,"cateId":"191227142744614814","label":"\u94c2\u91d1","level":2},{"id":53,"pid":16,"cateId":"191227143033474974","label":"\u9ec4\u91d1","level":2},{"id":69,"pid":16,"cateId":"191227143433394882","label":"K\u91d1","level":2},{"id":90,"pid":16,"cateId":"191227143635929227","label":"\u73cd\u73e0","level":2},{"id":120,"pid":16,"cateId":"200309152154778292","label":"\u94f6\u9970","level":2}]},{"id":19,"pid":0,"cateId":"191227142419605666","label":"\u9879\u94fe","level":1,"children":[{"id":20,"pid":19,"cateId":"191227142419611557","label":"\u94bb\u77f3","level":2},{"id":36,"pid":19,"cateId":"191227142754387144","label":"\u5b9d\u77f3","level":2},{"id":41,"pid":19,"cateId":"191227142821651661","label":"\u73cd\u73e0","level":2},{"id":45,"pid":19,"cateId":"191227142826109465","label":"\u94c2\u91d1","level":2},{"id":47,"pid":19,"cateId":"191227142829182282","label":"K\u91d1","level":2},{"id":50,"pid":19,"cateId":"191227142902668120","label":"\u9ec4\u91d1","level":2},{"id":100,"pid":19,"cateId":"191227143749028707","label":"\u94f6\u9970","level":2}]},{"id":25,"pid":0,"cateId":"191227142605541722","label":"\u624b\u956f","level":1,"children":[{"id":102,"pid":25,"cateId":"200108100709459671","label":"\u9ec4\u91d1","level":2},{"id":103,"pid":25,"cateId":"200108100808590756","label":"K\u91d1","level":2},{"id":104,"pid":25,"cateId":"200108100824602617","label":"\u94bb\u77f3","level":2},{"id":105,"pid":25,"cateId":"200108100836209140","label":"\u94c2\u91d1","level":2}]},{"id":27,"pid":0,"cateId":"191227142608961718","label":"\u5b9d\u77f3","level":1},{"id":31,"pid":0,"cateId":"191227142719211510","label":"\u811a\u94fe","level":1},{"id":42,"pid":0,"cateId":"191227142821658116","label":"\u73cd\u73e0","level":1},{"id":49,"pid":0,"cateId":"191227142859222288","label":"\u9ec4\u91d1","level":1},{"id":57,"pid":0,"cateId":"191227143048012179","label":"\u91d1\u7247","level":1},{"id":66,"pid":0,"cateId":"191227143428188577","label":"\u4e32\u9970","level":1},{"id":101,"pid":0,"cateId":"191227143749036747","label":"\u94f6\u9970","level":1},{"id":106,"pid":null,"cateId":"200108101014016471","label":"\u6212\u6307-\u53ef\u523b\u5b57\u6b3e","level":1},{"id":107,"pid":null,"cateId":"200108150355201875","label":"IP\u8054\u540d\u6b3e","level":1,"children":[{"id":108,"pid":107,"cateId":"200108150412705050","label":"\u9634\u9633\u5e08","level":2},{"id":111,"pid":107,"cateId":"200108151728721674","label":"\u738b\u8005\u8363\u8000","level":2}]},{"id":109,"pid":null,"cateId":"200108150431512819","label":"2020\u65b0\u5e74\u63a8\u8350\u6b3e","level":1},{"id":110,"pid":null,"cateId":"200108150852434298","label":"\u751f\u8096\u6b3e\u96c6\u5408","level":1},{"id":119,"pid":null,"cateId":"200304123848736723","label":"MINTYGREEN","level":1}]}';
//        echo $str;exit;
        $cateList = Category::select('id', 'parent_cat_id as pid', 'cat_name as label')->get()->toArray();
//        $cateList = Category::select('id', 'parent_cat_id', 'category_id as cateId', 'category_name as label')->get()->toArray();

        $Tree = new Tree();
        $tree = $Tree->getTreeData($cateList, 'level', 'label', 'id', 'pid');

        return $this->success($tree);
    }

    /**
     * 为您推荐
    //'1' => '用户最近浏览的前6个产品',
    //'7' => '默认Sort升序',
    //'8' => '默认Sort降序',
    //'9' => '自定义商品',
     */
    public function recommend(Request $request)
    {
        $code = $request->code;
        $flag = $request->flag??'';

        if(!in_array($flag,ProductService::$recommendConfigDBName)) return $this->error(0,'推荐类型不正确');
        $pService = new ProductService();

        $ProductHelp = new ProductHelp();
        $recommendConfig = $pService->getRecInfoFromCache($flag);
        if (false === $recommendConfig) {
            return $this->success(['list' => []]);
        }

        $prodIds = [];
        switch ($recommendConfig['config_value']) {
            case '1':
                //壮壮提供足迹接口
                $http = new Http();
                //获取商品信息
                $recentBack = $http->curl('footprint/getPids', ['page' => 1,'page_size'=>6]);
                if (1 === $recentBack['code'] && isset($recentBack['data']['ids'])) {
                    $prodIds = $recentBack['data']['ids'];
                }
                break;
            case '2':
                $prodView = $ProductHelp->redisModel->_zrevrange(config('redis.prodSalesByUsage') . '###' . $usage, 0, 5);
                if (!empty($prodView)) {
                    $prodIds = $prodView;
                }
                break;
            case '3':
                $prodView = $ProductHelp->redisModel->_zrevrange(config('redis.prodViewByUsage') . '###' . $usage, 0, 5);
                if (!empty($prodView)) {
                    $prodIds = $prodView;
                }
                break;
            case '4':
                //壮壮提供收藏夹接口
                $http = new Http();
                //获取商品信息
                $recentBack = $http->curl('fav/getPagePids',  ['page' => 1,'page_size'=>6]);
                if (1 === $recentBack['code'] && isset($recentBack['data']['idList'])) {
                    $prodIds = $recentBack['data']['ids'];
                }
                break;
            case '5':
                $pBack = $pService->getLatestPids(6);
                if (!empty($pBack)) {
                    $prodIds = $pBack;
                }
                break;
            case '6':
                $pService = new ProductService();
                $pBack = $pService->getLatestPids(6);
                if (!empty($pBack)) {
                    $prodIds = $pBack;
                }
                break;
            case '9':
                $pService = new ProductService();
                $pBack = $pService->getLatestPids(6);
                if (!empty($pBack)) {
                    $prodIds = $pBack;
                }
                break;
        }

        $list = [];
        if (!empty($prodIds)) {
            $prodsInfo = $ProductHelp->redisModel->_hmget(config('redis.prodLevelInfo'), $prodIds);
            $prodsDisplayStatus = $ProductHelp->redisModel->_hmget(config('redis.prodDisplay'), $prodIds);

            $deProdsInfo = [];
            $promotionRequest = [];
            foreach ($prodsInfo as $prodId => $enProdInfo) {
                if (false === $enProdInfo) {
                    continue;
                }
                $deProdInfo = json_decode($enProdInfo, true);
                $deProdsInfo[$prodId] = $deProdInfo;
                $promotionRequest[] = ['model_id' => $deProdInfo['section'], 'cid' => $deProdInfo['product_type'], 'styleNumber' => $deProdInfo['style_number']];
            }

            if (!empty($deProdsInfo)) {
                $deGoldenPrice = $ProductHelp->fetchGoldenPrice();
                $prosInfo = $ProductHelp->fetchPromotionList($promotionRequest);
                foreach ($deProdsInfo as $prodId => $deProdInfo) {
                    $spu = $ProductHelp->setSpuData($deProdInfo, $deGoldenPrice, $prodsDisplayStatus[$prodId], 0, 0, $prosInfo);
                    $list[] = $spu;
                }
            }
        }
        $return = [];
        $return['list'] = $list;
        return $this->success($return);
    }

    /**
     * 获取凑单产品列表.
     */
    public function getRuleCate(Request $request)
    {
        $ruleId = $request->ruleId;
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

        $enCatProdRelation = $ProductHelp->redisModel->_hget(config('redis.vmappingCateProd'), $ruleId);
        if (!empty($enCatProdRelation)) {
            $catProdRelation = json_decode($enCatProdRelation, true);
            $prodsInfo = $ProductHelp->redisModel->_hmget(config('redis.prodLevelInfo'), $catProdRelation);
            $prodsSalesInfo = $ProductHelp->redisModel->_hmget(config('redis.prodSales'), $catProdRelation);
            $hProdsDateInfo = $ProductHelp->redisModel->_hmget(config('redis.hProdDisplayDate'), $catProdRelation);

            if (!empty($prodsInfo)) {
                $prodsDisplayStatus = $ProductHelp->redisModel->_hmget(config('redis.prodDisplay'), $catProdRelation);

                $deProdsInfo = [];
                $promotionRequest = [];
                foreach ($prodsInfo as $prodId => $enProdInfo) {
                    if (false === $enProdInfo) {
                        continue;
                    }
                    if ('1' !== $prodsDisplayStatus[$prodId]) {
                        continue;
                    }
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
                        //过滤brandsite商品
                        if ($spu['brandSite'] === true) {
                            continue;
                        }
                        $filterSpuDataByPrice = $ProductHelp->filterSpuDataByPrice($spu, $fillterMinPrice, $fillterMaxPrice);
                        if (!$filterSpuDataByPrice) {
                            continue;
                        }
                        $list[] = $spu;
                    }
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
        if (isset($deCateLevelInfo['banner']) && !empty($deCateLevelInfo['banner'])) {
            $return['banner'] = $deCateLevelInfo['banner'];
        }
        if (!empty($fillterMetal)) {
            $return['fillterMetal'] = $fillterMetal;
        }
        if (!empty($fillterJewel)) {
            $return['fillterJewel'] = $fillterJewel;
        }

        return $this->success($return);
    }
}
