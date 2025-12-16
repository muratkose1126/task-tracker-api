<?php

namespace Tests\Traits;

use Illuminate\Testing\TestResponse;

trait AssertsJsonResponse
{
    protected function assertJsonDataStructure(TestResponse $response, array $structure): TestResponse
    {
        return $response->assertJsonStructure(['data' => $structure]);
    }

    protected function assertJsonResourceStructure(TestResponse $response, array $structure): TestResponse
    {
        return $response->assertJsonStructure(['data' => $structure]);
    }

    protected function assertJsonErrorStructure(TestResponse $response): TestResponse
    {
        return $response->assertJsonStructure(['message']);
    }

    protected function assertJsonResourceCollection(TestResponse $response, array $structure): TestResponse
    {
        return $response->assertJsonStructure(['data' => [$structure]]);
    }

    protected function assertJsonHasData(TestResponse $response): TestResponse
    {
        return $response->assertJsonStructure(['data']);
    }

    protected function assertJsonValidationErrors(TestResponse $response, array $fields): TestResponse
    {
        return $response->assertJsonStructure(['message', 'errors' => $fields]);
    }
}
