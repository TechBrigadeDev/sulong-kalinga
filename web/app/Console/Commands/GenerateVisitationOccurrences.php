<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Visitation;
use App\Models\RecurringPattern;
use Carbon\Carbon;

class GenerateVisitationOccurrences extends Command
{
    protected $signature = 'visitations:generate-occurrences {--months=3} {--visitation=}';
    protected $description = 'Generate occurrences for recurring visitations';

    public function handle()
    {
        $months = $this->option('months');
        $visitationId = $this->option('visitation');
        
        $this->info("Generating occurrences for the next {$months} months");
        
        // If a specific visitation is specified, only generate for that one
        if ($visitationId) {
            $visitation = Visitation::find($visitationId);
            if ($visitation) {
                $this->info("Generating occurrences for visitation #{$visitationId}");
                $occurrenceIds = $visitation->generateOccurrences($months);
                $this->info("Generated " . count($occurrenceIds) . " occurrences");
            } else {
                $this->error("Visitation #{$visitationId} not found");
            }
            return;
        }
        
        // Otherwise, generate for all recurring visitations
        $visitations = Visitation::whereHas('recurringPattern')->get();
        
        $this->info("Found {$visitations->count()} recurring visitations");
        $bar = $this->output->createProgressBar($visitations->count());
        
        $totalGenerated = 0;
        
        foreach ($visitations as $visitation) {
            $occurrenceIds = $visitation->generateOccurrences($months);
            $totalGenerated += count($occurrenceIds);
            $bar->advance();
        }
        
        $bar->finish();
        $this->info("\nGenerated {$totalGenerated} occurrences for {$visitations->count()} visitations");
    }
}