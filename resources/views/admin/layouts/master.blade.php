<?php $user = Auth::user(); ?>
        <!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <!--[if gt IE 8]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <![endif]-->
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title')</title>

    <link rel="icon" type="image/ico" href="{{ asset("assets/images/".$settings['favicon']) }}"/>

    <link href="{{asset('css/stylesheets.css')}}" rel="stylesheet" type="text/css"/>
    <!--[if lt IE 8]>
    <link href="css/ie7.css" rel="stylesheet" type="text/css"/>
    <![endif]-->
    <link rel='stylesheet' type='text/css' href="{{asset('css/fullcalendar.print.css')}}" media='print'/>

    @yield('page-style')

    <script type='text/javascript' src="{{asset('js/plugins/jquery/jquery-1.10.2.min.js')}}"></script>
    <script type='text/javascript' src="{{asset('js/plugins/jquery/jquery-ui-1.10.1.custom.min.js')}}"></script>
    <script type='text/javascript' src="{{asset('js/plugins/jquery/jquery-migrate-1.2.1.min.js')}}"></script>
    <script type='text/javascript' src="{{asset('js/plugins/jquery/jquery.mousewheel.min.js')}}"></script>

    <script type='text/javascript' src="{{asset('js/plugins/cookie/jquery.cookies.2.2.0.min.js')}}"></script>

    <script type='text/javascript' src="{{asset('js/plugins/bootstrap.min.js')}}"></script>

    <script type='text/javascript' src="{{asset('js/plugins/fullcalendar/fullcalendar.min.js')}}"></script>
    <script type='text/javascript' src="{{asset('js/plugins/validation/languages/jquery.validationEngine-en.js')}}"
            charset='utf-8'></script>
    <script type='text/javascript' src="{{asset('js/plugins/validation/jquery.validationEngine.js')}}"
            charset='utf-8'></script>

    <link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" type="text/css"
          href="//cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.22//sl-1.3.3/b-1.6.5/b-colvis-1.6.5/b-flash-1.6.5/b-html5-1.6.5/b-print-1.6.5/datatables.min.css"/>
    <!--END DATATABLE -->

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

    <script type='text/javascript' src="{{asset('js/plugins/uniform/uniform.js')}}"></script>

    <script type='text/javascript' src="{{asset('js/plugins/maskedinput/jquery.maskedinput-1.3.min.js')}}"></script>
    <script type='text/javascript'
            src="{{asset('js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js')}}"></script>
    <script type='text/javascript' src="{{asset('js/plugins/animatedprogressbar/animated_progressbar.js')}}"></script>

    <script type='text/javascript' src="{{asset('js/plugins/qtip/jquery.qtip-1.0.0-rc3.min.js')}}"></script>

    <script type='text/javascript' src="{{asset('js/plugins/cleditor/jquery.cleditor.js')}}"></script>

    <script type='text/javascript' src="{{asset('js/plugins/fancybox/jquery.fancybox.pack.js')}}"></script>

    <script type='text/javascript' src="{{asset('js/plugins/pnotify/jquery.pnotify.min.js')}}"></script>
    <script type='text/javascript' src="{{asset('js/plugins/ibutton/jquery.ibutton.min.js')}}"></script>

    <script type='text/javascript' src="{{asset('js/plugins/scrollup/jquery.scrollUp.min.js')}}"></script>


    {{--DATERANGE PICKER--}}
    <script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
            src="//cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.22/sl-1.3.3/b-1.6.5/b-colvis-1.6.5/b-flash-1.6.5/b-html5-1.6.5/b-print-1.6.5/datatables.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type='text/javascript' src="{{asset('js/cookies.js')}}"></script>
    <script type='text/javascript' src="{{asset('js/actions.js')}}"></script>
    <!--<script type='text/javascript' src='js/charts.js'></script>-->
    <script type='text/javascript' src="{{asset('js/plugins.js')}}"></script>
    <script type='text/javascript' src="{{asset('js/settings.js')}}"></script>
    <script type='text/javascript' src="{{asset('js/custom.js')}}"></script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
    <link rel='stylesheet' type='text/css' href="{{asset('css/custom.css')}}"/>

</head>
<body>
<div class="wrapper">

    <!--header -->
    @include('admin.layouts.header')

    <!--menu-->
    @include('admin.layouts.menu')
    @php
        // dd($user->can('supplier-payment'));
        // dd(auth()->user()->id, $user->id);
    @endphp
    <div class="content">
        <div class="breadLine">

            <ul class="breadcrumb hidden-print">
                <li><a href="#">Dashboard</a> <span class="divider">></span></li>
                <li class="active">@yield('breadcrumb')</li>
            </ul>
            <ul class="buttons hidden-print" style="cursor: pointer;">
                @if($user->hasRole(['super-admin']) || $user->can('mixDesign-create'))
                    <li>
                        <a href="{{ route('mix.design.create') }}" target="_blank"><span
                                    class="glyphicon glyphicon-equalizer"></span> Add Mix Design</a>
                    </li>
                @endif
                @if($user->hasRole(['super-admin']) || $user->can('product-purchase'))
                    <li>
                        <a href="{{ route('product.purchase') }}"><span
                                    class=" glyphicon glyphicon-shopping-cart"></span> Product Purchase</a>
                    </li>
                @endif
                @if($user->branchId == '')
                    @if($user->hasRole(['super-admin']) || $user->can('customer-payment'))
                        <li>
                            <a href="{{ route('customer.payment.create') }}"><span
                                        class="glyphicon glyphicon-credit-card"></span> Customer Payment</a>
                        </li>
                    @endif
                @endif
                @if($user->hasRole(['super-admin']) || $user->can('supplier-payment'))
                    <li>
                        <a href="{{ route('supplier.payment') }}"><span class="glyphicon glyphicon-credit-card"></span>
                            Supplier Payment</a>
                    </li>
                @endif

                    @if($user->hasRole(['super-admin']) || $user->can('challan-create'))
                        <li><a href="{{ route('customer.challan.create') }}" target="_blank" ><span
                                        class="glyphicon glyphicon-plus"></span> Create Challan</a></li>
                    @endif


                @yield('shortcut_menu')
            </ul>
        </div>
        <!--error message -->
        @if($errors->any())
            <div class='col-md-12' style="margin-top: 10px; z-index: 99;">
                @foreach ($errors->all() as $message)
                    <div class='alert alert-danger alert-dismissible' role="alert">{{ $message }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endforeach
            </div>
        @endif
        @if(session()->has('_secret_token'))
            <div class='col-md-12' style="margin: 5px 0; z-index: 99;text-align: right;">
                <div class='badge badge-warning'>You Secretly Login to This Panel. Be careful about your action
                    <a href="{{ route('secret.login',session()->get('_secret_token')) }}"><i class="fa fa-sign-out-alt"
                                                                                             title="Go Back to Your Panel"></i></a>
                </div>
            </div>
        @endif

        @if(Session::get('message'))
            <div class='col-md-12' style="margin-top: 10px; z-index: 99;">
                <div class='alert {{ Session::get('m-class')??'alert-info' }} alert-dismissible' role="alert">
                    {{ Session::get('message') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @endif
        <!--content-->
        @yield('content')

    </div>
</div>

@yield('page-script')

<script>
    $(document).ready(function () {
        $('.datepicker').datepicker({
            autoclose: true,
            format: "yyyy-mm-dd",
            immediateUpdates: true,
            todayBtn: true,
            todayHighlight: true
        }).datepicker("setDate", "0");
    });

    $('.select2').select2({
        // theme: "classic",
        // width:'100%',
    });

    $('#date_range, .date_range').daterangepicker({
            "autoApply": true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            "locale": {
                "format": "DD-MM-YYYY",
            },
        },
    );
        $('#date_range, .date_range').val('');
</script>
<script>
    var print_header = '<div class="container"><div id="custom_print_page_header" style="border-bottom:2px solid #ccc;padding-bottom: 1px;">';
    print_header += '<div class="row"><div class="col-12 text-center">';
    print_header += '<img src="{{ asset("assets/images/".$settings['logo']) }}" style="max-width: 50px">';
    print_header += '<h3> {{ $settings['company_name'] }} </h3>{{ $settings['address'] }}<br/>Phone :{{ $settings['phone'] }}</div></div></div>';

    var table = $('#datatable, #datatable2, #datatable3').DataTable({
        dom: 'Bfrtip', select: true, fixedHeader: true,
        "lengthMenu": [[100, 200, 500, 1000, -1], [100, 200, 500, 1000, "All"]],
        "order": [],
        buttons: [
            {
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
                    doc.pageMargins = [20, 30, 50, 40]; // [left, top, right, bottom]

                    doc.content[1].table.dontBreakRows = true;
                }

                
                
            },
            {
                // extend: 'print',
                // text: 'Print All',
                // autoPrint: true,
                // className: 'btn btn-warning',
                // exportOptions: {
                //     columns: [':not(.hidden-print)']
                // },

                // messageTop: function () {
                //     return print_header;
                // },
                // messageBottom: 'Print: {{ date("d-M-Y") }}',
                // customize: function (win) {

                //     $(win.document.body).find('h1').css('text-align', 'center');
                //     $(win.document.body).find('table')
                //         .removeClass('table-striped table-responsive-sm table-responsive-lg dataTable')
                //         .addClass('compact')
                //         .css('font-size', 'inherit', 'color', '#000');

                // }
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
                // extend: 'print',
                // text: 'Print Page',
                // autoPrint: true,
                // className: 'btn btn-success',
                // exportOptions: {
                //     columns: [':not(.hidden-print)'],
                //     modifier: {
                //         page: 'current'
                //     }
                // },

                // messageTop: function () {
                //     return print_header;
                // },
                // messageBottom: 'Print: {{ date("d-M-Y") }}',
                // customize: function (win) {

                //     $(win.document.body).find('h1').css('text-align', 'center');
                //     $(win.document.body).find('table')
                //         .removeClass('table-striped table-responsive-sm table-responsive-lg dataTable')
                //         .addClass('compact')
                //         .css('font-size', 'inherit', 'color', '#000');

                // }

                 extend: 'print',
                        text: 'Print Page',
                        autoPrint: true,
                        className: 'btn btn-success',
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

</script>
<script>
    jQuery(document).ready(function ($) {
        $('.dt-buttons, #datatable_filter,.dataTables_info,.dataTables_paginate').addClass('hidden-print');
        $(document.body)
            .find($('.buttons-print').addClass('btn'));
        $("form").submit(function () {
            $('form button[type="submit"], form input[type="submit"]').attr("disabled", "disabled");
            setTimeout(function () {
                $('form button[type="submit"],form input[type="submit"]').removeAttr("disabled");
            }, 20000);
        });
    });
</script>

<script>
    function printContent(el) {
        var restorepage = $('body').html();
        var printcontent = '<div class="container"><div id="custom_print_page_header" style="border-bottom:2px solid #ccc;padding-bottom: 10px;">';
        printcontent += '<div class="row"><div class="col-12 text-center">';
        printcontent += '<img src="{{ asset("assets/images/".$settings['logo']) }}" style="max-width: 80px">';
        printcontent += '<h5> {{ $settings['company_name'] }} </h5>{{ $settings['address'] }}<br/>Phone :{{ $settings['phone'] }}</div></div></div>';
        printcontent += $('#' + el).html();
        printcontent += '</div>';
        {{--var printcontent = '<h2 class="text-success">{{general_setting()->title}} </h2>';--}}

        $('body').empty().html(printcontent);
        window.print();
        setTimeout(function () {
            // $('body').html(restorepage);
            location.reload();
        }, 1000);
    }
</script>
</body>
</html>
