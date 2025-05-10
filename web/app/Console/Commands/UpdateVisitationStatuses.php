<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VisitationOccurrence;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CleanupOldVisitationOccurrences extends Command
{
    protected $signature = 'visitations:cleanup-old-occurrences {--months=12}';
    protected $description = 'Remove old visitation occurrences that exceed the retention period';

    public function handle()
    {
        $months = $this->option('months');
        $cutoffDate = Carbon::now()->subMonths($months)->format('Y-m-d');
        
        $this->info("Cleaning up visitation occurrences older than {$cutoffDate}...");
        
        // Count how many will be affected
        $count = VisitationOccurrence::where('occurrence_date', '<', $cutoffDate)->count();
        
        if ($count > 0) {
            if ($this->confirm("This will remove {$count} old occurrence records. Continue?")) {
                DB::beginTransaction();
                
                try {
                    // Delete old occurrences
                    VisitationOccurrence::where('occurrence_date', '<', $cutoffDate)->delete();
                    
                    DB::commit();
                    $this->info("Successfully removed {$count} old occurrence records.");
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("Error removing old occurrences: " . $e->getMessage());
                }
            } else {
                $this->info("Operation cancelled.");
            }
        } else {
            $this->info("No old occurrences found to clean up.");
        }
        
        return 0;
    }
}