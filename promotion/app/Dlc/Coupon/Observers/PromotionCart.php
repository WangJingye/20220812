<?php namespace App\Dlc\Coupon\Observers;

use App\Dlc\Coupon\Service\HelperService;
use Illuminate\Support\Facades\Log;
use App\Model\Promotion\Cart as Model;
use App\Dlc\Coupon\Service\CouponService as Service;

class PromotionCart
{
    public function created(Model $model)
    {

    }

    public function updated(Model $model)
    {

    }

    public function saved(Model $model)
    {
//        HelperService::getInstance()->log('test',$model->toArray());
        if($model->isDirty()) {
            $couponService = Service::getInstance();
            $couponService->setCache($model->id,$model->toArray());
        }
    }

    public function deleted(Model $model)
    {

    }

    public function restored(Model $model)
    {

    }

    public function forceDeleted(Model $model)
    {

    }
}
