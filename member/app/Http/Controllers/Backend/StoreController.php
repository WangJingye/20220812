<?php


namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\Role;
use App\Service\StoresService;
use App\Model\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Validator;
use Exception;
#use App\Support\CaptchaApi;
use Illuminate\Support\Facades\DB;


class StoreController extends Controller
{

    /**
     * 列表，支持模糊查询.
     */
    public function list(Request $request)
    {
        $limit = $request->limit ?: 10;

        $store = new Store;
        if ($request->store_name) {
            $store = $store->where('store_name', 'like', '%'.$request->store_name . '%');
        }

        $data = $store->orderBy('id', 'desc')->paginate($limit)->toArray();

        $return = [];
        if ($data) {
            $return['pageData'] = $data['data'];
            $return['count'] = $data['total'];

        }
        return $this->success('success', $return);
    }

    /**
     * 获取
     */
    public function getStore(Request $request)
    {
        $id = $request->id;
        $skuInfo = Store::where('id', $id)->first()->toArray();

        return $this->success('success', $skuInfo);
    }

    /**
     * 编辑
     */
    public function updateStore(Request $request)
    {
        $id = $request->id;
        $updateData = $request->all();
        unset($updateData['id'], $updateData['_url']);
        $exception = DB::transaction(function () use ($id, $updateData) {
            Store::updateOrCreate(
                ['id' => $id],
                $updateData
            );
        });
        if (is_null($exception)) {
            //$store = Store::pluck('store_name', 'store_id')->toArray();
            //Redis::set('store.list', json_encode($store));
            return $this->success([]);
        } else {
            \Log::info($exception);

            return $this->error('失败');
        }
    }


        /**
     * 列表，支持模糊查询.
     */
    public function AllList(Request $request)
    {
        $store = new Store();
        $data = Store::pluck('store_name', 'store_id')->toArray();
        //$data = $store->orderBy('id', 'desc')->select(['store_id','store_name'])->get()->toArray();
        $return = [];
        if ($data) {
            $return = $data;
        }
        return $this->success('success', $return);
    }


}