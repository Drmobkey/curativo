<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tags;
use Spatie\Permission\Models\Role;
class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();

        $admin = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();
        if (!$admin) {
            $this->command->error('Admin user tidak ditemukan!');
            return;
        }

        // Buat kategori
        $categories = collect([
            'Pertolongan Pertama',
            'Darurat Medis',
            'Kondisi Anak',
        ])->map(fn($name) => Category::create([
                'id' => Str::uuid(),
                'name' => $name,
                'slug' => Str::slug($name),
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]));

        // Buat tag
        $tags = collect(['P3K', 'Cedera', 'Anak', 'Darurat', 'Medis', 'Umum'])->map(fn($name) => Tags::create([
            'id' => Str::uuid(),
            'name' => $name,
            'slug' => Str::slug($name),
            'created_by' => $admin->id,
            'updated_by' => $admin->id,
        ]));

        // Judul & konten
        $articles = [
            'Pingsan' => [
                'Pindahkan korban ke tempat yang aman.',
                'Baringkan dengan posisi kaki lebih tinggi dari kepala.',
                'Longgarkan pakaian yang ketat.',
                'Periksa napas dan detak jantung.',
                'Segera hubungi bantuan medis jika tidak sadar dalam 1 menit.',
            ],
            'Terkilir' => [
                'Istirahatkan bagian yang terkilir.',
                'Kompres dengan es selama 15-20 menit.',
                'Balut bagian yang cedera dengan perban elastis.',
                'Tinggikan bagian yang terkilir untuk mengurangi bengkak.',
                'Hindari memijat bagian yang terkilir.',
            ],
            'Tersedak' => [
                'Minta korban untuk batuk kuat jika masih bisa bernapas.',
                'Jika tidak bisa bicara/napas, lakukan 5 pukulan di punggung.',
                'Lanjutkan dengan 5 dorongan Heimlich (abdominal thrust).',
                'Ulangi hingga benda keluar atau korban tidak sadar.',
                'Jika pingsan, lakukan CPR dan hubungi bantuan medis.',
            ],
            'Tersedak Pada Anak' => [
                'Untuk bayi: baringkan telungkup, lakukan 5 tepukan punggung.',
                'Balikkan dan lakukan 5 dorongan dada dengan 2 jari.',
                'Untuk anak >1 tahun, lakukan seperti orang dewasa (Heimlich).',
                'Periksa mulut, jangan mengorek jika tidak terlihat benda.',
                'Segera cari bantuan medis jika tak kunjung membaik.',
            ],
            'Mimisan' => [
                'Duduk tegak, jangan berbaring.',
                'Condongkan tubuh ke depan.',
                'Cubiti hidung selama 10 menit tanpa henti.',
                'Kompres dingin di hidung/dahi.',
                'Jika mimisan terus >20 menit, hubungi dokter.',
            ],
            'Gigitan Ular' => [
                'Jauhkan korban dari ular dan tetap tenang.',
                'Batasi gerakan untuk memperlambat penyebaran bisa.',
                'Longgarkan pakaian dan perhiasan di sekitar luka.',
                'Jangan hisap/memotong luka.',
                'Segera bawa ke fasilitas kesehatan terdekat.',
            ],
            'Kejang' => [
                'Jauhkan benda berbahaya di sekitar penderita.',
                'Letakkan sesuatu yang empuk di bawah kepala.',
                'Jangan menahan gerakan atau memasukkan apapun ke mulut.',
                'Catat durasi kejang.',
                'Hubungi bantuan jika kejang >5 menit atau berulang.',
            ],
            'Serangan Jantung' => [
                'Segera dudukkan atau baringkan korban.',
                'Longgarkan pakaian ketat.',
                'Berikan aspirin jika sadar dan tidak alergi.',
                'Lakukan CPR jika tidak sadar dan tidak bernapas.',
                'Panggil bantuan medis segera.',
            ],
            'Demam' => [
                'Berikan cairan yang cukup.',
                'Gunakan kompres hangat (bukan dingin).',
                'Berikan obat penurun demam jika perlu.',
                'Gunakan pakaian tipis dan nyaman.',
                'Periksa ke dokter jika demam >3 hari atau sangat tinggi.',
            ],
        ];

        foreach ($articles as $title => $points) {
            $content = "<h3>Pertolongan Pertama pada {$title}</h3><ul>";
            foreach ($points as $point) {
                $content .= "<li>{$point}</li>";
            }
            $content .= "</ul>";

            $article = Article::create([
                'id' => Str::uuid(),
                'title' => $title,
                'slug' => Str::slug($title),
                'content' => $content,
                'image' => null,
                'status' => 'published',
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
                'views' => rand(10, 100),
            ]);

            $article->categories()->attach($categories->random(1)->pluck('id'));
            $article->tags()->attach($tags->random(rand(1, 3))->pluck('id'));
        }
    }
}

