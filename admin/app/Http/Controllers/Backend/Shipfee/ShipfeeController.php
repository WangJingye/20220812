<?php namespace App\Http\Controllers\Backend\Shipfee;

use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\Redis;
use function GuzzleHttp\Promise\all;

class ShipfeeController extends Controller
{   
    public function index()
    {

        return view('backend.shipfee.index');
    }

    public function dataList(){
        $response = $this->curl('shipfee/list',request()->all());
        return $response;
    }

    public function view()
    {
        $response = $this->curl('shipfee/detail',request()->all());
        return view('backend.shipfee.view', ['detail' => $response['data']]);
    }


    public function update()
    {
        return $this->curl('shipfee/update',request()->all());
    }
    public function add()
    {
        $response = $this->curl('inner/getAllProvince');
        $provinces = $response['data'];
        return view('backend.shipfee.edit',['provinces'=>$provinces]);
    }

    public function edit()
    {
        $params = request()->all();
        $response = $this->curl('inner/getAllProvince');
        $provinces = $response['data'];
        $id = request()->get('id');
        $is_default = (request()->get('is_default')==1)?1:0;
        if($id||$is_default){
            $response = $this->curl('shipfee/detail',$params);
            return view('backend.shipfee.edit', ['detail' => $response['data'],'is_default'=>$is_default,['provinces'=>$provinces]]);
        }return view('backend.shipfee.edit', ['detail' => [],'is_default'=>$is_default,['provinces'=>$provinces]]);
    }

}
