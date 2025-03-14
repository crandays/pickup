@extends('admin.layout.base')

@section('title', 'Request History ')

@section('content')

    <div class="content-area py-1">
        <div class="container-fluid">
            <div class="box box-block bg-white">
                <h5 class="mb-1">User Ride Cancel Reasons</h5>
                @if(count($requests) != 0)
                    <table class="table table-striped table-bordered dataTable" id="table-ride-cancel">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Booking ID</th>
                            <th>User Name</th>
                            <th>Provider Name</th>
                            <th>Date &amp; Time</th>
                            <th>Status</th>
                            <th>Cancel Reason</th>
                            <th>Payment Mode</th>
                            <th>Payment Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($requests as $index => $request)
                            <tr>
                                <td>{{ $request->id }}</td>
                                <td>{{ $request->booking_id }}</td>
                                <td>
                                    @if($request->provider)
                                        {{ $request->user->first_name }} {{ $request->user->last_name }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($request->provider)
                                        {{ $request->provider->first_name }} {{ $request->provider->last_name }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    @if($request->created_at)
                                        <span class="text-muted">{{$request->created_at->diffForHumans()}}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $request->status }}</td>
                                <td>
                                    @if($request->cancel_reason != "")
                                        {{ $request->cancel_reason }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $request->payment_mode }}</td>
                                <td>
                                    @if($request->paid)
                                        Paid
                                    @else
                                        Not Paid
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>#</th>
                            <th>Booking ID</th>
                            <th>User Name</th>
                            <th>Provider Name</th>
                            <th>Date &amp; Time</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Payment Mode</th>
                            <th>Payment Status</th>
                        </tr>
                        </tfoot>
                    </table>
                @else
                    <h6 class="no-result">No results found</h6>
                @endif
            </div>
        </div>
    </div>
@endsection
