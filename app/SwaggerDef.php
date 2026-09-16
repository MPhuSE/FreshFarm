<?php

namespace App;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Nong San Xanh API",
 *     version="1.0.0",
 *     description="Tài liệu API cho dự án Nông Sản Xanh."
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class SwaggerDef {}
