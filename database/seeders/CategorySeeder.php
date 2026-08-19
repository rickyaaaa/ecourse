<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Support\MockData;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed the categories table.
     *
     * Sumbernya App\Support\MockData supaya data di database sama persis
     * dengan data tiruan yang dipakai halaman frontend sebelumnya.
     */
    public function run(): void
    {
        foreach (MockData::categories() as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']],
            );
        }
    }
}
