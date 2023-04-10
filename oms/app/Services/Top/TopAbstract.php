<?php namespace App\Services\Top;

abstract class TopAbstract
{
    /**
     * @var \Illuminate\Http\Request;
     */
    public $request;
    public function __construct($request){
        $this->request = $request;
    }

    public function execute():array
    {
        return ['response'=>$this->request];
    }
}
