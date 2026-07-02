<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Customers;
use App\Repositories\CustomersRepo;
use App\Services\OtpNotifier;
use OpenApi\Attributes as OA;

/**
 * Token-based customer auth API (/api/m/auth/* and /api/m/account/*).
 *
 * Auth endpoints are rate limited stricter than the read API (throttle:5,1) to
 * resist brute force. Tokens are random 64-char strings stored as a SHA-256
 * hash; the plaintext is only ever returned to the client once.
 */
class CustomerAuthController extends Controller
{
    protected $customersRepo;
    protected $otpNotifier;

    public function __construct(CustomersRepo $customersRepo, OtpNotifier $otpNotifier)
    {
        $this->customersRepo = $customersRepo;
        $this->otpNotifier   = $otpNotifier;
    }

    // ---- helpers ---------------------------------------------------------

    private function respond($data, int $status = 200)
    {
        return response()->json(['status' => $status, 'data' => $data], $status);
    }

    private function fail(string $message, int $status = 422, $errors = null)
    {
        $data = ['message' => $message];
        if ($errors) {
            $data['errors'] = $errors;
        }
        return $this->respond($data, $status);
    }

    private function issueToken(Customers $customer): string
    {
        $plain = Str::random(64);
        $customer->api_token = hash('sha256', $plain);
        $customer->save();
        return $plain;
    }

    private function customerPayload(Customers $c): array
    {
        return [
            'id'            => $c->id,
            'first_name'    => $c->first_name,
            'last_name'     => $c->last_name,
            'phone'         => $c->phone,
            'email'         => $c->email,
            'reward_points' => $c->total_reward_points,
        ];
    }

    private function findByIdentifier(?string $identifier): ?Customers
    {
        if (! $identifier) {
            return null;
        }
        return Customers::where('phone', $identifier)->orWhere('email', $identifier)->first();
    }

    private function matchOtp(string $identifier, $code): ?Customers
    {
        return Customers::where('otpcode', $code)
            ->where(function ($q) use ($identifier) {
                $q->where('phone', $identifier)->orWhere('email', $identifier);
            })->first();
    }

    // ---- endpoints -------------------------------------------------------

    /** Register a customer (phone/email per registration_type) and send an OTP. */
    #[OA\Post(
        path: '/api/m/auth/register',
        summary: 'Register a customer and send an OTP (rate limited 5/min)',
        tags: ['Customer Auth'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['firstname', 'password', 'password_confirmation'],
            properties: [
                new OA\Property(property: 'firstname', type: 'string'),
                new OA\Property(property: 'lastname', type: 'string'),
                new OA\Property(property: 'phone', type: 'string', description: 'Required when registration is by phone or both'),
                new OA\Property(property: 'email', type: 'string', description: 'Required when registration is by email or both'),
                new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8),
                new OA\Property(property: 'password_confirmation', type: 'string', format: 'password'),
            ]
        )),
        responses: [
            new OA\Response(response: 200, description: 'Account created; OTP sent'),
            new OA\Response(response: 422, description: 'Validation failed'),
            new OA\Response(response: 429, description: 'Too many attempts'),
        ]
    )]
    public function register(Request $request)
    {
        $regType = bp_option('registration_type', 'phone');

        $rules = ['firstname' => 'required', 'password' => 'required|confirmed|min:8'];
        if ($regType === 'phone' || $regType === 'both') {
            $rules['phone'] = 'required|unique:customers|min:10';
        }
        if ($regType === 'email' || $regType === 'both') {
            $rules['email'] = 'required|email|unique:customers';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->fail('Validation failed.', 422, $validator->errors());
        }

        if (! $this->customersRepo->createCustomer($request)) {
            return $this->fail('Registration failed.', 500);
        }

        $identifier = ($regType === 'email') ? $request->email : $request->phone;

        return $this->respond([
            'message'    => 'Account created. Enter the OTP sent to you to verify.',
            'identifier' => $identifier,
        ]);
    }

    /** Verify the registration OTP and issue a token. */
    #[OA\Post(
        path: '/api/m/auth/verify',
        summary: 'Verify the registration OTP and receive a token (rate limited 5/min)',
        tags: ['Customer Auth'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['identifier', 'code'],
            properties: [
                new OA\Property(property: 'identifier', type: 'string', description: 'The phone or email used at registration'),
                new OA\Property(property: 'code', type: 'string', description: 'The OTP code'),
            ]
        )),
        responses: [
            new OA\Response(response: 200, description: 'Verified; returns token + customer'),
            new OA\Response(response: 422, description: 'Invalid OTP'),
            new OA\Response(response: 429, description: 'Too many attempts'),
        ]
    )]
    public function verify(Request $request)
    {
        $validator = Validator::make($request->all(), ['identifier' => 'required', 'code' => 'required']);
        if ($validator->fails()) {
            return $this->fail('Validation failed.', 422, $validator->errors());
        }

        $customer = $this->matchOtp($request->identifier, $request->code);
        if (! $customer) {
            return $this->fail('Invalid OTP code.', 422);
        }

        $customer->is_verified = 1;
        $customer->status = $customer->status ?? 1;
        $customer->otpcode = null;
        $token = $this->issueToken($customer);

        return $this->respond(['token' => $token, 'customer' => $this->customerPayload($customer)]);
    }

    /** Authenticate with identifier + password and issue a token. */
    #[OA\Post(
        path: '/api/m/auth/login',
        summary: 'Log in with phone/email + password (rate limited 5/min)',
        tags: ['Customer Auth'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['identifier', 'password'],
            properties: [
                new OA\Property(property: 'identifier', type: 'string', description: 'Phone or email'),
                new OA\Property(property: 'password', type: 'string', format: 'password'),
            ]
        )),
        responses: [
            new OA\Response(response: 200, description: 'Returns token + customer'),
            new OA\Response(response: 401, description: 'Invalid credentials'),
            new OA\Response(response: 403, description: 'Inactive or unverified account'),
            new OA\Response(response: 429, description: 'Too many attempts'),
        ]
    )]
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), ['identifier' => 'required', 'password' => 'required']);
        if ($validator->fails()) {
            return $this->fail('Validation failed.', 422, $validator->errors());
        }

        $customer = $this->findByIdentifier($request->identifier);
        if (! $customer || ! Hash::check($request->password, (string) $customer->password)) {
            return $this->fail('Invalid credentials.', 401);
        }
        if ((int) $customer->status !== 1) {
            return $this->fail('Account is inactive.', 403);
        }
        if ((int) $customer->is_verified !== 1) {
            return $this->fail('Account is not verified.', 403);
        }

        $token = $this->issueToken($customer);

        return $this->respond(['token' => $token, 'customer' => $this->customerPayload($customer)]);
    }

    /** Send a password-reset OTP. Never reveals whether the account exists. */
    #[OA\Post(
        path: '/api/m/auth/forgot-password',
        summary: 'Request a password-reset OTP (rate limited 5/min)',
        tags: ['Customer Auth'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['identifier'],
            properties: [new OA\Property(property: 'identifier', type: 'string', description: 'Phone or email')]
        )),
        responses: [
            new OA\Response(response: 200, description: 'Generic success (does not disclose account existence)'),
            new OA\Response(response: 429, description: 'Too many attempts'),
        ]
    )]
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), ['identifier' => 'required']);
        if ($validator->fails()) {
            return $this->fail('Validation failed.', 422, $validator->errors());
        }

        $customer = $this->findByIdentifier($request->identifier);
        if ($customer) {
            $customer->otpcode = random_int(100000, 999999);
            $customer->save();
            $this->otpNotifier->send($customer, $customer->otpcode);
        }

        return $this->respond(['message' => 'If the account exists, an OTP has been sent.']);
    }

    /** Reset the password with a valid OTP and invalidate existing tokens. */
    #[OA\Post(
        path: '/api/m/auth/reset-password',
        summary: 'Reset the password with an OTP (rate limited 5/min)',
        tags: ['Customer Auth'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['identifier', 'code', 'password', 'password_confirmation'],
            properties: [
                new OA\Property(property: 'identifier', type: 'string', description: 'Phone or email'),
                new OA\Property(property: 'code', type: 'string', description: 'The OTP code'),
                new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8),
                new OA\Property(property: 'password_confirmation', type: 'string', format: 'password'),
            ]
        )),
        responses: [
            new OA\Response(response: 200, description: 'Password updated'),
            new OA\Response(response: 422, description: 'Invalid OTP or validation error'),
            new OA\Response(response: 429, description: 'Too many attempts'),
        ]
    )]
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required',
            'code'       => 'required',
            'password'   => 'required|confirmed|min:8',
        ]);
        if ($validator->fails()) {
            return $this->fail('Validation failed.', 422, $validator->errors());
        }

        $customer = $this->matchOtp($request->identifier, $request->code);
        if (! $customer) {
            return $this->fail('Invalid OTP code.', 422);
        }

        $customer->password = Hash::make($request->password);
        $customer->otpcode = null;
        $customer->api_token = null; // force re-login everywhere
        $customer->save();

        return $this->respond(['message' => 'Password updated. Please log in again.']);
    }

    /** Authenticated customer profile. */
    #[OA\Get(
        path: '/api/m/account/profile',
        summary: 'Authenticated customer profile',
        tags: ['Customer Auth'],
        parameters: [new OA\Parameter(name: 'X-BP-Token', in: 'header', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Customer profile'),
            new OA\Response(response: 401, description: 'Missing or invalid token'),
        ]
    )]
    public function profile(Request $request)
    {
        return $this->respond(['customer' => $this->customerPayload($request->user())]);
    }

    /** Revoke the current token. */
    #[OA\Post(
        path: '/api/m/account/logout',
        summary: 'Revoke the current token',
        tags: ['Customer Auth'],
        parameters: [new OA\Parameter(name: 'X-BP-Token', in: 'header', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [
            new OA\Response(response: 200, description: 'Logged out'),
            new OA\Response(response: 401, description: 'Missing or invalid token'),
        ]
    )]
    public function logout(Request $request)
    {
        $customer = $request->user();
        $customer->api_token = null;
        $customer->save();

        return $this->respond(['message' => 'Logged out.']);
    }
}
