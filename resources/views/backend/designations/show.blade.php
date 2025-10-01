@extends('backend.layouts.app')
@section('content')
    <main id="main" class="main">
        <div class="pagetitle">
            <h1>View Green Note</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                    <li class="breadcrumb-item active">View Green Note</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->
        <section class="section">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Green Note 001</h5>
                            <button class="btn btn-primary">Download</button>
                            <!-- Vertical Form -->
                            <img src="Expense-approval-template_page-0001.jpg" class="img-fluid">
                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Attached Supporting Docs</h5>
                            <!-- Vertical Form -->
                            <!-- Table with stripped rows -->
                            <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>
                                            S no.
                                        </th>
                                        <th>File name</th>
                                        <th>File</th>
                                        <th data-type="date" data-format="DD/MM/YYYY">Upload Date</th>
                                        <th>Uploaded By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>GST certificate</td>
                                        <td><a href=""><i class="bi bi-file-earmark-text-fill"></i></a></td>
                                        <td>2005/02/11</td>
                                        <td>Maker</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Pan Number</td>
                                        <td><a href=""><i class="bi bi-file-earmark-text-fill"></i></a></td>
                                        <td>2005/02/11</td>
                                        <td>Maker</td>
                                    </tr>
                                </tbody>
                            </table>
                            <!-- End Table with stripped rows -->
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Comments</h5>
                            <img src="sYxEhzezNFzV7no8hzbvjVLWbBUc6DEedXM3qdg4.png" class="img-fluid">
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">eSign</h5>
                            <button class="btn btn-success">Click Here to Sign</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main><!-- End #main -->
@endsection
@push('script')
@endpush
