@extends('admin.layouts.master')

@section('content')
    <div class="container">

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
            @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif


        {{-- Heading and Add Button --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Project</h4>
            <a href="{{ route('admin.investments.create') }}" class="btn btn-primary">Add Project</a>
        </div>



        {{-- Header: Title + Search aligned on same row --}}
{{--        <div class="mb-3" style="display: flex; justify-content: space-between; align-items: center;">--}}
{{--            <button class="btn btn-outline-secondary btn-sm" disabled>--}}
{{--                <i class="fa fa-download"></i> Export--}}
{{--            </button>--}}

{{--            <div style="display: flex; align-items: center;">--}}
{{--                <label for="searchInput" style="margin-right: 8px; font-weight: bold;">Search</label>--}}
{{--                <input type="text" id="searchInput" placeholder="Search by name, phone or address..."--}}
{{--                       style="width: 250px; padding: 5px 10px; border: 1px solid #ccc; border-radius: 4px;">--}}
{{--            </div>--}}
{{--        </div>--}}


        {{-- Investment Table --}}
        <div class="card">
            <div class="block-fluid table-sorting clearfix">
                <table cellpadding="0" cellspacing="0" width="100%" class="table" id="datatable">
                    <thead>
                    <tr>
                        <th width="3%"><input type="checkbox" name="checkall"/></th>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Total Investment</th>
                        <th>Total Returned</th>
                        <th>Remaining Amount</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($investors as $key => $investor)
                        @php
                            $credit = DB::table('investment_statements')->where('investor_id', $investor->id)->sum('credit');
                            $debit = DB::table('investment_statements')->where('investor_id', $investor->id)->sum('debit');
                        @endphp
                        <tr>
                            <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $investor->name }}</td>
                            <td>{{ $investor->phone }}</td>
                            <td>{{ $investor->address }}</td>
                            <td>৳ {{ number_format($debit, 2) }}</td>
                            <td>৳ {{ number_format($credit, 2) }}</td>
                            <td>৳ {{ number_format( $debit- $credit, 2) }}</td>
                            <td class="hidden-print">
                                <!-- Edit Button -->
                                <button class="btn btn-sm btn-outline-secondary edit-btn"
                                        data-id="{{ $investor->id }}"
                                        data-name="{{ $investor->name }}"
                                        data-phone="{{ $investor->phone }}"
                                        data-address="{{ $investor->address }}"
                                        title="Edit Investor">
                                    <span class="fa fa-edit"></span>
                                </button>

                                <!-- View Button -->
                                <a href="{{ route('admin.investments.show', $investor->id) }}" title="View Investor">
                                    <span class="fa fa-eye"></span>
                                </a>

                                <!-- Delete Button -->
                                <button class="btn btn-sm btn-outline-danger delete-btn"
                                        data-id="{{ $investor->id }}"
                                        title="Delete Investor">
                                    <span class="fa fa-trash"></span>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No investors found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

      @if(auth()->user()->hasRole(['super-admin']))
    <!-- Edit Investment Modal -->
    <div class="modal fade" id="editInvestorModal" tabindex="-1" role="dialog" aria-labelledby="editInvestorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="editInvestorForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header" style="padding: 14px 20px">
                        <h5 class="modal-title">Edit Investor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -21px">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editInvestorId">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" id="editInvestorName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" id="editInvestorPhone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" id="editInvestorAddress" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
    <!-- Delete Confirmation Modal -->
      @if(auth()->user()->hasRole(['super-admin']))
    <div class="modal fade" id="deleteInvestorModal" tabindex="-1" role="dialog" aria-labelledby="deleteInvestorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="deleteInvestorForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Investor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this investor?
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
@endif
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            $('#searchInput').on('keyup', function () {
                let value = $(this).val().toLowerCase();
                $('#investorTable tbody tr').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            // Search
            $('#searchInput').on('keyup', function () {
                let value = $(this).val().toLowerCase();
                $('#investorTable tbody tr').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Edit Button Click
            $('.edit-btn').on('click', function () {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let phone = $(this).data('phone');
                let address = $(this).data('address');

                $('#editInvestorId').val(id);
                $('#editInvestorName').val(name);
                $('#editInvestorPhone').val(phone);
                $('#editInvestorAddress').val(address);

                $('#editInvestorForm').attr('action', '{{ url('investment/update') }}/' + id);


                $('#editInvestorModal').modal('show');
            });

            // Delete Button Click
            $('.delete-btn').on('click', function () {
                let id = $(this).data('id');
                $('#deleteInvestorForm').attr('action', '{{ url('investment/delete') }}/' + id);
                $('#deleteInvestorModal').modal('show');
            });
        });
    </script>

@endsection
