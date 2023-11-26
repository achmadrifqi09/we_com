<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateImage()
    {
        Storage::fake('images');

        $this->post('/api/images', [
            'image' => UploadedFile::fake()->image('image.jpg')
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'id' => '',
                    'image' => '',
                ]
            ]);
    }
}
