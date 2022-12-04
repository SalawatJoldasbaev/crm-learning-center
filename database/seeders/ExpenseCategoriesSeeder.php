<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            "Boshqalar",
            "Ma'muriy xarajatlar",
            "Binolarni ijaraga to'lov",
            "Oylik",
            "Marketing",
            "Idora",
            "Uy xarajatlari",
            "Soliqlar",
            "Investitsiyalar",
            "To'lovni qaytarish",
        ];
        foreach ($categories as $key => $category) {
            ExpenseCategory::create([
                'name' => $category
            ]);
        }
    }
}
