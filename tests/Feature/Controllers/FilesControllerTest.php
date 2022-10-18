<?php

namespace Tests\Feature\Controllers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class FilesControllerTest extends TestCase
{
    public function brokerTypeProvider(): array
    {
        return [
            'Composer' => ['COMPOSER_FILE', 'composer.json'],
            'Npm' => ['NPM_FILE', 'package.json']
        ];
    }

    /** @test */
    public function it_returns_invalid_file_if_broker_type_is_not_recognized(): void
    {
        Redis::partialMock()
            ->expects('publish')
            ->never();

        $file = UploadedFile::fake()->create('random.json', 1024);

        $payload = [
            'uuid' => Str::uuid(),
            'file' => $file
        ];

        $this->postJson('api/files', $payload)
            ->assertStatus(ResponseAlias::HTTP_BAD_REQUEST)
            ->assertJson([
                'error' => 'Invalid file'
            ]);
    }

    /** @test */
    public function it_returns_no_content_if_successfully(): void
    {
        Redis::partialMock()
            ->expects('publish')
            ->once();

        $file = UploadedFile::fake()->create('composer.json', 1024);

        $payload = [
            'uuid' => Str::uuid(),
            'file' => $file
        ];

        $this->postJson('api/files', $payload)
            ->assertStatus(ResponseAlias::HTTP_NO_CONTENT);
    }

    /**
     * @dataProvider brokerTypeProvider
     * @test
     */
    public function it_publish_to_redis_in_the_correct_channel(string $brokerType, string $filename): void
    {
        $file = UploadedFile::fake()->create($filename, 1024);

        $payload = [
            'uuid' => Str::uuid(),
            'file' => $file
        ];

        $brokerPayload = [
            'headers' => [
                'uuid' => $payload['uuid'],
            ],
            'payload' => json_decode(file_get_contents($file->getRealPath()))
        ];

        Redis::partialMock()
            ->expects('publish')
            ->once()
            ->with($brokerType, json_encode($brokerPayload));

        $this->postJson('api/files', $payload)
            ->assertStatus(ResponseAlias::HTTP_NO_CONTENT);
    }
}
