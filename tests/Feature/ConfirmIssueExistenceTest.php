<?php

namespace Tests\Feature;

use App\User;
use App\Report;
use App\IssueConfirmation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConfirmIssueExistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_confirm_an_issue()
    {
        $nasser = factory(User::class)->create();
        $sabah = factory(User::class)->create();
        $report = factory(Report::class)->create([ 'reporter_id' => $nasser ]);
        $token = \JWTAuth::fromUser($sabah);

        $response = $this->json('POST', route('reports.confirm'), [
            'token' => $token,
            'report_id' => $report->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'report',
                'status'
            ]);
    }

    public function test_authenticated_user_can_confirm_issue_only_one_time()
    {
        $nasser = factory(User::class)->create();
        $sabah = factory(User::class)->create();
        $report = factory(Report::class)->create([ 'reporter_id' => $nasser ]);
        $sbahaConfirmation = factory(IssueConfirmation::class)->create([
            'report_id' => $report->id,
            'reporter_id' => $sabah->id
        ]);
        
        $token = \JWTAuth::fromUser($sabah);

        $response = $this->json('POST', route('reports.confirm'), [
            'token' => $token,
            'report_id' => $report->id,
        ]);
        
        $response->assertStatus(201);

        $this->assertEquals(1, IssueConfirmation::all()->count());
    }

    public function test_should_respond_with_not_found_status_for_indefined_report()
    {
        $sabah = factory(User::class)->create();
        $token = \JWTAuth::fromUser($sabah);

        $response = $this->json('POST', route('reports.confirm'), [
            'token' => $token,
            'report_id' => 1,
        ]);

        $response->assertStatus(404);
        $this->assertEquals(0, IssueConfirmation::all()->count());
    }

    public function test_a_guest_cannot_confirm_an_issue()
    {
        $response = $this->json('POST', route('reports.confirm'), [
            'report_id' => 1,
        ]);
        $response->assertStatus(401);
        $this->assertEquals(0, IssueConfirmation::all()->count());
    }
}
