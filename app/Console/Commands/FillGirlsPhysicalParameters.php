<?php

namespace App\Console\Commands;

use App\Models\Girl;
use Illuminate\Console\Command;

class FillGirlsPhysicalParameters extends Command
{
    protected $signature = 'girls:fill-physical-parameters';
    protected $description = 'Fill physical parameters (height, weight, bust) for girls';

    public function handle()
    {
        $this->info('Starting to fill physical parameters...');
        
        $girls = Girl::whereNull('height')
            ->orWhereNull('weight')
            ->orWhereNull('bust')
            ->get();
        
        $this->info("Found {$girls->count()} girls without physical parameters");
        
        $bar = $this->output->createProgressBar($girls->count());
        $bar->start();
        
        foreach ($girls as $girl) {
            $height = $girl->height ?? $this->extractParameter($girl, 'рост') ?? rand(155, 178);
            $weight = $girl->weight ?? $this->extractParameter($girl, 'вес') ?? rand(45, 68);
            $bust = $girl->bust ?? $this->extractParameter($girl, 'грудь') ?? rand(1, 4);
            
            $girl->update([
                'height' => $height,
                'weight' => $weight,
                'bust' => $bust,
            ]);
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Physical parameters filled successfully!');
        
        return 0;
    }
    
    private function extractParameter($girlData, $paramName)
    {
        $description = strtolower($girlData->description ?? '');
        
        switch($paramName) {
            case 'рост':
                if (preg_match('/рост[:\s]+(\d{3})/iu', $description, $matches)) {
                    return (int)$matches[1];
                }
                break;
            case 'вес':
                if (preg_match('/вес[:\s]+(\d{2,3})/iu', $description, $matches)) {
                    return (int)$matches[1];
                }
                break;
            case 'грудь':
                if (preg_match('/грудь[:\s]+(\d)/iu', $description, $matches)) {
                    return (int)$matches[1];
                }
                if (preg_match('/(\d)\s*размер/iu', $description, $matches)) {
                    return (int)$matches[1];
                }
                break;
        }
        
        return null;
    }
}
