<x-pondit-limitless-master>
    {{-- @dd($purchase_requisition) --}}
    @php
    $fields = [];
    @endphp
    <x-pondit-pl-card title="{{ $purchase_requisition[0]['report_name'] }}">
        @csrf
        @if (count($purchase_requisition) > 0)
        <div class="row">
            <input type="hidden" id="report-name" value="{{ $purchase_requisition[0]['slug'] }}">
            @foreach ($purchase_requisition as $filter)
            @php
                $reqValueArray  = explode('-', $filter['request']);
                $reqType        = $reqValueArray[0];
                $reqValue       = end($reqValueArray);
                $fields[]       = $reqValue;
                // @dd($reqValue)
                @endphp
            @if ($filter['request'] && $reqType == 'select2')
            <x-ba-select2 :select="$reqValue" />
            @elseif($filter['request'] && $reqType == 'input')
            <div class="col-md-4">
                <div class="form-group">
                    @php
                        $users = DB::table('users')->latest()->get();
                    @endphp
                    <select id="{{ $reqValue }}" class="form-control user">
                        <option value="">Select User Name</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} </option>

                        @endforeach
                    </select>
                </div>
            </div>
            @endif
            @endforeach
            <div class="col-md-1">
                <div class="form-group">
                    <button type="button" id="load-report" class="btn btn-sm bg-success rounded-round"><i
                            class="fas fa-search"></i> Load</button>
                </div>
            </div>
        </div>
        <div class="row" id="jqGridContainer" class="my-2"></div>
        @endif
        <x-slot name="cardFooter">
            <div></div>
            <div>
                <button type="button" id="excel-btn"
                    class="d-none btn btn-outline bg-success btn-icon text-success btn-sm border-success border-2 rounded-round legitRipple mr-1"><i
                        class="fas fa-file-excel"></i></button>
                <button type  = "button" id = "pdf-btn"
                        class = "d-none btn btn-outline bg-danger btn-icon text-danger btn-sm border-danger border-2 rounded-round legitRipple mr-1"><i
                    class="fas fa-file-pdf"></i></button>
            </div>
            <div></div>
        </x-slot>
    </x-pondit-pl-card>

    @push('js')
    <script src="{{ asset('vendor/BA/report/assets/libs/jqGrid-js/js/trirand/i18n/grid.locale-en.js') }}"></script>
    <script src="{{ asset('vendor/BA/report/assets/libs/jqGrid-js/js/trirand/jquery.jqGrid.min.js') }}"></script>
    <script src="{{ asset('vendor/BA/report/assets/libs/jqGrid-js/js/jszip.min.js') }}"></script>
    <script src="{{ asset('vendor/BA/report/assets/libs/jqGrid-js/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('vendor/BA/report/assets/libs/jqGrid-js/js/trirand/pdf_maker.js') }}"></script>
    <script src="{{ asset('vendor/BA/report/assets/libs/jqGrid-js/js/trirand/vfs_fonts.js') }}"></script>

    <script src="{{ asset("vendor/pondit/pondithelper/assets/js/config.js") }}"></script>
    <script src="{{ asset("vendor/pondit/pondithelper/assets/js/select2-init.js") }}"></script>
    {{-- <script src="{{ asset("vendor/BA/report/assets/js/report.js") }}"></script> --}}
    <script src="{{ asset('vendor/BTAL/purchaserequisition/assets/js/report.js') }}"></script>
    <script>
    (function($){
        $(document).ready(function(){
            slug = "{!! $purchase_requisition[0]['slug'] !!}";

            $('.user').select2();

        });
    })(jQuery)

    function fieldData() {
        let
            fields  = @json($fields),
            data    = {};

        $.each(fields, function(index, value) {
            data[value] = $('#'+value).val();
        });
        return data;
    }
    </script>
    @endpush

    @push('css')
    <link rel="stylesheet" href="{{ asset('vendor/BA/report/assets/libs/jqGrid-js/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/BA/report/assets/libs/jqGrid-js/css/trirand/ui.jqgrid.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/BA/report/assets/libs/jqGrid-js/css/custom-jqgrid.css') }}">
    @endpush
</x-pondit-limitless-master>
