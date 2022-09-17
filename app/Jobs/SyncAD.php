<?php

namespace App\Jobs;

use App\Http\Repositories\ADSync;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Company;
use App\Mutex;
use App\User;
use App\Job;

class SyncAD implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // NULL when syncAll, certain value when sync for that company's AD users
    public $company_admin;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($company_admin = NULL)
    {
        $this->company_admin = $company_admin;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $company_admin = $this->company_admin;
        $adSync = new ADSync;

        if (is_null($company_admin)) {
            while (true) {
                $no_queue = Job::where('queue', 'adsync')->count() == 0;
                if ($no_queue) {
                    break;
                }
                sleep(60); // Wait for 1 min
            }

            // Lock
            Mutex::where('name', Mutex::$regular_ad_sync)->forceDelete();
            $lock = new Mutex;
            $lock->name = Mutex::$regular_ad_sync;
            $lock->value = 1;
            $lock->save();

            $adSync->syncAll();

            // Unlock
            Mutex::where('name', Mutex::$regular_ad_sync)->forceDelete();
        } else {
            $user = User::find($company_admin);
            $user->company->sync_processed = -2; // Waiting for mutex to be unlocked
            $user->save();

            while (true) {
                $unlocked = Mutex::where('name', Mutex::$regular_ad_sync)
                                ->where('value', '>', 0)
                                ->count() == 0;
                if ($unlocked) {
                    break;
                }
                sleep(60);
            }

            $azure = $user->azure;
            $adSync->sync(
                $azure->tenant_id,
                $azure->client_id,
                $azure->client_secret,
                $user->company->id,
                $this->job->getJobId()
            );
        }
    }
}
