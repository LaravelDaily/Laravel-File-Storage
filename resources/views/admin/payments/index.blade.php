@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')

@section('content')
    <h3 class="page-title">@lang('quickadmin.payments.title')</h3>
    @can('payment_create')
    <p>
        
        
    </p>
    @endcan

    

    <div class="panel panel-default">
        <div class="panel-heading">
            @lang('quickadmin.qa_list')
        </div>

        <div class="panel-body table-responsive">
            <table class="table table-bordered table-striped {{ count($payments) > 0 ? 'datatable' : '' }} ">
                <thead>
                    <tr>
                        
                        <th>@lang('quickadmin.payments.fields.user')</th>
                        <th>@lang('quickadmin.payments.fields.role')</th>
                        <th>@lang('quickadmin.payments.fields.payment-amount')</th>
                        
                    </tr>
                </thead>
                
                <tbody>
                    @if (count($payments) > 0)
                        @foreach ($payments as $payment)
                            <tr data-entry-id="{{ $payment->id }}">
                                
                                <td field-key='user'>{{ $payment->user->email or '' }}</td>
                                <td field-key='role'>{{ $payment->role->title or '' }}</td>
                                <td field-key='payment_amount'>{{ $payment->payment_amount }}</td>
                                
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6">@lang('quickadmin.qa_no_entries_in_table')</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('javascript') 
    <script>
        
    </script>
@endsection