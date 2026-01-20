@extends('admin.layouts.master')
@section('title', 'Investment Report')
@section('breadcrumb', 'Investment Report')
@section('content')
    <div class="workplace">

        <div class="row">
            <div class="col-md-12">

                <div class="head clearfix">
                    <div class="col-md-4">
                        <div class="isw-documents"></div>
                        <h3>Investment Report
                            <span class="src-info">

                            </span>

                        </h3>

                    </div>

                    <div class="col-md-8 search_box" style="margin-top: 4px;">
                        <form action="" class="form-horizontal">
                            <div class="" align="right">
                                <div class="col-md-4">
                                    <select name="type" id="type" class="form-control">
                                        <option value="">Search with a type</option>
                                        <option value="cash" {{ request('type')=='cash'?'selected':'' }}>Cash</option>
                                        <option value="bank" {{ request('type')=='bank'?'selected':'' }}>Bank</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="search_text" id="search_name" value="{{ request('search_text')??'' }}" class="form-control" placeholder="Enter Search Text" />
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" name="date_range" id="date_range" value="{{ request('date_range')??'' }}"
                                               class="form-control" placeholder="Date Range" autocomplete="off"/>
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
                    <h5 id="date_info"></h5>
                    <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Account Name/AC.no</th>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($total_investment=0)
                        @if(request('type') != 'cash' )
                            @foreach($bank_investments as $investment)
                                <tr>
                                    <td>{{ date('d-M-Y',  strtotime($investment->ref_date)) }}</td>
                                    <td>{{ 'Bank: '.$investment->bank_info->short_name.', A/C no: '.$investment->bank_info->account_no }}</td>
                                    <td>{{ $investment->description }}</td>
                                    <td>{{ number_format($investment->credit,2) }}</td>
                                </tr>
                                @php($total_investment+=$investment->credit)
                            @endforeach
                        @endif
                        @if(request('type') != 'bank')
                            @foreach($cash_investments as $investment)
                                <tr>
                                    <td>{{ date('d-M-Y',  strtotime($investment->ref_date)) }}</td>
                                    <td>Cash</td>
                                    <td>{{ $investment->description }}</td>
                                    <td>{{ number_format($investment->credit,2) }}</td>
                                </tr>
                                @php($total_investment+=$investment->credit)
                            @endforeach
                        @endif
                        </tbody>
                        <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><b>Total:</b></td>
                            <td><b>{{ number_format($total_investment,2) }}</b></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>
@endsection

@section('page-script')
    <script>

        $(document).ready(function() {
            $('#btn_search').on('click',function(){
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                if(to_date < from_date)
                {
                    alert('The To date is less then from date');
                    return false;
                }
            });

            $("#search_name").autocomplete({
                source : '{!!URL::route('autoComplete',['table_name' => 'gen_incomes'])!!}',
                minLenght:1,
                autoFocus:true,

            });
        } );
    </script>
@endsection

