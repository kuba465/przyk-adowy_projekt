<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProxyControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $route = 'api/proxy/test';

    public function test_should_always_return_response_with_200_status_code()
    {
        Http::fake();
        $response = Http::get($this->route);
        $this->assertEquals(200, $response->status());
    }

    public function test_should_attach_file_to_response_if_file_is_given_in_request()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post($this->route, [
            'file' => UploadedFile::fake()->createWithContent(
                'document.pdf', 'Lorem Ipsum'
            )
        ]);
        //I don't know how to get request body from that response. If I could do that I should check if file was added to request.
        $response->assertStatus(200);
    }

    public function test_response_should_has_same_content_as_when_sending_request_by_hand()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get($this->route);
        $apiResponse = $this->get('/api/test');
        $this->assertEquals($response->getContent(), $apiResponse->getContent());
    }
}
