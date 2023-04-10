<?php namespace App\Http\Controllers\Api\Product;

use Illuminate\Http\Request;
use App\Exceptions\ApiPlaintextException;
use App\Http\Controllers\Api\ApiController;
use App\Repositories\Product\ProductRepository;
use App\Repositories\Product\Notify\StockRepository;

class NotifyController extends ApiController
{
    
    public function saveNotifyRequest(Request $request){
        $params = $request->all();
        $product_id = $params['id']??'';
        $sku = $params['sku']??'';
        $mobile = $params['mobile']??'';
        if(!$sku or !$mobile){
            throw new ApiPlaintextException("参数错误");
        }
        $data = ['product_id'=>$product_id,
            'sku'=>$sku,
            'mobile'=>$mobile,
            'created_at'=>date('y-m-d H:i:s'),
        ];
        $return = (new StockRepository())->save($data);
        
        $data = [$return];
        if(is_string($return)){
            return [
                'code'=>1,
                'message'=>$return,
            ];
        }
        return $this->success($data);
        
    }
    
}
