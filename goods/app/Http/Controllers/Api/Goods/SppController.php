<?php

namespace App\Http\Controllers\Api\Goods;

use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Model\Goods\Spp;
use App\Model\Goods\SppRelate;
use App\Model\Goods\SppHelp;

class SppController extends Controller
{
    protected $_model = Spp::class;
    public $ruleTypeMap = [
        '1' => '按品牌系列',
        '2' => '按用途',
        '3' => '按材质',
        '4' => '按指定款号',
        '5' => '全部',
    ];

    /**
     * 图文详情规则列表.
     */
    public function list(Request $request)
    {
        $limit = $request->limit ?: 10;
        $deSppDatas = Spp::paginate($limit)->toArray();
        $SppHelp = new SppHelp();
        foreach ($deSppDatas['data'] as &$deSppData) {
            $deSppData['_rule_type'] = $this->ruleTypeMap[$deSppData['rule_type']];
            switch ($deSppData['rule_type']) {
                case 1:
                    $deSppData['_content'] = $SppHelp->getBrandCollName($deSppData['step_o'], $deSppData['step_t']);
                    break;
                case 2:
                    $deSppData['_content'] = $SppHelp->getUsageName($deSppData['step_o']);
                    break;
                case 3:
                    $deSppData['_content'] = $SppHelp->getProdTypeName($deSppData['step_o']);
                    break;
                case 4:
                    $deSppData['_content'] = $deSppData['include_style_number'];
                    break;
                case 5:
                    $deSppData['_content'] = '全部产品';
                    break;
            }
            $deSppData['_image'] = $SppHelp->getFirstSppImage($deSppData['image']);
        }
        $return = [];
        $return['pageData'] = $deSppDatas['data'];
        $return['count'] = $deSppDatas['total'];

        return $this->success($return);
    }

    /**
     * 创建图文规则.
     */
    public function add(Request $request)
    {
        $SppHelp = new SppHelp();

        $validBack = $SppHelp->sppValidate($request);
        if (is_string($validBack)) {
            return $this->error(0, $validBack);
        }

        $dbValidBack = $SppHelp->sppValidateDb($validBack);
        if (true !== $dbValidBack) {
            return $this->error(0, $dbValidBack);
        }

        $sppDataBack = $SppHelp->setSppDbData($validBack);
        try {
            $ruleId = Spp::insertGetId($sppDataBack);
            return $this->success([]);
        } catch (Exception $e) {
            return $this->error(0, $e->getMessage());
        }
    }

    /**
     * 编辑图文规则.
     */
    public function edit(Request $request)
    {
        $ruleId = $request->id;
        $SppHelp = new SppHelp();

        $validBack = $SppHelp->sppValidate($request);
        if (is_string($validBack)) {
            return $this->error(0, $validBack);
        }

        $dbValidBack = $SppHelp->sppValidateDb($validBack, $ruleId);

        if (true !== $dbValidBack) {
            return $this->error(0, $validBack);
        }

        $sppDataBack = $SppHelp->setSppDbData($validBack, $ruleId);
        $exception = DB::transaction(function () use ($ruleId, $sppDataBack) {
            Spp::updateOrCreate(
                ['id' => $ruleId],
                $sppDataBack
            );
        });

        if (is_null($exception)) {
            return $this->success([]);
        } else {
            return $this->error(0, $exception);
        }
    }

    /**
     * 删除图文规则.
     */
    public function del(Request $request)
    {
        $ruleId = $request->ruleId;
        $exception = DB::transaction(function () use ($ruleId) {
            Spp::destroy($ruleId);
            SppRelate::where('rule_id', $ruleId)->delete();
        });

        return is_null($exception) ? 1 : 0;
    }

    /**
     * 获取图文规则.
     */
    public function getSppRule(Request $request)
    {
        $ruleId = $request->ruleId;
        $ruleInfo = Spp::where('id', $ruleId)->first()->toArray();

        $return = [];
        $return['data'] = $ruleInfo;

        return json_encode($return);
    }

}
