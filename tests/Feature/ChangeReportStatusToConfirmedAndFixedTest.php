<?php

namespace Tests\Feature;

use JWTAuth;
use App\User;
use App\Report;
use App\ReportFix;
use Tests\TestCase;
use App\ReportConfirmation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChangeReportStatusToConfirmedAndFixedTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_confirm_an_existinng_report()
    {
        $nasser = factory(User::class)->create();
        $sabah = factory(User::class)->create();
        $token = JWTAuth::fromUser($nasser);

        $report = factory(Report::class)->make();
        $sabah->reports()->save($report);

        $response = $this->json('POST', route('reports.confirmations.store', ['id' => $report->id]), [
            'token' => $token
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

    }

    public function test_guest_cannot_confirm_a_report()
    {
        $report = factory(Report::class)->create();

        $response = $this->json('POST', route('reports.confirmations.store', ['id' => $report->id]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_authenticated_user_cannot_confirm_his_own_report()
    {
        $user = factory(User::class)->create();
        $token = JWTAuth::fromUser($user);
        $report = factory(Report::class)->make();
        $user->reports()->save($report);

        $response = $this->json('POST', route('reports.confirmations.store', ['id' => $report->id]), [
            'token' => $token
        ]);

        $response->assertStatus(Response::HTTP_NOT_ACCEPTABLE);
    }

    public function test_authenticated_user_cannot_confirm_inexistent_report()
    {
        $user = factory(User::class)->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->json('POST', route('reports.confirmations.store', ['id' => 9999]), [
            'token' => $token
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_authenticated_user_can_uncofirm_existing_report()
    {
        $user = factory(User::class)->create();
        $token = JWTAuth::fromUser($user);
        $report = factory(Report::class)->make();
        $user->reports()->save($report);
        $report->confirmBy($user);
        $this->assertEquals(1, ReportConfirmation::all()->count());
        $response = $this->json('DELETE', route('reports.confirmations.destroy', ['id' => $report->id]), [
            'token' => $token
        ]);
        $this->assertEquals(0, ReportConfirmation::all()->count());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_authenticated_user_cannot_uncofirm_inexisting_report()
    {
        $user = factory(User::class)->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->json('DELETE', route('reports.confirmations.destroy', ['id' => 9999]), [
            'token' => $token
        ]);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
    
    public function test_guest_cannot_uncofirm_report()
    {
        $user = factory(User::class)->create();
        $token = JWTAuth::fromUser($user);
        $report = factory(Report::class)->make();
        $user->reports()->save($report);
        $report->confirmBy($user);
        $this->assertEquals(1, ReportConfirmation::all()->count());
        $response = $this->json('DELETE', route('reports.confirmations.destroy', ['id' => $report->id]), [
        ]);
        $this->assertEquals(1, ReportConfirmation::all()->count());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_authenticated_user_can_request_the_reporter_to_change_report_status_to_fixed()
    {
        $nasser = factory(User::class)->create();
        $sabah = factory(User::class)->create();
        $report = factory(Report::class)->make();
        $nasser->reports()->save($report);
        $token = JWTAuth::fromUser($sabah);
        $response = $this->json('POST', route('reports.fix.store', ['id' => $report->id]), [
            'token' => $token
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_authenticated_user_cannot_request_inexistent_report_to_change_status_to_fixed()
    {
        $user = factory(User::class)->create();
        $token = JWTAuth::fromUser($user);
        $response = $this->json('POST', route('reports.fix.store', ['id' => 9999]), [
            'token' => $token
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_guest_cannot_request_fix_for_a_report()
    {
        $report = factory(Report::class)->create();
        $response = $this->json('POST', route('reports.fix.store', ['id' => $report->id]));

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_authenticated_user_can_cancel_fix_request_of_a_report()
    {
        $nasser = factory(User::class)->create();
        $sabah = factory(User::class)->create();
        $report = factory(Report::class)->make();
        $nasser->reports()->save($report);
        $token = JWTAuth::fromUser($sabah);
        $report->fixedRequestBy($sabah);
        $this->assertEquals(1, ReportFix::all()->count());
        $response = $this->json('DELETE', route('reports.fix.destroy', ['id' => $report]), [
            'token' => $token
        ]);
        $this->assertEquals(0, ReportFix::all()->count());
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
    
    public function test_guest_cannot_cancel_request_fix_for_a_report()
    {
        $report = factory(Report::class)->create();
        $user = factory(User::class)->create();
        $report->fixedRequestBy($user);
        $response = $this->json('DELETE', route('reports.fix.destroy', ['id' => $report->id]));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
