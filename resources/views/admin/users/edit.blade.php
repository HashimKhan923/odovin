@extends('admin.layouts.app')
@section('title','Edit User')
@section('content')
<a href="{{ route('admin.users.show',$user) }}" class="back-link"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>Back to User</a>
<div class="page-header">
    <div><div class="page-title">Edit User</div><div class="page-sub">{{ $user->name }}</div></div>
</div>
<div style="max-width:640px;">
    <div class="card">
        <form method="POST" action="{{ route('admin.users.update',$user) }}">
            @csrf @method('PUT')
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" value="{{ old('name',$user->name) }}" class="form-input" required>
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" value="{{ old('email',$user->email) }}" class="form-input" required>
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">New Password <span style="color:var(--text-tertiary)">(leave blank to keep)</span></label>
                    <input type="password" name="password" class="form-input" placeholder="••••••••">
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="user_type" class="form-select" required>
                    <option value="user" {{ old('user_type',$user->user_type)=='user'?'selected':'' }}>User</option>
                    <option value="admin" {{ old('user_type',$user->user_type)=='admin'?'selected':'' }}>Admin</option>
                    <option value="support" {{ old('user_type',$user->user_type)=='support'?'selected':'' }}>Support</option>
                </select>
            </div>
            <div style="display:flex;gap:.75rem;justify-content:flex-end;padding-top:.75rem;">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection