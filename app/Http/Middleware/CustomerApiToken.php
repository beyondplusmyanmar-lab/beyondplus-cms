<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Customers;

/**
 * Authenticates a customer for the protected /api/m/account/* endpoints using
 * the personal access token carried in the X-BP-Token header. Tokens are stored
 * hashed, so a database leak never exposes usable tokens.
 */
class CustomerApiToken
{
    public function handle($request, Closure $next)
    {
        $token = trim((string) $request->header('X-BP-Token'));

        if ($token === '') {
            return response()->json(['status' => 401, 'data' => ['message' => 'Authentication required.']], 401);
        }

        $customer = Customers::where('api_token', hash('sha256', $token))
            ->where('status', 1)
            ->where('is_verified', 1)
            ->first();

        if (! $customer) {
            return response()->json(['status' => 401, 'data' => ['message' => 'Invalid or expired token.']], 401);
        }

        $request->setUserResolver(fn () => $customer);

        return $next($request);
    }
}
