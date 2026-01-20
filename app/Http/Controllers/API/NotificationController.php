<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponse;

class NotificationController extends Controller
{
    use ApiResponse;
    // Get all notifications
    public function index(Request $request)
    {
        try {
            $notifications = $request->user()->notifications;
            return $this->success($notifications, 'All notifications fetched successfully', 200);
        } catch (\Exception $e) {
            return $this->error([], 'Something went wrong', 200);
        }
    }

    // Get unread notifications
    public function unread(Request $request)
    {
        try {
            $unread = $request->user()->unreadNotifications;

            if ($unread->isEmpty()) {
                return $this->success([], 'No unread notifications', 200);
            }

            return $this->success($unread, 'Unread notifications fetched successfully', 200);
        } catch (\Exception $e) {
            return $this->error([], 'Something went wrong', 500);
        }
    }


    // Mark single notification as read
    public function markAsRead(Request $request, $id)
    {
        try {
            $notification = $request->user()->notifications()->findOrFail($id);
            $notification->markAsRead();
            return $this->success(null, 'Notification marked as read', 200);
        } catch (\Exception $e) {
            return $this->error([], 'Something went wrong', 500);
        }
    }

    // Mark all notifications as read
    public function markAllAsRead(Request $request)
    {
        try {
            $request->user()->unreadNotifications->markAsRead();
            return $this->success(null, 'All notifications marked as read', 200);
        } catch (\Exception $e) {
            return $this->error([], 'Something went wrong', 500);
        }
    }
    // Delete a single notification
    public function delete(Request $request)
    {

        $id = $request->id;
    
        try {
            $notification = $request->user()->notifications()->findOrFail($id);
            $notification->delete();

            return $this->success(null, 'Notification deleted successfully', 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->error([], 'Notification not found', 404);
        } catch (\Exception $e) {
            \Log::error($e);
            return $this->error([], 'Something went wrong', 500);
        }
    }
}
