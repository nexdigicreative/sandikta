<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Superadmin
        User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@sandikta.sch.id',
            'password' => Hash::make('superadmin123'),
            'role' => 'superadmin',
            'is_active' => true,
            'must_change_password' => false,
        ]);

        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@sandikta.sch.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'must_change_password' => false,
        ]);



        // Categories
        $categories = [
            ['name' => 'Pemrograman', 'slug' => 'pemrograman', 'description' => 'Buku tentang pemrograman dan pengembangan software'],
            ['name' => 'Jaringan Komputer', 'slug' => 'jaringan-komputer', 'description' => 'Buku tentang jaringan dan infrastruktur IT'],
            ['name' => 'Desain Grafis', 'slug' => 'desain-grafis', 'description' => 'Buku tentang desain grafis dan multimedia'],
            ['name' => 'Matematika', 'slug' => 'matematika', 'description' => 'Buku pelajaran matematika'],
            ['name' => 'Bahasa Indonesia', 'slug' => 'bahasa-indonesia', 'description' => 'Buku pelajaran Bahasa Indonesia'],
            ['name' => 'Bahasa Inggris', 'slug' => 'bahasa-inggris', 'description' => 'Buku pelajaran Bahasa Inggris'],
            ['name' => 'Ilmu Pengetahuan Alam', 'slug' => 'ipa', 'description' => 'Buku pelajaran IPA'],
            ['name' => 'Ilmu Pengetahuan Sosial', 'slug' => 'ips', 'description' => 'Buku pelajaran IPS'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
