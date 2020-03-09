<?php

namespace Tests\Feature;

use App\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_should_return_list_of_reports()
    {
        factory(Report::class, 8)->create();
        $response = $this->json('GET', route('reports.index'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'reporter_id',
                    'description',
                    'picture',
                    'lat',
                    'lng'
                ]
            ]
        ]);
    }
}
