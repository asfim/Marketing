@extends('admin.layouts.master')
@section('title', 'Customer Bill Details')
@section('breadcrumb', 'Customer Bill Details')
@section('page-style')
    <style>
        table tbody.client_info tr td h5 {
            padding: 0;
            margin: 5px 0;
        }
    </style>
@endsection
@section('content')

    <div class="workplace">

        <div class="row" id="bill-details">

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="col-md-4 no-print">
                        <div class="isw-documents"></div>
                        <h4>Bill Summary : <span class="src-info">Bill No: {{$invoice_no}}</span></h4>
                    </div>

                    <div class="col-md-8 text-right" style="margin-top: 4px;">
                        <span class="btn btn-sm btn-info hidden-print" onclick="printBill('bill-details', 'pad')"> <i
                                    class="fa fa-print"></i> Print (With Pad)</span>
                        <span class="btn btn-sm btn-success hidden-print"
                              onclick="printBill('bill-details', 'non-pad')"> <i class="fa fa-print"></i> Print (Without Pad)</span>
                    </div>
                </div>
                <div class="block-fluid table-sorting clearfix">
                    <table cellpadding="0" cellspacing="0" width="100%"
                           style="border-collapse:separate; border-spacing:0 1px;" class="table-hover"
                           id="summary_table">
                        <tbody class="client_info">
                        <tr style="padding-bottom: 1px!important;">
                            <td width="65%">
                                <h5 style="padding:0px 5px;"><b>Client Name: </b>
                                    {{ $bill_row->customer->name }}</h5>
                            </td>
                            <td width="65%"><h5 style="padding:1px 5px;"><b>Bill Date: </b>
                                    {{ date('d.m.Y',  strtotime($bill_row->bill_date)) }}
                                </h5>
                            </td>
                        </tr>
                        <tr>
                            <td width="65%">
                                <h5 style="padding:1px 5px;"><b>Client Address: </b>
                                    {{ $bill_row->customer->address }}
                                </h5>
                            </td>
                            <td>
                                <h5 class="print-only" style=" display: none ;padding:1px 5px;">

                                    <b>Bill No:</b>
                                    {{ $user_bill_no }}</h5>

                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <h5 class="no-print" style="    margin: 5px;;">
                                        <b>Bill No:</b>
                                    </h5>

                                    <!-- Input form visible only on screen -->
                                    <div class="no-print" style="display: inline-flex; align-items: center; gap: 5px;">
                                        <form id="userBillNoForm" method="POST"
                                              action="{{ route('bill.updateUserBillNo', $bill_row->id) }}"
                                              style="display: inline-flex; align-items: center; gap: 5px; margin: 0;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="demobill" value="{{$demobill}}">

                                            <input
                                                    type="text"
                                                    name="user_bill_no"
                                                    value="{{ old('user_bill_no', $user_bill_no) }}"
                                                    style="font-size: 14px; width: 175px;"
                                                    required
                                            >
                                            <button type="submit"
                                                    style="padding: 4px 12px; cursor: pointer; background-color: #28a745; color: white; border: none; border-radius: 4px;">
                                                Save
                                            </button>

                                        </form>
                                    </div>
                                </div>


                            </td>


                        </tr>
                        <tr>
                            <td>
                                <h5 style="padding:1px 5px;"><b>Project Name: </b>
                                    {{ ($project_row != "")?$project_row->name:'' }}
                                </h5>
                            </td>
                            <td>
                                <h5 style="padding:1px 5px;"><b>Concrete Strength: </b>
                                    {{$bill_row->psi.'psi'}}
                                </h5>
                            </td>
                        </tr>
                        <tr>
                            <td><h5 style="padding:1px 5px;"><b>Project Address:</b>
                                    {{ ($project_row != "")?$project_row->address:'' }}
                                </h5></td>
                            <td><h5 style="padding:1px 5px;"><b>Concrete Method:</b>
                                    {{$bill_row->concrete_method}}
                                </h5></td>


                        </tr>
                        <tr>
                            <td>
                                <h5 style="padding:1px 5px;"><b>Bill Reference No:</b>
                                    {{$invoice_no}}
                                </h5>
                            </td>


                            <td>
                                <h5 class="print-only" style=" display: none ;padding:1px 5px;">

                                    <b>Work Order NO:</b>
                                    {{ $user_work_order_no }}</h5>

                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <h5 class="no-print" style="    margin: 5px;;">
                                        <b>Work Order NO:</b>
                                    </h5>

                                    <!-- Input form visible only on screen -->
                                    <div class="no-print" style="display: inline-flex; align-items: center; gap: 5px;">
                                        <form id="userworkNoForm" method="POST"
                                              action="{{ route('bill.updateUserWorkOrderNo', $bill_row->id) }}"
                                              style="display: inline-flex; align-items: center; gap: 5px; margin: 0;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="demobill" value="{{$demobill}}">
                                            <input
                                                    type="text"
                                                    name="user_work_order_no"
                                                    value="{{ old('user_work_order_no', $user_work_order_no) }}"
                                                    style="font-size: 14px; width: 120px;"

                                            >
                                            <button type="submit"
                                                    style="padding: 4px 12px; cursor: pointer; background-color: #28a745; color: white; border: none; border-radius: 4px;">
                                                Save
                                            </button>

                                        </form>
                                    </div>
                                </div>


                            </td>


                        </tr>
                        </tbody>
                    </table>

                    <div style="margin-bottom:10px;"></div>
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable1">
                        <thead>
                        <tr>
                            <th style="text-align: center">S/N</th>
                            <th style="text-align: center">Challan No</th>
                            <th style="text-align: center">Challan Date</th>
                            <th style="text-align: center">Qty(Cu.M)</th>
                            <th style="text-align: center">Qty(CFT)</th>
                            <th style="text-align: center">Rate(Per CFT)</th>
                            <th style="text-align: center">Amount(BDT)</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php $sl = 1; ?>
                        @foreach($challan_rows as $challan_row)
                            <tr>
                                <td style="text-align: center">{{$sl}}</td>
                                <td style="text-align: center">{{$challan_row->challan_no}}</td>
                                <td style="text-align: center"><?php echo date('d-M-y', strtotime($challan_row->sell_date)); ?></td>
                                <td style="text-align: center">{{number_format($challan_row->cuM,2)}}</td>
                                <td style="text-align: center">{{number_format($challan_row->cuM*35.315,3)}}</td>
                                @if ($challan_row->rate <= 0.00)
{{--                                    <td>{{ $challan_row->mix_design->rate }} </td>--}}
                                    <td style="text-align: center">{{ number_format($rate_per_cft,2)  }} </td>
                                    <td style="text-align: center">{{number_format($challan_row->cuM * 35.315 * $rate_per_cft,2)}}</td>
                                @else
                                    {{--                                    after edit rate show will be here--}}
                                    <td>{{ number_format($challan_row->rate,2) }}</td>
                                    <td style="text-align: center">{{number_format($challan_row->cuM * 35.315 *$challan_row->rate,2)}}</td>
                                @endif

                            </tr>
                                <?php $sl++; ?>
                        @endforeach
                        </tbody>

                        <tfoot>
                        <tr>
                            <td colspan="3" style="font-weight:bold; text-align: center">Total</td>
                            <td style="font-weight:bold;text-align:center;">{{ number_format($bill_row->total_cuM, 2) }}</td>
                            <td style="font-weight:bold; text-align:center;">{{ number_format($bill_row->total_cft, 3) }}</td>
                            {{-- <td style="font-weight:bold; text-align:center;">{{ number_format($rate_per_cft, 2) }}</td> --}}
                            <td style="font-weight:bold; text-align:center;">--</td>
                            <td style="font-weight:bold; text-align:center;">{{ number_format($bill_row->total_amount_before_discount - $bill_row->pump_charge, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="font-weight:bold; text-align: center">Pump Charge</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align:center;">{{ number_format($bill_row->pump_charge, 2) }}</td>
                        </tr>


                        <tr>
                            <td colspan="3" style="font-weight:bold; text-align: center">Sub-Total</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align:center;">{{ number_format($bill_row->total_amount_before_discount, 2) }}</td>
                        </tr>

                        {{--                            <tr>--}}
                        {{--                                <td colspan="3" style="font-weight:bold; text-align: center">Discount</td>--}}
                        {{--                                <td></td>--}}
                        {{--                                <td></td>--}}
                        {{--                                <td></td>--}}
                        {{--                                <td style="text-align:right;">--}}
                        {{--                                    {{ number_format($bill_row->total_amount_before_discount - $bill_row->total_amount, 2) }}--}}
                        {{--                                </td>--}}
                        {{--                            </tr>--}}

                        @php
                            $aitAmount = $bill_row->total_amount * $bill_row->ait / (100 - $bill_row->ait);
                            $vatAmount = $bill_row->total_amount * $bill_row->vat / 100;
                        @endphp

                        @if($aitAmount != 0)
                            <tr>
                                <td colspan="3" style="font-weight:bold; text-align: center">AIT ({{ $bill_row->ait }}
                                    %)
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="text-align:center;">
                                    {{ number_format($aitAmount, 2) }}
                                </td>
                            </tr>
                        @endif

                        @if($vatAmount != 0)
                            <tr>
                                <td colspan="3" style="font-weight:bold; text-align: center">VAT ({{ $bill_row->vat }}
                                    %)
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td style="text-align:center;">
                                    {{ number_format($vatAmount, 2) }}
                                </td>
                            </tr>
                        @endif


                        <tr>
                            <td colspan="3" style="font-weight:bold; text-align: center">Grand Total</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align:center; font-weight:bold;">
                                {{ number_format(
                                  $bill_row->total_amount
                                  + ($bill_row->total_amount * $bill_row->ait / 100)
                                  + ($bill_row->total_amount * $bill_row->vat / 100), 2) }}
                            </td>
                        </tr>


                        </tfoot>

                    </table>
                </div>
            </div>
        </div>
        <div class="dr"><span></span></div>
    </div>
@endsection
<script>


    // SHIZAN WORKS
    function printBill(elementId, mode) {
    const PAGE_ROW_LIMIT = 20; // Max rows per page

    const billContent = document.getElementById(elementId);

    const summaryTable = billContent.querySelector('#summary_table')?.outerHTML || '';

    const datatable1 = billContent.querySelector('#datatable1');
    if (!datatable1) {
        alert('Data table not found!');
        return;
    }

    const thead = datatable1.querySelector('thead').outerHTML;
    const tbodyRows = Array.from(datatable1.querySelectorAll('tbody tr'));
    const tfoot = datatable1.querySelector('tfoot')?.outerHTML || '';
    const totalRows = tbodyRows.length;

    // Adjust footer space dynamically
    const MIN_FOOTER_SPACE = (totalRows == 10 || totalRows == 11) ? 12 : 10;

    const pagesHtml = [];
    let startIndex = 0;

    while (startIndex < totalRows) {
        let endIndex = startIndex + PAGE_ROW_LIMIT;
        let isLastPage = endIndex >= totalRows;

        if (isLastPage && (totalRows - startIndex) > (PAGE_ROW_LIMIT - MIN_FOOTER_SPACE)) {
            endIndex = startIndex + (PAGE_ROW_LIMIT - MIN_FOOTER_SPACE);
            isLastPage = false;
        }

        const pageRows = tbodyRows.slice(startIndex, endIndex).map(row => row.outerHTML).join('');

        let tableHtml = `
            <table cellpadding="0" cellspacing="0" width="100%" class="table">
                ${thead}
                <tbody style= >${pageRows}</tbody>
                ${isLastPage ? tfoot : ''}
            </table>
        `;

        let summarySection = (startIndex === 0) ? summaryTable : '';

        pagesHtml.push(createPageContent(summarySection, tableHtml, isLastPage, mode, startIndex === 0));
        startIndex = endIndex;
    }

    // Handle any leftover rows (rare, but safe)
    if (startIndex < totalRows) {
        const pageRows = tbodyRows.slice(startIndex).map(row => row.outerHTML).join('');

        let tableHtml = `
            <table cellpadding="0" cellspacing="0" width="100%" class="table">
                ${thead}
                <tbody>${pageRows}</tbody>
                ${tfoot}
            </table>
        `;

        pagesHtml.push(createPageContent('', tableHtml, true, mode, false));
    }

    function createPageContent(summarySection, tableHtml, isLastPage, mode, isFirstPage) {
        let invoiceTitle = isFirstPage ? '<div class="invoice-title">Invoice</div>' : '';

        let paymentSection = isLastPage ? `
            <div class="payment-note" style="font-size: 15px; font-weight: bold;">
                <b>In Words:</b> {{ $grand_total_in_words }}<br><br>
            </div>
            <div class="appreciate">
                We will highly appreciate to receive the payment at the earliest
            </div>
            <div class="signature-area">
                <span>Received By</span>
                <span>Confirmed By</span>
            </div>
        ` : '';

        const watermarkHtml = (mode !== 'pad') ? `
            <img src="{{ asset('assets/images/logo.png') }}" class="watermark" />
        ` : '';

        return `
            <div class="content">
                ${watermarkHtml}
                ${invoiceTitle}
                ${summarySection}
                <div style="margin-bottom:20px;"></div>
                ${tableHtml}
                ${paymentSection}
            </div>
        `;
    }

    const headerHtml = `
        <div class="header">
            <div class="header-content">
                <img src="{{ asset('assets/images/logo.png') }}" class="logo">
                <div class="company-name">Concept Concrete Limited</div>
            </div>
        </div>
    `;

    const footerHtml = `
        <div class="footer">
            <div class="border-green"></div>
            <div class="border-black"></div>
            <div class="footer-content">
                Head Office: 1 No. Joykali Mondir Road (1st Floor), Wari, Dhaka-1203. 📞02-226638877 ✉ccbd16@gmail.com 🅵/cclbd16
            </div>
        </div>
    `;

    const allPagesHtml = pagesHtml.map(content => `
        <div class="page">
            ${mode === 'non-pad' ? headerHtml : ''}
            ${content}
            ${mode === 'non-pad' ? footerHtml : ''}
        </div>
    `).join('');

    const fullHtml = `<!DOCTYPE html>
        <html>
        <head>
            <title>Invoice</title>
            <style>
                @page {
                    size: A4;
                    margin: 0;
                }
                body {
                    margin: 0;
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                }
                @media print {
                    .no-print {
                        display: none !important;
                    }
                    .print-only {
                        display: block !important;
                    }
                }
                table tbody.client_info tr td {
                    padding: 0;
                }
                .header, .footer {
                    position: fixed;
                    left: 0;
                    right: 0;
                    background: white;
                    z-index: 10;
                }
                .watermark {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    opacity: 0.07;
                    width: 500px;
                    height: auto;
                    z-index: 0;
                }
                .header {
                    height: 90px;
                    padding: 20px 45px 20px 45px;
                    border-bottom: 4px solid green;
                    top: 0;
                }
                .footer {
                    height: 80px;
                    bottom: 0;
                    width: 100%;
                    padding: 0 35px;
                    box-sizing: border-box;
                    page-break-inside: avoid;
                    z-index: 10;
                    font-size: 13px;
                    border-top: 4px solid black;
                }
                .footer-content p {
                    margin: 0;
                }
                .content {
                    margin: 0;
                    padding: 120px 20px 100px 20px;
                    box-sizing: border-box;
                    min-height: calc(297mm - 180px);
                }
                .invoice-title {
                    text-align: center;
                    font-size: 25px;
                    font-weight: bold;
                    margin: 30px 0 20px 0;
                }
                .payment-note {
                    font-size: 14px;
                    text-align: center;
                    margin: 20px 0;
                }
                .appreciate {
                    font-size: 16px;
                    text-align: center;
                    margin: 10px 0;
                }
                .signature-area {
                    margin-top: 60px;
                    padding: 0 70px;
                    display: flex;
                    justify-content: space-between;
                }
                .logo {
                    height: 70px;
                    margin-right: 15px;
                }
                .company-name {
                    font-size: 40px;
                    font-weight: bold;
                    color: #00aeef;
                    flex: 1;
                }
                .header-content {
                    display: flex;
                    align-items: center;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    page-break-inside: auto;
                }
                thead {
                    display: table-header-group;
                }
                tfoot {
                    display: table-footer-group;
                }
                tr {
                    page-break-inside: avoid;
                    page-break-after: auto;
                }
                table, th, td {
                    border: 1px solid #ddd;

                }
                th, td {
                    padding: 8px;
                    text-align: left;
                }
                #summary_table,
                #summary_table th,
                #summary_table td {
                    border: none !important;
                }
                #summary_table h5 {
                    margin: 0;
                    padding: 2px 4px;
                    font-size: 12px;
                    font-weight: normal;
                }
                .page {
                    page-break-after: always;
                    position: relative;
                    height: 297mm;
                    width: 210mm;
                }

                .footer .border-green {
                    height: 4px;
                    background-color: green;
                    width: 100%;
                }
                .footer .border-black {
                    height: 4px;
                    background-color: black;
                    width: 100%;
                }
                      .table tbody tr:nth-child(even) {
                    background-color: #f2f2f2;
                }
                .table tbody tr:nth-child(odd) {
                    background-color: #ffffff;
                }
            </style>
        </head>
        <body>
            ${allPagesHtml}
        </body>
        </html>
    `;

    const printWindow = window.open('', '_blank');
    printWindow.document.open();
    printWindow.document.write(fullHtml);
    printWindow.document.close();

    printWindow.onload = () => {
        printWindow.focus();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    };
}

</script>
