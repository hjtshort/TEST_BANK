<?php

namespace Tests\Feature;

use App\Models\FileImport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_login()
    {
        $user = User::factory()->create();
        $response = $this->postJson('/api/login',
            ['email' => $user->email ?? "test@gmail.com", 'password' => 'password'],
            ['Accept' => 'application/json']
        );

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'token'
            ]);
    }

    public function test_cannot_login()
    {
        $response = $this->postJson('/api/login',
            ['email' => "test@gmail.com", 'password' => 'password1'],
            ['Accept' => 'application/json']
        );

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'email'
                ]
            ]);
    }


    public function test_cannot_upload_without_token()
    {
        $response = $this->post('api/import',
            ['file' => 'aaa'],
            ['Content-Type' => 'multipart/form-data',
                'Accept' => 'application/json'
            ]
        );


        $response->assertStatus(401)
            ->assertJsonStructure([
                'message',
            ]);
    }

    public function test_cannot_upload_without_file()
    {

        // Fake user and create token
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->post('api/import',
            ['file' => 'aaa'],
            ['Content-Type' => 'multipart/form-data',
                'Accept' => 'application/json'
            ]
        );


        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'file'
                ]
            ]);
    }


    public function test_cannot_upload_file_not_excel()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $file = UploadedFile::fake()->create('document.pdf', 10);

        $response = $this->post('api/import',
            ['file' => $file],
            ['Content-Type' => 'multipart/form-data',
                'Accept' => 'application/json'
            ]
        );

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'file'
                ]
            ]);
    }


    public function test_can_upload_but_not_import_because_malformed()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $file = UploadedFile::fake()->create('document.xlsx', 10);

        $response = $this->post('api/import',
            ['file' => $file],
            ['Content-Type' => 'multipart/form-data',
                'Accept' => 'application/json'
            ]
        );

        $response->assertStatus(400)
            ->assertJsonStructure([
                'message',
            ]);
    }


    public function test_cannot_get_list_transaction_without_token()
    {
        $response = $this->get('api/transaction',
            [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                'message',
            ]);
    }


    public function test_can_get_list_transaction()
    {

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->get('api/transaction',
            [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
            ]);
    }


    public function test_cannot_get_file_imported_without_token()
    {
        $response = $this->get('api/user/file-imported',
            [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                'message',
            ]);
    }


    public function test_can_get_file_imported()
    {
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );
        $response = $this->get('api/user/file-imported',
            [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
            ]);
    }


    public function test_cannot_get_list_transaction_fail_without_token()
    {

        $user = User::factory()->create();

        $fileImport = FileImport::factory()->create([
            'import_by' => $user->id,
        ]);

        $response = $this->get('api/user/file-imported/' . $fileImport->id . '/fails',
            [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                'message',
            ]);
    }


    public function test_cannot_get_list_transaction_fail_with_id_not_found()
    {

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->get('api/user/file-imported/1231231232131231fails',
            [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(404);
    }


    public function test_can_get_list_transaction_fail()
    {

        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $fileImport = FileImport::factory()->create([
            'import_by' => auth()->id(),
        ]);

        $response = $this->get('api/user/file-imported/' . $fileImport->id . '/fails',
            [
                'Accept' => 'application/json'
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
            ]);
    }
}
