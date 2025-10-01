@extends('backend.layouts.app')
@section('content')
    {{-- <div class="pagetitle">
        <h1>Blank Page</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                <li class="breadcrumb-item">Pages</li>
                <li class="breadcrumb-item active">Blank</li>
            </ol>
        </nav>
    </div><!-- End Page Title --> --}}

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        @can('create-role')
                            <a href="{{ route('backend.users.index') }}" class="btn btn-outline-success btn-sm my-2"><i
                                    class="bi bi-list"></i> User List</a>
                        @endcan
                        <h5 class="card-title">Add User</h5>
                        <form class="row g-3" action="{{ route('backend.users.store') }}" method="post">
                            @csrf

                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ old('name') ?? '' }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Username</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                        id="name" name="username" value="{{ old('username') ?? '' }}">
                                    @error('username')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label for="email" class="form-label">Email
                                    Address</label>
                                <div class="col-md-12">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') ?? '' }}">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="password"
                                    class="form-label">Password</label>
                                <div class="col-md-12">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation"
                                    class="form-label">Confirm Password</label>
                                <div class="col-md-12">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="roles" class="form-label">Roles</label>
                                <div class="col-md-12">
                                    <select class="form-select select2 @error('roles') is-invalid @enderror" multiple
                                        aria-label="Roles" id="roles" name="roles[]">
                                        @forelse ($roles as $role)
                                            @if ($role != 'Super Admin')
                                                <option value="{{ $role }}"
                                                {{ (collect(old('roles'))->contains($role)) ? 'selected':'' }}>
                                                    {{ $role }}
                                                </option>
                                            @else
                                                @if (Auth::user()->hasRole('Super Admin'))
                                                    <option value="{{ $role }}"
                                                        {{ in_array($role, $userRoles ?? []) ? 'selected' : '' }}>
                                                        {{ $role }}
                                                    </option>
                                                @endif
                                            @endif

                                        @empty
                                        @endforelse
                                    </select>
                                    @error('roles')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <input type="submit" class="col-md-2 offset-md-0 btn btn-primary" value="Save">
                            </div>

                        </form>
                    </div>

                </div>

            </div>
    </section>
@endsection
@push('script')
<script>
    $('.select2').select2();
</script>
@endpush
