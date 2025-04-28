<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {

            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.']
            ]);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json(
            [
                'token' => $token
            ],
            200
        );
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(
            ['message' => 'Logout successful'],
            200
        );
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully'], 201);
    }
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
    public function refresh(Request $request)
    {
        return response()->json(['message' => 'Token refreshed successfully'], 200);
    }
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Logic to send password reset link

        return response()->json(['message' => 'Password reset link sent'], 200);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Logic to reset password

        return response()->json(['message' => 'Password reset successfully'], 200);
    }
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'verification_code' => 'required|string',
        ]);

        // Logic to verify email

        return response()->json(['message' => 'Email verified successfully'], 200);
    }
    public function resendVerificationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Logic to resend verification email

        return response()->json(['message' => 'Verification email resent'], 200);
    }
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Logic to change password

        return response()->json(['message' => 'Password changed successfully'], 200);
    }
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email',
        ]);

        // Logic to update user profile

        return response()->json(['message' => 'Profile updated successfully'], 200);
    }
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        // Logic to delete user account

        return response()->json(['message' => 'Account deleted successfully'], 200);
    }
    public function getUserEvents(Request $request)
    {
        $user = $request->user();
        $events = $user->events()->with('attendees')->get();

        return response()->json($events);
    }
    public function getUserAttendees(Request $request)
    {
        $user = $request->user();
        $attendees = $user->attendees()->with('event')->get();

        return response()->json($attendees);
    }
    public function getUserNotifications(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications;

        return response()->json($notifications);
    }
    public function markNotificationAsRead(Request $request, $notificationId)
    {
        $user = $request->user();
        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['message' => 'Notification marked as read'], 200);
        }

        return response()->json(['message' => 'Notification not found'], 404);
    }
    public function deleteNotification(Request $request, $notificationId)
    {
        $user = $request->user();
        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->delete();
            return response()->json(['message' => 'Notification deleted'], 200);
        }

        return response()->json(['message' => 'Notification not found'], 404);
    }
    public function getUserSettings(Request $request)
    {
        $user = $request->user();
        $settings = $user->settings;

        return response()->json($settings);
    }
    public function updateUserSettings(Request $request)
    {
        $user = $request->user();
        $settings = $request->validate([
            'notification_preferences' => 'sometimes|array',
            'privacy_settings' => 'sometimes|array',
        ]);

        $user->settings()->update($settings);

        return response()->json(['message' => 'Settings updated successfully'], 200);
    }
    public function deleteUserSettings(Request $request)
    {
        $user = $request->user();
        $user->settings()->delete();

        return response()->json(['message' => 'Settings deleted successfully'], 200);
    }
}
