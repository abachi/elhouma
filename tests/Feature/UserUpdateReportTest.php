<?php

namespace Tests\Feature;

use JWTAuth;
use App\User;
use App\Report;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserUpdateReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_update_description_of_his_report()
    {
        $user = factory(User::class)->create();
        $token = JWTAuth::fromUser($user);
        $report = factory(Report::class)->make([
            'lat' => '31.6032088',
            'lng' => '-2.2257426',
            'description' => 'Example of a short description.',
            'picture' => UploadedFile::fake()->image('issue.jpg')
        ]);

        $user->reports()->save($report);

        $response = $this->json('PATCH', route('reports.update', ['id' => $report->id]), [
            'token' => $token,
            'description' => 'Updated description.'
        ]);

        $response
            ->assertStatus(Response::HTTP_ACCEPTED)
            ->assertJson([
            'report' => [
                'description' => 'Updated description.',
            ]
        ]);
    }

    public function test_authenticated_user_can_update_his_report_picture()
    {
        Storage::fake('public');
        $old = UploadedFile::fake()->image('old.jpg');
        Storage::disk('public')->put('images/', $old);
        $user = factory(User::class)->create();
        $report = factory(Report::class)->make([
            'picture' => 'images/'.$old->hashName(),
        ]);
        $user->reports()->save($report);
        $token = JWTAuth::fromUser($user);
        $new = UploadedFile::fake()->image('new.jpg');

        Storage::disk('public')->assertExists('images/'.$old->hashName());
        $response = $this->json('PUT', route('reports.picture.update', ['id' => $report->id]), [
            'token' => $token,
            'picture' => $new,
        ]);
        
        $oldPicture = $report->picture;
        $newPicture = $report->refresh()->picture;
        
        $this->assertNotEquals($newPicture, $oldPicture);
        $response->assertStatus(Response::HTTP_ACCEPTED)
        ->assertJson([
            'report' =>  [
                'picture' => $newPicture
                ]
            ]);
        Storage::disk('public')->assertMissing('images/'.$old->hashName());
        Storage::disk('public')->assertExists('images/'.$new->hashName());
    }

    public function test_guest_cannot_update_report_description_of_another_user()
    {
        $report = factory(Report::class)->create();
        $response = $this->json('PATCH', route('reports.update', ['id' => $report->id]), [
            'description' => 'new description'
        ]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_authenticated_user_cannot_update_description_of_inexistent_report()
    {
        $user = factory(User::class)->create();;
        $token = JWTAuth::fromUser($user);
        $response = $this->json('PATCH', route('reports.update', ['id' => 9999]), [
            'token' => $token,
            'description' => 'Example of description',
        ]);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_authenticated_user_cannot_update_picture_of_inexistent_report()
    {
        $user = factory(User::class)->create();;
        $token = JWTAuth::fromUser($user);
        $response = $this->json('PUT', route('reports.picture.update', ['id' => 9999]), [
            'token' => $token,
            'picture' => UploadedFile::fake()->image('random.jpg'),
        ]);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
