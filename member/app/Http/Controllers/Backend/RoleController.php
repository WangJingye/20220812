<?php


namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Model\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Exception;
use Illuminate\Support\Facades\Redis;


class RoleController extends Controller
{
    /**
     * 列表，支持模糊查询.
     */
    public function list(Request $request)
    {
        $limit = $request->limit ?: 10;

        $role = new Role();
        if ($request->role_name) {
            $emply = $role->where('role_name', 'like', $request->role_name . '%');
        }

        $data = $role->paginate($limit)->toArray();

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
    public function getRole(Request $request)
    {
        $id = $request->id;
        $info = Role::where('id', $id)->first()->toArray();
        $return = $info;

        return $this->success('success', $return);
    }

    /**
     * 编辑
     */
    public function updateRole(Request $request)
    {
        $skuIdx = $request->id;
        $updateData = $request->all();
        unset($updateData['id'], $updateData['_url']);
        $exception = DB::transaction(function () use ($skuIdx, $updateData) {
            Role::updateOrCreate(
                ['id' => $skuIdx],
                $updateData
            );
        });

        if (is_null($exception)) {

            //$roles = Role::pluck('role_name', 'id')->toArray();
            //Redis::set('store.role.list', json_encode($roles));

            return $this->success([]);
        } else {
            return $this->error($exception);
        }
    }


    /**
     * 列表，支持模糊查询.
     */
    public function AllList(Request $request)
    {
        $role = new Role();
        $data = Role::pluck('role_name', 'id')->toArray();
        //$data = $role->orderBy('id', 'desc')->select(['id','role_name'])->get()->toArray();
        $return = [];
        if ($data) {
            $return = $data;
        }
        return $this->success('success', $return);
    }
}