<?php

namespace App\Support;

/**
 * Atribut visual (warna gradient & ikon thumbnail) kursus berdasarkan slug
 * kategori. Ini murni presentasi — bukan kolom di database — supaya kartu
 * kursus tetap berwarna-warni tanpa perlu menyimpan aset gambar sungguhan.
 * Palet ini sengaja disamakan dengan yang dipakai App\Support\MockData agar
 * tampilan katalog konsisten sebelum & sesudah terhubung ke database asli.
 */
class CoursePresentation
{
    private const PALETTE = [
        'pengembangan-web' => ['color' => 'from-indigo-500 to-purple-500', 'icon' => '🧑‍💻'],
        'data-ai' => ['color' => 'from-emerald-500 to-teal-500', 'icon' => '📊'],
        'desain' => ['color' => 'from-fuchsia-500 to-pink-500', 'icon' => '🎨'],
        'bisnis-karier' => ['color' => 'from-amber-500 to-yellow-500', 'icon' => '📈'],
        'bahasa' => ['color' => 'from-blue-500 to-indigo-500', 'icon' => '🗣️'],
    ];

    private const FALLBACK = ['color' => 'from-slate-500 to-gray-500', 'icon' => '📘'];

    public static function thumbnailColor(?string $categorySlug): string
    {
        return (self::PALETTE[$categorySlug] ?? self::FALLBACK)['color'];
    }

    public static function thumbnailIcon(?string $categorySlug): string
    {
        return (self::PALETTE[$categorySlug] ?? self::FALLBACK)['icon'];
    }
}
