<?php

namespace App\Console\Commands\temp;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Favourite;

class MoveFavorites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temp:move_favourites';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move favourites from users table to favourites table';

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
     * @return int
     */
    public function handle()
    {
        $this->info('Moving favourites from users table to favourites table is starting');

        $users = User::all();

        foreach ($users as $user) {
            $favourites = json_decode($user->favorites);
            if ($favourites) {
                foreach ($favourites as $favourite) {
                    try {
                        $res = Favourite::create([
                            'user_id'=>$user->id,
                            'paragraph_theme_id'=>$favourite->id,
                            'type'=>'paragraph',
                            'time_delta'=>$favourite->date_time,
                        ]);
                    } catch(\Exception $e) {
                        $this->info($e);
                    }
                }
            }
        }

        $favourites = Favourite::all();
        foreach ($favourites as $favourite) {
            $favourite->created_at = date('Y-m-d H:i:s',$favourite->time_delta);
            $favourite->time_delta = null;
            $favourite->save();
        }

        $this->info('Moving favourites from users table to favourites table is completed successfully');
        return 0;
    }
}
