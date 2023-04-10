<?php

namespace App\Console\Commands;

use App\Model\Users;
use App\Service\CrmUsersService;
use Illuminate\Console\Command;


class LoadUserLevel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Load:level';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'load User';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        try{
            $crm = new CrmUsersService();
            Users::chunk(100, function ($users)use ($crm) {
                foreach ($users as $user) {
                  $data = $crm->userInfo(['pos_id'=>$user->pos_id]);
                  if($data){
                      Users::where('pos_id',$user->pos_id)->update([
                          'points'=>$data['AvailablePoints'],
                          'level'=>$data['CustomerType']
                      ]);
                  }


                }
            });

        }  catch (\Exception $exception){
            $this->error($exception->getMessage());
        }
    }
}
