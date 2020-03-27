<?php

namespace Tests\Feature;

use JWTAuth;
use App\User;
use App\Report;
use App\ReportFix;
use App\ReportConfirmation;
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
        $confirmation = ReportConfirmation::create([
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
        
        $this->assertEquals(0, ReportConfirmation::all()->count());
        
        $this->assertTrue($report->confirmBy($sabah));
        
        $this->assertEquals(1, ReportConfirmation::all()->count());
    }

    public function test_authentcated_user_can_sees_his_own_posted_reports()
    {
        $reports = factory(Report::class, 5)->make();
        $user = factory(User::class)->create();
        $token = JWTAuth::fromUser($user);
        $user->reports()->saveMany($reports);
        
        $response = $this->json('GET', route('users.reports.index'), [
            'token' => $token
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'description',
                        'lat',
                        'lng',
                        'picture'
                    ]
                ]
            ]);
    }

    public function test_report_should_have_a_fixed_request()
    {
        $report = factory(Report::class)->make();
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();
        $userA->reports()->save($report);

        $this->assertEquals(0, ReportFix::all()->count());
        
        $report->fixedRequestBy($userB);

        $this->assertEquals(1, ReportFix::all()->count());
        $this->assertEquals($userB->id, ReportFix::all()->first()->user_id);
    }

    public function test_authenticated_user_cannot_see_another_user_reports()
    {
        $nasser = factory(User::class)->create();
        $sabah = factory(User::class)->create();
        $token = JWTAuth::fromUser($nasser);
        $reports = factory(Report::class, 3)->make();
        $sabah->reports()->saveMany($reports);

        $response = $this->json('GET', route('users.reports.index'), [
            'token' => $token
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                'data' => []
        ]);
    }

    public function test_guest_should_receive_unauthorized_response_trying_to_access_his_reprots()
    {
        $this->json('GET', route('users.reports.index', ['id' => 999]))->assertStatus(401);
    }
    
    public function test_authenticated_user_can_delete_his_own_report()
    {
        $user = factory(User::class)->create();
        $token = JWTAuth::fromUser($user);
        $report = factory(Report::class)->make();
        $user->reports()->save($report);

        $response = $this->json('DELETE', route('users.reports.destroy', ['id' => $report->id]), [
            'token' => $token
        ]);

        $response->assertStatus(204);
    }
    
    public function test_authenticated_user_cannot_delete_inexsitent_report()
    {
        $user = factory(User::class)->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->json('DELETE', route('users.reports.destroy', ['id' => 9999]), [
            'token' => $token
        ]);

        $response
            ->assertStatus(404)
            ->assertJson([
                'error' => __('There is no report with this id.')
            ]);
    }

    public function test_guest_cannot_delete_others_reports()
    {
        $user = factory(User::class)->create();
        $token = JWTAuth::fromUser($user);
        $report = factory(Report::class)->make();
        $user->reports()->save($report);

        $response = $this->json('DELETE', route('users.reports.destroy', ['id' => $report->id]));

        $response->assertStatus(401);
    }
}
