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
                            <a href="{{ route('backend.roles.create') }}" class="btn btn-outline-success btn-sm my-2"><i
                                    class="bi bi-plus-circle"></i> Add New</a>
                        @endcan
                        <h5 class="card-title">Edit Role</h5>

                        <form class="row g-3" action="{{ route('backend.roles.update', $role->id) }}" method="post">
                            @csrf
                            @method('PUT')
                            <div class="col-12">
                                <label for="inputNanme4" class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ $role->name }}">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            @forelse ($permissions as $permission)
                                <div class="col-4 @error('permissions') is-invalid @enderror">

                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="{{ $permission->name }}"
                                            name="permissions[]" value="{{ $permission->id }}"
                                            {{ in_array($permission->id, $rolePermissions ?? []) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="{{ $permission->name }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                    </option>
                                </div>
                            @empty
                            @endforelse

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                                <button type="reset" class="btn btn-secondary btn-sm">Reset</button>
                                @can('edit-role')
                                    <a href="{{ route('backend.roles.index') }}" class="btn btn-outline-success btn-sm my-2"><i
                                            class="bi bi-plus-circle"></i> Back To List</a>
                                @endcan
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </section>
@endsection
@push('script')
@endpush
