<?php

namespace App\Documentation;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Laravel API 文档",
 *     description="Laravel RESTful API 文档",
 *     @OA\Contact(
 *         email="admin@example.com",
 *         name="API 支持团队"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 *
 * @OA\Server(
 *     url="/api",
 *     description="API 服务器"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class OpenApiDocumentation
{
    // 这个类只用于存放OpenAPI注释
}
