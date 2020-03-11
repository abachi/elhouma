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
        Storage::fake('images');
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
        Storage::disk('local')->assertMissing('issue.jpg');
        $this->assertNull(Report::all()->first());
        $response = $this->json('POST', route('reports.store'), $this->reportDataWithToken());
        $report = Report::all()->first();
        $this->assertNotNull($report);
        Storage::disk('local')->assertExists($report->picture);
        $response
            ->assertStatus(201)
            ->assertJsonStructure(['report']);
    }

    public function test_cannot_add_a_report_with_invalid_token()
    {
        $this->assertNull(Report::all()->first());
        $response = $this->json('POST', route('reports.store'), $this->reportData());
        $response->assertStatus(401);
    }

    public function test_should_not_add_report_with_missing_latitude()
    {
        $this->assertNull(Report::all()->first());
        $response = $this->json('POST', route('reports.store'), $this->reportDataWithToken([
            'lat' => '',
        ]));
        $this->assertNull(Report::all()->first());
        $response->assertStatus(422);
    }

    public function test_should_not_add_report_with_invalid_latitude()
    {
        $this->assertNull(Report::all()->first());
        $response = $this->json('POST', route('reports.store'), $this->reportDataWithToken([
            'lat' => 'invalid_lat',
        ]));
        $this->assertNull(Report::all()->first());
        $response->assertStatus(422);
    }

    public function test_should_not_add_report_with_missing_longitude()
    {
        $this->assertNull(Report::all()->first());
        $response = $this->json('POST', route('reports.store'), $this->reportDataWithToken([
            'lng' => '',
        ]));
        $this->assertNull(Report::all()->first());
        $response->assertStatus(422);
    }

    public function test_should_not_add_report_with_invalid_longitude()
    {
        $this->assertNull(Report::all()->first());
        $response = $this->json('POST', route('reports.store'), $this->reportDataWithToken([
            'lng' => 'invalid_lng',
        ]));
        $this->assertNull(Report::all()->first());
        $response->assertStatus(422);
    }

    public function test_should_not_add_report_with_missing_picture()
    {
        $this->assertNull(Report::all()->first());
        $response = $this->json('POST', route('reports.store'), $this->reportDataWithToken([
            'picture' => '',
        ]));
        $this->assertNull(Report::all()->first());
        $response->assertStatus(422);
    }

    public function test_should_not_add_report_with_invalid_picture()
    {
        $this->assertNull(Report::all()->first());
        $response = $this->json('POST', route('reports.store'), $this->reportDataWithToken([
            'picture' => UploadedFile::fake()->image('issue.php')
        ]));
        $this->assertNull(Report::all()->first());
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
