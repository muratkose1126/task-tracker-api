<?php

/**
 * @OA\Info(
 *     title="Task Tracker API",
 *     version="1.0.0",
 *     description="A comprehensive task tracking API built with Laravel",
 *     contact={
 *         "name": "API Support"
 *     }
 * )
 *
 * @OA\Server(
 *     url="http://localhost/api/v1",
 *     description="Local Development Server"
 * )
 *
 * @OA\Server(
 *     url="https://api.example.com/v1",
 *     description="Production Server"
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Login with email and password to get the authentication token",
 *     name="Bearer",
 *     in="header",
 *     scheme="bearer",
 *     securityScheme="sanctum",
 * )
 */

namespace App\Http\Controllers;

abstract class Controller
{
    //
}
