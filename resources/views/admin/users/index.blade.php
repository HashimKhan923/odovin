
@extends('admin.layouts.app')
@section('title','Users')
@section('content')
<div class="page-header">
    <div>
        <div class="page-title">Users</div>
        <div class="page-sub">Manage all registered users</div>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        New User
    </a>
</div>
<form method="GET" class="filter-bar">
    <div style="flex:1;min-width:180px;">
        <div style="font-size:.72rem;color:var(--text-tertiary);margin-bottom:.375rem;">Search</div>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or email…" class="form-input" style="margin:0;">
    </div>
    <div>
        <div style="font-size:.72rem;color:var(--text-tertiary);margin-bottom:.375rem;">Status</div>
        <select name="status" class="form-select" style="margin:0;width:140px;">
            <option value="">All Status</option>
            <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
            <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
        </select>
    </div>
    <div>
        <div style="font-size:.72rem;color:var(--text-tertiary);margin-bottom:.375rem;">Sort by</div>
        <select name="sort_by" class="form-select" style="margin:0;width:140px;">
            <option value="created_at">Date Joined</option>
            <option value="name" {{ request('sort_by')=='name'?'selected':'' }}>Name</option>
            <option value="email" {{ request('sort_by')=='email'?'selected':'' }}>Email</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary" style="align-self:flex-end;">Filter</button>
    @if(request()->anyFilled(['search','status','sort_by']))
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary" style="align-self:flex-end;">Clear</a>
    @endif
</form>
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Vehicles</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($users as $user)
        <tr>
            <td style="color:var(--text-tertiary);font-size:.78rem;">{{ $user->id }}</td>
            <td>
                <div style="display:flex;align-items:center;gap:.75rem;">
                    <div style="width:34px;height:34px;border-radius:8px;flex-shrink:0;
                        background:linear-gradient(135deg,var(--accent),var(--accent-alt));
                        display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem;">
                        {{ substr($user->name,0,1) }}
                    </div>
                    <span style="font-weight:600;">{{ $user->name }}</span>
                </div>
            </td>
            <td style="color:var(--text-secondary);">{{ $user->email }}</td>
            <td><span class="pill pill-{{ $user->user_type }}">{{ $user->user_type }}</span></td>
            <td style="font-weight:600;">{{ $user->vehicles_count }}</td>
            <td>
                @if($user->email_verified_at)
                    <span class="pill pill-active">Active</span>
                @else
                    <span class="pill pill-inactive">Inactive</span>
                @endif
            </td>
            <td style="color:var(--text-tertiary);font-size:.8rem;">{{ $user->created_at->format('M d, Y') }}</td>
            <td>
                <div style="display:flex;gap:.375rem;flex-wrap:wrap;">
                    <a href="{{ route('admin.users.show',$user) }}" class="act act-view">View</a>
                    <a href="{{ route('admin.users.edit',$user) }}" class="act act-edit">Edit</a>
                    <form action="{{ route('admin.users.toggle-status',$user) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="act {{ $user->email_verified_at ? 'act-del' : 'act-ok' }}">
                            {{ $user->email_verified_at ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>
                    @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy',$user) }}" method="POST" style="display:inline;"
                          onsubmit="return confirm('Delete {{ addslashes($user->name) }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="act act-del">Delete</button>
                    </form>
                    @endif
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;padding:3rem;color:var(--text-tertiary);">No users found.</td></tr>
        @endforelse
        </tbody>
    </table>
    @if($users->hasPages())
    <div style="padding:1rem 1.25rem;border-top:1px solid var(--border-color);">{{ $users->links() }}</div>
    @endif
</div>
@endsection