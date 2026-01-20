@extends('admin.layouts.master')
@section('title', 'Fund Transfer Statement')
@section('breadcrumb', 'Fund Transfer Statement')

@section('content')
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Fund Transfer Statement -
                        @if(request()->filled('from_date') && request()->filled('to_date'))

                            <span class="src-info">({{ $fromDate }} to {{ $toDate }})</span>
                        @else
                            <span class="src-info">(Last 30 days)</span>

                        @endif
                    </h1>
                    <form class="form-inline pull-right" method="GET">
                        <input type="date" name="from_date" value="{{ $fromDate ?? '' }}" class="form-control" required>
                        <input type="date" name="to_date" value="{{ $toDate ?? '' }}" class="form-control" required>
                        <button type="submit" class="btn btn-info">Filter</button>
                    </form>
                </div>


                <div class="block-fluid table-sorting clearfix">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th width="3%"><input type="checkbox" name="checkall"/></th>
                            <th>Date</th>
                            <th>Transaction ID</th>
                            <th>Description</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Balance</th>
                            <th>Source</th>
                         
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($statements as $s)
                        
                            <tr>
                                <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                                <td>{{ $s->posting_date }}</td>
                                <td>{{ $s->transaction_id }}</td>
                                <td>{{ $s->description }}</td>
                                <td>{{ number_format($s->debit, 2) }}</td>
                                <td>{{ number_format($s->credit, 2) }}</td>
                                <td>{{ number_format($s->balance, 2) }}</td>
                                <td>
                                    @if (property_exists($s, 'bank_info_id') && $s->bank_info_id)
                                        {{ \App\Models\BankInfo::find($s->bank_info_id)->bank_name ?? 'Bank' }}
                                    @else
                                        Cash
                                    @endif
                                </td>
                             

                                {{-- <td>@if (strtolower($s->deposit_type) === 'bank')
                                                    {{ $s->bank_name }}
                                                @elseif(strtolower($s->deposit_type) === 'cash')
                                                    Cash
                                                @else
                                                    {{ $s->deposit_type ?? 'N/A' }}
                                                @endif</td> --}}
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
