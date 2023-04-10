<?php

namespace App\Model\Goods;

use App\Lib\Http;

class ProductHelp
{
    public $redisModel = null;

    public function __construct()
    {
        $this->redisModel = new RedisModel();
    }

    public static $filter = [
        'goldenType' => [
            'golden' => [
                'name' => '黄金',
                'include' => [
                    '24KG', '24GG', '23KG',
                ],
                'status' => false,
            ],
            '18k' => [
                'name' => '18K金',
                'include' => [
                    '18YW', '18YR', '18YO', '18YB', '18WR', '18WO', '18WB', '18TT', '18RB', '18OR', '18KY', '18KW', '18KS', '18KR', '18KO', '18KG', '18KB', '14YW', '14WR', 'M950', 'M900', 'M850',
                ],
                'status' => false,
            ],
            'pt' => [
                'name' => '铂金',
                'include' => [
                    'P999', 'P950', 'P900', 'P850', 'P585', 'P500', 'M950', 'M900', 'M850',
                ],
                'status' => false,
            ],
            'silver' => [
                'name' => '银',
                'include' => [
                    'SILV', 'S999',
                ],
                'status' => false,
            ],
        ],
        'productType' => [
            'diamond' => [
                'name' => '钻石',
                'include' => ['DF', 'DI'],
                'status' => false,
            ],
            'pearl' => [
                'name' => '珍珠',
                'include' => ['TF', 'PL'],
                'status' => false,
            ],
            'gem' => [
                'name' => '彩宝',
                'include' => ['XF', 'GS', 'QF', 'SS'],
                'status' => false,
            ],
        ],
    ];

    /**
     * 用途.
     */
    public static $usage = [
        'B' => '手链',
        'C' => '串饰',
        'D' => '金片',
        'E' => '耳饰',
        'H' => '配饰',
        'K' => '手镯',
        'M' => '脚链',
        'N' => '项链',
        'O' => '摆件',
        'P' => '吊坠',
        'R' => '戒指',
        'S' => '生生金宝金片',
    ];

    public static $offerPic = [
        'minus' => 'https://wecassets.chowsangsang.com.cn/MiniApp/Images/mj.png',
        'cut' => 'https://wecassets.chowsangsang.com.cn/MiniApp/Images/mz.png',
        'sample' => 'https://wecassets.chowsangsang.com.cn/MiniApp/Images/manzeng.png',
    ];
    public static $optionTrans = [
        'length' => [
            'name' => '链长',
            'unit' => '厘米',
        ],
        'weight' => [
            'name' => '重量',
            'unit' => '克',
        ],
        'weight_virtual' => [
            'name' => '重量',
            'unit' => '克',
        ],
        'style' => [
            'name' => '款式',
            'unit' => '',
        ],
        'size' => [
            'name' => '尺码',
            'unit' => '',
        ],
        'ring_size' => [
            'name' => '圈度',
            'unit' => '',
        ],
        'price' => [
            'name' => '价格',
            'unit' => '',
        ],
        'cstandard' => [
            'name' => '规格',
            'unit' => '',
        ],
        'diamond_set' => [
            'name' => '主石克拉重',
            'unit' => '',
        ],
    ];
    /**
     * 产品类型.
     */
    public static $prodType = [
        '1' => 'GA',
        '2' => 'PA',
        '3' => 'GB',
        '4' => 'GI',
        '5' => 'GF',
        '6' => 'PF',
        '7' => 'MP',
        '8' => 'DF',
        '9' => 'DI',
        '10' => 'XF',
        '11' => 'GS',
        '12' => 'QF',
        '13' => 'SS',
        '14' => 'TF',
        '15' => 'PL',
        '16' => 'FJ',
        '17' => 'SF',
    ];

    public static $prodTypeDesc = [
        'GA' => '计价足金饰品',
        'PA' => '计价铂金饰品',
        'GB' => '金条/金片',
        'GI' => '生生金宝（金片）',
        'GF' => '定价足金饰品',
        'PF' => '定价铂金饰品',
        'MP' => '固定价复合贵金属饰品',
        'DF' => '固定价钻石饰品',
        'DI' => '钻石饰品',
        'XF' => '固定价宝石饰品',
        'GS' => '宝石饰品',
        'QF' => '固定价半宝石饰品',
        'SS' => '半宝石饰品',
        'TF' => '固定价珍珠饰品',
        'PL' => '珍珠饰品',
        'FJ' => 'K金饰品',
        'SF' => '纯银饰品',
    ];
    /**
     * 品牌系列.
     */
    public static $brandColl = [
        [
            'code' => 'C446',
            'name' => 'Charme',
            'sub' => [
                [
                    'code' => 'S0525',
                    'name' => '字母',
                ],
                [
                    'code' => 'S0529',
                    'name' => '数字',
                ],
                [
                    'code' => 'S0530',
                    'name' => '星运神话',
                ],
                [
                    'code' => 'S0531',
                    'name' => '文化祝福',
                ],
                [
                    'code' => 'S0540',
                    'name' => '彩色玻璃珠',
                ],
                [
                    'code' => 'S0528',
                    'name' => '爱情',
                ],
                [
                    'code' => 'S0526',
                    'name' => '可爱',
                ],
                [
                    'code' => 'S0527',
                    'name' => '童话系列',
                ],
                [
                    'code' => 'S0545',
                    'name' => '酷黑',
                ],
            ],
        ],
        [
            'code' => 'PR',
            'name' => 'Promessa',
        ],
        [
            'code' => 'C504',
            'name' => '文化祝福',
            'sub' => [
                [
                    'code' => 'S0457',
                    'name' => '六字大明咒',
                ],
                [
                    'code' => 'S0658',
                    'name' => '传奇',
                ],
                [
                    'code' => 'S0657',
                    'name' => '东方古祖',
                ],
                [
                    'code' => 'S0541',
                    'name' => '格桑花',
                ],
                [
                    'code' => 'S0627',
                    'name' => '佛有缘',
                ],
                [
                    'code' => 'S0542',
                    'name' => '八瑞相',
                ],
            ],
        ],
        [
            'code' => 'C500',
            'name' => '爱情密语',
        ],
        [
            'code' => 'VA',
            'name' => 'V&A',
            'sub' => [
                [
                    'code' => 'S0459',
                    'name' => 'Bless',
                ],
                [
                    'code' => 'S0178',
                    'name' => 'Posy Ring',
                ],
            ],
        ],
        [
            'code' => 'C543',
            'name' => 'g*系列',
        ],
        [
            'code' => 'S0458',
            'name' => 'Finger Play',
        ],
        [
            'code' => 'S0625',
            'name' => 'Ear Play',
        ],
        [
            'code' => 'S0633',
            'name' => 'Wrist Play',
        ],
        [
            'code' => 'C516',
            'name' => '薄荷系列',
        ],
        [
            'code' => 'C507',
            'name' => 'Noir',
        ],
        [
            'code' => 'C407',
            'name' => 'La pelle',
        ],
        [
            'code' => 'C455',
            'name' => 'Lace',
        ],
        [
            'code' => 'C456',
            'name' => 'PetChat',
        ],
        [
            'code' => 'C288',
            'name' => '心影',
        ],
        [
            'code' => 'IL',
            'name' => '全爱钻',
            'sub' => [
                [
                    'code' => 'S0279',
                    'name' => '婚嫁',
                ],
                [
                    'code' => 'S0539',
                    'name' => 'Iconic',
                ],
                [
                    'code' => 'S0398',
                    'name' => '典雅',
                ],
            ],
        ],
        [
            'code' => 'C270',
            'name' => '炫动',
        ],
        [
            'code' => 'LH',
            'name' => 'Lady Heart',
        ],
        [
            'code' => 'C515',
            'name' => 'Daily luxe',
        ],
        [
            'code' => 'C514',
            'name' => '遇见',
        ],
        [
            'code' => 'C342',
            'name' => '生生有礼',
            'sub' => [
                [
                    'code' => 'S0522',
                    'name' => '新生篇',
                ],
                [
                    'code' => 'S0520',
                    'name' => '祝寿篇',
                ],
                [
                    'code' => 'S0523',
                    'name' => '新婚贺礼篇',
                ],
                [
                    'code' => 'S0524',
                    'name' => '贺年生肖篇',
                ],
                [
                    'code' => 'S0521',
                    'name' => '珍藏篇',
                ],
            ],
        ],
        [
            'code' => 'C501',
            'name' => '生生有囍',
            'sub' => [
                [
                    'code' => 'S0534',
                    'name' => '喻意篇',
                ],
                [
                    'code' => 'S0535',
                    'name' => '简约篇',
                ],
                [
                    'code' => 'S0536',
                    'name' => '龙凤篇',
                ],
                [
                    'code' => 'S0537',
                    'name' => '花卉篇',
                ],
            ],
        ],
        [
            'code' => 'C395',
            'name' => 'Happy Floret',
        ],
        [
            'code' => 'HD',
            'name' => 'Hodel',
        ],
        [
            'code' => 'KA',
            'name' => 'Kashikey',
        ],
        [
            'code' => 'C396',
            'name' => 'Infinity',
        ],
        [
            'code' => 'MB',
            'name' => 'Marco Bicego',
        ],
        [
            'code' => 'OP',
            'name' => '航海王',
        ],
        [
            'code' => 'ONI',
            'name' => '阴阳师',
        ],
        [
            'code' => 'NTO',
            'name' => '火影忍者',
        ],
        [
            'code' => 'HOK',
            'name' => '王者荣耀',
        ],
        [
            'code' => 'QQ',
            'name' => 'QQ Family',
        ],
    ];
    /**
     * 钻石.
     */
    public $diamondMetal = [
        'DD' => '钻石',
        'LD' => '蓝钻石',
        'PD' => '粉红钻石',
        'YD' => '黄钻石',
        'GD' => '灰钻石',
        'OD' => '橙钻石',
        'BD' => '褐钻石',
        'AD' => '黑钻石',
        'RD' => '绿钻石',
        'MD' => '彩色钻石',
        'UD' => '紫钻石',
        'HD' => '钻石',
    ];
    /**
     * 珍珠.
     */
    public $pearlMetal = [
        'SP' => '南洋养殖珍珠',
        'CP' => 'Akoya养殖珍珠',
        'MA' => '贝附珍珠',
        'FP' => '淡水养殖珍珠',
        'GP' => '南洋养殖金珍珠',
        'BP' => '大溪地养殖珍珠',
        'DF' => '淡水养殖珍珠',
    ];
    /**
     * 宝石.
     */
    public $gemMetal = [
        'JE' => '翡翠',
        'BJ' => '翡翠',
        'RU' => '红宝石',
        'EM' => '祖母绿',
        'SA' => '蓝宝石',
        'RS' => '红宝石及蓝宝石',
        'RE' => '红宝石及祖母绿',
        'ES' => '祖母绿及蓝宝石',
        'TG' => '红宝石、蓝宝石及祖母绿',
        'PS' => '粉红色蓝宝石',
        'YS' => '黄色蓝宝石',
        'PU' => '紫色蓝宝石',
        'OS' => '橙色蓝宝石',
        'GR' => '绿色蓝宝石',
        'FS' => '彩色蓝宝石',
        'TS' => '橙色蓝宝石',
        'CS' => '无色蓝宝石',
        'FR' => '红宝石',
        'GG' => '绿色石榴石',
        'GN' => '镁铁铝榴石',
        'CR' => '黄晶',
        'AT' => '紫晶',
        'GQ' => '绿水晶',
        'SQ' => '烟晶',
        'PE' => '橄榄石',
        'TU' => '碧玺',
        'TM' => '粉红碧玺',
        'IN' => '蓝碧玺',
        'GT' => '绿碧玺',
        'YM' => '黄碧玺',
        'TP' => '蓝托帕石',
        'YT' => '黄托帕石',
        'TT' => '托帕石',
        'CT' => '无色托帕石',
        'TR' => '绿松石',
        'OP' => '欧泊',
        'AQ' => '海蓝宝石',
        'BQ' => '黑石英石',
        'PQ' => '紫石英石',
        'NQ' => '芙蓉石',
        'YQ' => '黄石英石',
        'CL' => '玉髓',
        'IO' => '堇青石',
        'ID' => '符山石',
        'ZI' => '锆石',
        'AM' => '琥珀',
        'PX' => '辉石',
        'KU' => '紫锂辉石',
        'HI' => '翠绿锂辉石',
        'AG' => '玛瑙',
        'CH' => '绿玉髓',
        'PR' => '葡萄石',
        'MS' => '月光石',
        'GB' => '绿色绿柱石',
        'YB' => '黄色绿柱石',
        'MO' => '粉红色绿柱石',
        'OX' => '黑玉髓',
        'CA' => '珊瑚',
        'PY' => '黄铁矿',
        'AR' => '文石',
        'FU' => '莹石',
        'NE' => '软玉',
        'SI' => '锰铝榴石',
        'RB' => '红碧玺',
        'SL' => '方钠石',
        'LL' => '青金石',
        'AI' => '变石',
        'TE' => '木变石',
        'HM' => '赤铁矿',
        'TZ' => '坦桑石',
        'RQ' => '水晶',
        'AE' => '铁铝榴石',
        'RN' => '蔷薇辉石',
        'PM' => '贝壳 (贝母)',
        'KS' => 'Keshi 珍珠',
        'GA' => '石榴石',
        'PG' => '镁铝榴石',
        'TV' => '铬钒钙铝榴石',
        'AN' => '钙铁榴石',
        'DE' => '翠榴石',
        'CO' => '海螺珠',
        'FM' => '月光石',
        'SN' => '尖晶石',
        'DO' => '透辉石',
        'LT' => '托帕石 (处理)',
        'LB' => '拉长石',
        'MC' => '孔雀石',
        'RC' => '菱锰矿',
        'OL' => '蛋白石',
        'QJ' => '石英-硬玉',
        'FJ' => '长石-硬玉',
        'QF' => '石英、长石-硬玉',
    ];
    /**
     * 1%悦享钱.
     */
    public $typeForOne = [
        '1',
        '2',
    ];

    /**
     * 5%悦享钱.
     */
    public $typeForFive = [
        '5',
        '6',
        '7',
        '8',
        '9',
        '10',
        '11',
        '12',
        '13',
        '14',
        '15',
        '16',
        '17',
    ];

    /**
     * 获取悦享钱规则.
     */
    public function getPoint($prodType)
    {
        $index = array_search($prodType, $this->prodType);
        if (in_array($index, $this->typeForOne)) {
            return 1;
        } elseif (in_array($index, $this->typeForFive)) {
            return 5;
        } else {
            return 0;
        }
    }

    /**
     * 获取价格.
     */
    public function getSkuPrice($priceType, $prodType, $price, $weight, $goldenPrice, $laborPrice, $discount = 100)
    {
        if ('Y' === $priceType) {//计价
            if ('GA' === $prodType || 'PA' === $prodType) {
                return (int)($weight * $goldenPrice + $laborPrice * $discount / 100);
            } else {
                return (int)($weight * $goldenPrice);
            }
        } else {
            return (int)($price * $discount / 100);
        }
    }

    /**
     * 获取不包含工费价格.
     */
    public function getSkuPriceNoLabor($priceType, $price, $weight, $goldenPrice)
    {
        if ('Y' === $priceType) {//计价
            return $weight * $goldenPrice;
        } else {
            return $price;
        }
    }

    /**
     * 获取工费.
     */
    public function getLaborPrice($productType, $laborPrice)
    {
        if ('GA' === $productType || 'PA' === $productType) {//计价
            return $laborPrice;
        } else {
            return 0;
        }
    }

    /**
     * 获取直接折扣.
     */
    public function getDisCount($styleCatalogItem, $offerList)
    {
        //取出直接折扣
        $directOffer = [];
        $directOffer['discount'] = '';
        $directOffer['rule_name'] = '';
        $directOffer['type'] = '';
        if (isset($offerList[$styleCatalogItem])) {
            $directOffer['discount'] = $offerList[$styleCatalogItem]['discount'];
            $directOffer['rule_name'] = $offerList[$styleCatalogItem]['rule_name'];
            $directOffer['type'] = $offerList[$styleCatalogItem]['type'];
        }

        return $directOffer;
    }

    /**
     * 获取商品资料.
     */
    public function getGoodsData($spuData, $skuData)
    {
        $standardList = [];
        $usageCode = $spuData['usage_code'];
        $productType = $spuData['product_type'];
        switch ($usageCode) {
            case 'B':
                if (in_array($productType, ['GF', 'PF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '长度:' . $skuData['length'] . '厘米';
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['GA', 'PA', 'GO'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '长度:' . $skuData['length'] . '厘米';
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    $dtObj[] = '工费:￥' . $skuData['labor_price'];
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['FJ', 'SF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '长度:' . $skuData['length'] . '厘米';
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['DF', 'DI', 'XF', 'GS', 'QF', 'SS', 'TF', 'PL'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '长度:' . $skuData['length'] . '厘米';
                    if ($skuData['certificates']) {
                        $certificates = json_decode($skuData['certificates'], true);
                        $dtObj[] = '珠宝鉴定证书:' . $this->setCertificates($certificates);
                    }
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;

                    if (isset($skuData['final_boms']) && !empty($skuData['final_boms'])) {
                        $commonGoodsData = $this->setCommonGoodsData($skuData['final_boms']);
                        if (!empty($commonGoodsData)) {
                            $standardList = array_merge($standardList, $commonGoodsData);
                        }
                    }
                }
                break;
            case 'C':
                if (in_array($productType, ['GF', 'PF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:不低于' . $skuData['weight'] . '克';
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                }
                break;
            case 'D':
                if (in_array($productType, ['GF', 'PF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['GB', 'GI'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    $dtObj[] = '工费:￥' . $skuData['labor_price'];
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                }
                break;
            case 'E':
                if (in_array($productType, ['GF', 'PF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    if ($skuData['earrings']) {
                        $dtObj[] = '耳壁材质:' . $skuData['earrings'];
                    }
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['GA', 'PA', 'GO'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    if ($skuData['earrings']) {
                        $dtObj[] = '耳壁材质:' . $skuData['earrings'];
                    }
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    $dtObj[] = '工费:￥' . $skuData['labor_price'];
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['FJ', 'SF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    if ($skuData['earrings']) {
                        $dtObj[] = '耳壁材质:' . $skuData['earrings'];
                    }
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['DF', 'DI', 'XF', 'GS', 'QF', 'SS', 'TF', 'PL'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    if ($skuData['earrings']) {
                        $dtObj[] = '耳壁材质:' . $skuData['earrings'];
                    }
                    if ($skuData['certificates']) {
                        $certificates = json_decode($skuData['certificates'], true);
                        $dtObj[] = '珠宝鉴定证书:' . $this->setCertificates($certificates);
                    }
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;

                    if (isset($skuData['final_boms']) && !empty($skuData['final_boms'])) {
                        $commonGoodsData = $this->setCommonGoodsData($skuData['final_boms']);
                        if (!empty($commonGoodsData)) {
                            $standardList = array_merge($standardList, $commonGoodsData);
                        }
                    }
                }
                break;
            case 'H':
                if (in_array($productType, ['FJ', 'SF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['DF', 'DI', 'XF', 'GS', 'QF', 'SS', 'TF', 'PL'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    if ($skuData['certificates']) {
                        $certificates = json_decode($skuData['certificates'], true);
                        $dtObj[] = '珠宝鉴定证书:' . $this->setCertificates($certificates);
                    }
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;

                    if (isset($skuData['final_boms']) && !empty($skuData['final_boms'])) {
                        $commonGoodsData = $this->setCommonGoodsData($skuData['final_boms']);
                        if (!empty($commonGoodsData)) {
                            $standardList = array_merge($standardList, $commonGoodsData);
                        }
                    }
                }
                break;
            case 'K':
                if (in_array($productType, ['GF', 'PF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '尺码:' . $skuData['size'];
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['GA', 'PA', 'GO'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '尺码:' . $skuData['size'];
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    $dtObj[] = '工费:￥' . $skuData['labor_price'];
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['FJ', 'SF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '尺码:' . $skuData['size'];
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['DF', 'DI', 'XF', 'GS', 'QF', 'SS', 'TF', 'PL'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '尺码:' . $skuData['size'];
                    if ($skuData['certificates']) {
                        $certificates = json_decode($skuData['certificates'], true);
                        $dtObj[] = '珠宝鉴定证书:' . $this->setCertificates($certificates);
                    }
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;

                    if (isset($skuData['final_boms']) && !empty($skuData['final_boms'])) {
                        $commonGoodsData = $this->setCommonGoodsData($skuData['final_boms']);
                        if (!empty($commonGoodsData)) {
                            $standardList = array_merge($standardList, $commonGoodsData);
                        }
                    }
                }
                break;
            case 'M':
                if (in_array($productType, ['GF', 'PF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '长度:' . $skuData['length'] . '厘米';
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['GA', 'PA', 'GO'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '长度:' . $skuData['length'] . '厘米';
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    $dtObj[] = '工费:￥' . $skuData['labor_price'];
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['FJ', 'SF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '长度:' . $skuData['length'] . '厘米';
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['DF', 'DI', 'XF', 'GS', 'QF', 'SS', 'TF', 'PL'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '长度:' . $skuData['length'] . '厘米';
                    if ($skuData['certificates']) {
                        $certificates = json_decode($skuData['certificates'], true);
                        $dtObj[] = '珠宝鉴定证书:' . $this->setCertificates($certificates);
                    }
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;

                    if (isset($skuData['final_boms']) && !empty($skuData['final_boms'])) {
                        $commonGoodsData = $this->setCommonGoodsData($skuData['final_boms']);
                        if (!empty($commonGoodsData)) {
                            $standardList = array_merge($standardList, $commonGoodsData);
                        }
                    }
                }
                break;
            case 'N':
                if (in_array($productType, ['GF', 'PF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '长度:' . $skuData['length'] . '厘米';
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['GA', 'PA', 'GO'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '长度:' . $skuData['length'] . '厘米';
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    $dtObj[] = '工费:￥' . $skuData['labor_price'];
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['FJ', 'SF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '长度:' . $skuData['length'] . '厘米';
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['DF', 'DI', 'XF', 'GS', 'QF', 'SS', 'TF', 'PL'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '长度:' . $skuData['length'] . '厘米';
                    if ($skuData['certificates']) {
                        $certificates = json_decode($skuData['certificates'], true);
                        $dtObj[] = '珠宝鉴定证书:' . $this->setCertificates($certificates);
                    }
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;

                    if (isset($skuData['final_boms']) && !empty($skuData['final_boms'])) {
                        $commonGoodsData = $this->setCommonGoodsData($skuData['final_boms']);
                        if (!empty($commonGoodsData)) {
                            $standardList = array_merge($standardList, $commonGoodsData);
                        }
                    }
                }
                break;
            case 'O':
                if (in_array($productType, ['GF', 'PF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['GA', 'PA', 'GO'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    $dtObj[] = '工费:￥' . $skuData['labor_price'];
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                }
                break;
            case 'P':
                if (in_array($productType, ['GF', 'PF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '项链:不包含';
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['GA', 'PA', 'GO'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '项链:不包含';
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    $dtObj[] = '工费:￥' . $skuData['labor_price'];
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['FJ', 'SF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '项链:不包含';
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['DF', 'DI', 'XF', 'GS', 'QF', 'SS', 'TF', 'PL'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '项链:不包含';
                    if ($skuData['certificates']) {
                        $certificates = json_decode($skuData['certificates'], true);
                        $dtObj[] = '珠宝鉴定证书:' . $this->setCertificates($certificates);
                    }
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;

                    if (isset($skuData['final_boms']) && !empty($skuData['final_boms'])) {
                        $commonGoodsData = $this->setCommonGoodsData($skuData['final_boms']);
                        if (!empty($commonGoodsData)) {
                            $standardList = array_merge($standardList, $commonGoodsData);
                        }
                    }
                }
                break;
            case 'R':
                if (in_array($productType, ['GF', 'PF', 'MP'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '圈度:' . $skuData['ring_size'];
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['GA', 'PA', 'GO'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '圈度:' . $skuData['ring_size'];
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    $dtObj[] = '工费:￥' . $skuData['labor_price'];
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['FJ', 'SF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '圈度:' . $skuData['ring_size'];
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['DF', 'DI', 'XF', 'GS', 'QF', 'SS', 'TF', 'PL'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '圈度:' . $skuData['ring_size'];
                    if ($skuData['certificates']) {
                        $certificates = json_decode($skuData['certificates'], true);
                        $dtObj[] = '珠宝鉴定证书:' . $this->setCertificates($certificates);
                    }
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;

                    if (isset($skuData['final_boms']) && !empty($skuData['final_boms'])) {
                        $commonGoodsData = $this->setCommonGoodsData($skuData['final_boms']);
                        if (!empty($commonGoodsData)) {
                            $standardList = array_merge($standardList, $commonGoodsData);
                        }
                    }
                }
                break;
            case 'S':
                if (in_array($productType, ['GF', 'PF'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                } elseif (in_array($productType, ['GB', 'GI'])) {
                    $dto = [];
                    $dto['title'] = self::$usage[$usageCode] . '资料';
                    $dtObj[] = '款号:#' . $spuData['section'];
                    $dtObj[] = '材质:' . $spuData['gold_type'];
                    $dtObj[] = '重量:约' . $skuData['weight'] . '克';
                    $dtObj[] = '价格类型:' . $this->getPriceTypeName($spuData['price_type']);
                    $dtObj[] = '工费:￥' . $skuData['labor_price'];
                    if ($skuData['product_part']) {
                        $dtObj[] = '配件:' . $skuData['product_part'];
                    }
                    $dto['value'] = $dtObj;
                    $standardList[] = $dto;
                }
                break;
        }

        return $standardList;
    }

    public function setCommonGoodsData($boms)
    {
        $boms = json_decode($boms, true);
        $standardList = [];
        foreach ($boms as $finalBom) {
            $tmpBom = [];
            $tmpDtObj = [];
            if (in_array($finalBom['material_code'], array_keys($this->diamondMetal))) {
                $tmpBom['title'] = '钻石资料';
                $tmpDtObj[] = '宝石物料:' . $this->diamondMetal[$finalBom['material_code']];
                if (isset($finalBom['shape']) && !empty($finalBom['shape'])) {
                    $tmpDtObj[] = '形状/切工:' . $finalBom['shape'];
                }
                if (isset($finalBom['color']) && !empty($finalBom['color'])) {
                    $tmpDtObj[] = '颜色级别:' . $finalBom['color'];
                }
                if (isset($finalBom['clarity_code']) && !empty($finalBom['clarity_code'])) {
                    $tmpDtObj[] = '净度级别:' . $finalBom['clarity_code'];
                }
                $tmpDtObj[] = '钻石数量:' . $finalBom['qty'];
                $tmpDtObj[] = '总克拉重:' . $finalBom['bom_weight'] . '克拉';
                if (isset($finalBom['certificates']) && !empty($finalBom['certificates'])) {
                    $certificates = $finalBom['certificates'];
                    $tmpDtObj[] = '鉴定证书:' . $this->setCertificates($certificates);
                }
                $tmpBom['value'] = $tmpDtObj;
            } elseif (in_array($finalBom['material_code'], array_keys($this->pearlMetal))) {
                $tmpBom['title'] = $this->pearlMetal[$finalBom['material_code']] . '资料';
                $tmpDtObj[] = '宝石物料:' . $this->pearlMetal[$finalBom['material_code']];
                if (isset($finalBom['color']) && !empty($finalBom['color'])) {
                    $tmpDtObj[] = '颜色:' . $finalBom['color'];
                }
                if (isset($finalBom['shape']) && !empty($finalBom['shape'])) {
                    $tmpDtObj[] = '形状:' . $finalBom['shape'];
                }
                $tmpDtObj[] = '珍珠数量:' . $finalBom['qty'];
                if (isset($finalBom['pearl_size']) && !empty($finalBom['pearl_size'])) {
                    $tmpDtObj[] = '珍珠尺寸:' . $finalBom['pearl_size'] . '克拉';
                }
                $tmpBom['value'] = $tmpDtObj;
            } else {
                $tmpBom['title'] = $this->gemMetal[$finalBom['material_code']] . '资料';
                $tmpDtObj[] = '宝石物料:' . $this->gemMetal[$finalBom['material_code']];
                if (isset($finalBom['shape']) && !empty($finalBom['shape'])) {
                    $tmpDtObj[] = '形状/切工:' . $finalBom['shape'];
                }
                $tmpDtObj[] = '宝石数量:' . $finalBom['qty'];
                $tmpDtObj[] = '总克拉重:' . $finalBom['bom_weight'] . '克拉';
                $tmpBom['value'] = $tmpDtObj;
            }

            $standardList[] = $tmpBom;
        }

        return $standardList;
    }

    /**
     * 获取金价.
     */
    public function getGoldenPrice($goldenPrices, $productType)
    {
        switch ($productType) {
            case 'GA':
                return $goldenPrices['goldRates']['G_JW_SELL']['price'];
            case 'PA':
                return $goldenPrices['goldRates']['PT950_JW_SELL']['price'];
            case 'GB':
                return $goldenPrices['goldRates']['G_BAR_SELL']['price'];
            case 'GI':
                return $goldenPrices['goldRates']['006']['price'];
            default:
                return 0;
        }
    }

    public function displayOptionTrans($displayKeys)
    {
        $return = [];
        foreach ($displayKeys as $displayKey) {
            $tmpDisplayVal = isset(self::$optionTrans[$displayKey]) ? self::$optionTrans[$displayKey] : $displayKey;
            $return[] = ['key' => $displayKey, 'name' => $tmpDisplayVal['name']];
        }

        return $return;
    }

    /**
     * 对克重范围key作特殊处理，返回为克重key，方便前端作门店默认选中.
     */
    public function finalDisplayOption($options)
    {
        $return = [];
        if (!empty($options)) {
            foreach ($options as $key => $option) {
                if ('weight_virtual' === $option['key']) {
                    $options[$key]['key'] = 'weight';
                }
            }
        }

        return $return;
    }

    /**
     * 获取选择维度.
     */
    public function getDisplayOption($prodType, $usage, $isSpecDisplay, $withMDiamond = false)
    {
        if ('1' == $isSpecDisplay) {
            switch ($prodType) {
                case 'GF':
                    switch ($usage) {
                        case 'C':
                            return ['cstandard'];
                    }
            }
        } else {
            switch ($prodType) {
                case 'GA':
                    switch ($usage) {
                        case 'B':
                            return ['length', 'weight'];
                        case 'D':
                            return ['weight'];
                        case 'E':
                            return ['weight'];
                        case 'H':
                            return ['weight'];
                        case 'K':
                            return ['size', 'weight'];
                        case 'M':
                            return ['length', 'weight'];
                        case 'N':
                            return ['length', 'weight'];
                        case 'P':
                            return ['weight'];
                        case 'R':
                            return ['ring_size', 'weight'];
                        case 'S':
                            return ['weight'];
                    }
                    break;
                case 'PA':
                    switch ($usage) {
                        case 'B':
                            return ['length', 'weight'];
                        case 'D':
                            return ['weight'];
                        case 'E':
                            return ['weight'];
                        case 'H':
                            return ['weight'];
                        case 'K':
                            return ['size', 'weight'];
                        case 'M':
                            return ['length', 'weight'];
                        case 'N':
                            return ['length', 'weight'];
                        case 'P':
                            return ['weight'];
                        case 'R':
                            return ['ring_size', 'weight'];
                        case 'S':
                            return ['weight'];
                    }
                    break;
                case 'GB':
                    switch ($usage) {
                        case 'D':
                            return ['weight'];
                        case 'S':
                            return ['weight'];
                    }
                    break;
                case 'GI':
                    switch ($usage) {
                        case 'D':
                            return ['weight'];
                        case 'S':
                            return ['weight'];
                    }
                    break;
                case 'GF':
                    switch ($usage) {
                        case 'B':
                            return ['length'];
                        case 'D':
                            return ['weight'];
                        case 'K':
                            return ['size'];
                        case 'M':
                            return ['length'];
                        case 'N':
                            return ['length'];
                        case 'R':
                            return ['ring_size'];
                    }
                    break;
                case 'PF':
                    switch ($usage) {
                        case 'B':
                            return ['length'];
                        case 'D':
                            return ['weight'];
                        case 'K':
                            return ['size'];
                        case 'M':
                            return ['length'];
                        case 'N':
                            return ['length'];
                        case 'R':
                            return ['ring_size'];
                    }
                    break;
                case 'MP':
                    switch ($usage) {
                        case 'B':
                            return ['length'];
                        case 'K':
                            return ['size'];
                        case 'M':
                            return ['length'];
                        case 'N':
                            return ['length'];
                        case 'R':
                            return ['ring_size'];
                    }
                    break;
                case 'DF':
                    switch ($usage) {
                        case 'B':
                            return ['length'];
                        case 'K':
                            return ['size'];
                        case 'M':
                            return ['length'];
                        case 'N':
                            return ['length'];
                        case 'R':
                            return ['ring_size'];
                    }
                    break;
                case 'DI':
                    switch ($usage) {
                        case 'B':
                            if ($withMDiamond) {
                                return ['length', 'diamond_set'];
                            } else {
                                return ['length'];
                            }
                        // no break
                        case 'E':
                            if ($withMDiamond) {
                                return ['diamond_set'];
                            }
                            break;
                        case 'H':
                            if ($withMDiamond) {
                                return ['diamond_set'];
                            }
                            break;
                        case 'K':
                            if ($withMDiamond) {
                                return ['size', 'diamond_set'];
                            } else {
                                return ['size'];
                            }
                        // no break
                        case 'M':
                            if ($withMDiamond) {
                                return ['length', 'diamond_set'];
                            } else {
                                return ['length'];
                            }
                        // no break
                        case 'N':
                            if ($withMDiamond) {
                                return ['length', 'diamond_set'];
                            } else {
                                return ['length'];
                            }
                        // no break
                        case 'P':
                            if ($withMDiamond) {
                                return ['diamond_set'];
                            }
                            break;
                        case 'R':
                            if ($withMDiamond) {
                                return ['ring_size', 'diamond_set'];
                            } else {
                                return ['ring_size'];
                            }
                    }
                    break;
                case 'XF':
                    switch ($usage) {
                        case 'B':
                            return ['length'];
                        case 'K':
                            return ['size'];
                        case 'M':
                            return ['length'];
                        case 'N':
                            return ['length'];
                        case 'R':
                            return ['ring_size'];
                    }
                    break;
                case 'GS':
                    switch ($usage) {
                        case 'B':
                            return ['length'];
                        case 'H':
                            return ['price'];
                        case 'K':
                            return ['size'];
                        case 'M':
                            return ['length'];
                        case 'N':
                            return ['length'];
                        case 'R':
                            return ['ring_size'];
                    }
                    break;
                case 'QF':
                    switch ($usage) {
                        case 'B':
                            return ['length'];
                        case 'K':
                            return ['size'];
                        case 'M':
                            return ['length'];
                        case 'N':
                            return ['length'];
                        case 'R':
                            return ['ring_size'];
                    }
                    break;
                case 'SS':
                    switch ($usage) {
                        case 'B':
                            return ['length'];
                        case 'H':
                            return ['price'];
                        case 'K':
                            return ['size'];
                        case 'M':
                            return ['length'];
                        case 'N':
                            return ['length'];
                        case 'R':
                            return ['ring_size'];
                    }
                    break;
                case 'TF':
                    switch ($usage) {
                        case 'B':
                            return ['length'];
                        case 'K':
                            return ['size'];
                        case 'M':
                            return ['length'];
                        case 'N':
                            return ['length'];
                        case 'R':
                            return ['ring_size'];
                    }
                    break;
                case 'PL':
                    switch ($usage) {
                        case 'B':
                            return ['length'];
                        case 'H':
                            return ['price'];
                        case 'K':
                            return ['size'];
                        case 'M':
                            return ['length'];
                        case 'N':
                            return ['length'];
                        case 'R':
                            return ['ring_size'];
                    }
                    break;
                case 'FJ':
                    switch ($usage) {
                        case 'B':
                            return ['length'];
                        case 'K':
                            return ['size'];
                        case 'M':
                            return ['length'];
                        case 'N':
                            return ['length'];
                        case 'R':
                            return ['ring_size'];
                    }
                    break;
                case 'SF':
                    switch ($usage) {
                        case 'B':
                            return ['length'];
                        case 'K':
                            return ['size'];
                        case 'M':
                            return ['length'];
                        case 'N':
                            return ['length'];
                        case 'R':
                            return ['ring_size'];
                    }
                    break;
                case 'CHARME':
                    switch ($usage) {
                        case 'CH':
                            return ['style', 'length'];
                    }
                    break;
            }
        }

        return [];
    }

    /**
     * 获取选择维度.
     */
    public function getDoorDisplayOption($prodType, $usage, $isSpecDisplay, $withMDiamond = false)
    {
        if ('1' == $isSpecDisplay) {
            switch ($prodType) {
                case 'GF':
                    switch ($usage) {
                        case 'C':
                            return ['cstandard'];
                    }
            }
        } else {
            switch ($prodType) {
                case 'GA':
                    switch ($usage) {
                        case 'B':
                            return ['length', 'weight_virtual'];
                        case 'E':
                            return ['weight_virtual'];
                        case 'K':
                            return ['size', 'weight_virtual'];
                        case 'N':
                            return ['length', 'weight_virtual'];
                        case 'P':
                            return ['weight_virtual'];
                        case 'R':
                            return ['ring_size', 'weight_virtual'];
                    }
                    break;
                case 'PA':
                    switch ($usage) {
                        case 'B':
                            return ['length', 'weight_virtual'];
                        case 'E':
                            return ['weight_virtual'];
                        case 'K':
                            return ['size', 'weight_virtual'];
                        case 'N':
                            return ['length', 'weight_virtual'];
                        case 'P':
                            return ['weight_virtual'];
                        case 'R':
                            return ['ring_size', 'weight_virtual'];
                    }
                    break;
                case 'GB':
                    switch ($usage) {
                        case 'D':
                            return ['weight'];
                    }
                    break;
                case 'GI':
                    switch ($usage) {
                        case 'S':
                            return ['weight'];
                    }
                    break;
                case 'GF':
                    switch ($usage) {
                        case 'B':
                            return ['length'];
                        case 'D':
                            return ['weight'];
                        case 'K':
                            return ['size'];
                        case 'M':
                            return ['length'];
                        case 'N':
                            return ['length'];
                        case 'R':
                            return ['ring_size'];
                    }
                    break;
                case 'PF':
                case 'MP':
                case 'DF':
                    switch ($usage) {
                        case 'B':
                            return ['length'];
                        case 'K':
                            return ['size'];
                        case 'M':
                            return ['length'];
                        case 'N':
                            return ['length'];
                        case 'R':
                            return ['ring_size'];
                    }
                    break;
                case 'DI':
                    switch ($usage) {
                        case 'B':
                            return ['length'];
                        case 'E':
                            if ($withMDiamond) {
                                return ['diamond_set'];
                            }
                            break;
                        case 'K':
                            return ['size'];
                        case 'M':
                            return ['length'];
                        case 'N':
                            if ($withMDiamond) {
                                return ['diamond_set', 'length'];
                            } else {
                                return ['length'];
                            }
                        // no break
                        case 'P':
                            if ($withMDiamond) {
                                return ['diamond_set'];
                            }
                            break;
                        case 'R':
                            if ($withMDiamond) {
                                return ['diamond_set', 'ring_size'];
                            } else {
                                return ['ring_size'];
                            }
                    }
                    break;
                case 'XF':
                case 'GS':
                case 'QF':
                case 'SS':
                case 'TF':
                case 'PL':
                case 'FJ':
                case 'SF':
                    switch ($usage) {
                        case 'B':
                            return ['length'];
                        case 'K':
                            return ['size'];
                        case 'M':
                            return ['length'];
                        case 'N':
                            return ['length'];
                        case 'R':
                            return ['ring_size'];
                    }
                    break;
            }
        }

        return [];
    }

    /**
     * 设置维度的值.
     */
    public function setSkuOption($options, $rawSkuInfo, $productType, $laborPrice)
    {
        $optionsValue = [];
        if ('GA' === $productType || 'PA' === $productType) {
            foreach ($options as $optionObj) {
                $tmpOptionVal = [];
                $optionKey = $optionObj['key'];
                $tmpOptionVal['key'] = $optionKey;
                if ('weight' === $optionKey) {
                    $tmpOptionVal['value'] = (string)$rawSkuInfo[$optionKey] . self::$optionTrans[$optionKey]['unit'] . '-工费' . $laborPrice . '元';
                } else {
                    $tmpOptionVal['value'] = (string)$rawSkuInfo[$optionKey] . self::$optionTrans[$optionKey]['unit'];
                }
                $tmpOptionVal['size'] = (string)$rawSkuInfo[$optionKey];
                $optionsValue[] = $tmpOptionVal;
            }
        } else {
            foreach ($options as $optionObj) {
                $tmpOptionVal = [];
                $optionKey = $optionObj['key'];
                $tmpOptionVal['key'] = $optionKey;
                if ('diamond_set' === $optionKey) {
                    $weight = explode('/', $rawSkuInfo[$optionKey])[0];
                    $tmpOptionVal['value'] = (string)$rawSkuInfo[$optionKey] . self::$optionTrans[$optionKey]['unit'];
                    $tmpOptionVal['size'] = (string)$weight;
                } else {
                    $tmpOptionVal['value'] = (string)$rawSkuInfo[$optionKey] . self::$optionTrans[$optionKey]['unit'];
                    $tmpOptionVal['size'] = (string)$rawSkuInfo[$optionKey];
                }
                $optionsValue[] = $tmpOptionVal;
            }
        }

        return $optionsValue;
    }

    /**
     * 设置维度的值.
     */
    public function setDoorSkuOption($options, $rawSkuInfo, $weights)
    {
        $optionsValue = [];
        foreach ($options as $optionObj) {
            $tmpOptionVal = [];
            $optionKey = $optionObj['key'];
            $tmpOptionVal['key'] = $optionKey;
            if ('diamond_set' === $optionKey) {
                $weight = explode('/', $rawSkuInfo[$optionKey])[0];
                $weightRange = $this->setWeightRange($weight, true);
                $tmpOptionVal['min'] = (string)$weightRange['min'];
                $tmpOptionVal['max'] = (string)$weightRange['max'];
                $tmpOptionVal['unit'] = self::$optionTrans[$optionKey]['unit'];
                $tmpOptionVal['value'] = $tmpOptionVal['min'] . ' - ' . $tmpOptionVal['max'] . $tmpOptionVal['unit'];
            } elseif ('weight_virtual' === $optionKey) {
                $tmpOptionVal['key'] = 'weight';
                $weightRange = $this->setWeightRange($rawSkuInfo['weight']);
                $tmpOptionVal['min'] = (string)$weightRange['min'];
                $tmpOptionVal['max'] = (string)$weightRange['max'];
                $tmpOptionVal['unit'] = self::$optionTrans[$optionKey]['unit'];
                $tmpOptionVal['value'] = $tmpOptionVal['min'] . ' - ' . $tmpOptionVal['max'] . $tmpOptionVal['unit'];
            } elseif ('weight' === $optionKey) {
                $tmpOptionVal['min'] = (string)$rawSkuInfo[$optionKey];
                $tmpOptionVal['max'] = (string)$rawSkuInfo[$optionKey];
                $tmpOptionVal['unit'] = self::$optionTrans[$optionKey]['unit'];
                if (!in_array($rawSkuInfo[$optionKey], $weights)) {
                    $weights[] = $rawSkuInfo[$optionKey];
                }
                $tmpOptionVal['value'] = $tmpOptionVal['min'] . $tmpOptionVal['unit'];
            } else {
                $tmpOptionVal['min'] = (string)$rawSkuInfo[$optionKey];
                $tmpOptionVal['max'] = (string)$rawSkuInfo[$optionKey];
                $tmpOptionVal['unit'] = self::$optionTrans[$optionKey]['unit'];
                $tmpOptionVal['value'] = $tmpOptionVal['min'] . $tmpOptionVal['unit'];
            }

            $optionsValue[] = $tmpOptionVal;
        }

        return $optionsValue;
    }

    public function setWeightRange($weight, $isDI = false)
    {
        if (!$isDI) {
            if ($weight < 10) {
                $floor = floor($weight + 0.5);
                if ($weight >= $floor) {
                    $min = $floor;
                    $max = $floor + 0.49;
                } else {
                    $max = $floor - 0.01;
                    $min = $floor - 0.5;
                }

                return ['min' => number_format($min, 3), 'max' => number_format($max, 3)];
            } elseif ($weight < 20) {
                $floor = floor($weight);
                $min = $floor;
                $max = $floor + 0.99;

                return ['min' => number_format($min, 3), 'max' => number_format($max, 3)];
            } else {
                $weight = $weight / 10;
                $floor = floor($weight + 0.5);
                if ($weight >= $floor) {
                    $min = $floor;
                    $max = $floor + 0.49;
                } else {
                    $max = $floor - 0.01;
                    $min = $floor - 0.5;
                }
                $min = $min * 10;
                $max = $max * 10;

                return ['min' => number_format($min, 3), 'max' => number_format($max, 3)];
            }
        } else {
            $floor = floor($weight);
            $min = $floor;
            $max = $floor + 0.99;

            return ['min' => number_format($min, 3), 'max' => number_format($max, 3)];
        }
    }

    /**
     * 获取维度个数.
     */
    public function setDimensionCount($skus, $prodType)
    {
        $count = 0;
        if ('GA' === $prodType || 'PA' === $prodType) {
            $dCountArr = [];
            $optionsList = array_column($skus, 'option');
            foreach ($optionsList as $options) {
                foreach ($options as $option) {
                    if ('weight' !== $option['key']) {
                        if (!isset($dCountArr[$option['key']]) || !in_array($option['value'], $dCountArr[$option['key']])) {
                            $dCountArr[$option['key']][] = $option['value'];
                        }
                    }
                }
            }
            if (!empty($dCountArr)) {
                $dCountVal = array_values($dCountArr);
                $count = count($dCountVal[0]);
            }
        }

        return $count;
    }

    /**
     * 设置分类分享图.
     */
    public function getCateShareInfo($cateInfo)
    {
        $share = [];
        $share['title'] = isset($cateInfo['share_content']) && !empty($cateInfo['share_content']) ? $cateInfo['share_content'] : '';
        $share['image'] = isset($cateInfo['share_image']) && !empty($cateInfo['share_image']) ? $cateInfo['share_image'] : '';

        return $share;
    }

    /**
     * 设置分类头图.
     */
    public function getKvInfo($cateInfo)
    {
        $kv = isset($cateInfo['category_kv_image']) && !empty($cateInfo['category_kv_image']) ? $cateInfo['category_kv_image'] : '';

        return $kv;
    }

    /**
     * 设置SPU.
     */
    public function setSpuData($deProdLevelInfo, $goldenPrices, $prodDisplayStatus, $sales, $new, $offerList = [])
    {
        $spu = [];

        $spu['pdtId'] = $deProdLevelInfo['product_id'];
        $spu['name'] = $deProdLevelInfo['product_name'];
        $spu['sales'] = $sales;
        $spu['new'] = $new;
        $spu['series'] = $deProdLevelInfo['series'];
        $spu['section'] = '#' . $deProdLevelInfo['section'];
        $spu['standardDscp'] = $deProdLevelInfo['product_description'];
        $kvs = $this->getKvs($deProdLevelInfo);
        $picUrl = $kvs ? $kvs[0]['data']['src'] : 'https://wecassets.chowsangsang.com.cn/miniStore/default.jpg';
        $spu['picUrl'] = $picUrl;

        $prodSkuRelation = $this->redisModel->_zrevrange(config('redis.mappingProdSku') . '###' . $deProdLevelInfo['product_id'], 0, -1);
        $reSkusInfo = $this->redisModel->_hmget(config('redis.skuLevelInfo'), $prodSkuRelation);
        $skusInfo = [];
        $withMDiamond = false;
        if ($reSkusInfo) {
            foreach ($reSkusInfo as $skuId => $reSkuInfo) {
                if (!$reSkuInfo) {
                    continue;
                }
                $deReSkuInfo = json_decode($reSkuInfo, true);
                if (isset($deReSkuInfo['has_main_material']) && $deReSkuInfo['has_main_material']) {
                    $withMDiamond = true;
                }
                $skusInfo[$skuId] = $deReSkuInfo;
            }
        }
        $skuDisplayOpt = $this->getDisplayOption($deProdLevelInfo['product_type'], $deProdLevelInfo['usage_code'], $deProdLevelInfo['is_specDisplay'], $withMDiamond);
        $displayOption = $this->displayOptionTrans($skuDisplayOpt);
        $spu['option'] = $displayOption ?: [];

        //直接折扣信息
        $directOffer = $this->getDisCount($deProdLevelInfo['section'], $offerList);
        $disCount = 'product_discount' === $directOffer['type'] ? $directOffer['discount'] : 100;
        $spu['promotionShort'] = $directOffer['rule_name'];

        $skus = [];
        $isInventory = false;
        $minOriPrice = 0;
        $minPrice = 0;
        $brandSite = false;
        if ($skusInfo) {
            $stockInfo = $this->redisModel->_hmget(config('redis.store'), $prodSkuRelation);
            // $displayInfo = $this->redisModel->_hmget(config('redis.skuDisplay'), $prodSkuRelation);
            $goldenPrice = $this->getGoldenPrice($goldenPrices, $deProdLevelInfo['product_type']);
            $weights = [];
            $start = 0;
            $length = count($skusInfo);
            foreach ($skusInfo as $skuId => $skuInfo) {
                if (isset($skuInfo['is_brandSite']) && $skuInfo['is_brandSite'] == '1') {
                    $brandSite = true;
                }
                ++$start;
                $inventory = isset($stockInfo[$skuId]) && $stockInfo[$skuId] ? (int)$stockInfo[$skuId] : 0;
                if (0 === $inventory) {
                    if ($start !== $length || !empty($skus)) {
                        continue;
                    }
                }
                $tmpDeSkuInfo = $skuInfo;

                $tmpInSkuInfo = [];
                $tmpInSkuInfo['skuId'] = $tmpDeSkuInfo['sku'];
                // if ('1' !== $displayInfo[$skuId]) {
                //     continue;
                // }
                $tmpInSkuInfo['series'] = $spu['series'];
                $tmpInSkuInfo['name'] = $spu['name'];
                $tmpInSkuInfo['weight'] = $tmpDeSkuInfo['weight'];
                $tmpInSkuInfo['picUrl'] = $picUrl;
                $tmpInSkuInfo['laborPrice'] = $this->getLaborPrice($deProdLevelInfo['product_type'], $tmpDeSkuInfo['labor_price']);
                $tmpInSkuInfo['oriPrice'] = $this->getSkuPrice($deProdLevelInfo['price_type'], $deProdLevelInfo['product_type'], $tmpDeSkuInfo['price'], $tmpDeSkuInfo['weight'], $goldenPrice, $tmpInSkuInfo['laborPrice']);
                if (0 === $minOriPrice) {
                    $minOriPrice = $tmpInSkuInfo['oriPrice'];
                } else {
                    $minOriPrice = $tmpInSkuInfo['oriPrice'] < $minOriPrice ? $tmpInSkuInfo['oriPrice'] : $minOriPrice;
                }
                $tmpInSkuInfo['price'] = $this->getSkuPrice($deProdLevelInfo['price_type'], $deProdLevelInfo['product_type'], $tmpDeSkuInfo['price'], $tmpDeSkuInfo['weight'], $goldenPrice, $tmpInSkuInfo['laborPrice'], $disCount);
                if (0 === $minPrice) {
                    $minPrice = $tmpInSkuInfo['price'];
                } else {
                    $minPrice = $tmpInSkuInfo['price'] < $minPrice ? $tmpInSkuInfo['price'] : $minPrice;
                }
                $tmpInSkuInfo['inventory'] = $inventory;
                if ($tmpInSkuInfo['inventory']) {
                    $isInventory = true;
                }
                $tmpInSkuInfo['option'] = $this->setSkuOption($spu['option'], $tmpDeSkuInfo, $deProdLevelInfo['product_type'], $tmpInSkuInfo['laborPrice']);
                $tmpInSkuInfo['standardList'] = $this->getGoodsData($deProdLevelInfo, $tmpDeSkuInfo);
                $skus[] = $tmpInSkuInfo;
                $weights[] = $tmpDeSkuInfo['weight'];
            }
        }
        $spu['brandSite'] = $brandSite;
        $latestSkus = [];
        //推货筛选
        if (!empty($skus)) {
            $dimensionCount = $this->setDimensionCount($skus, $deProdLevelInfo['product_type']);
            $latestSkus = $this->setRealSkus($skus, $weights, $deProdLevelInfo['usage_code'], $deProdLevelInfo['product_type'], $dimensionCount, $withMDiamond);
        }
        $spu['skus'] = $latestSkus;
        $spu['oriPrice'] = $minOriPrice;
        $spu['price'] = $minPrice;
        $spu['isInventory'] = $isInventory;
        $spu['isDisplay'] = '1' === $prodDisplayStatus ? true : false;
        $spu['isStoreLocator'] = 1 === $deProdLevelInfo['is_storeLocator'] ? true : false;

        return $spu;
    }

    /**
     * 设置门店SPU.
     */
    public function setDoorsSpuData($deProdLevelInfo)
    {
        $spu = [];

        $spu['pdtId'] = $deProdLevelInfo['product_id'];
        $spu['name'] = $deProdLevelInfo['product_name'];
        $spu['series'] = $deProdLevelInfo['series'];
        $spu['section'] = '#' . $deProdLevelInfo['section'];
        $kvs = $this->getKvs($deProdLevelInfo);
        $picUrl = $kvs ? $kvs[0]['data']['src'] : 'https://wecassets.chowsangsang.com.cn/miniStore/default.jpg';
        $spu['picUrl'] = $picUrl;

        $prodSkuRelation = $this->redisModel->_zrevrange(config('redis.mappingProdDSku') . '###' . $deProdLevelInfo['product_id'], 0, -1);
        $reSkusInfo = $this->redisModel->_hmget(config('redis.dSkuLevelInfo'), $prodSkuRelation);
        $skusInfo = [];
        $withMDiamond = false;
        if ($reSkusInfo) {
            foreach ($reSkusInfo as $skuId => $reSkuInfo) {
                if (!$reSkuInfo) {
                    continue;
                }
                $deReSkuInfo = json_decode($reSkuInfo, true);
                if (isset($deReSkuInfo['has_main_material']) && $deReSkuInfo['has_main_material']) {
                    $withMDiamond = true;
                }
                $skusInfo[$skuId] = $deReSkuInfo;
            }
        }
        $skuDisplayOpt = $this->getDoorDisplayOption($deProdLevelInfo['product_type'], $deProdLevelInfo['usage_code'], $deProdLevelInfo['is_specDisplay'], $withMDiamond);
        $displayOption = $this->displayOptionTrans($skuDisplayOpt);

        $skus = [];
        $weights = [];
        if ($skusInfo) {
            $start = 0;
            $length = count($skusInfo);
            foreach ($skusInfo as $skuId => $skuInfo) {
                $tmpInSkuInfo = [];
                $tmpInSkuInfo['skuId'] = $skuInfo['sku'];
                $tmpInSkuInfo['series'] = $spu['series'];
                $tmpInSkuInfo['name'] = $spu['name'];
                $tmpInSkuInfo['weight'] = $skuInfo['weight'];
                $tmpInSkuInfo['picUrl'] = $picUrl;
                $tmpInSkuInfo['option'] = $this->setDoorSkuOption($displayOption, $skuInfo, $weights);
                $skus[] = $tmpInSkuInfo;
            }
        }
        $spu['option'] = $this->finalDisplayOption($displayOption);

        if ('GF' === $deProdLevelInfo['product_type'] && 'D' === $deProdLevelInfo['usage_code'] && count($weights) <= 1) {
            $spu['option'] = [];
            foreach ($skus as &$tmpSku) {
                $tmpSku['option'] = [];
            }
        }
        $spu['skus'] = $skus;

        return $spu;
    }

    /**
     * 获取头图.
     */
    public function getKvs($deProdLevelInfo)
    {
        $kvs = [];

        $kvs = isset($deProdLevelInfo['kv_images']) && !empty($deProdLevelInfo['kv_images']) ? $deProdLevelInfo['kv_images'] : [];

        return $kvs;
    }

    /**
     * 获取图文详情.
     */
    public function getDetails($deProdLevelInfo, $commonImgs)
    {
        $kvs = [];

        $kvs = isset($deProdLevelInfo['detail_images']) && !empty($deProdLevelInfo['detail_images']) ? $deProdLevelInfo['detail_images'] : [];

        if (false !== $commonImgs) {
            $deCommonImgs = json_decode($commonImgs, true);
            $kvs = array_merge($kvs, $deCommonImgs);
        }

        return $kvs;
    }

    /**
     * 赚取悦享钱.
     */
    public function getPointPer($price, $productType)
    {
        if (in_array($productType, ['GA', 'PA'])) {
            return 1;
        } elseif (in_array($productType, ['GF', 'PF', 'MP', 'DF', 'DI', 'XF', 'GS', 'QF', 'SS', 'TF', 'PL', 'FJ', 'SF'])) {
            return 5;
        } else {
            return 0;
        }
    }

    /**
     * 最高可使用悦享钱.
     */
    public function getPointMax($price, $productType)
    {
        if (in_array($productType, ['GF', 'PF', 'MP', 'DF', 'DI', 'XF', 'GS', 'QF', 'SS', 'TF', 'PL', 'FJ', 'SF'])) {
            if ($price >= 3000) {
                return (int)ceil($price * 0.5);
            } elseif ($price >= 1000 && $price < 3000) {
                return (int)ceil($price * 0.3);
            } else {
                return 0;
            }
        } elseif (in_array($productType, ['CHARME'])) {
            return (int)ceil($price * 0.99);
        } else {
            return 0;
        }
    }

    /**
     * 退换货规则图.
     */
    public function getDelivery()
    {
        $deReturn = $this->redisModel->_hget(config('redis.sysConfig'), 'return');
        $delivery = false !== $deReturn ? $deReturn : '';

        return $delivery;
    }

    /**
     * 证书展示结构生成.
     */
    public function setCertificates($certificates)
    {
        $certStr = '';
        $certArr = [];
        if (!empty($certificates)) {
            foreach ($certificates as $certificateObj) {
                $tmpCertStr = '';
                if (empty($certificateObj['certificateNumber'])) {
                    continue;
                }
                $tmpCertStr = $certificateObj['certificateNumber'];
                if (!empty($certificateObj['organization'])) {
                    $tmpCertStr .= '(' . $certificateObj['organization'] . ')';
                }
                $certArr[] = $tmpCertStr;
            }
        }
        if (!empty($certArr)) {
            $certStr = implode("\n", $certArr);
        }

        return $certStr;
    }

    /**
     * 设定产品的筛选条件.
     */
    public function getGoldenTypeFilter($productList, &$fillterMetal = [], &$fillterJewel = [])
    {
        $allGoldenType = array_column($productList, 'gold_type_code');
        $uniqueGoldenType = !empty($allGoldenType) ? array_unique($allGoldenType) : [];
        $allProductType = array_column($productList, 'product_type');
        $uniqueProductType = !empty($allProductType) ? array_unique($allProductType) : [];
        $fillterMetal = [];
        foreach (self::$filter['goldenType'] as $goldenKey => $goldenInfo) {
            $tmpFillterMetal = [];
            $tmpFillterMetal['key'] = $goldenKey;
            $tmpFillterMetal['name'] = $goldenInfo['name'];
            $tmpFillterMetal['able'] = false;
            if (array_intersect($uniqueGoldenType, $goldenInfo['include'])) {
                $tmpFillterMetal['able'] = true;
            }
            $fillterMetal[] = $tmpFillterMetal;
        }
        $fillterJewel = [];
        foreach (self::$filter['productType'] as $pTypeKey => $pTypeInfo) {
            $tmpFillterJewel = [];
            $tmpFillterJewel['key'] = $pTypeKey;
            $tmpFillterJewel['name'] = $pTypeInfo['name'];
            $tmpFillterJewel['able'] = false;
            if (array_intersect($uniqueProductType, $pTypeInfo['include'])) {
                $tmpFillterJewel['able'] = true;
            }
            $fillterJewel[] = $tmpFillterJewel;
        }
    }

    /**
     * 产品筛选.
     */
    public function filterSpuData($deProdInfo, $fillterCategory = ['all'], $fillterMetal = [], $fillterJewel = [])
    {
        if ($fillterCategory !== ['all']) {
            if (!in_array($deProdInfo['usage_code'], $fillterCategory)) {
                return false;
            }
        }
        if (!empty($fillterMetal)) {
            $inArray = false;
            foreach ($fillterMetal as $filterKey) {
                if (in_array($deProdInfo['gold_type_code'], self::$filter['goldenType'][$filterKey]['include'])) {
                    $inArray = true;
                }
            }
            if (!$inArray) {
                return false;
            }
        }
        if (!empty($fillterJewel)) {
            $inArray = false;
            foreach ($fillterJewel as $filterKey) {
                if (in_array($deProdInfo['product_type'], self::$filter['productType'][$filterKey]['include'])) {
                    $inArray = true;
                }
            }
            if (!$inArray) {
                return false;
            }
        }

        return true;
    }

    /**
     * 产品筛选.
     */
    public function filterSpuDataByPrice($spu, $fillterMinPrice, $fillterMaxPrice)
    {
        if ($spu['price'] < $fillterMinPrice) {
            return false;
        }
        if ('N/A' !== $fillterMaxPrice && $spu['price'] > $fillterMaxPrice) {
            return false;
        }

        return true;
    }

    /**
     * 产品排序.
     */
    public function sortSpuList($list, $sortKey, $sort)
    {
        $sortArr = [];
        $sortIndexArr = [];
        $sortIndexArr = array_keys($list);
        $sortAction = '1' == $sort ? SORT_ASC : SORT_DESC;
        if ('price' === $sortKey) {
            $sortArr = array_column($list, 'price');
            array_multisort($sortArr, $sortAction, SORT_NUMERIC, $sortIndexArr, $sortAction, SORT_NUMERIC, $list);
        }
        if ('new' === $sortKey) {
            $sortArr = array_column($list, 'new');
            array_multisort($sortArr, $sortAction, SORT_NUMERIC, $sortIndexArr, $sortAction, SORT_NUMERIC, $list);
        }
        if ('sales' === $sortKey) {
            $sortArr = array_column($list, 'sales');
            array_multisort($sortArr, $sortAction, SORT_NUMERIC, $sortIndexArr, $sortAction, SORT_NUMERIC, $list);
        }

        return $list;
    }

    /**
     * 校验SPU库存.
     */
    public function checkSpuStock($prodId)
    {
        $prodSkuRelation = $this->redisModel->_zrevrange(config('redis.mappingProdSku') . '###' . $prodId, 0, -1);
        $stockInfo = $this->redisModel->_hmget(config('redis.store'), $prodSkuRelation);

        $isInventory = false;
        foreach ($stockInfo as $skuId => $stock) {
            if (false !== $stock && (int)$stock > 0) {
                $isInventory = true;
                break;
            }
        }

        return ['status' => $isInventory, 'skuStocks' => $stockInfo];
    }

    /**
     * 价格类型名称.
     */
    public function getPriceTypeName($code)
    {
        if ('Y' === $code) {//计价
            return '计价';
        } else {
            return '定价';
        }
    }

    /**
     * 设置推货的sku.
     */
    public function setRealSkus($skus, $weights, $usageCode, $prodType, $dimensionCount, $withMDiamond)
    {
        $realArray = [];
        array_multisort($weights, SORT_ASC, SORT_NUMERIC, $skus);
        switch ($usageCode) {
            case 'B':
                switch ($prodType) {
                    case 'GA':
                    case 'PA':
                        if (count($skus) > 6) {
                            switch ($dimensionCount) {
                                case '1':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 3, 3);
                                    break;
                                case '2':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 3, 0);
                                    break;
                                case '3':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 2, 0);
                                    break;
                                case '4':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 1, 0, 2);
                                    break;
                                case '5':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 1, 0, 1);
                                    break;
                                case '6':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 1, 0);
                                    break;
                                default:
                                    $realArray = $this->getSkusByPriceWithAll($skus);
                            }
                        } else {
                            $realArray = $this->getSkusByPriceWithAll($skus);
                        }
                        break;
                    case 'GF':
                    case 'PF':
                    case 'MP':
                    case 'DF':
                    case 'XF':
                    case 'QF':
                    case 'TF':
                    case 'SF':
                        $realArray = $this->getSkusByPriceWithAll($skus);
                        break;
                    case 'DI':
                    case 'GS':
                    case 'SS':
                    case 'PL':
                    case 'FJ':
                        $realArray = $this->getSkusByLength($skus);
                        break;
                }
                break;
            case 'C':
                $realArray = $this->getSkusByPriceWithAll($skus);
                break;
            case 'D':
                switch ($prodType) {
                    case 'GA':
                    case 'PA':
                        if (count($skus) > 6) {
                            $realArray = $this->getArrayElement($skus);
                        } else {
                            $realArray = $this->getSkusByPriceWithAll($skus);
                        }
                        break;
                    case 'GB':
                    case 'GI':
                    case 'GF':
                    case 'PF':
                        $realArray = $this->getSkusByPriceWithAll($skus);
                        break;
                }
                break;
            case 'E':
                switch ($prodType) {
                    case 'GA':
                    case 'PA':
                        if (count($skus) > 6) {
                            $realArray = $this->getArrayElement($skus);
                        } else {
                            $realArray = $this->getSkusByPriceWithAll($skus);
                        }
                        break;
                    case 'GF':
                    case 'PF':
                    case 'MP':
                    case 'DF':
                    case 'XF':
                    case 'QF':
                    case 'TF':
                    case 'SF':
                        $realArray = $this->getSkusByPriceWithAll($skus);
                        break;
                    case 'DI':
                        if ($withMDiamond) {
                            $realArray = $this->getSkusByPriceWithAll($skus);
                        } else {
                            $realArray = $this->getSkusByPrice($skus);
                        }
                        break;
                    case 'GS':
                    case 'SS':
                    case 'PL':
                    case 'FJ':
                        $realArray = $this->getSkusByPrice($skus);
                        break;
                }
                break;
            case 'H':
                switch ($prodType) {
                    case 'GA':
                    case 'PA':
                        if (count($skus) > 6) {
                            $realArray = $this->getArrayElement($skus);
                        } else {
                            $realArray = $this->getSkusByPriceWithAll($skus);
                        }
                        break;
                    case 'GF':
                    case 'PF':
                    case 'MP':
                    case 'DF':
                    case 'XF':
                    case 'QF':
                    case 'TF':
                    case 'SF':
                        $realArray = $this->getSkusByPriceWithAll($skus);
                        break;
                    case 'DI':
                    case 'GS':
                    case 'SS':
                    case 'PL':
                    case 'FJ':
                        $realArray = $this->getSkusByPrice($skus);
                        break;
                }
                break;
            case 'K':
                switch ($prodType) {
                    case 'GA':
                    case 'PA':
                        if (count($skus) > 6) {
                            switch ($dimensionCount) {
                                case '1':
                                    $realArray = $this->getSkusByRingSizeAWeight($skus, 3, 3);
                                    break;
                                case '2':
                                    $realArray = $this->getSkusByRingSizeAWeight($skus, 3, 0);
                                    break;
                                case '3':
                                    $realArray = $this->getSkusByRingSizeAWeight($skus, 2, 0);
                                    break;
                                case '4':
                                    $realArray = $this->getSkusByRingSizeAWeight($skus, 1, 0, 2);
                                    break;
                                case '5':
                                    $realArray = $this->getSkusByRingSizeAWeight($skus, 1, 0, 1);
                                    break;
                                case '6':
                                    $realArray = $this->getSkusByRingSizeAWeight($skus, 1, 0);
                                    break;
                                default:
                                    $realArray = $this->getSkusByPriceWithAll($skus);
                            }
                        } else {
                            $realArray = $this->getSkusByPriceWithAll($skus);
                        }
                        break;
                    case 'GF':
                    case 'PF':
                    case 'MP':
                    case 'DF':
                    case 'XF':
                    case 'QF':
                    case 'TF':
                    case 'SF':
                        $realArray = $this->getSkusByPriceWithAll($skus);
                        break;
                    case 'DI':
                    case 'GS':
                    case 'SS':
                    case 'PL':
                    case 'FJ':
                        $realArray = $this->getSkusByRingSize($skus);
                        break;
                }
                break;
            case 'M':
                switch ($prodType) {
                    case 'GA':
                    case 'PA':
                        if (count($skus) > 6) {
                            switch ($dimensionCount) {
                                case '1':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 3, 3);
                                    break;
                                case '2':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 3, 0);
                                    break;
                                case '3':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 2, 0);
                                    break;
                                case '4':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 1, 0, 2);
                                    break;
                                case '5':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 1, 0, 1);
                                    break;
                                case '6':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 1, 0);
                                    break;
                                default:
                                    $realArray = $this->getSkusByPriceWithAll($skus);
                            }
                        } else {
                            $realArray = $this->getSkusByPriceWithAll($skus);
                        }
                        break;
                    case 'GF':
                    case 'PF':
                    case 'MP':
                    case 'DF':
                    case 'XF':
                    case 'QF':
                    case 'TF':
                    case 'SF':
                        $realArray = $this->getSkusByPriceWithAll($skus);
                        break;
                    case 'DI':
                    case 'GS':
                    case 'SS':
                    case 'PL':
                    case 'FJ':
                        $realArray = $this->getSkusByLength($skus);
                        break;
                }
                break;
            case 'N':
                switch ($prodType) {
                    case 'GA':
                    case 'PA':
                        if (count($skus) > 6) {
                            switch ($dimensionCount) {
                                case '1':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 3, 3);
                                    break;
                                case '2':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 3, 0);
                                    break;
                                case '3':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 2, 0);
                                    break;
                                case '4':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 1, 0, 2);
                                    break;
                                case '5':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 1, 0, 1);
                                    break;
                                case '6':
                                    $realArray = $this->getSkusByLengthAWeight($skus, 1, 0);
                                    break;
                                default:
                                    $realArray = $this->getSkusByPriceWithAll($skus);
                            }
                        } else {
                            $realArray = $this->getSkusByPriceWithAll($skus);
                        }
                        break;
                    case 'GF':
                    case 'PF':
                    case 'MP':
                    case 'DF':
                    case 'XF':
                    case 'QF':
                    case 'TF':
                    case 'SF':
                        $realArray = $this->getSkusByPriceWithAll($skus);
                        break;
                    case 'DI':
                        if ($withMDiamond) {
                            $realArray = $this->getSkusByPriceWithAll($skus);
                        } else {
                            $realArray = $this->getSkusByLength($skus);
                        }
                        break;
                    case 'GS':
                    case 'SS':
                    case 'PL':
                    case 'FJ':
                        $realArray = $this->getSkusByLength($skus);
                        break;
                }
                break;
            case 'P':
                switch ($prodType) {
                    case 'GA':
                    case 'PA':
                        if (count($skus) > 6) {
                            $realArray = $this->getArrayElement($skus);
                        } else {
                            $realArray = $this->getSkusByPriceWithAll($skus);
                        }
                        break;
                    case 'GF':
                    case 'PF':
                    case 'MP':
                    case 'DF':
                    case 'XF':
                    case 'QF':
                    case 'TF':
                    case 'SF':
                        $realArray = $this->getSkusByPriceWithAll($skus);
                        break;
                    case 'DI':
                        if ($withMDiamond) {
                            $realArray = $this->getSkusByPriceWithAll($skus);
                        } else {
                            $realArray = $this->getSkusByPrice($skus);
                        }
                        break;
                    case 'GS':
                    case 'SS':
                    case 'PL':
                    case 'FJ':
                        $realArray = $this->getSkusByPrice($skus);
                        break;
                }
                break;
            case 'R':
                switch ($prodType) {
                    case 'GA':
                    case 'PA':
                        if (count($skus) > 6) {
                            switch ($dimensionCount) {
                                case '1':
                                    $realArray = $this->getSkusByRingSizeAWeight($skus, 3, 3);
                                    break;
                                case '2':
                                    $realArray = $this->getSkusByRingSizeAWeight($skus, 3, 0);
                                    break;
                                case '3':
                                    $realArray = $this->getSkusByRingSizeAWeight($skus, 2, 0);
                                    break;
                                case '4':
                                    $realArray = $this->getSkusByRingSizeAWeight($skus, 1, 0, 2);
                                    break;
                                case '5':
                                    $realArray = $this->getSkusByRingSizeAWeight($skus, 1, 0, 1);
                                    break;
                                case '6':
                                    $realArray = $this->getSkusByRingSizeAWeight($skus, 1, 0);
                                    break;
                                default:
                                    $realArray = $this->getSkusByPriceWithAll($skus);
                            }
                        } else {
                            $realArray = $this->getSkusByPriceWithAll($skus);
                        }
                        break;
                    case 'GF':
                    case 'PF':
                    case 'MP':
                    case 'DF':
                    case 'XF':
                    case 'QF':
                    case 'TF':
                    case 'SF':
                        $realArray = $this->getSkusByPriceWithAll($skus);
                        break;
                    case 'DI':
                        if ($withMDiamond) {
                            $realArray = $this->getSkusByPriceWithAll($skus);
                        } else {
                            $realArray = $this->getSkusByRingSize($skus);
                        }
                        break;
                    case 'GS':
                    case 'SS':
                    case 'PL':
                    case 'FJ':
                        $realArray = $this->getSkusByRingSize($skus);
                        break;
                }
                break;
            case 'S':
                switch ($prodType) {
                    case 'GA':
                    case 'PA':
                        if (count($skus) > 6) {
                            $realArray = $this->getArrayElement($skus);
                        } else {
                            $realArray = $this->getSkusByPriceWithAll($skus);
                        }
                        break;
                    case 'GB':
                    case 'GI':
                    case 'GF':
                    case 'PF':
                        $realArray = $this->getSkusByPriceWithAll($skus);
                        break;
                }
                break;
            case 'CH':
                switch ($prodType) {
                    case 'CHARME':
                        $realArray = $this->getSkusByPriceWithAll($skus);
                        break;
                }
                break;
        }

        return $realArray;
    }

    /**
     * 数组中取出前后3个元素.
     */
    public function getArrayElement($array, $front = 3, $end = 3)
    {
        if (empty($array)) {
            return [];
        }
        $frontElements = array_slice($array, 0, $front);
        if (0 !== $end) {
            $endElements = array_slice($array, '-' . $end);
        } else {
            $endElements = [];
        }
        $latestElements = array_merge($frontElements, $endElements);

        return $latestElements;
    }

    /**
     * 按照长度选取sku.
     */
    public function getSkusByLength($skus)
    {
        $realArray = [];
        $lengthSkus = [];
        $lengthCount = [];
        foreach ($skus as $skuKey => $sku) {
            $length = $sku['option'][0]['value'];
            if (isset($lengthSkus[$length])) {
                if ($sku['oriPrice'] < $lengthCount[$length]) {
                    $lengthSkus[$length] = $skuKey;
                    $lengthCount[$length] = $sku['oriPrice'];
                }
            } else {
                $lengthSkus[$length] = $skuKey;
                $lengthCount[$length] = $sku['oriPrice'];
            }
        }
        $skusArray = [];
        foreach ($lengthSkus as $lenSkuKey) {
            $skusArray[] = $skus[$lenSkuKey];
        }
        $realArray = array_values($skusArray);

        return $realArray;
    }

    /**
     * 全量推时候按价格排序.
     */
    public function getSkusByPriceWithAll($skus)
    {
        $realArray = [];
        $priceSkus = [];
        $priceCount = [];
        foreach ($skus as $skuKey => $sku) {
            if (!empty($sku['option'])) {
                //多维度每个维度组合推价格最便宜的sku
                $optionKeys = array_column($sku['option'], 'value');
                $optionStr = implode('###', $optionKeys);
                if (isset($priceSkus[$optionStr])) {
                    if ($sku['oriPrice'] < $priceCount[$optionStr]) {
                        $priceSkus[$optionStr] = $skuKey;
                        $priceCount[$optionStr] = $sku['oriPrice'];
                    }
                } else {
                    $priceSkus[$optionStr] = $skuKey;
                    $priceCount[$optionStr] = $sku['oriPrice'];
                }
            } else {
                //没有维度推价格最便宜的sku
                $optionStr = 'price';
                if (isset($priceSkus[$optionStr])) {
                    if ($sku['oriPrice'] < $priceCount[$optionStr]) {
                        $priceSkus[$optionStr] = $skuKey;
                        $priceCount[$optionStr] = $sku['oriPrice'];
                    }
                } else {
                    $priceSkus[$optionStr] = $skuKey;
                    $priceCount[$optionStr] = $sku['oriPrice'];
                }
            }
        }
        if (!empty($priceSkus)) {
            $skusArray = [];
            foreach ($priceSkus as $priSkuKey) {
                $skusArray[] = $skus[$priSkuKey];
            }
            $realArray = array_values($skusArray);
        }

        return $realArray;
    }

    /**
     * 按照长度选取sku.
     */
    public function getSkusByLengthAWeight($skus, $front, $end, $addition = false)
    {
        $realArray = [];
        $lengthSkus = [];
        $lengthSorts = [];
        foreach ($skus as $skuKey => $sku) {
            foreach ($sku['option'] as $option) {
                if ('weight' !== $option['key']) {
                    $length = $option['value'];
                    $lengthSkus[$length][] = $skuKey;
                    $lengthSorts[$length][] = $sku['weight'];
                }
            }
        }

        $skusArray = [];
        if (!empty($lengthSkus)) {
            foreach ($lengthSkus as $lengthKey => $lengthVal) {
                array_multisort($lengthSorts[$lengthKey], SORT_ASC, SORT_NUMERIC, $lengthVal);
                $partList = $this->getArrayElement($lengthVal, $front, $end);
                $skusArray = array_merge($skusArray, $partList);
            }
        }

        if (false === $addition) {
            if (!empty($skusArray)) {
                foreach ($skusArray as $lenSkuKey) {
                    $realArray[] = $skus[$lenSkuKey];
                }
            }
        } else {
            if (!empty($skusArray)) {
                foreach ($skusArray as $lenSkuKey) {
                    $realArray[] = $skus[$lenSkuKey];
                    unset($skus[$lenSkuKey]);
                }

                $skus = array_values($skus);
                $sorts = array_column($skus, 'weight');
                array_multisort($sorts, SORT_ASC, SORT_NUMERIC, $skus);
                $realArray[] = $skus[0];
                if (2 === $addition) {
                    $realArray[] = $skus[1];
                }
            }
        }

        return $realArray;
    }

    /**
     * 按照圈度选取sku.
     */
    public function getSkusByRingSizeAWeight($skus, $front, $end, $addition = false)
    {
        $realArray = [];
        $ringSizeSkus = [];
        $ringSizeSorts = [];
        foreach ($skus as $skuKey => $sku) {
            foreach ($sku['option'] as $option) {
                if ('weight' !== $option['key']) {
                    $ringSize = $option['value'];
                    $ringSizeSkus[$ringSize][] = $skuKey;
                    $ringSizeSorts[$ringSize][] = $sku['weight'];
                }
            }
        }

        $skusArray = [];
        if (!empty($ringSizeSkus)) {
            foreach ($ringSizeSkus as $ringSizeKey => $ringSizeVal) {
                array_multisort($ringSizeSorts[$ringSizeKey], SORT_ASC, SORT_NUMERIC, $ringSizeVal);
                $partList = $this->getArrayElement($ringSizeVal, $front, $end);
                $skusArray = array_merge($skusArray, $partList);
            }
        }

        if (false === $addition) {
            if (!empty($skusArray)) {
                foreach ($skusArray as $rsSkuKey) {
                    $realArray[] = $skus[$rsSkuKey];
                }
            }
        } else {
            if (!empty($skusArray)) {
                foreach ($skusArray as $rsSkuKey) {
                    $realArray[] = $skus[$rsSkuKey];
                    unset($skus[$rsSkuKey]);
                }

                $skus = array_values($skus);
                $sorts = array_column($skus, 'weight');
                array_multisort($sorts, SORT_ASC, SORT_NUMERIC, $skus);
                $realArray[] = $skus[0];
                if (2 === $addition) {
                    $realArray[] = $skus[1];
                }
            }
        }

        return $realArray;
    }

    /**
     * 按照圈度选取sku.
     */
    public function getSkusByRingSize($skus)
    {
        $realArray = [];
        $ringSizeSkus = [];
        $ringSizeCount = [];
        foreach ($skus as $skuKey => $sku) {
            $ringSize = $sku['option'][0]['value'];
            if (isset($ringSizeSkus[$ringSize])) {
                if ($sku['oriPrice'] < $ringSizeCount[$ringSize]) {
                    $ringSizeSkus[$ringSize] = $skuKey;
                    $ringSizeCount[$ringSize] = $sku['oriPrice'];
                }
            } else {
                $ringSizeSkus[$ringSize] = $skuKey;
                $ringSizeCount[$ringSize] = $sku['oriPrice'];
            }
        }
        $skusArray = [];
        foreach ($ringSizeSkus as $ringSizeSkuKey) {
            $skusArray[] = $skus[$ringSizeSkuKey];
        }
        $realArray = array_values($skusArray);

        return $realArray;
    }

    /**
     * 按照价格选取sku.
     */
    public function getSkusByPrice($skus)
    {
        $realArray = [];
        $priceSkus = [];
        $price = [];
        foreach ($skus as $skuKey => $sku) {
            $priceSkus[] = $sku;
            $price[] = $sku['oriPrice'];
        }
        array_multisort($price, SORT_ASC, SORT_NUMERIC, $priceSkus);

        $realArray[] = $priceSkus[0];

        return $realArray;
    }

    /**
     * 获取sku当前选项.
     */
    public function getSkuOption($sku, $prodType, $usage, $isSpecDisplay, $laborPrice)
    {
        $withMDiamond = false;
        if ($sku['has_main_material']) {
            $withMDiamond = true;
        }

        $displayOption = $this->displayOptionTrans($this->getDisplayOption($prodType, $usage, $isSpecDisplay, $withMDiamond));
        $displayOptionKey = $this->setSkuOption($displayOption, $sku, $prodType, $laborPrice);

        return ['displayOption' => $displayOption, 'displayOptionKey' => $displayOptionKey];
    }

    /**
     * 获取金价.
     */
    public function fetchGoldenPrice()
    {
        $goldenKey = config('redis.goldenPriceInfo');
        $redisModel = new RedisModel();
        $redisModel->_setDb(0);
        $enGoldenPrice = $redisModel->_get($goldenKey);
        $deGoldenPrice = json_decode($enGoldenPrice, true);

        return $deGoldenPrice;
    }

    /**
     * 获取促销列表.
     */
    public function fetchPromotionList($request)
    {
        $http = new Http();
        //获取商品信息
        $proBack = $http->curl('promotion/cart/productList', $request);
        $prosInfo = [];
        if (!empty($proBack)) {
            foreach ($proBack as $promotion) {
                $prosInfo[$promotion['model_id']] = $promotion;
            }
        }

        return $prosInfo;
    }

    /**
     * 获取产品的所有促销.
     */
    public function fetchAllPromotion($request)
    {
        $http = new Http();
        //获取商品信息
        $proBack = $http->curl('promotion/cart/productDetail', $request);
        $promotionDt = [];
        if (!empty($proBack) && !empty($proBack['rule'])) {
            foreach ($proBack['rule'] as $ruleData) {
                $tmpProDt = [];
                $tmpProDt['icon'] = '';
                $tmpProDt['id'] = $ruleData['rule_id'];
                $tmpProDt['dscp'] = $ruleData['rule_name'];
                if ('product_discount' === $ruleData['type'] || 'n_piece_n_discount' === $ruleData['type']) {
                    $tmpProDt['icon'] = self::$offerPic['cut'];
                } elseif ('order_n_discount' === $ruleData['type'] || 'full_reduction_of_order' === $ruleData['type']) {
                    $tmpProDt['icon'] = self::$offerPic['minus'];
                } elseif ('gift' === $ruleData['type']) {
                    $tmpProDt['icon'] = self::$offerPic['sample'];
                } else {
                    continue;
                }
                $promotionDt[] = $tmpProDt;
            }
        }

        return $promotionDt;
    }

    /**
     * 结构转化.
     */
    public function deStruct($input)
    {
        $deInputs = json_decode($input, true);
        $deOutput = [];
        if (is_array($deInputs) && !empty($deInputs)) {
            foreach ($deInputs as $deInput) {
                $tmpOutput = [];
                if ('image' === $deInput['tag']) {
                    $tmpOutput['tag'] = 'image';
                    $tmpOutput['name'] = '单图组件';
                    $tmpOutput['src'] = $deInput['data']['src'];
                    if (!empty($deInput['data']['action'])) {
                        if (!isset($deInput['data']['action']['data']) || empty($deInput['data']['action']['data'])) {
                            $deInput['data']['action']['data'] = [
                                'type' => '',
                                'path' => ''
                            ];
                        }
                        $tmpOutput['action'] = $deInput['data']['action'];
                    } else {
                        $tmpOutput['action'] = [
                            'data' => [
                                'type' => '',
                                'path' => ''
                            ],
                            'type' => 'none',
                            'route' => false
                        ];
                    }
                    $deOutput[] = $tmpOutput;
                } elseif ('video' === $deInput['tag']) {
                    $tmpOutput['tag'] = 'video';
                    $tmpOutput['name'] = '视频组件';
                    $tmpOutput['src'] = $deInput['data']['poster'] ?: '';
                    $tmpOutput['action'] = $deInput['data']['isAuto'] ?: false;
                    $tmpOutput['url'] = $deInput['data']['src'] ?: '';
                    $deOutput[] = $tmpOutput;
                } elseif ('image_prod_default' === $deInput['tag']) {
                    $tmpOutput['tag'] = 'image_prod_default';
                    $tmpOutput['name'] = '产品展示';
                    $tmpOutput['src'] = $deInput['data']['src'];
                    $tmpOutput['action'] = false;
                    $deOutput[] = $tmpOutput;
                } elseif ('image_model_default' === $deInput['tag']) {
                    $tmpOutput['tag'] = 'image_model_default';
                    $tmpOutput['name'] = '模特展示';
                    $tmpOutput['src'] = $deInput['data']['src'];
                    $tmpOutput['action'] = false;
                    $deOutput[] = $tmpOutput;
                }
            }
        }

        return json_encode($deOutput);
    }

    /**
     * 结构转化.
     */
    public function enStruct($input)
    {
        $deInputs = json_decode($input, true);
        $deOutput = [];
        if (is_array($deInputs) && !empty($deInputs)) {
            foreach ($deInputs as $deInput) {
                $tmpOutput = [];
                if ('image' === $deInput['tag'] || 'image_prod_default' === $deInput['tag'] || 'image_model_default' === $deInput['tag']) {
                    $tmpOutput['tag'] = $deInput['tag'];
                    $tmpOutput['data'] = [
                        'src' => $deInput['src'],
                        'action' => $deInput['action'],
                    ];
                    $deOutput[] = $tmpOutput;
                } elseif ('video' === $deInput['tag']) {
                    $tmpOutput['tag'] = 'video';
                    $tmpOutput['data'] = [
                        'poster' => $deInput['src'],
                        'src' => $deInput['url'],
                        'isAuto' => $deInput['action'],
                    ];
                    $deOutput[] = $tmpOutput;
                }
            }
        }

        return json_encode($deOutput);
    }

    /**
     * 获取sku门店.
     */
    public function getDoorsBySku($skus)
    {
        $return = [];
        foreach ($skus as $sku) {
            $inventorys = $this->redisModel->_smembers(config('redis.mappingSkuInventory') . '###' . $sku);
            if (false === $inventorys || empty($inventorys)) {
                continue;
            }
            $doors = $this->redisModel->_hmget(config('redis.mappingInventoryStore'), $inventorys);
            foreach ($doors as $door) {
                if (false === $door) {
                    continue;
                } else {
                    if (!in_array($door, $return)) {
                        $return[] = $door;
                    }
                }
            }
        }

        return $return;
    }

    /**
     *获取能否刻字.
     */
    public function getCanLetter($canLetter, $usageCode, $ringSize)
    {
        if ('R' === $usageCode && '00' === $ringSize) {
            return 0;
        }

        return $canLetter;
    }

    /**
     *获取刻字字数限制.
     */
    public function getLetterLimit($canLetter, $letterInfo)
    {
        if (0 === $canLetter) {
            return 0;
        } else {
            if (preg_match('/\d+/', $letterInfo, $arr)) {
                return (int)$arr[0];
            } else {
                return 5;
            }
        }
    }
}
