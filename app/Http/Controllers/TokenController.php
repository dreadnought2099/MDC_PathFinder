<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TokenController extends Controller
{
    /**
     * Check if a room token exists
     */
    public function checkRoomExists(string $token): JsonResponse
    {
        try {
            // Route constraint already ensures valid format
            $room = Room::findByValidToken($token);

            return response()->json([
                'exists'  => $room !== null,
                'room_id' => $room?->id,
                'token'   => $token,
            ]);
        } catch (\Exception $e) {
            Log::error('Token validation error', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'exists' => false,
                'error'  => 'Token validation failed',
            ], 500);
        }
    }

    /**
     * Validate token format via API (useful for forms, not QR)
     */
    public function validateTokenFormat(Request $request): JsonResponse
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json([
                'valid' => false,
                'error' => 'Token is required',
            ], 400);
        }

        return response()->json([
            'valid'               => Room::isValidTokenFormat($token),
            'token'               => $token,
            'format_requirements' => '64 character hexadecimal string',
        ]);
    }

    /**
     * Get room by token (for scanner redirect)
     */
    public function getRoomByToken(string $token)
    {
        // Route constraint already ensures valid format
        $room = Room::findByValidToken($token);

        if (!$room) {
            abort(404, 'Office not found');
        }

        // Pass to scanner controller
        return app(ScannerController::class)->showRoom($room);
    }
}
