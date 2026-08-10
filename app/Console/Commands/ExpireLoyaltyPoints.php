<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Models\LoyalPoint;
use App\Models\User;
use Carbon\Carbon;

class ExpireLoyaltyPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-loyalty-points';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \DB::transaction(function () {
            // 1. Find all batches that have expired
            $expiredPoints = LoyalPoint::where('expired_at', '<=', Carbon::now())
                ->where('points', '>', 0)
                ->where('is_redeemed', false) // Only pick non-redeemed records
                ->get();

            foreach ($expiredPoints as $point) {
                // 3. Deduct the expired amount from the user's total balance
                User::where('id', $point->user_id)->decrement('coins', $point->points);
                // 2. Update the batch: zero out points and mark as redeemed
                $point->update([
                    'points' => 0,
                    'is_redeemed' => true,
                ]);
            }
        });

        $this->info('Expired loyalty points processed successfully.');
    }
}
