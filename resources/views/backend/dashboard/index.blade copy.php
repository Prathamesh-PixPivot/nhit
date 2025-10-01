@extends('backend.layouts.app')

@section('content')
    <section class="section dashboard">
        <div class="row">
            <!-- Left side columns -->
            <div class="col-lg-12 ">
                <div class="row">
                    <!-- Sales Card -->
                    <div class="col-md-6 mx-auto">
                        <div class="card info-card sales-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-center align-items-center flex-column">
                                    <h3 class="py-3">{{ config('app.full_name') }}</h3>

                                    <img src="{{ asset('theme/assets/img/logo.png') }}" alt=""
                                        style="max-height: 300px;">

                                </div>
                            </div>
                        </div>
                    </div><!-- End Sales Card -->
                </div>
            </div><!-- End Left side columns -->
        </div>
        <div class="row">
            <!-- Left side columns -->
            <div class="col-lg-12 ">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">All Notes </h5>
                            <!-- Vertical Form -->
                            @can(['export-excel-note'])
                                <form method="GET" action="{{ route('backend.note.export.note.excel') }}"
                                    class="d-flex gap-2">
                                    <input type="date" class="form-control w-25" name="start_date"
                                        value="{{ request('start_date') }}">
                                    <input type="date" class="form-control w-25" name="end_date"
                                        value="{{ request('end_date') }}">
                                    <button type="submit" class="btn btn-outline-primary btn-sm col-3">
                                        <i class="bi bi-file-earmark-arrow-down-fill"></i> Export Excel Note
                                    </button>
                                </form>
                            @endcan
                            {{-- <table class="table datatable">
                                <thead>
                                    <tr>
                                        <th>S no.</th>
                                        <th>Name</th>
                                        <th>Green Note</th>
                                        <th>Payment Note</th>
                                        <th>Reimbursement Note</th>
                                        <th>Bank Letter Note</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($userData as $index => $item)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $item['name'] }}</td>
                                            <td>
                                                {{ $item['green_statuses'] ?: '-' }}

                                                @if ($item['id'] == auth()->id() && !empty($item['green_ids']))
                                                    <form action="{{ route('backend.dashboard.user.green.notes') }}"
                                                        method="POST" id="greenNoteForm-{{ $item['id'] }}">
                                                        @csrf
                                                        <input type="hidden" name="ids"
                                                            value="{{ implode(',', $item['green_ids']) }}">
                                                        <a href="#"
                                                            onclick="document.getElementById('greenNoteForm-{{ $item['id'] }}').submit(); return false;">
                                                            <i class="bi bi-arrow-right-square-fill"></i>
                                                        </a>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $item['payment_statuses'] ?: '-' }}
                                                @if ($item['id'] == auth()->id() && !empty($item['payment_ids']))
                                                    <form action="{{ route('backend.dashboard.user.payment.notes') }}"
                                                        method="POST" id="paymentNoteForm-{{ $item['id'] }}">
                                                        @csrf
                                                        <input type="hidden" name="ids"
                                                            value="{{ implode(',', $item['payment_ids']) }}">
                                                        <a href="#"
                                                            onclick="document.getElementById('paymentNoteForm-{{ $item['id'] }}').submit(); return false;">
                                                            <i class="bi bi-arrow-right-square-fill"></i>
                                                        </a>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>{{ $item['reimbursement_statuses'] ?: '-' }}
                                                @if ($item['id'] == auth()->id() && !empty($item['reimbursement_ids']))
                                                    <form
                                                        action="{{ route('backend.dashboard.user.reimbursement.notes') }}"
                                                        method="POST" id="reimbursementNoteForm-{{ $item['id'] }}">
                                                        @csrf
                                                        <input type="hidden" name="ids"
                                                            value="{{ implode(',', $item['reimbursement_ids']) }}">
                                                        <a href="#"
                                                            onclick="document.getElementById('reimbursementNoteForm-{{ $item['id'] }}').submit(); return false;">
                                                            <i class="bi bi-arrow-right-square-fill"></i>
                                                        </a>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>{{ $item['bankLetter_statuses'] ?: '-' }}
                                                @if ($item['id'] == auth()->id() && !empty($item['bankLetter_ids']))
                                                    <input type="hidden" id="hidden_ids"
                                                        value="{{ implode(',', $item['bankLetter_ids']) }}">
                                                    <form action="{{ route('backend.dashboard.user.bank.letter.notes') }}"
                                                        method="POST" id="bankLetterNoteForm-{{ $item['id'] }}">
                                                        @csrf
                                                        <input type="hidden" name="ids"
                                                            value="{{ implode(',', $item['bankLetter_ids']) }}">

                                                        <a href="#"
                                                            onclick="submitBankLetterForm({{ $item['id'] }})">
                                                            <i class="bi bi-arrow-right-square-fill"></i>
                                                        </a>
                                                    </form>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('backend.dashboard.filter', ['id' => $item['id']]) }}">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Left side columns -->
        {{-- @canany(['show-dashboard']) --}}
        @if (auth()->user()->hasRole('Super Admin Live') || auth()->user()->can('show-dashboard'))
            <div class="row">
                <div class="pagetitle">
                    <h1 class=" border-secondary border-bottom pb-3 mb-5">Expense Approval Note</h1>
                </div><!-- End Page Title -->
                <!-- Sales Card -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card sales-card">



                        <div class="card-body">
                            <h5 class="card-title">Expense Approval Note <span>| Current Month</span></h5>

                            <div id="trafficCharta" style="min-height: 500px;" class="echart"></div>

                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    const chartData = {!! json_encode($dataCurrent) !!};
                                    const total = chartData.reduce((sum, item) => sum + (item.value || 0), 0);
                                    echarts.init(document.querySelector("#trafficCharta")).setOption({
                                        tooltip: {
                                            trigger: 'item'
                                        },
                                        legend: {
                                            top: '0%',
                                            left: 'center',
                                            formatter: function(name) {
                                                const item = chartData.find(i => i.name === name);
                                                return `${name} (${item?.value ?? 0})`;
                                            }
                                        },
                                        series: [{
                                            name: 'Expense Approval Note',
                                            type: 'pie',
                                            radius: ['40%', '70%'],
                                            center: ['50%', '60%'],
                                            avoidLabelOverlap: false,
                                            label: {
                                                show: true,
                                                position: 'center',
                                                formatter: `Total\n${total}`,
                                                fontSize: 16,
                                                fontWeight: 'bold',
                                                color: '#555'
                                            },
                                            emphasis: {
                                                label: {
                                                    show: true,
                                                    fontSize: '18',
                                                    fontWeight: 'bold'
                                                }
                                            },
                                            labelLine: {
                                                show: false
                                            },
                                            data: chartData
                                        }]
                                    });
                                });
                            </script>
                        </div>

                    </div>
                </div><!-- End Sales Card -->

                <!-- Revenue Card -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card revenue-card">
                        <div class="card-body">
                            <h5 class="card-title">Expense Approval Note <span>| Till Date</span></h5>
                            <div id="trafficChartb" style="min-height: 500px;" class="echart"></div>

                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    const chartDataB = {!! json_encode($dataTill) !!};
                                    const total = chartDataB.reduce((sum, item) => sum + (item.value || 0), 0);

                                    echarts.init(document.querySelector("#trafficChartb")).setOption({
                                        tooltip: {
                                            trigger: 'item'
                                        },
                                        legend: {
                                            top: '1%', // legend ko thoda neeche shift
                                            left: 'center',
                                            formatter: function(name) {
                                                const item = chartDataB.find(i => i.name === name);
                                                return `${name} (${item?.value ?? 0})`;
                                            },
                                        },
                                        series: [{
                                            name: 'Expense Approval Note',
                                            type: 'pie',
                                            radius: ['40%', '70%'],
                                            center: ['50%', '60%'],
                                            avoidLabelOverlap: false,
                                            label: {
                                                show: true,
                                                position: 'center',
                                                formatter: `Total\n${total}`,
                                                fontSize: 16,
                                                fontWeight: 'bold',
                                                color: '#555'
                                            },
                                            emphasis: {
                                                label: {
                                                    show: true,
                                                    fontSize: '18',
                                                    fontWeight: 'bold'
                                                }
                                            },
                                            labelLine: {
                                                show: false
                                            },
                                            data: chartDataB
                                        }]
                                    });
                                });
                            </script>

                        </div>

                    </div>
                </div><!-- End Revenue Card -->
                <div class="pagetitle">
                    <h1 class=" border-secondary border-bottom pb-3 mb-5">Reimbursement Note</h1>
                </div><!-- End Page Title -->
                <!-- Sales Card -->
                {{-- <div class="col-xxl-4 col-md-6">
                    <div class="card info-card sales-card">
                        <div class="card-body">
                            <h5 class="card-title">Reimbursement Note <span>| Current Month</span></h5>

                            <div id="reimbursementCharta" style="min-height: 500px;" class="echart"></div>

                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    const chartDataReimbursement = {!! json_encode($dataReimbursementCurrent) !!};
                                    const total = chartDataReimbursement.reduce((sum, item) => sum + (item.value || 0), 0);

                                    echarts.init(document.querySelector("#reimbursementCharta")).setOption({
                                        tooltip: {
                                            trigger: 'item'
                                        },
                                        legend: {
                                            top: '1%',
                                            left: 'center',
                                            formatter: function(name) {
                                                const item = chartDataReimbursement.find(i => i.name === name);
                                                return `${name} (${item?.value ?? 0})`;
                                            },
                                            itemGap: 14
                                        },
                                        series: [{
                                            name: 'Expense Approval Note',
                                            type: 'pie',
                                            radius: ['40%', '70%'],
                                            center: ['50%', '60%'],
                                            avoidLabelOverlap: false,
                                            label: {
                                                show: true,
                                                position: 'center',
                                                formatter: `Total\n${total}`,
                                                fontSize: 16,
                                                fontWeight: 'bold',
                                                color: '#555'
                                            },
                                            emphasis: {
                                                label: {
                                                    show: true,
                                                    fontSize: '18',
                                                    fontWeight: 'bold'
                                                }
                                            },
                                            labelLine: {
                                                show: false
                                            },
                                            data: chartDataReimbursement
                                        }]
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div> --}}
                <!-- End Sales Card -->

                <!-- Revenue Card -->
                {{-- <div class="col-xxl-4 col-md-6">
                    <div class="card info-card revenue-card">
                        <div class="card-body">
                            <h5 class="card-title">Reimbursement Note <span>| Till Date</span></h5>

                            <div id="reimbursementChartaTill" style="min-height: 500px;" class="echart"></div>
                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    const chartDataReimbursementTill = {!! json_encode($dataReimbursementTill) !!};
                                    const total = chartDataReimbursementTill.reduce((sum, item) => sum + (item.value || 0), 0);

                                    echarts.init(document.querySelector("#reimbursementChartaTill")).setOption({
                                        tooltip: {
                                            trigger: 'item'
                                        },
                                        legend: {
                                            top: '1%',
                                            left: 'center',
                                            formatter: function(name) {
                                                const item = chartDataReimbursementTill.find(i => i.name === name);
                                                return `${name} (${item?.value ?? 0})`;
                                            },
                                            itemGap: 14
                                        },
                                        series: [{
                                            name: 'Reimbursement Note',
                                            type: 'pie',
                                            radius: ['40%', '70%'],
                                            center: ['50%', '60%'],
                                            avoidLabelOverlap: false,
                                            label: {
                                                show: true,
                                                position: 'center',
                                                formatter: `Total\n${total}`,
                                                fontSize: 16,
                                                fontWeight: 'bold',
                                                color: '#555'
                                            },
                                            emphasis: {
                                                label: {
                                                    show: true,
                                                    fontSize: '18',
                                                    fontWeight: 'bold'
                                                }
                                            },
                                            labelLine: {
                                                show: false
                                            },
                                            data: chartDataReimbursementTill
                                        }]
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div> --}}
                <!-- End Revenue Card -->

                <div class="pagetitle">
                    <h1 class=" border-secondary border-bottom pb-3 mb-5">Payment Note</h1>
                </div><!-- End Page Title -->
                <!-- Sales Card -->
                {{-- <div class="col-xxl-4 col-md-6">
                    <div class="card info-card sales-card">
                        <div class="card-body">
                            <h5 class="card-title">Payment Note <span>| Current Month</span></h5>

                            <div id="paymentChartaTill" style="min-height: 500px;" class="echart"></div>
                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    const chartDataPaymentCurrent = {!! json_encode($dataPaymentCurrent) !!};
                                    const total = chartDataPaymentCurrent.reduce((sum, item) => sum + (item.value || 0), 0);

                                    echarts.init(document.querySelector("#paymentChartaTill")).setOption({
                                        tooltip: {
                                            trigger: 'item'
                                        },
                                        legend: {
                                            top: '5%',
                                            left: 'center',
                                            formatter: function(name) {
                                                const item = chartDataPaymentCurrent.find(i => i.name === name);
                                                return `${name} (${item?.value ?? 0})`;
                                            },
                                            itemGap: 14
                                        },
                                        series: [{
                                            name: 'Payment Note',
                                            type: 'pie',
                                            radius: ['40%', '70%'],
                                            center: ['50%', '60%'],
                                            avoidLabelOverlap: false,
                                            label: {
                                                show: true,
                                                position: 'center',
                                                formatter: `Total\n${total}`,
                                                fontSize: 16,
                                                fontWeight: 'bold',
                                                color: '#555'
                                            },
                                            emphasis: {
                                                label: {
                                                    show: true,
                                                    fontSize: '18',
                                                    fontWeight: 'bold'
                                                }
                                            },
                                            labelLine: {
                                                show: false
                                            },
                                            data: chartDataPaymentCurrent
                                        }]
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div> --}}
                <!-- End Sales Card -->

                <!-- Revenue Card -->
                {{-- <div class="col-xxl-4 col-md-6">
                    <div class="card info-card revenue-card">
                        <div class="card-body">
                            <h5 class="card-title">Payment Note <span>| Till Date</span></h5>
                            <div id="paymentChartaCurrent" style="min-height: 500px;" class="echart"></div>
                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    const chartDataPaymentTill = {!! json_encode($dataPaymentTill) !!};
                                    const total = chartDataPaymentTill.reduce((sum, item) => sum + (item.value || 0), 0);

                                    echarts.init(document.querySelector("#paymentChartaCurrent")).setOption({
                                        tooltip: {
                                            trigger: 'item'
                                        },
                                        legend: {
                                            top: '5%', // top margin increase
                                            left: 'center',
                                            formatter: function(name) {
                                                const item = chartDataPaymentTill.find(i => i.name === name);
                                                return `${name} (${item?.value ?? 0})`;
                                            },
                                            itemGap: 14 // spacing between items
                                        },
                                        series: [{
                                            name: 'Payment Note',
                                            type: 'pie',
                                            radius: ['40%', '70%'],
                                            center: ['50%', '60%'], // chart thoda neeche
                                            avoidLabelOverlap: false,
                                            label: {
                                                show: true,
                                                position: 'center',
                                                formatter: `Total\n${total}`,
                                                fontSize: 16,
                                                fontWeight: 'bold',
                                                color: '#555'
                                            },
                                            emphasis: {
                                                label: {
                                                    show: true,
                                                    fontSize: '18',
                                                    fontWeight: 'bold'
                                                }
                                            },
                                            labelLine: {
                                                show: false
                                            },
                                            data: chartDataPaymentTill
                                        }]
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div> --}}
                <!-- End Revenue Card -->
                <div class="pagetitle">
                    <h1 class=" border-secondary border-bottom pb-3 mb-5">Bank Letter</h1>
                </div><!-- End Page Title -->
                <!-- Sales Card -->
                {{-- <div class="col-xxl-4 col-md-6">
                    <div class="card info-card sales-card">
                        <div class="card-body">
                            <h5 class="card-title">Bank Letter <span>| Current Month</span></h5>

                            <div id="bankChartaTill" style="min-height: 500px;" class="echart"></div>
                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    const chartDataBankCurrent = {!! json_encode($currentMonthFormatted) !!};
                                    const total = chartDataBankCurrent.reduce((sum, item) => sum + (item.value || 0), 0);

                                    echarts.init(document.querySelector("#bankChartaTill")).setOption({
                                        tooltip: {
                                            trigger: 'item'
                                        },
                                        legend: {
                                            top: '5%',
                                            left: 'center',
                                            formatter: function(name) {
                                                const item = chartDataBankCurrent.find(i => i.name === name);
                                                return `${name} (${item?.value ?? 0})`;
                                            },
                                            itemGap: 14
                                        },
                                        series: [{
                                            name: 'Bank Letter',
                                            type: 'pie',
                                            radius: ['40%', '70%'],
                                            center: ['50%', '60%'],
                                            avoidLabelOverlap: false,
                                            label: {
                                                show: true,
                                                position: 'center',
                                                formatter: `Total\n${total}`,
                                                fontSize: 16,
                                                fontWeight: 'bold',
                                                color: '#555'
                                            },
                                            emphasis: {
                                                label: {
                                                    show: true,
                                                    fontSize: '18',
                                                    fontWeight: 'bold'
                                                }
                                            },
                                            labelLine: {
                                                show: false
                                            },
                                            data: chartDataBankCurrent
                                        }]
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div> --}}
                <!-- End Sales Card -->

                <!-- Revenue Card -->
                {{-- <div class="col-xxl-4 col-md-6">
                    <div class="card info-card revenue-card">
                        <div class="card-body">
                            <h5 class="card-title">Bank Letter <span>| Till Date</span></h5>
                            <div id="bankChartaCurrent" style="min-height: 500px;" class="echart"></div>
                            <script>
                                document.addEventListener("DOMContentLoaded", () => {
                                    const chartDataBankTill = {!! json_encode($tillDateFormatted) !!};
                                    const total = chartDataBankTill.reduce((sum, item) => sum + (item.value || 0), 0);

                                    echarts.init(document.querySelector("#bankChartaCurrent")).setOption({
                                        tooltip: {
                                            trigger: 'item'
                                        },
                                        legend: {
                                            top: '5%', // top margin increase
                                            left: 'center',
                                            formatter: function(name) {
                                                const item = chartDataBankTill.find(i => i.name === name);
                                                return `${name} (${item?.value ?? 0})`;
                                            },
                                            itemGap: 14 // spacing between items
                                        },
                                        series: [{
                                            name: 'Bank Letter',
                                            type: 'pie',
                                            radius: ['40%', '70%'],
                                            center: ['50%', '60%'], // chart thoda neeche
                                            avoidLabelOverlap: false,
                                            label: {
                                                show: true,
                                                position: 'center',
                                                formatter: `Total\n${total}`,
                                                fontSize: 16,
                                                fontWeight: 'bold',
                                                color: '#555'
                                            },
                                            emphasis: {
                                                label: {
                                                    show: true,
                                                    fontSize: '18',
                                                    fontWeight: 'bold'
                                                }
                                            },
                                            labelLine: {
                                                show: false
                                            },
                                            data: chartDataBankTill
                                        }]
                                    });
                                });
                            </script>
                        </div>
                    </div>
                </div> --}}
                <!-- End Revenue Card -->
            </div>
            {{-- @endcanany --}}
        @endif

    </section>
@endsection
@push('script')
    <script>
        function submitBankLetterForm(id) {
            document.getElementById('bankLetterNoteForm-' + id).submit();
        }
    </script>
    <script>
        $('.select2').select2();
    </script>
@endpush
