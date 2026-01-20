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
            <h4>Borrowers</h4>
            <a href="{{ route('admin.borrowers.create') }}" class="btn btn-primary">Add Borrower</a>
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


        {{-- Borrower Table --}}
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
                        <th>Total Borrowed</th>
                        <th>Total Repaid</th>
                        <th>Remaining Amount</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($borrowers as $key => $borrower)
                        @php
                            $credit = DB::table('borrower_statements')->where('borrower_id', $borrower->id)->sum('credit');
                            $debit = DB::table('borrower_statements')->where('borrower_id', $borrower->id)->sum('debit');
                        @endphp
                        <tr>
                            <td><input type="checkbox" class="row-checkbox"></td> {{-- NEW --}}
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $borrower->name }}</td>
                            <td>{{ $borrower->phone }}</td>
                            <td>{{ $borrower->address }}</td>
                            <td>৳ {{ number_format($credit, 2) }}</td>
                            <td>৳ {{ number_format($debit, 2) }}</td>
                            <td>৳ {{ number_format($credit - $debit, 2) }}</td>
                            <td class="hidden-print">
                                <!-- Edit Button -->
                                <button class="btn btn-sm btn-outline-secondary edit-btn"
                                        data-id="{{ $borrower->id }}"
                                        data-name="{{ $borrower->name }}"
                                        data-phone="{{ $borrower->phone }}"
                                        data-address="{{ $borrower->address }}"
                                        title="Edit Borrower">
                                    <span class="fa fa-edit"></span>
                                </button>

                                <!-- View Button -->
                                <a href="{{ route('admin.borrowers.show', $borrower->id) }}" title="View Borrower">
                                    <span class="fa fa-eye"></span>
                                </a>

                                <!-- Delete Button -->
                                <button class="btn btn-sm btn-outline-danger delete-btn"
                                        data-id="{{ $borrower->id }}"
                                        title="Delete Borrower">
                                    <span class="fa fa-trash"></span>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No borrowers found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Edit Borrower Modal -->
      @if(auth()->user()->hasRole(['super-admin']))
    <div class="modal fade" id="editBorrowerModal" tabindex="-1" role="dialog" aria-labelledby="editBorrowerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
           
            <form id="editBorrowerForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header" style="padding: 14px 20px">
                        <h5 class="modal-title">Edit Borrower</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-top: -21px">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editBorrowerId">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" id="editBorrowerName" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" name="phone" id="editBorrowerPhone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" id="editBorrowerAddress" class="form-control"></textarea>
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
    <div class="modal fade" id="deleteBorrowerModal" tabindex="-1" role="dialog" aria-labelledby="deleteBorrowerModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="deleteBorrowerForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Borrower</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this borrower?
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
                $('#borrowerTable tbody tr').filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            // Edit Button Click
            $('.edit-btn').on('click', function () {
                let id = $(this).data('id');
                let name = $(this).data('name');
                let phone = $(this).data('phone');
                let address = $(this).data('address');

                $('#editBorrowerId').val(id);
                $('#editBorrowerName').val(name);
                $('#editBorrowerPhone').val(phone);
                $('#editBorrowerAddress').val(address);

                $('#editBorrowerForm').attr('action', '{{ url('borrower/update') }}/' + id);

                $('#editBorrowerModal').modal('show');
            });

            // Delete Button Click
            $('.delete-btn').on('click', function () {
                let id = $(this).data('id');
                $('#deleteBorrowerForm').attr('action', '{{ url('borrower/delete') }}/' + id);
                $('#deleteBorrowerModal').modal('show');
            });
        });
    </script>
@endsection
