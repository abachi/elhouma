<?php

namespace Tests\Feature;

use App\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_see_total_number_of_reports()
    {
        factory(Report::class, 2)->create();
        factory(Report::class, 3)->states('confirmed')->create();
        factory(Report::class, 4)->states('fixed')->create();

        $response = $this->json('GET', route('stats.reports.total'));

        $response
            ->assertStatus(200)
            ->assertJson([
                'total' => 9,
                'waiting' => 2,
                'confirmed' => 3,
                'fixed' => 4
            ]);
    }
}