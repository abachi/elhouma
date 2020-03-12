<?php

namespace Tests\Feature;

use App\User;
use App\Report;
use App\IssueConfirmation;
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

    public function test_should_return_true_when_the_report_is_already_confirmed_by_a_user()
    {
        $nasser = factory(User::class)->create();
        $sabah = factory(User::class)->create();
        $report = factory(Report::class)->create(['reporter_id' => $nasser->id]);
        
        $this->assertFalse($report->isConfirmedBy($sabah));
        $confirmation = IssueConfirmation::create([
            'reporter_id' => $sabah->id,
            'report_id' => $report->id,
        ]);
        $this->assertTrue($report->isConfirmedBy($sabah));
    }

    public function test_report_should_be_confirmed_by_a_given_user()
    {
        $nasser = factory(User::class)->create();
        $sabah = factory(User::class)->create();
        $report = factory(Report::class)->create(['reporter_id' => $nasser->id]);
        
        $this->assertEquals(0, IssueConfirmation::all()->count());
        
        $this->assertTrue($report->confirmBy($sabah));
        
        $this->assertEquals(1, IssueConfirmation::all()->count());
    }
}
