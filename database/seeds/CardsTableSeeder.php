<?php
/**
 * Created by PhpStorm.
 * Cards: Cards
 * Date: 12/9/2018
 * Time: 12:50 AM
 */

use Illuminate\Database\Seeder;

class CardsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create 10 users using the user factory
        factory(App\Cards::class, 10)->create();
    }
}