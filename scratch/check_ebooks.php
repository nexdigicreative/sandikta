<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use App\Models\Ebook;
use Illuminate\Support\Facades\Storage;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$ebooks = Ebook::all();
echo "Total Ebooks: " . $ebooks->count() . "\n";
foreach ($ebooks as $ebook) {
    echo "ID: {$ebook->id}, Title: {$ebook->title}, Path: {$ebook->file_path}\n";
    $exists = Storage::disk('local')->exists($ebook->file_path);
    echo "Exists in local disk: " . ($exists ? 'YES' : 'NO') . "\n";
    if (!$exists) {
        echo "Full path checked: " . Storage::disk('local')->path($ebook->file_path) . "\n";
    }
    echo "-------------------\n";
}
