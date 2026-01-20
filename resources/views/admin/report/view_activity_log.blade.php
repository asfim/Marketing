@extends('admin.layouts.master')
@section('title', 'Activity Log')
@section('breadcrumb', 'Activity Log')
@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Activity Logs</h1>

                    <div class="col-md-8 search_box" style="margin-top: 4px; float: right;">
                        <form action="{{ route('activity.log') }}" class="form-horizontal">
                            <div class="form-row" align="right">
                                {{--{{csrf_field()}}--}}
                                <select name="user_id" style="padding: 5px;">
                                    <option value="">Search by user</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->userId }}" {{ ($user->userId == request('user_id'))?'selected':'' }}>{{ $user->userName }}</option>
                                    @endforeach
                                </select>
                                <input type="text"  name="search_text" placeholder="Enter Search Text"/>
                                <input type="text" name="date_range" id="date_range" placeholder="Search by date" />
                                <button type="submit" class="btn btn-default">Search</button>
                            </div>

                        </form>
                    </div>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-condensed table-hover" id="datatable2">
                        <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Activity</th>
                            <th>By</th>
                            <th>DateTime</th>
                            {{--<th>Category</th>--}}
                            <th>IP</th>
                            <th>Page</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($activity_logs as $log)
                        <tr>
                            <td></td>
                            <td>{{ $log->log_action }}</td>
                            <td>{{ $log->username }}</td>
                            <td>{{ $log->log_time }}</td>
{{--                            <td>{{ $log->log_name }}</td>--}}
                            <td>{{ $log->ip }}</td>
                            <td>{{ $log->page }}</td>
                        </tr>
                       @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>

@section('page-script')
    <script>
        jQuery(document).ready(function($) {
            $('#datatable2').DataTable({
                "fnRowCallback" : function(nRow, aData, iDisplayIndex){
                    $("td:first", nRow).html(iDisplayIndex +1);
                    return nRow;
                },
                dom: 'Bfrtip',
                "lengthMenu": [[100, 200, 500, 1000, -1], [100, 200, 500, 1000, "All"]],
                buttons: ['pageLength',
                    {
                        extend: 'csv',
                        // className: 'btn btn-success',
                        exportOptions: {
                            columns: [':not(.hidden-print)']
                        },
                    },
                    {
                        extend: 'pdf',
                        // className: 'btn btn-info',
                        exportOptions: {
                            columns: [':not(.hidden-print)']
                        },
                    },
                    {
                        extend: 'print',
                        text: 'Print All',
                        autoPrint: true,
                        // className: 'btn btn-warning',
                        exportOptions: {
                            columns: [':not(.hidden-print)']
                        },

                        messageTop: function () {
                            return '<h2 class="text-center">{{ 'Branch Name' }}</h2>'
                        },
                        messageBottom: 'Print: {{ date("d-M-Y") }}',
                        customize: function (win) {

                            $(win.document.body).find('h1').css('text-align', 'center');
                            $(win.document.body).find('table')
                                .removeClass('table-striped table-responsive-sm table-responsive-lg dataTable')
                                .addClass('compact')
                                .css('font-size', 'inherit', 'color', '#000');

                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print Page',
                        autoPrint: true,
                        // className: 'btn btn-info',
                        exportOptions: {
                            columns: [':not(.hidden-print)'],
                            modifier: {
                                page: 'current'
                            }
                        },

                        messageTop: function () {
                            return '<h2 class="text-center">{{ 'Branch Name' }}</h2>'
                        },
                        messageBottom: 'Print: {{ date("d-M-Y") }}',
                        customize: function (win) {

                            $(win.document.body).find('h1').css('text-align', 'center');
                            $(win.document.body).find('table')
                                .removeClass('table-striped table-responsive-sm table-responsive-lg dataTable')
                                .addClass('compact')
                                .css('font-size', 'inherit', 'color', '#000');

                        }

                    }

                ]

            } );
        });
    </script>
@endsection
@endsection

