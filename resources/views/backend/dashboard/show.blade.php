@extends('backend.layouts.app')

@section('content')
    <section class="section dashboard">
        <div class="row">
            <!-- Left side columns -->
            <div class="col-lg-12 ">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <strong>{{ $userData['name'] }}</strong>
                        </div>
                        <div class="card-body">
                            <p><strong>Green Notes:</strong><br>{!! $userData['green_statuses'] ?: '-' !!}</p>
                            <p><strong>Payment Notes:</strong><br>{!! $userData['payment_statuses'] ?: '-' !!}</p>
                            <p><strong>Reimbursement Notes:</strong><br>{!! $userData['reimbursement_statuses'] ?: '-' !!}</p>
                        </div>
                    </div>
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
