<?php

use Illuminate\Database\Seeder;

class ReportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Report::class, 5)->create([
            'reporter_id' => App\User::first(),
        ]);
    }
}
