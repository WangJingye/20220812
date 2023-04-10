<?php namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

class ApiExampleController extends ApiController
{
    public function demo(Request $request){
        return $this->success($request->all());
    }
}
