<?php

namespace Tests\Feature;

use App\User;
use App\Report;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserAddReportTest extends TestCase
{
    use RefreshDatabase;

    private function reportData($data = [])
    {
        return array_merge([
            'lat' => '31.6032088',
            'lng' => '-2.2257426',
            'description' => 'Example of a short description.',
            'picture' => UploadedFile::fake()->image('issue.jpg')
        ], $data);
    }

    private function reportDataWithToken($data = [])
    {
        $data = $this->reportData($data);
        $data['token'] = \JWTAuth::fromUser(factory(User::class)->create());
        return $data;
    }

    public function test_authenticated_user_can_add_a_valid_report()
    {
        Storage::disk('public')->assertMissing('issue.jpg');
        $this->assertNull(Report::all()->first());
        $response = $this->json('POST', route('reports.store'), $this->reportDataWithToken());
        $report = Report::all()->first();
        $this->assertNotNull($report);
        Storage::disk('public')->assertExists($report->picture);
        $response
            ->assertStatus(201)
            ->assertJsonStructure(['report']);
    }

    public function test_cannot_add_a_report_with_invalid_token()
    {
        $response = $this->json('POST', route('reports.store'), $this->reportData());
        $this->assertEquals(0, Report::all()->count());
        $response->assertStatus(401);
    }

    public function test_should_not_add_report_with_missing_latitude()
    {
        $response = $this->json('POST', route('reports.store'), $this->reportDataWithToken([
            'lat' => '',
        ]));
        $this->assertEquals(0, Report::all()->count());
        $response->assertStatus(422);
    }

    public function test_should_not_add_report_with_invalid_latitude()
    {
        $response = $this->json('POST', route('reports.store'), $this->reportDataWithToken([
            'lat' => 'invalid_lat',
        ]));
        $this->assertEquals(0, Report::all()->count());
        $response->assertStatus(422);
    }

    public function test_should_not_add_report_with_missing_longitude()
    {
        $response = $this->json('POST', route('reports.store'), $this->reportDataWithToken([
            'lng' => '',
        ]));
        $this->assertEquals(0, Report::all()->count());
        $response->assertStatus(422);
    }

    public function test_should_not_add_report_with_invalid_longitude()
    {
        $response = $this->json('POST', route('reports.store'), $this->reportDataWithToken([
            'lng' => 'invalid_lng',
        ]));
        $this->assertEquals(0, Report::all()->count());
        $response->assertStatus(422);
    }

    public function test_should_not_add_report_with_missing_picture()
    {
        $response = $this->json('POST', route('reports.store'), $this->reportDataWithToken([
            'picture' => '',
        ]));
        $this->assertEquals(0, Report::all()->count());
        $response->assertStatus(422);
    }

    public function test_should_not_add_report_with_invalid_picture()
    {
        $response = $this->json('POST', route('reports.store'), $this->reportDataWithToken([
            'picture' => UploadedFile::fake()->image('issue.php')
        ]));
        $this->assertEquals(0, Report::all()->count());
        $response->assertStatus(422);
    }

    public function test_should_add_report_with_optional_description()
    {
        $this->assertNull(Report::all()->first());
        $response = $this->json('POST', route('reports.store'), $this->reportDataWithToken([
            'description' => ''
        ]));
        $this->assertNotNull(Report::all()->first());
        $response
            ->assertStatus(201)
            ->assertJsonStructure(['report']);
    }
}
