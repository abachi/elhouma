<?php

namespace Tests\Feature;

use App\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_see_only_confirmed_reports()
    {
        factory(Report::class, 2)->create();
        factory(Report::class, 3)->states('confirmed')->create();
        factory(Report::class, 4)->states('fixed')->create();

        $response = $this->json('GET', route('filter.reports.confirmed'));

        $response->assertStatus(200);
        
        $response = $response->decodeResponseJson();
        
        $this->assertEquals(3, sizeof($response['data']));

        foreach ($response['data'] as $report) {
            $this->assertTrue($report['confirmed']);
        }
    }

    public function test_guest_can_see_only_fixed_reports()
    {
        factory(Report::class, 2)->create();
        factory(Report::class, 3)->states('confirmed')->create();
        factory(Report::class, 4)->states('fixed')->create();

        $response = $this->json('GET', route('filter.reports.fixed'));

        $response->assertStatus(200);
        
        $response = $response->decodeResponseJson();
        
        $this->assertEquals(4, sizeof($response['data']));

        foreach ($response['data'] as $report) {
            $this->assertTrue($report['fixed']);
        }
    }

    public function test_guest_can_see_only_not_confirmed_and_not_fixed_reports()
    {
        factory(Report::class, 2)->create();
        factory(Report::class, 3)->states('confirmed')->create();
        factory(Report::class, 4)->states('fixed')->create();

        $response = $this->json('GET', route('filter.reports.waiting'));

        $response->assertStatus(200);
        
        $response = $response->decodeResponseJson();
        
        $this->assertEquals(2, sizeof($response['data']));

        foreach ($response['data'] as $report) {
            $this->assertFalse($report['confirmed']);
            $this->assertFalse($report['fixed']);
        }
    }
}