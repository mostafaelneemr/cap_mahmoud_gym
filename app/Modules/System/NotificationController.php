<?php

namespace App\Modules\System;

use App\Models\InAppNotification;
use App\Services\InAppNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends SystemController
{
    protected InAppNotificationService $notificationService;

    public function __construct(InAppNotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Display a listing of user notifications.
     */
    public function index(Request $request)
    {
        $authUser = Auth::guard('user')->user();
        if ($authUser && $authUser->user_type == 2) {
            abort(403, __('Unauthorized access.'));
        }

        $userId = $authUser ? $authUser->id : null;
        $filter = $request->get('filter', 'all');

        $unreadCount = $this->notificationService->getUnreadCount($userId);
        $notifications = $this->notificationService->getPaginatedNotifications($userId, 15, $filter);

        $this->viewData['breadcrumb'][] = [
            'text' => __('Notifications')
        ];
        $this->viewData['pageTitle'] = __('Notifications');
        $this->viewData['notifications'] = $notifications;
        $this->viewData['unreadCount'] = $unreadCount;
        $this->viewData['filter'] = $filter;

        return $this->view('notifications.index', $this->viewData);
    }

    /**
     * Mark a specific notification as read.
     */
    public function read(Request $request, $id)
    {
        $userId = Auth::guard('user')->id();
        $notification = InAppNotification::where('id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();

        $this->notificationService->markAsRead($notification, $userId);
        $unreadCount = $this->notificationService->getUnreadCount($userId);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => __('Notification marked as read.'),
                'unread_count' => $unreadCount,
            ]);
        }

        // If redirect URL is provided or notification has an action_url
        $targetUrl = $request->input('redirect_to', $notification->action_url);
        if ($targetUrl) {
            return redirect($targetUrl);
        }

        return redirect()->back()->with('msg', __('Notification marked as read.'))->with('type', 'success');
    }

    /**
     * Mark all notifications as read for current user.
     */
    public function readAll(Request $request)
    {
        $userId = Auth::guard('user')->id();
        $affected = $this->notificationService->markAllAsRead($userId);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => __('All notifications marked as read.'),
                'affected' => $affected,
                'unread_count' => 0,
            ]);
        }

        return redirect()->back()->with('msg', __('All notifications marked as read.'))->with('type', 'success');
    }

    /**
     * AJAX endpoint to fetch unread count and latest notifications for the header dropdown.
     */
    public function dropdown(Request $request)
    {
        $userId = Auth::guard('user')->id();
        if (!$userId) {
            return response()->json(['unread_count' => 0, 'notifications' => []]);
        }

        $unreadCount = $this->notificationService->getUnreadCount($userId);
        $recent = $this->notificationService->getRecentNotifications($userId, 7);

        $items = $recent->map(function ($item) {
            $icon = $item->icon ?? 'ki-notification-status';
            // Normalize legacy FontAwesome classes to KeenIcons
            if (str_starts_with($icon, 'fa-') || str_starts_with($icon, 'fas ') || str_starts_with($icon, 'fa ')) {
                $icon = match (true) {
                    str_contains($icon, 'dumbbell'), str_contains($icon, 'weight'), str_contains($icon, 'scale') => 'ki-weight',
                    str_contains($icon, 'check') => 'ki-check-circle',
                    str_contains($icon, 'times'), str_contains($icon, 'xmark') => 'ki-cross-circle',
                    str_contains($icon, 'camera') => 'ki-camera',
                    str_contains($icon, 'utensils') => 'ki-cup',
                    str_contains($icon, 'calendar') => 'ki-calendar',
                    default => 'ki-notification-status',
                };
            }

            return [
                'id' => $item->id,
                'type' => $item->type,
                'title' => $item->title,
                'message' => $item->message,
                'action_url' => $item->action_url,
                'icon' => $icon,
                'is_read' => $item->isRead(),
                'created_at_human' => $item->created_at ? $item->created_at->diffForHumans() : '',
                'read_url' => route('system.notifications.read', $item->id),
            ];
        });

        return response()->json([
            'status' => true,
            'unread_count' => $unreadCount,
            'notifications' => $items,
        ]);
    }
}
