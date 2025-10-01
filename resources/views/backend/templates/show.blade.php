@extends('backend.layouts.app')
@section('content')
    {{-- <div class="pagetitle">
        <h1>Blank Page {{ request()->route('slno') ?? 'N/A' }}</h1>
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
                        <form action="{{ route('backend.templates.templateCommon', request()->route('tpl')) }}" method="post"
                            id="tplForm">
                            @csrf
                            {{-- <h5 class="card-title">Letter Preview ({{ucwords(str_replace("-", " ", request()->route('tpl')))}})</h5> --}}
                            <input type="hidden" id="slno" name="slno" value="{{ request()->slno ?? '' }}"
                                placeholder="Enter SL NO.">
                            <!--<p>This is an examle page with no contrnt. You can use it as a starter for your custom pages.-->
                            <!--</p>-->
                            <div class="row mb-3 mt-3" bis_skin_checked="1">
                                <div class="col-sm-10" bis_skin_checked="1">
                                    <button type="button" class="btn btn-sm btn-primary templateGeneratePDF"
                                        data-url="{{ route('backend.templates.templateCommonGenPdf', request()->route('tpl')) }}"
                                        style="display: none;"><i class="bi bi-download"></i> Download PDF</button>
                                </div>
                            </div>
                            <div id="tplTableData">

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('script')
    <script>
        // var formData = $('.mybids-filter-form').serializeArray();
        // formData.push({ name: "viewType", value: viewType });
        // $('.mybids-filter-form').serialize()
        var formData = $('#tplForm').serializeArray();
        formData.push({
            name: "formId",
            value: 'tplForm'
        })
        formData.push({
            name: "outputId",
            value: 'tplTableData'
        })
        getTemplateView($('#tplForm').attr('action'), formData);
        $("#slno:input").on("keyup change, ready", function(e) {
            var formData = $('#tplForm').serializeArray();
            formData.push({
                name: "formId",
                value: 'tplForm'
            })
            formData.push({
                name: "outputId",
                value: 'tplTableData'
            })
            getTemplateView($('#tplForm').attr('action'), formData);
            /* $.ajax({
                url: '{{ "backend.templates.'+slno" }}',
                type: 'post',
                dataType: 'json',
                data: formData,
                contentType: 'application/json',
                success: function(response) {
                    console.log(response)
                    },
                    response: JSON.stringify(person)
                    }); */
        })

        $(document).on("click", ".templateGeneratePDF", function(e) {
            e.preventDefault();
            /* var formData = [];
            formData.push({
                name: "slno",
                value: $("#slno:input").val()
            }) */

            var formData = $('#tplForm').serializeArray();
            // formData.push({
            //     name: "formId",
            //     value: 'tplForm'
            // })
            // formData.push({
            //     name: "outputId",
            //     value: 'tplTableData'
            // })
            templateGeneratePDFAjax($(this).data('url'), formData);
        });
    </script>
@endpush
