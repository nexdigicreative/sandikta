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

        // Sample Users/Murid
        User::create([
            'nis' => '10001',
            'name' => 'Budi Santoso',
            'kelas' => 'XII RPL 1',
            'tanggal_lahir' => '2008-05-15',
            'password' => Hash::make('15052008'), // ddmmyyyy
            'role' => 'user',
            'is_active' => true,
            'must_change_password' => true,
        ]);

        User::create([
            'nis' => '10002',
            'name' => 'Siti Nurhaliza',
            'kelas' => 'XII RPL 1',
            'tanggal_lahir' => '2008-08-20',
            'password' => Hash::make('20082008'),
            'role' => 'user',
            'is_active' => true,
            'must_change_password' => true,
        ]);

        User::create([
            'nis' => '10003',
            'name' => 'Ahmad Fadillah',
            'kelas' => 'XI TKJ 2',
            'tanggal_lahir' => '2009-01-10',
            'password' => Hash::make('10012009'),
            'role' => 'user',
            'is_active' => true,
            'must_change_password' => true,
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
