<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends ApiController
{
    protected $model = User::class;
    protected $cacheKey = 'users';

    public function __construct()
    {
        $this->cacheTtl = env('QUERY_CACHE_TTL', 300);
    }

    /**
     * Get all doctors
     */
    public function getDoctors()
    {
        try {
            $doctors = User::where('role', 'dokter')
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'specialization', 'phone']);

            return $this->successResponse($doctors, 'Doctors retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve doctors: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get doctor schedule
     */
    public function getDoctorSchedule($id)
    {
        try {
            $doctor = User::where('role', 'dokter')
                ->where('id', $id)
                ->where('is_active', true)
                ->firstOrFail();

            // You can add schedule logic here
            $schedule = [
                'doctor' => $doctor->only(['id', 'name', 'specialization']),
                'schedule' => [] // Add actual schedule data
            ];

            return $this->successResponse($schedule, 'Doctor schedule retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Doctor not found', 404);
        }
    }

    /**
     * User login
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return $this->errorResponse('Invalid credentials', 401);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'user' => $user,
                'token' => $token
            ], 'Login successful');
        } catch (\Exception $e) {
            return $this->errorResponse('Login failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * User registration
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'role' => 'required|in:admin,dokter,staf_klinik',
                'specialization' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20'
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'specialization' => $request->specialization,
                'phone' => $request->phone,
                'is_active' => true
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->successResponse([
                'user' => $user,
                'token' => $token
            ], 'Registration successful', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Registration failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * User logout
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return $this->successResponse(null, 'Logout successful');
        } catch (\Exception $e) {
            return $this->errorResponse('Logout failed: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get current user
     */
    public function user(Request $request)
    {
        try {
            return $this->successResponse($request->user(), 'User data retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve user data: ' . $e->getMessage(), 500);
        }
    }
}
