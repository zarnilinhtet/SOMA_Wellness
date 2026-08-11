<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClassSchedule;
use Carbon\Carbon;

class CompleteClassSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:complete-class-schedules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically mark class schedules as completed after their end date and time';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        $schedules = ClassSchedule::where('status', '!=', 'completed')
            ->where(function ($query) use ($now) {

                // End date has already passed
                $query->whereDate('end_date', '<', $now->toDateString())

                    // OR end date is today and end time has passed
                    ->orWhere(function ($query) use ($now) {
                    $query->whereDate('end_date', $now->toDateString())
                        ->whereTime('end_time', '<=', $now->format('H:i:s'));
                });
            })
            ->get();

        foreach ($schedules as $schedule) {
            $schedule->update([
                'status' => 'completed',
            ]);
        }

        $this->info(
            $schedules->count() . ' class schedule(s) marked as completed.'
        );

        return Command::SUCCESS;
    }
}