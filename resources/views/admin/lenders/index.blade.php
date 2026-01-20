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
            <h4>Lenders</h4>
            <a href="{{ route('admin.lenders.create') }}" class="btn btn-primary">Add Lender</a>
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

        {{-- Lender Table --}}
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
                        <th>Total Lent</th>
                        <th>Total Received</th>
                        <th>Remaining Amount</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($lenders as $key => $lender)
                        @php
                            $credit = DB::table('lender_statements')->where('lender_id', $lender->id)->sum('credit');
                            $debit = DB::table('lender_statements')->where('lender_id', $lender->id)->sum('debit');
                        @endphp
                        <tr>
                            <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $lender->name }}</td>
                            <td>{{ $lender->phone }}</td>
                            <td>{{ $lender->address }}</td>
                            <td>৳ {{ number_format($debit, 2) }}</td>
                            <td>৳ {{ number_format($credit, 2) }}</td>
                            <td>৳ {{ number_format( $debit- $credit, 2) }}</td>
                            <td class="hidden-print">
                                <!-- Edit Button -->
                                <button class="btn btn-sm btn-outline-secondary edit-btn"
                                        data-id="{{ $lender->id }}"
                                        data-name="{{ $lender->name }}"
                                        data-phone="{{ $lender->phone }}"
                                        data-address="{{ $lender->address }}"
                                        title="Edit Lender">
                                    <span class="fa fa-edit"></span>
                                </button>

                                <!-- View Button -->
                                <a href="{{ route('admin.lenders.show', $lender->id) }}" title="View Lender">
                                    <span class="fa fa-eye"></span>
                                </a>

                                <!-- Delete Button -->
                                <button class="btn btn-sm btn-outline-danger delete-btn"
                                        data-id="{{ $lender->id }}"
                                        title="Delete Lender">
                                    <span class="fa fa-trash"></span>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No lenders found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  @if(auth()->user()->hasRole(['super-admin']))
    <!-- Edit Lender Modal -->
    <div class="modal fade" id="editLenderModal" tabindex="-1" role="dialog" aria-labelledby="editLenderModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="editLenderForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header" style="padding: 14px 20px">
                        <h5 class="modal-title">Edit Lender</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -21px">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editLenderId">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" id="editLenderName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" id="editLenderPhone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" id="editLenderAddress" class="form-control"></textarea>
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
  @if(auth()->user()->hasRole(['super-admin']))
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteLenderModal" tabindex="-1" role="dialog" aria-labelledby="deleteLenderModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="deleteLenderForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Lender</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this lender?
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
                $('#lenderTable tbody tr').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Edit Button Click
            $('.edit-btn').on('click', function () {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let phone = $(this).data('phone');
                let address = $(this).data('address');

                $('#editLenderId').val(id);
                $('#editLenderName').val(name);
                $('#editLenderPhone').val(phone);
                $('#editLenderAddress').val(address);

                $('#editLenderForm').attr('action', '{{ url('lender/update') }}/' + id);

                $('#editLenderModal').modal('show');
            });

            // Delete Button Click
            $('.delete-btn').on('click', function () {
                let id = $(this).data('id');
                $('#deleteLenderForm').attr('action', '{{ url('lender/delete') }}/' + id);
                $('#deleteLenderModal').modal('show');
            });
        });
    </script>
@endsection
