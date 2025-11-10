<?php

namespace App\Modules\Sitemap\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Girl;
use App\Models\Masseuse;
use App\Models\Salon;
use App\Models\StripClub;
use Illuminate\Support\Facades\URL;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = $this->generateUrls();
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        foreach ($urls as $url) {
            $xml .= "    <url>\n";
            $xml .= "        <loc>" . htmlspecialchars($url['loc']) . "</loc>\n";
            $xml .= "        <lastmod>" . $url['lastmod'] . "</lastmod>\n";
            $xml .= "        <changefreq>" . $url['changefreq'] . "</changefreq>\n";
            $xml .= "        <priority>" . $url['priority'] . "</priority>\n";
            $xml .= "    </url>\n";
        }
        
        $xml .= '</urlset>';
        
        return response($xml)
            ->header('Content-Type', 'text/xml');
    }
    
    private function generateUrls()
    {
        $urls = [];
        
        $urls[] = [
            'loc' => URL::to('/'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '1.0'
        ];
        
        $urls[] = [
            'loc' => URL::to('/contact'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.3'
        ];
        
        $urls[] = [
            'loc' => URL::to('/map'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'weekly',
            'priority' => '0.7'
        ];
        
        $urls[] = [
            'loc' => URL::to('/intim-map'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '0.8'
        ];
        
        $urls[] = [
            'loc' => URL::to('/masseuse'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '0.8'
        ];
        
        $urls[] = [
            'loc' => URL::to('/salons'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '0.8'
        ];
        
        $urls[] = [
            'loc' => URL::to('/strip-clubs'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'daily',
            'priority' => '0.8'
        ];
        
        $urls[] = [
            'loc' => URL::to('/selected'),
            'lastmod' => now()->toAtomString(),
            'changefreq' => 'weekly',
            'priority' => '0.5'
        ];
        
        $girls = Girl::select('anketa_id', 'updated_at')->get();
        foreach ($girls as $girl) {
            $urls[] = [
                'loc' => URL::to('/girl/' . $girl->anketa_id),
                'lastmod' => $girl->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.6'
            ];
        }
        
        $masseuses = Masseuse::select('anketa_id', 'updated_at')->get();
        foreach ($masseuses as $masseuse) {
            $urls[] = [
                'loc' => URL::to('/masseuse/' . $masseuse->anketa_id),
                'lastmod' => $masseuse->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.6'
            ];
        }
        
        $salons = Salon::select('salon_id', 'updated_at')->get();
        foreach ($salons as $salon) {
            $urls[] = [
                'loc' => URL::to('/salon/' . $salon->salon_id),
                'lastmod' => $salon->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.6'
            ];
        }
        
        $stripClubs = StripClub::select('club_id', 'updated_at')->get();
        foreach ($stripClubs as $club) {
            $urls[] = [
                'loc' => URL::to('/strip-club/' . $club->club_id),
                'lastmod' => $club->updated_at->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.6'
            ];
        }
        
        return $urls;
    }
}

