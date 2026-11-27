<?php

namespace Tests\Feature;

use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    public function test_user_with_required_role_is_allowed(): void
    {
        $request = Request::create('/nasabah/dashboard');
        $request->setUserResolver(fn () => new User(['role' => 'nasabah']));

        $response = (new RoleMiddleware())->handle(
            $request,
            fn () => response('allowed'),
            'nasabah',
        );

        $this->assertSame('allowed', $response->getContent());
    }

    public function test_user_without_required_role_is_forbidden(): void
    {
        $request = Request::create('/admin/dashboard');
        $request->setUserResolver(fn () => new User(['role' => 'nasabah']));

        $this->expectException(HttpException::class);

        (new RoleMiddleware())->handle(
            $request,
            fn () => response('allowed'),
            'admin',
        );
    }
}
