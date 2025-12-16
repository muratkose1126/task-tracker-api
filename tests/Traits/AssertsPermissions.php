<?php

namespace Tests\Traits;

use Illuminate\Testing\TestResponse;

trait AssertsPermissions
{
    protected function assertCanAccess(TestResponse $response): TestResponse
    {
        return $response->assertStatus(200);
    }

    protected function assertCanCreate(TestResponse $response): TestResponse
    {
        return $response->assertStatus(201);
    }

    protected function assertCannotAccess(TestResponse $response): TestResponse
    {
        return $response->assertStatus(403);
    }

    protected function assertCannotCreate(TestResponse $response): TestResponse
    {
        return $response->assertStatus(403);
    }

    protected function assertUnprocessableData(TestResponse $response): TestResponse
    {
        return $response->assertStatus(422);
    }

    protected function assertNotFoundData(TestResponse $response): TestResponse
    {
        return $response->assertStatus(404);
    }

    protected function assertDeletedData(TestResponse $response): TestResponse
    {
        return $response->assertNoContent();
    }

    protected function assertUnauthorized(TestResponse $response): TestResponse
    {
        return $response->assertStatus(401);
    }
}
