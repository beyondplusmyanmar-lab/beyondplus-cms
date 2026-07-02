<?php

namespace App\Http\Controllers\Api;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'JSON API for Beyond Plus CMS.',
    title: 'Beyond Plus CMS API Documentation',
    contact: new OA\Contact(email: 'admin@example.com'),
    license: new OA\License(name: 'Apache 2.0', url: 'http://www.apache.org/licenses/LICENSE-2.0.html')
)]
#[OA\Server(url: L5_SWAGGER_CONST_HOST, description: 'Demo API Server')]
class Controller
{
}
