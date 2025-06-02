@extends('admin/layouts/master')

@section('title', 'Order List')
@section('style')
{{-- style --}}
@endsection
@section('content')
<div class="d-flex flex-column justify-content-between h-100">
    <div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold mb-0">Edit Permissions for {{ $agent->name }}</h2>
            <!-- Optional add button -->
            <!-- <a href="#" class="btn btn-primary" id="addAgentBtn">Add New Agent</a> -->
        </div>

        <form method="POST" action="{{ route('admin.agents.update.permission', $agent) }}">
            @csrf

            <div class="mb-4">
                <h4 class="mb-3">Roles</h4>
                <div class="row">
                    @foreach($roles as $role)
                        <div class="col-md-4 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="role_{{ $role->name }}" name="roles[]" value="{{ $role->name }}"
                                    {{ $agent->hasRole($role->name) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $role->name }}">
                                    {{ $role->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <h4 class="mb-3">Permissions</h4>
                <div class="row">
                    @foreach($permissions as $permission)
                        <div class="col-md-4 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="perm_{{ $permission->name }}" name="permissions[]" value="{{ $permission->name }}"
                                    {{ $agent->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm_{{ $permission->name }}">
                                    {{ $permission->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('script')
    <script>
    </script>
@endsection