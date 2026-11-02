@extends('system.layout')

@section('content')
<div class="d-flex flex-column gap-7 gap-lg-10">
    <!-- Header / Action bar -->
    <div class="card card-flush shadow-sm">
        <div class="card-header align-items-center py-5 gap-2 gap-md-5">
            <div class="card-title">
                <div class="d-flex align-items-center gap-3">
                    <span class="symbol symbol-45px symbol-circle bg-light-primary">
                        <i class="ki-outline ki-notification-status text-primary fs-2x p-3"></i>
                    </span>
                    <div>
                        <h2 class="fs-2 fw-bold mb-0">{{ __('Notifications Center') }}</h2>
                        <span class="text-muted fs-7">{{ __('Track all domain alerts, workout updates, load adjustments, and progress submissions.') }}</span>
                    </div>
                </div>
            </div>
            <div class="card-toolbar flex-row-fluid justify-content-end gap-3">
                <!-- Filter buttons -->
                <div class="btn-group" role="group">
                    <a href="{{ route('system.notifications.index', ['filter' => 'all']) }}" 
                       class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-light' }}">
                        {{ __('All') }}
                    </a>
                    <a href="{{ route('system.notifications.index', ['filter' => 'unread']) }}" 
                       class="btn btn-sm {{ $filter === 'unread' ? 'btn-primary' : 'btn-light' }} position-relative">
                        {{ __('Unread') }}
                        @if($unreadCount > 0)
                            <span class="badge badge-sm badge-circle badge-danger ms-2">{{ $unreadCount }}</span>
                        @endif
                    </a>
                </div>

                @if($unreadCount > 0)
                    <form action="{{ route('system.notifications.read-all') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-light-success">
                            <i class="ki-outline ki-check-circle fs-4 me-1"></i>
                            {{ __('Mark all as read') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Notification List -->
    <div class="card card-flush shadow-sm">
        <div class="card-body p-0">
            @if($notifications->isEmpty())
                <div class="text-center py-15">
                    <div class="symbol symbol-70px symbol-circle bg-light-info mb-4">
                        <i class="ki-outline ki-notification fs-3x text-info p-4"></i>
                    </div>
                    <h3 class="fs-3 fw-bold text-gray-800">{{ __('No notifications found') }}</h3>
                    <p class="text-muted fs-6 mb-0">{{ $filter === 'unread' ? __('You have caught up with all notifications!') : __('No notifications have been received yet.') }}</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-row-bordered table-row-dashed align-middle gs-6 gy-5 mb-0">
                        <tbody>
                            @foreach($notifications as $notification)
                                @php
                                    $isUnread = !$notification->isRead();
                                @endphp
                                <tr class="{{ $isUnread ? 'bg-light-primary bg-opacity-40' : '' }}">
                                    <!-- Icon -->
                                    <td class="w-50px text-center ps-6">
                                        <div class="symbol symbol-40px symbol-circle {{ $isUnread ? 'bg-primary text-white' : 'bg-light text-muted' }}">
                                            <i class="ki-outline {{ $notification->icon ?? 'ki-notification-status' }} fs-3 {{ $isUnread ? 'text-white' : 'text-primary' }}"></i>
                                        </div>
                                    </td>

                                    <!-- Content -->
                                    <td>
                                        <div class="d-flex flex-column">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="fs-6 {{ $isUnread ? 'fw-bolder text-gray-900' : 'fw-semibold text-gray-800' }}">
                                                    {{ $notification->title }}
                                                </span>
                                                @if($isUnread)
                                                    <span class="badge badge-sm badge-light-primary fw-bold">{{ __('New') }}</span>
                                                @endif
                                            </div>
                                            <p class="text-muted fs-7 mb-1">{{ $notification->message }}</p>
                                            <div class="d-flex align-items-center gap-3 text-gray-400 fs-8">
                                                <span><i class="ki-outline ki-time fs-7 me-1"></i>{{ $notification->created_at->diffForHumans() }}</span>
                                                <span>•</span>
                                                <span>{{ $notification->created_at->format('Y-m-d h:i A') }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="text-end pe-6">
                                        <div class="d-flex justify-content-end align-items-center gap-2">
                                            @if($notification->action_url)
                                                <a href="{{ route('system.notifications.read', ['id' => $notification->id, 'redirect_to' => $notification->action_url]) }}" 
                                                   class="btn btn-sm btn-light-primary">
                                                    <i class="ki-outline ki-eye fs-5 me-1"></i>
                                                    {{ __('View') }}
                                                </a>
                                            @endif

                                            @if($isUnread)
                                                <form action="{{ route('system.notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-icon btn-light-success" title="{{ __('Mark as read') }}">
                                                        <i class="ki-outline ki-check fs-4"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($notifications->hasPages())
                    <div class="card-footer py-4 d-flex justify-content-center">
                        {{ $notifications->withQueryString()->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection

