<?php

namespace App\Model\Goods;

class SppHelp
{
    /**
     * 图文详情校验.
     */
    public function sppValidate($request)
    {
        $return = [];
        $rule_type = $request->rule_type;
        $image = $request->image;
        if (empty($image)) {
            return 'invalid_image';
        }
        $return['rule_type'] = $rule_type;
        $return['image'] = $image;
        switch ($rule_type) {
            case 1:
                $brand_coll = $request->brand_coll;
                $is_child = $request->is_child;
                $sub_brand_coll = $request->sub_brand_coll;

                $return['brand_coll'] = $brand_coll;
                $brandCollList = ProductHelp::$brandColl;
                $brandCollCodeList = array_column($brandCollList, 'code');
                $brandCollIndex = array_search($brand_coll, $brandCollCodeList);
                if (false !== $brandCollIndex) {
                    if (isset($brandCollList[$brandCollIndex]['sub'])) {
                        $return['is_child'] = $is_child;
                        $subBrandCollList = $brandCollList[$brandCollIndex]['sub'];
                        $subBrandCollCodeList = array_column($subBrandCollList, 'code');
                        if ('1' === $is_child) {
                            if (null !== $sub_brand_coll) {
                                $subBrandCollIndex = array_search($sub_brand_coll, $subBrandCollCodeList);
                                if (false !== $subBrandCollIndex) {
                                    $return['sub_brand_coll'] = [$sub_brand_coll];
                                } else {
                                    return 'mismatch_sub_brand_coll_code';
                                }
                            } else {
                                return 'invalid_sub_brand_coll_code';
                            }
                        } elseif ('2' === $is_child) {
                            $return['sub_brand_coll'] = $subBrandCollCodeList;
                        } else {
                            return 'invalid_child_type';
                        }
                    }
                } else {
                    return 'invalid_brand_coll_code';
                }
                break;
            case 2:
                $usageCode = $request->usage;
                if (null !== $usageCode) {
                    if (isset(ProductHelp::$usage[$usageCode])) {
                        $return['usage'] = $usageCode;
                    } else {
                        return 'mismatch_usage_code';
                    }
                } else {
                    return 'invalid_usage_code';
                }
                break;
            case 3:
                $prodTypeCode = $request->prod_type;
                if (null !== $prodTypeCode) {
                    if (isset(ProductHelp::$prodTypeDesc[$prodTypeCode])) {
                        $return['prod_type'] = $prodTypeCode;
                    } else {
                        return 'mismatch_product_type_code';
                    }
                } else {
                    return 'invalid_product_type_code';
                }
                break;
            case 4:
                $styleNumber = strtoupper($request->style_number);
                if (null !== $styleNumber && '' !== trim($styleNumber)) {
                    $styleNumberList = explode(',', $styleNumber);
                    if (!empty($styleNumberList)) {
                        $return['style_number'] = $styleNumberList;
                    } else {
                        return 'invalid_style_number';
                    }
                } else {
                    return 'invalid_style_number';
                }
                break;
            case 5:
                break;
            default:
                return 'invalid_rule_type';
        }

        return $return;
    }

    public function sppValidateDb($data, $ruleId = '')
    {
        $whereIn = [];
        $ruleType = $data['rule_type'];
        switch ($ruleType) {
            case 1:
                $whereChildIn = [];
                $whereChildIn = ['0', '2'];
                $ruleBack = Spp::where('rule_type', $ruleType)->where('step_o', $data['brand_coll'])->whereIn('is_child', $whereChildIn)->get()->toArray();
                if ('' === $ruleId) {
                    if (!empty($ruleBack)) {
                        return 'part_rule_exist';
                    }
                } else {
                    if (!empty($ruleBack)) {
                        $ruleIds = array_column($ruleBack, 'id');
                        if (1 !== count($ruleIds) || $ruleIds[0] != $ruleId) {
                            return 'part_rule_exist';
                        }
                    }
                }

                if (isset($data['is_child'])) {
                    $ruleOneBack = Spp::where('rule_type', '1')->where('step_o', $data['brand_coll'])->where('is_child', '1')->get()->toArray();
                    if (!empty($ruleOneBack)) {
                        if ('' === $ruleId) {
                            if ('2' == $data['is_child']) {
                                return 'part_rule_exist';
                            } else {
                                foreach ($ruleOneBack as $ruleRow) {
                                    if ($data['sub_brand_coll'][0] === $ruleRow['step_t']) {
                                        return 'part_rule_exist';
                                    }
                                }
                            }
                        } else {
                            $ruleIds = array_column($ruleOneBack, 'id');
                            if ('2' == $data['is_child']) {
                                if (1 !== count($ruleIds) || $ruleIds[0] != $ruleId) {
                                    return 'part_rule_exist';
                                }
                            } else {
                                foreach ($ruleOneBack as $ruleRow) {
                                    if ($data['sub_brand_coll'][0] === $ruleRow['step_t'] && $ruleRow['id'] != $ruleId) {
                                        return 'part_rule_exist';
                                    }
                                }
                            }
                        }
                    }
                }
                break;
            case 2:
                $ruleOneBack = Spp::where('rule_type', $ruleType)->where('step_o', $data['usage'])->get()->toArray();
                if ('' === $ruleId) {
                    if (!empty($ruleOneBack)) {
                        return 'part_rule_exist';
                    }
                } else {
                    if (!empty($ruleOneBack)) {
                        $ruleIds = array_column($ruleOneBack, 'id');
                        if (1 !== count($ruleIds) || $ruleIds[0] != $ruleId) {
                            return 'part_rule_exist';
                        }
                    }
                }
                break;
            case 3:
                $ruleOneBack = Spp::where('rule_type', $ruleType)->where('step_o', $data['prod_type'])->get()->toArray();
                if ('' === $ruleId) {
                    if (!empty($ruleOneBack)) {
                        return 'part_rule_exist';
                    }
                } else {
                    if (!empty($ruleOneBack)) {
                        $ruleIds = array_column($ruleOneBack, 'id');
                        if (1 !== count($ruleIds) || $ruleIds[0] != $ruleId) {
                            return 'part_rule_exist';
                        }
                    }
                }
                break;
            case 4:
                $ruleOneBack = Spp::where('rule_type', $ruleType)->get()->toArray();
                if ('' === $ruleId) {
                    if (!empty($ruleOneBack)) {
                        foreach ($ruleOneBack as $ruleRow) {
                            $items = explode(',', $ruleRow['include_style_number']);
                            $inter = array_intersect($data['style_number'], $items);
                            if (!empty($inter)) {
                                return 'part_rule_exist';
                            }
                        }
                    }
                } else {
                    if (!empty($ruleOneBack)) {
                        foreach ($ruleOneBack as $ruleRow) {
                            if ($ruleRow['id'] == $ruleId) {
                                continue;
                            }
                            $items = explode(',', $ruleRow['include_style_number']);
                            $inter = array_intersect($data['style_number'], $items);
                            if (!empty($inter)) {
                                return 'part_rule_exist';
                            }
                        }
                    }
                }
                break;
            case 5:
                $ruleBack = Spp::where('rule_type', $ruleType)->get()->toArray();
                if ('' === $ruleId) {
                    if (!empty($ruleBack)) {
                        return 'part_rule_exist';
                    }
                } else {
                    if (!empty($ruleBack)) {
                        $ruleIds = array_column($ruleBack, 'id');
                        if (1 !== count($ruleIds) || $ruleIds[0] != $ruleId) {
                            return 'part_rule_exist';
                        }
                    }
                }
                break;
        }

        return true;
    }

    public function setSppDbData($data, $ruleId = '')
    {
        $insert = [];
        $ruleType = $data['rule_type'];
        $insert['rule_type'] = $ruleType;
        $insert['step_o'] = null;
        $insert['include_style_number'] = null;
        $insert['is_child'] = 0;
        $insert['step_t'] = null;
        $insert['image'] = $data['image'];
        switch ($ruleType) {
            case 1:
                $insert['step_o'] = $data['brand_coll'];
                if (isset($data['is_child'])) {
                    $insert['is_child'] = $data['is_child'];
                    if (1 == $data['is_child']) {
                        $insert['step_t'] = $data['sub_brand_coll'][0];
                    }
                }
                break;
            case 2:
                $insert['step_o'] = $data['usage'];
                break;
            case 3:
                $insert['step_o'] = $data['prod_type'];
                break;
            case 4:
                $insert['include_style_number'] = implode(',', $data['style_number']);
                break;
        }
        $time = date('Y-m-d H:i:s');

        if ('' === $ruleId) {
            $insert['created_at'] = $time;
        }
        $insert['updated_at'] = $time;

        return $insert;
    }

    public function setSppRelateDbData($ruleId, $data)
    {
        $insert = [];
        $ruleType = $data['rule_type'];
        switch ($ruleType) {
            case 1:
                if (isset($data['is_child'])) {
                    foreach ($data['sub_brand_coll'] as $subCode) {
                        $tmpInsertData = [];
                        $tmpInsertData['rule_id'] = $ruleId;
                        $tmpInsertData['rule_type'] = $ruleType;
                        $tmpInsertData['rule_data'] = json_encode(['pt' => $data['brand_coll'], 'ch' => $subCode]);
                        $insert[] = $tmpInsertData;
                    }
                } else {
                    $tmpInsertData = [];
                    $tmpInsertData['rule_id'] = $ruleId;
                    $tmpInsertData['rule_type'] = $ruleType;
                    $tmpInsertData['rule_data'] = json_encode(['pt' => $data['brand_coll']]);
                    $insert[] = $tmpInsertData;
                }
                break;
            case 2:
                $tmpInsertData = [];
                $tmpInsertData['rule_id'] = $ruleId;
                $tmpInsertData['rule_type'] = $ruleType;
                $tmpInsertData['rule_data'] = json_encode(['us' => $data['usage']]);
                $insert[] = $tmpInsertData;
                break;
            case 3:
                $tmpInsertData = [];
                $tmpInsertData['rule_id'] = $ruleId;
                $tmpInsertData['rule_type'] = $ruleType;
                $tmpInsertData['rule_data'] = json_encode(['dt' => $data['prod_type']]);
                $insert[] = $tmpInsertData;
                break;
            case 4:
                foreach ($data['style_number'] as $stNbr) {
                    $tmpInsertData = [];
                    $tmpInsertData['rule_id'] = $ruleId;
                    $tmpInsertData['rule_type'] = $ruleType;
                    $tmpInsertData['rule_data'] = json_encode(['sn' => $stNbr]);
                    $insert[] = $tmpInsertData;
                }
                break;
        }

        return $insert;
    }

    public function getUsageName($code)
    {
        return ProductHelp::$usage[$code];
    }

    public function getProdTypeName($code)
    {
        return ProductHelp::$prodTypeDesc[$code];
    }

    public function getBrandCollName($stepO, $stepT)
    {
        $brandCollList = ProductHelp::$brandColl;
        $brandCollCodeList = array_column($brandCollList, 'code');
        $brandCollIndex = array_search($stepO, $brandCollCodeList);

        if ($stepT === null) {
            return $brandCollList[$brandCollIndex]['name'];
        } else {
            if (isset($brandCollList[$brandCollIndex]['sub'])) {
                $subBrandCollCodeList = array_column($brandCollList[$brandCollIndex]['sub'], 'code');
                $subBrandCollIndex = array_search($stepT, $subBrandCollCodeList);

                return $brandCollList[$brandCollIndex]['name'] . ' - ' . $brandCollList[$brandCollIndex]['sub'][$subBrandCollIndex]['name'];
            } else {
                return $brandCollList[$brandCollIndex]['name'];
            }
        }
    }


    /**
     * 获取图文规则.
     */
    public function getFirstSppImage($enImages)
    {
        $deImages = json_decode($enImages, true);
        $sppImage = '';
        if (!empty($deImages)) {
            if (isset($deImages[0]) && isset($deImages[0]['data'])) {
                $sppImage = $deImages[0]['data']['src'];
            }
        }
        return $sppImage;
    }
}
