<?php

namespace App\Http\Controllers\Backend\Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Backend\Controller;
use App\Model\OrderMail;

class MailController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        //return view('backend.config.mail');
        $data = OrderMail::where('status','0')->whereNotNull('email')->select(['email','id'])->get()->toArray();
        if (!empty($data)) {
        }
        return json_encode(['data' => $data]);
        //return view('backend.config.mail', ['detail' => array_to_object($data)]);
    }
}
