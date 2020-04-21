<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            ContactsTableSeeder::class,

            CategoriesTableSeeder::class,
            UnitsTableSeeder::class,
            ProductsTableSeeder::class,

            AdjustmentsTableSeeder::class,
            AdjustmentProductsTableSeeder::class,

            PurchasesTableSeeder::class,
            PurchaseProductsTableSeeder::class,

            SellingsTableSeeder::class,
            SellingProductsTableSeeder::class,

            PurchasePaymentsTableSeeder::class,
            SellingPaymentsTableSeeder::class,
            
            PurchaseReturnsTableSeeder::class,
            PurchaseReturnProductsTableSeeder::class,

            SellingReturnsTableSeeder::class,
            SellingReturnProductsTableSeeder::class,

            TasksTableSeeder::class,
            RemindersTableSeeder::class,
        ]);
    }
}
