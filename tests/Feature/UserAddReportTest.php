<?php

namespace Tests\Feature;

use JWTAuth;
use App\User;
use App\Report;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserAddReportTest extends TestCase
{
    use RefreshDatabase;

    private $validData;

    public function setUp() : void
    {
        parent::setUp();
        Storage::fake('s3');
        $user = factory(User::class)->create();
        $token = JWTAuth::fromUser($user);
        $this->validData = [
            'token' => $token,
            'lat' => '31.6032088',
            'lng' => '-2.2257426',
            'description' => 'Example of a short description.',
            'picture' => UploadedFile::fake()->image('issue.jpg'),
            'confirmed' => false,
            'fixed' => false,
        ];
    }

    public function test_authenticated_user_can_add_a_valid_report()
    {
        $this->assertNull(Report::all()->first());
        
        $response = $this->postJson(route('reports.store'), $this->validData);

        $report = Report::all()->first();
        $this->assertNotNull($report);
        Storage::disk('s3')->assertExists($report->pciture);
        $response->assertStatus(201);
        $response->assertJsonStructure(['report']);
    }

    public function test_should_add_report_with_optional_description()
    {
        $this->assertNull(Report::all()->first());
        $data = array_merge($this->validData, ['description' => null]);
        $response = $this->json('POST', route('reports.store'), $data);
        $this->assertNotNull(Report::all()->first());
        $response->assertStatus(201);
        $response->assertJsonStructure(['report']);
    }

    /**
     * @dataProvider reportDataProvider
     */
    public function testReportInputValidation($invalidData, $key)
    {
        $data = array_merge($this->validData, $invalidData);
        $response = $this->postJson(route('reports.store', $data));
        $response->assertStatus(422);
        $response->assertJsonValidationErrors([$key]);
    }

    public function reportDataProvider()
    {
        return [
            [['lat' => null], 'lat'],
            [['lat' => 'not-numeric'], 'lat'],
            [['lng' => null], 'lng'],
            [['lng' => 'not-numeric'], 'lng'],
            [['picture' => null], 'picture'],
            [['picture' => UploadedFile::fake()->create('not-image.pdf')], 'picture'],
        ];
    }
}
