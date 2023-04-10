<?php namespace App\Http\Controllers;

use App\Exceptions\ApiPlaintextException;
use Illuminate\Http\Request;
use App\Services\Top\TopHelper;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Model\Search\Redirect;

class AdController extends Controller
{
    /**
     *
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function itFront(Request $request)
    {
////set merchant server domain name as ".linktech.cn"
       $merchant_domain=".dlc.com.cn";
       $all = $request->all();
       if(!get_cfg_var("register_globals"))
       {
           $a_id  = $all["a_id"]??'';
           $m_id  = $all["m_id"]??'';
           $c_id  = $all["c_id"]??'';
           $l_id  = $all["l_id"]??'';
           $l_type1 = $all["l_type1"]??'';
           $rd    = $all["rd"]??'';
           $url   = $all["url"]."?aid=".($all["a_id"]??'');
       }
       Header("P3P:CP=\"NOI DEVa TAIa OUR BUS UNI\"");

       If($rd==0)SetCookie("LTINFO","$a_id|$c_id|$l_id|$l_type1|",0,"/", $merchant_domain);
       else SetCookie("LTINFO","$a_id|$c_id|$l_id|$l_type1|",time()+($rd*24*60*60),"/", $merchant_domain);

       Header("Location: $url");
       Header("URI: $url");

    }



}
