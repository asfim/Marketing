@extends('admin.layouts.master')
@section('title', 'Customer Challan List')
@section('breadcrumb', 'Customer Challan List')
<?php $user = Auth::user(); ?>
@section('content')
    <div class="workplace">

        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-3">
                        {{-- <div class="isw-documents"></div> --}}
                        <h1>
                            Challan List
                            <span
                                class="src-info">{{ request('search_text') == '' && request('date_range') == '' ? '- Last 30 Days' : '- ' . request('date_range') }}</span>
                        </h1>
                    </div>

                    <div class="col-md-9 search_box" style="margin-top: 4px;">
                        <form action="" class="form-horizontal">
                            <div class="" align="right">
                                <div class="col-md-3">
                                    @if ($user->branchId == '' && ($user->hasRole(['super-admin']) || $user->can('branch-list')))
                                        <select name="branchId" id="branchId" class="form-control">
                                            <option value="">All Branch</option>
                                            <option value="head_office"
                                                {{ request('branchId') == 'head_office' ? 'selected' : '' }}>** Head Office
                                                Only
                                                **</option>
                                            @foreach ($branches as $branch)
                                                {
                                                <option value="{{ $branch->id }}"
                                                    {{ request('branchId') == $branch->id ? 'selected' : '' }}>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="hidden" name="branchId" id="branchId"
                                            value="{{ $user->branchId }}" />
                                    @endif
                                </div>
                                <div class="col-md-2">
                                    <select style="height: 28px;" name="challan_status" class="form-control">
                                        <option value="">Select Type</option>
                                        <option value="0">Submitted</option>
                                        <option value="1">Non Submitted</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" name="search_text" id="search_name"
                                        value="{{ request('search_text') ?? '' }}" class="form-control"
                                        placeholder="Enter Search Text" />
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" name="date_range" id="date_range"
                                            value="{{ request('date_range') ?? '' }}" class="form-control"
                                            placeholder="Date Range" autocomplete="off" />
                                        <div class="input-group-btn">
                                            <button type="submit" class="btn btn-default search-btn">Search</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable4">
                        <thead>
                            <tr>
                                <th>
                                        <input type="checkbox" value="" name="checkall"/>
                                        </th>
                                <th>CL. no</th>
                                <th>PSI</th>
                                <th>Sell Date</th>
                                <th>Cus Name</th>
                                <th>Project & Addr</th>
                                <th>Qty(Cu.M)</th>
                                <th>Qty(Cft)</th>
                                @if ($user->hasRole(['super-admin']) || $user->can('challan-rate-view'))
                                    <th>Rate</th>
                                @endif
                                <th>Total</th>
                                <th>Bill Status</th>
                                @if ($user->branchId == '')
                                    <th>Branch</th>
                                @endif
                                <th class="hidden-print">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $i = 1;
                            $total_cum = 0;
                            $total_cft = 0;
                            $total_amount = 0; ?>
                            @foreach ($challans as $chalan)
                                <tr>
                                    {{-- <td> --}}
                                    {{-- @if ($chalan->status == 1 && $checkbox_enable == 1) --}}
                                    {{-- <input type="checkbox" id="checkbox" value="{{ $chalan->id }}" name="checkbox[]"/> --}}
                                    {{-- @else --}}
                                    {{-- <span class="glyphicon glyphicon-warning-sign"></span> --}}
                                    {{-- @endif --}}
                                    {{-- </td> --}}
                                    <?php $qty_cft = $chalan->cuM * 35.315;
                                    $total = $qty_cft * $chalan->mix_design->rate;
                                    
                                    $total2 = $qty_cft * $chalan->rate;
                                    ?>
                                    <td>
                                        <input type="checkbox" id="checkbox" name="checkbox[]"/>
                                    </td>
                                    <td>{{ $chalan->challan_no }}</td>
                                    <td>{{ $chalan->mix_design->psi }}</td>
                                    <td>{{ date('d-M-y', strtotime($chalan->sell_date)) }}</td>
                                    <td>{{ $chalan->customer->name }}</td>
                                    <td>{{ $chalan->project->name }}, {{ $chalan->project->address }}</td>
                                    <td>{{ number_format($chalan->cuM, 2) }}</td>
                                    <td>{{ number_format($qty_cft, 2) }}</td>

                                    @if (($user->can('challan-rate-view') || $user->hasRole(['super-admin'])) && $chalan->rate <= 0.0)
                                        <td>{{ number_format($chalan->mix_design->rate, 2) }}</td>
                                        <td>{{ number_format($total, 2) }}</td>
                                    @else
                                        {{--                                    after edit rate show will be here --}}
                                        <td>{{ number_format($chalan->rate, 2) }}</td>
                                        <td>{{ number_format($total2, 2) }}</td>
                                    @endif

                                    <td>{{ $chalan->status == 1 ? 'Not Submitted' : 'Submitted' }}</td>
                                    @if ($user->branchId == '')
                                        <td>{{ $chalan->branch->name ?? '-' }}</td>
                                    @endif
                                    <td class="hidden-print">
                                        @if ($chalan->status == 1 && $chalan->demo_bill->status != 2)
                                            @if ($user->hasRole('super-admin') || $user->can('challan-edit'))
                                                <a role="button" class="challan-edit-btn"
                                                    data-challan_id="{{ $chalan->id }}"
                                                    data-challan_customer_id="{{ $chalan->customer_id }}"
                                                    data-challan_project_id="{{ $chalan->project->id }}"
                                                    data-challan_mix_design_id="{{ $chalan->mix_design_id }}"
                                                    data-challan_date="{{ date('m/d/Y', strtotime($chalan->sell_date)) }}"
                                                    data-challan_cuM="{{ $chalan->cuM }}"
                                                    data-challan_rate="{{ $chalan->rate <= 0.0 ? $chalan->mix_design->rate : $chalan->rate }}"
                                                    data-target="#challanEditModal" data-toggle="modal">
                                                    <span class="fa fa-edit"></span>
                                                </a>
                                            @endif
                                            @if ($user->hasRole('super-admin') || $user->can('challan-delete'))
                                                <a href="{{ route('customer.challan.delete', $chalan->id) }}"
                                                    onclick='return confirm("Are you sure you want to delete?");'
                                                    class="fa fa-trash"></a>
                                            @endif
                                        @elseif($chalan->demo_bill && $chalan->demo_bill->status == 2)
                                            <span class="text-success">Demo Bill Generated</span>
                                        @endif
                                    </td>
                                </tr>
                                <?php $total_cum += $chalan->cuM;
                                $total_cft += $qty_cft;
                                
                                if ($chalan->rate <= 0.0) {
                                    $total_amount += $total; // use mix_design->rate
                                } else {
                                    $total_amount += $total2; // use challan->rate
                                }
                                
                                ?>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background-color:#999999; color: #fff;">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><b>Total:</b></td>
                                <td><b>{{ number_format($total_cum, 2) }}</b></td>
                                <td><b>{{ number_format($total_cft, 2) }}</b></td>
                                @if ($user->hasRole(['super-admin']) || $user->can('challan-rate-view'))
                                    <td></td>
                                @endif
                                <td><b>{{ number_format($total_amount, 2) }}</b></td>
                                <td></td>
                                @if ($user->branchId == '')
                                    <td></td>
                                @endif
                                <td class="hidden-print"></td>
                            </tr>
                        </tfoot>

                    </table>
                    {{-- <div class="text-center">{{ $challans->links() }}</div> --}}
                    {{-- </form> --}}
                </div>
            </div>
        </div>
        <div class="dr"><span></span></div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="challanEditModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Challan Rate</h4>
                </div>
                <form action="{{ route('customer.challan.update') }}" method="post">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        {{-- <input type="hidden" name="customer_id" id="challan_customer_id" value=""/> --}}
                        <input type="hidden" name="id" id="challan_id" value="" />
                        <div class="row-form clearfix">
                            <label class="col-md-3">Customer: </label>
                            <div class="col-md-6">
                                <select class="select2" name="customer_id" id="challan_customer_id" required>
                                    <option value="">choose customer</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <label class="col-md-3">Project: </label>
                            <div class="col-md-6">
                                <select name="project_id" id="challan_project_id" required>
                                    <option value="">choose project</option>
                                </select>
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <label class="col-md-3">PSI: </label>
                            <div class="col-md-6">
                                <select name="mix_design_id" id="challan_mix_design_id" required>
                                    <option value="">choose PSI</option>
                                </select>
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <label class="col-md-3">Rate: </label>
                            <div class="col-md-6">
                                <input type="text" value="" name="rate" id="challan_rate" />
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <label class="col-md-3">Date: </label>
                            <div class="col-md-6">
                                <input type="text" value="" name="sell_date" id="challan_date"
                                    class="datepicker" required />
                            </div>
                        </div>
                        <div class="row-form clearfix">
                            <label class="col-md-3">Quantity(CuM): </label>
                            <div class="col-md-6">
                                <input type="text" value="" name="cuM" id="challan_cuM" required />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="text-align: center;">
                        <button class="btn btn-primary" type="submit">Save Updates</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
@section('page-script')
    <script>
        $(document).ready(function() {

            //auto complere for customer name
            $("#search_name").autocomplete({
                source: '{!! URL::route('autoComplete', ['table_name' => 'customers']) !!}',
                minLenght: 1,
                autoFocus: true,
            });

            $(document).on('click', '.challan-edit-btn', function() {

                var project_id = $(this).data('challan_project_id');
                var mix_design_id = $(this).data('challan_mix_design_id');

                $('#challan_project_id').empty();
                $('#challan_project_id').append($('<option>', {
                    value: '',
                    text: 'choose project'
                }));
                $('#challan_mix_design_id').empty();
                $('#challan_mix_design_id').append($('<option>', {
                    value: '',
                    text: 'choose PSI'
                }));
                $.ajax({
                    type: "POST",
                    url: "{{ route('customer.project.psi') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        customer_id: $(this).data('challan_customer_id')
                    },
                    dataType: 'JSON',
                    success: function(resp) {
                        $.each(resp.projects, function(i, member) {
                            $('#challan_project_id').append($('<option>', {
                                value: member["id"],
                                text: member["name"]
                            }));
                        });
                        $.each(resp.mix_design_psi, function(i, member) {
                            $('#challan_mix_design_id').append($('<option>', {
                                value: member["id"],
                                text: member["psi"]
                            }));
                        });

                        $('#challan_project_id').val(project_id);
                        $('#challan_mix_design_id').val(mix_design_id);
                    }
                });

                $('#challan_customer_id').val($(this).data('challan_customer_id')).change();
                $('#challan_id').val($(this).data('challan_id'));
                $('#challan_date').val($(this).data('challan_date'));
                $('#challan_cuM').val($(this).data('challan_cum'));
                $('#challan_rate').val($(this).data('challan_rate'));

            });

        });
    </script>



    <script type="text/javascript">
        $(document).ready(function() {



            //Buttons examples
            $('#datatable4').DataTable({


                dom: 'Bfrtip',
                select: true,
                fixedHeader: true,
                lengthMenu: [
                    [50, 100, 150, 200, 300, 500, 700, -1],
                    [50, 100, 150, 200, 300, 500, 700, "All"]
                ],
                "order": [],
                buttons: [{
                        extend: 'pageLength',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: [':not(.hidden-print)']
                        },
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-info',
                        exportOptions: {
                            columns: [':not(.hidden-print)']
                        },
                        footer: true,
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: [':not(.hidden-print)']
                        },
                        footer: true,
                        customize: function(doc) {
                         
                            if (doc.content[1].table && doc.content[1].table.body) {
                                var table = doc.content[1].table;
                                
                            }

                            
                            doc.defaultStyle.fontSize = 9;
                            doc.styles.tableHeader.fontSize = 10;
                            doc.pageMargins = [20, 30, 20, 40]; // [left, top, right, bottom]

                            

                            doc.content[1].table.dontBreakRows = true;
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print All',
                        autoPrint: true,
                        className: 'btn btn-warning',
                        exportOptions: {
                            columns: [':not(.hidden-print)']
                        },
                        footer: true,

                        messageTop: function() {
                            return print_header;
                        },
                        messageBottom: 'Print: {{ date('d-M-Y') }}',
                        customize: function(win) {

                            $(win.document.body).find('h1').css('text-align', 'center');
                            $(win.document.body).find('table')
                                .removeClass(
                                    'table-striped table-responsive-sm table-responsive-lg dataTable'
                                )
                                .addClass('compact')
                                .css('font-size', 'inherit', 'color', '#000');
                            $(win.document.body).find('table tfoot tr').css('border-top',
                                '2px solid #000');
                            var tfoot = $(win.document.body).find('table tfoot').detach();
                            $(win.document.body).find('table tbody').after(tfoot);

                            // Add CSS to prevent footer repetition
                            var css = 'tfoot { display: table-row-group; }',
                                head = $(win.document.head),
                                style = $('<style type="text/css"></style>').html(css);

                            $(head).append(style);


                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print Page',
                        autoPrint: true,
                        className: 'btn btn-success',
                        // exportOptions: {
                        //     columns: [':not(.hidden-print)'],
                        //     modifier: {
                        //         page: 'current'
                        //     }
                        // },
                        exportOptions: {
                            columns: [':not(.hidden-print)'],
                            stripHtml: false,
                            format: {
                                body: function(data, row, column, node) {
                                    return data;
                                }
                            }
                        },
                        footer: true,

                        messageTop: function() {
                            return print_header;
                        },
                        messageBottom: 'Print: {{ date('d-M-Y') }}',
                        customize: function(win) {

                            $(win.document.body).find('h1').css('text-align', 'center');
                            $(win.document.body).find('table')
                                .removeClass(
                                    'table-striped table-responsive-sm table-responsive-lg dataTable'
                                )
                                .addClass('compact')
                                .css('font-size', 'inherit', 'color', '#000');
                            $(win.document.body).find('table tfoot tr').css('border-top',
                                '2px solid #000');
                            var tfoot = $(win.document.body).find('table tfoot').detach();
                            $(win.document.body).find('table tbody').after(tfoot);

                            // Add CSS to prevent footer repetition
                            var css = 'tfoot { display: table-row-group; }',
                                head = $(win.document.head),
                                style = $('<style type="text/css"></style>').html(css);

                            $(head).append(style);



                        }

                    }

                ]
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            $(".datepicker").datepicker({
                changeMonth: true,
                changeYear: true,
                autoclose: true,
                todayHighlight: true,
                format: 'yyyy-mm-dd'
            });
        });
    </script>
@endsection
