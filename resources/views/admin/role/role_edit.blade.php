@extends('admin.layouts.master')
@section('title', 'Edit Role')
@section('breadcrumb', 'Edit Role')
@section('content')
    <style>
        .permission-section-title {
            color: #1e293b;
            font-weight: 700;
            font-size: 15px;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .permission-section-title i {
            color: #3b82f6;
            font-size: 15px;
        }

        .badge {
            background: #3b82f6;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 15px;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .permission-container {
            max-height: 450px;
            overflow-y: auto;
            padding-right: 8px;
            margin-right: -8px;
            font-size: 15px;
        }

        /* Custom scrollbar */
        .permission-container::-webkit-scrollbar {
            width: 6px;
        }

        .permission-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        .permission-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .permission-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Flexbox Grid Layout */
        .permissions-grid {
            display: flex;
            flex-direction: column;
            gap: 12px;
            font-size: 15px;
        }

        .permission-row {
            display: flex;
            gap: 20px;
            width: 100%;
            font-size: 15px;
        }

        .permission-item {
            display: flex;
            align-items: center;
            flex: 1;
            min-width: 0;
            font-size: 15px;
        }

        .permission-checkbox {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            cursor: pointer;
            accent-color: #3b82f6;
            border-radius: 4px;
            flex-shrink: 0;
            transition: all 0.2s ease;
            border: 2px solid #cbd5e1;
            font-size: 15px;
        }

        .permission-checkbox:hover {
            transform: scale(1.1);
            border-color: #3b82f6;
        }

        .permission-checkbox:checked {
            accent-color: #10b981;
            border-color: #10b981;
        }

        .permission-label {
            font-weight: 500;
            color: #334155;
            cursor: pointer;
            font-size: 15px;
            transition: all 0.2s ease;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex-grow: 1;
        }

        .permission-checkbox:checked+.permission-label {
            color: #10b981;
            font-weight: 600;
        }

        .permission-label:hover {
            color: #3b82f6;
        }

        /* Responsive: Single column on mobile */
        @media (max-width: 768px) {
            .permission-row {
                flex-direction: column;
                gap: 8px;
            }

            .permission-item {
                width: 100%;
            }
        }
    </style>
    <div class="workplace">
        <div class="row">
            <div class="col-md-12">
                <div class="head clearfix">
                    <div class="isw-documents"></div>
                    <h1>Edit Role</h1>
                </div>
                <div class="block-fluid">
                    <form action="{{ route('role.update', $role_data->id) }}" method="post" id="role_form"
                        class="form-horizontal">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="row-form clearfix">
                            <div class="col-md-6">
                                <div class="row-form clearfix">
                                    <div class="col-md-6">
                                        <label>Role Name</label>
                                        <input type="text" value="{{ $role_data->name }}" name="name" id="name"
                                            class="form-control" required />
                                    </div>
                                    <div class="col-md-6">
                                        <label>Display Name</label>
                                        <input type="text" value="{{ $role_data->display_name }}" name="display_name"
                                            id="display_name" class="form-control" />
                                    </div>
                                    <div class="col-md-12">
                                        <label>Description</label>
                                        <textarea name="description" id="description" cols="30" rows="3" class="form-control">{{ $role_data->description }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4 class="permission-section-title">
                                    <i class="fas fa-shield-alt"></i> Permissions
                                </h4>
                                <div class="permission-container">
                                    <div class="permissions-grid">
                                        @foreach ($permissions->sortBy('display_name')->chunk(2) as $chunk)
                                            <div class="permission-row">
                                                @foreach ($chunk as $permission)
                                                    <div class="permission-item">
                                                        <input type="checkbox" name="permission[]"
                                                            value="{{ $permission->id }}" id="perm_{{ $permission->id }}"
                                                            class="permission-checkbox"
                                                            {{ in_array($permission->id, $role_permissions) ? 'checked' : '' }} />
                                                        <label for="perm_{{ $permission->id }}" class="permission-label">
                                                            {{ $permission->display_name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="footer" style="text-align: center;">
                                <button type="submit" class="btn btn-primary">Submit</button>
                                <button type="reset" class="btn btn-danger">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>


        <div class="dr"><span></span></div>

    </div>
@endsection
