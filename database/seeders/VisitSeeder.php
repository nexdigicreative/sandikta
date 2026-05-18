<?php

namespace Database\Seeders;

use App\Models\Visit;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class VisitSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $browsers = ['Chrome', 'Safari', 'Firefox', 'Edge', 'Opera'];
        $platforms = ['Windows', 'macOS', 'Android', 'iOS', 'Linux'];
        $devices = ['Desktop', 'Mobile', 'Tablet'];
        $urls = [
            'http://localhost:8000/',
            'http://localhost:8000/ebooks',
            'http://localhost:8000/ebooks/1/read',
            'http://localhost:8000/ebooks/2/read',
            'http://localhost:8000/login',
            'http://localhost:8000/categories',
            'http://localhost:8000/profile',
        ];

        // Seed visits for the last 30 days
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            // Random number of visits per day (e.g., between 25 and 80)
            $visitsCount = rand(25, 80);

            for ($j = 0; $j < $visitsCount; $j++) {
                $browser = $this->getRandomWeighted($browsers, [60, 20, 10, 8, 2]);
                $platform = $this->getRandomWeighted($platforms, [50, 15, 20, 10, 5]);

                // Align device type with platform
                if ($platform === 'Android' || $platform === 'iOS') {
                    $device = $platform === 'iOS' && rand(1, 10) > 8 ? 'Tablet' : 'Mobile';
                } else {
                    $device = 'Desktop';
                }

                $url = $this->getRandomWeighted($urls, [35, 25, 15, 10, 10, 3, 2]);

                // IP Address and Session
                $ip = rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
                $sessionId = bin2hex(random_bytes(20));

                // Nullable user ID
                $userId = null;
                if (rand(1, 10) > 4 && $users->isNotEmpty()) {
                    $userId = $users->random()->id;
                }

                $createdAt = $date->copy()->setHour(rand(7, 22))->setMinute(rand(0, 59))->setSecond(rand(0, 59));

                Visit::create([
                    'user_id' => $userId,
                    'ip_address' => $ip,
                    'user_agent' => "Mozilla/5.0 (Mock User Agent) Browser/$browser OS/$platform",
                    'browser' => $browser,
                    'platform' => $platform,
                    'device' => $device,
                    'url' => $url,
                    'method' => 'GET',
                    'session_id' => $sessionId,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }
    }

    private function getRandomWeighted(array $values, array $weights)
    {
        $totalWeight = array_sum($weights);
        $rand = rand(1, $totalWeight);

        $currentWeight = 0;
        foreach ($values as $index => $value) {
            $currentWeight += $weights[$index];
            if ($rand <= $currentWeight) {
                return $value;
            }
        }

        return $values[0];
    }
}
