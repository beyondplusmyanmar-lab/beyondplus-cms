<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_store_display_and_delete_local_image(): void
    {
        $name = bp_store_image(UploadedFile::fake()->image('pic.jpg', 40, 40), 'test');

        $this->assertNotNull($name);
        $this->assertFileExists(public_path('uploads/'.$name));

        // Display resolver: local filename → /uploads path, full URL → as-is.
        $this->assertStringContainsString('uploads/'.$name, bp_upload_url($name));
        $this->assertSame('https://cdn.example.com/x.jpg', bp_upload_url('https://cdn.example.com/x.jpg'));

        bp_delete_upload($name);
        $this->assertFileDoesNotExist(public_path('uploads/'.$name));
    }

    public function test_non_image_upload_is_rejected(): void
    {
        $this->assertNull(bp_store_image(UploadedFile::fake()->create('script.php', 1), 'test'));
    }
}
