@extends('layouts.admin')

@section('title', 'Notifications')
@section('header', 'Notifications')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Notifications</h1>
                <p class="text-gray-600 mt-1">Manage your notifications and alerts</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.notifications.unread') }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 text-sm">
                    Unread Only
                </a>
                <form method="POST" action="{{ route('admin.notifications.markAllAsRead') }}" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit"
                            onclick="return confirm('Are you sure you want to mark all notifications as read?')"
                            class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 text-sm">
                        Mark All as Read
                    </button>
                </form>
            </div>
        </div>

        <div class="overflow-x-auto">
            @if($notifications->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($notifications as $notification)
                        <tr class="{{ !$notification->read_at ? 'bg-blue-50' : '' }}">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    @if($notification->data['message'])
                                        {{ $notification->data['message'] }}
                                    @else
                                        {{ $notification->data['order_number'] ?? 'New Notification' }}
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    @if(isset($notification->data['customer_name']))
                                        Customer: {{ $notification->data['customer_name'] }}
                                    @endif
                                    @if(isset($notification->data['total_amount']))
                                        | Total: ${{ number_format($notification->data['total_amount'] / 100, 2) }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $notification->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    {{ $notification->read_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $notification->read_at ? 'Read' : 'Unread' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    @if(!$notification->read_at)
                                        <form method="POST" action="{{ route('admin.notifications.markAsRead', $notification->id) }}" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="text-blue-600 hover:text-blue-900">Mark Read</button>
                                        </form>
                                    @endif
                                    <a href="{{ isset($notification->data['order_id']) ? route('orders.show', $notification->data['order_id']) : '#' }}"
                                       class="text-indigo-600 hover:text-indigo-900">
                                        View
                                    </a>
                                    <form method="POST" action="{{ route('admin.notifications.delete', $notification->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this notification?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-blue-900">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No notifications</h3>
                    <p class="mt-1 text-sm text-gray-500">You don't have any notifications at the moment.</p>
                </div>
            @endif
        </div>

        @if($notifications->count() > 0)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</div>
@endsection