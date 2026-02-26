
@extends('admin.layouts.app')
@section('title','Vehicles')
@section('content')
<div class="page-header">
    <div><div class="page-title">Vehicles</div><div class="page-sub">All registered vehicles on the platform</div></div>
</div>
<form method="GET" class="filter-bar">
    <div style="flex:1;min-width:180px;">
        <div style="font-size:.72rem;color:var(--text-tertiary);margin-bottom:.375rem;">Search</div>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Make, model, VIN, plate…" class="form-input" style="margin:0;">
    </div>
    <div>
        <div style="font-size:.72rem;color:var(--text-tertiary);margin-bottom:.375rem;">Type</div>
        <select name="type" class="form-select" style="margin:0;width:130px;">
            <option value="">All Types</option>
            @foreach(['car','truck','suv','van','motorcycle','ev','hybrid','other'] as $t)
            <option value="{{ $t }}" {{ request('type')==$t?'selected':'' }}>{{ ucfirst($t) }}</option>
            @endforeach
        </select>
    </div>
    <button type="submit" class="btn btn-primary" style="align-self:flex-end;">Filter</button>
    @if(request()->anyFilled(['search','type']))
        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary" style="align-self:flex-end;">Clear</a>
    @endif
</form>
<div class="table-wrap">
    <table>
        <thead><tr>
            <th>Vehicle</th><th>Owner</th><th>Type</th><th>Fuel</th><th>Mileage</th><th>Year</th><th>Added</th><th>Actions</th>
        </tr></thead>
        <tbody>
        @forelse($vehicles as $v)
        <tr>
            <td>
                <div style="font-weight:600;">{{ $v->year }} {{ $v->make }} {{ $v->model }}</div>
                <div style="font-size:.72rem;color:var(--text-tertiary);">{{ $v->license_plate ?: $v->vin ?: '—' }}</div>
            </td>
            <td>
                <div style="font-weight:600;">{{ $v->user->name ?? '—' }}</div>
                <div style="font-size:.72rem;color:var(--text-tertiary);">{{ $v->user->email ?? '' }}</div>
            </td>
            <td><span class="pill pill-active" style="text-transform:capitalize;">{{ $v->type }}</span></td>
            <td style="color:var(--text-secondary);font-size:.8rem;">{{ ucfirst($v->fuel_type ?? '—') }}</td>
            <td style="font-weight:600;">{{ number_format($v->mileage ?? 0) }} mi</td>
            <td style="color:var(--text-tertiary);">{{ $v->year }}</td>
            <td style="color:var(--text-tertiary);font-size:.8rem;">{{ $v->created_at->format('M d, Y') }}</td>
            <td>
                <div style="display:flex;gap:.375rem;">
                    <a href="{{ route('admin.vehicles.show',$v) }}" class="act act-view">View</a>
                    <form action="{{ route('admin.vehicles.destroy',$v) }}" method="POST" style="display:inline;"
                          onsubmit="return confirm('Delete this vehicle?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="act act-del">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;padding:3rem;color:var(--text-tertiary);">No vehicles found.</td></tr>
        @endforelse
        </tbody>
    </table>
    @if($vehicles->hasPages())
    <div style="padding:1rem 1.25rem;border-top:1px solid var(--border-color);">{{ $vehicles->links() }}</div>
    @endif
</div>
@endsection