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
                        <h5 class="card-title">Import Vendor Excel File</h5>
                        <form method="post" action="{{route('backend.import.vendors.store')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="row mb-3">
                              <label for="inputNumber" class="col-sm-2 col-form-label">File Upload</label>
                              <div class="col-sm-10">
                                <input class="form-control" type="file" name="import_vendor" id="formFile">
                              </div>
                            </div>
            
                            <div class="row mb-3">
                              <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">Upload</button>
                              </div>
                            </div>
            
                          </form>
                    </div>
                </div>

            </div>

        </div>
    </section>
@endsection
