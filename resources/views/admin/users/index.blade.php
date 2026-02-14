@extends('admin.layout')

@section('title', 'Users')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="h4 mb-1">Users</h2>
            <div class="muted">Admin overview of registered accounts.</div>
        </div>
    </div>

    @if ($users->count())
        <div class="table-shell">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-video">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td class="fw-semibold">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if ($user->is_admin)
                                        <span class="badge badge-soft">Admin</span>
                                    @else
                                        <span class="badge badge-soft">User</span>
                                    @endif
                                </td>
                                <td class="muted">{{ $user->created_at?->diffForHumans() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">
            {{ $users->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <p class="mb-0">No users found.</p>
            </div>
        </div>
    @endif
@endsection
