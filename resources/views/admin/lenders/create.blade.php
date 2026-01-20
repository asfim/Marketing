@extends('admin.layouts.master')
@section('title', 'Add Lender')

@section('content')
    <div class="workplace">
        <div class="container">
            <h4>Add New Lender</h4>

            <form action="{{ route('admin.lenders.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <label>Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                        @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-12 mt-3">
                        <label>Address</label>
                        <textarea name="address" class="form-control" style="height: 150px; width: 100%">{{ old('address') }}</textarea>
                        @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div style="margin-top: 10px">
                    <button class="btn btn-primary">Save Lender</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('page-script')
    <script>
        $(document).ready(function () {
            // No depositType/bank select in this form anymore
        });
    </script>
@endsection
