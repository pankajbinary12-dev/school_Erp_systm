@extends('student.layout')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="stat-card">
            <h5 style="color: #5a5c69; font-weight: 700; margin-bottom: 20px;">
                <i class="fas fa-bell me-2"></i>All Notifications
            </h5>
            
            @forelse($notifications as $notification)
            <div style="background: #f8f9fc; padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid 
                @if($notification->type == 'success') #1cc88a
                @elseif($notification->type == 'warning') #f6c23e
                @elseif($notification->type == 'danger') #e74a3b
                @else #36b9cc @endif;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex: 1;">
                        <h6 style="color: #5a5c69; font-weight: 600; margin-bottom: 5px;">
                            @if($notification->type == 'success')
                                <i class="fas fa-check-circle me-2" style="color: #1cc88a;"></i>
                            @elseif($notification->type == 'warning')
                                <i class="fas fa-exclamation-triangle me-2" style="color: #f6c23e;"></i>
                            @elseif($notification->type == 'danger')
                                <i class="fas fa-times-circle me-2" style="color: #e74a3b;"></i>
                            @else
                                <i class="fas fa-info-circle me-2" style="color: #36b9cc;"></i>
                            @endif
                            {{ $notification->title }}
                        </h6>
                        <p style="color: #858796; margin-bottom: 5px; font-size: 14px;">{{ $notification->message }}</p>
                        <small style="color: #858796; font-size: 12px;">
                            <i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                        </small>
                        <span class="badge" style="background: #667eea; color: white; padding: 3px 8px; font-size: 10px; margin-left: 10px;">
                            {{ ucfirst($notification->category) }}
                        </span>
                    </div>
                    @if(!$notification->is_read)
                    <span class="badge" style="background: #e74a3b; color: white; padding: 4px 8px; font-size: 11px;">
                        New
                    </span>
                    @endif
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 60px;">
                <i class="fas fa-bell-slash" style="font-size: 64px; opacity: 0.3; color: #858796; margin-bottom: 20px;"></i>
                <h5 style="color: #858796;">No notifications</h5>
            </div>
            @endforelse

            <!-- Pagination -->
            @if($notifications->hasPages())
            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
