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
                        @if ($errors->any())
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                                <strong>Validation Error(s):</strong>
                                <ul class="list-disc pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <h5 class="card-title">Update Profile ({{ $user->name }})</h5>
                        <form class="row g-3" action="{{ route('backend.users.my.profile', $user->id) }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="name" name="name" value="{{ $user->name }}" readonly>
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="emp_id" class="form-label">Employee Id</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('emp_id') is-invalid @enderror"
                                        id="emp_id" name="emp_id" value="{{ old('emp_id', $user->emp_id) ?? '' }}"
                                        readonly>
                                    @error('emp_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="name" class="form-label">Username</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('username') is-invalid @enderror"
                                        id="name" name="username" value="{{ $user->username ?? '' }}" readonly>
                                    @error('username')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="number" class="form-label">Contact Number</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control @error('number') is-invalid @enderror"
                                        id="number" name="number" value="{{ old('number', $user->number) ?? '' }}">
                                    @error('number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email
                                    Address</label>
                                <div class="col-md-12">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ $user->email }}" readonly>
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <div class="col-md-12">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password">
                                    @error('password')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <div class="col-md-12">
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation">
                                </div>
                            </div>


                            <!-- Designation Dropdown -->
                            <div class="col-md-6">
                                <label for="designation_id" class="form-label">Designation</label>
                                <input type="text" class="form-control" value="{{ $user->designation->name ?? '-' }}"
                                    readonly>

                            </div>

                            <!-- Department Dropdown -->
                            <div class="col-md-6 mt-3">
                                <label for="department_id" class="form-label">Department</label>
                                <input type="text" class="form-control" value="{{ $user->department->name ?? '-' }}"
                                    readonly>
                            </div>
                            <div class="col-md-6 row mt-3">
                                <label for="file" class="form-label">Add Your Signature</label>
                                <div>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror"
                                        id="file" name="file" accept=".png">
                                    @error('file')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mt-3" id="preview-box"
                                    style="{{ $user->file ? 'display: block;' : 'display: none;' }}; cursor: pointer;">
                                    <img id="file-preview" src="{{ $user->file ? asset('uploads/' . $user->file) : '' }}"
                                        alt="File Preview" width="40" height="40">
                                </div>
                            </div>
                            <!-- Full-Size Image Modal -->
                            <div id="imageModal"
                                style="display: none; position: fixed; z-index: 999; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); justify-content: center; align-items: center;">
                                <img id="modal-image" src="" alt="Full Image Preview"
                                    style="max-width: 90%; max-height: 90%; border-radius: 8px;">
                            </div>

                            <br>
                            <div class="col-3">
                                <label for="one" class="form-label">Name of Account holder: </label>
                                <input type="text" class="form-control" name="account_holder" id="one"
                                    value="{{ old('account_holder', @$user->account_holder) }}">
                                @error('account_holder')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Bank Name:</label>
                                <input type="text" class="form-control" name="bank_name" id="one"
                                    value="{{ old('bank_name', @$user->bank_name) }}">
                                @error('bank_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">Bank Account:</label>
                                <input type="text" class="form-control" name="bank_account" id="one"
                                    value="{{ old('bank_account', @$user->bank_account) }}">
                                @error('bank_account')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-3">
                                <label for="one" class="form-label">IFSC:</label>
                                <input type="text" class="form-control" name="ifsc_code" id="one"
                                    value="{{ old('ifsc_code', @$user->ifsc_code) }}">
                                @error('ifsc_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                {{-- <input type="submit" class="btn btn-primary" value="Update User"> --}}
                                <button type="submit" class="btn btn-primary">Update User</button>
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
        const fileInput = document.getElementById('file');
        const previewBox = document.getElementById('preview-box');
        const previewImage = document.getElementById('file-preview');
        const imageModal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modal-image');

        // Handle file input change
        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];

            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewBox.style.display = 'block';
                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                previewBox.style.display = 'none';
                previewImage.src = '';
                alert('Please select a valid image file.');
            }
        });

        // Show modal on preview click
        previewBox.addEventListener('click', function() {
            if (previewImage.src) {
                modalImage.src = previewImage.src;
                imageModal.style.display = 'flex';
            }
        });

        // Close modal on click anywhere
        imageModal.addEventListener('click', function() {
            imageModal.style.display = 'none';
        });
    </script>
@endpush
