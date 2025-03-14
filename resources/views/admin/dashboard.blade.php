@extends('admin.layout.base')

@section('title', 'Dashboard ')

@section('styles')
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link href="../mainindex/css/fontawesome-all.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="content-area py-1">
        <div class="container-fluid">
            <div class="row row-md">
                <div class="col-lg-3 col-md-6 col-xs-12">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{$rides->count()}}</h3>
                            <p>Total Rides</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="/admin/requests" class="small-box-footer">History <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-xs-12">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{currency($revenue)}}</h3>
                            <p>Total Revenue</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="/admin/statement" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-xs-12">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{$service}}</h3>
                            <p>All Services</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="/admin/service" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-xs-12">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{$user_cancelled}}</h3>
                            <p>User Cancelled Rides</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{ url('admin/user-cancel-rides') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

            </div>

            <div class="row row-md mb-2">
                <div class="col-md-12">
                    <div class="box box-block bg-white">

                        <h5 class="mb-1">
                            Loyality Point Gifts History
                        </h5>


                        <table class="table table-striped table-bordered dataTable" >

                            <thead>

                            <tr>
                                <th>ID</th>
                                <th>User / Provider</th>
                                <th>Buyer</th>
                                <th>Gift</th>
                                <th>Points on Purchase</th>
                                <th>Status</th>
                                <th>Created At</th>

                            </tr>

                            </thead>
                            <tbody>
                            @foreach($loyality_gift_purchases as $p_index => $p)
                                <tr>
                                    <td>{{ $p_index + 1 }}</td>
                                    <td>
                                        @if($p->buyer == 1)
                                            User
                                        @else
                                            Provider
                                        @endif
                                    </td>
                                    <td>
                                        @if($p->buyer == 1)
                                            <a href="{{ url('admin/user/'.$p->user($p->buyer_id)->id.'/edit') }}">{{ $p->user
                                    ($p->buyer_id)->first_name
                                    }}</a>
                                        @else

                                            <a href="{{ url('admin/provider/'.$p->provider($p->buyer_id)->id.'/edit') }}">{{ $p->provider($p->buyer_id)->first_name }}</a>

                                        @endif
                                    </td>
                                    <td>
                                        {{ $p->gift->title }}
                                    </td>
                                    <td>{{ $p->points_on_purchase }}</td>
                                    <td>
                                        @if($p->status == 1)
                                            <span class="tag tag-info">Ordered</span>
                                        @elseif($p->status ==2)
                                            <span class="tag tag-success">Delivered</span>
                                        @else
                                            <span class="tag tag-danger">Cancelled</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $p->created_at }}
                                    </td>

                                </tr>
                            @endforeach

                            </tbody>

                            <tfoot>

                            <tr>
                                <th>ID</th>
                                <th>User / Provider</th>
                                <th>Buyer</th>
                                <th>Gift</th>
                                <th>Points on Purchase</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>

                            </tfoot>

                        </table>

                    </div>


                </div>
            </div>
            <div class="row row-md mb-2">
                <div class="col-md-12">
                    <div class=" bg-white" style="box-shadow:none;">
                        <div class="box-block clearfix">
                            <h5 class="float-xs-left">Recent Rides</h5>
                        </div>
                        <table class="table mb-md-0 table " id="table-2" style="width:100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Driver</th>
                                <th>Ride Details</th>
                                <th>Date &amp; Time</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $diff = ['-success', '-info', '-warning', '-danger']; ?>

                            @foreach($rides as $index => $ride)
                                <tr>
                                    <th scope="row">{{$index + 1}}</th>

                                    <td>{{$ride->user['first_name']}} {{$ride->user['last_name']}}</td>
                                    <td>
                                        {{@$ride->provider->first_name}}  {{@$ride->provider->last_name}}
                                    </td>
                                    <td>
                                        @if($ride->status != "CANCELLED")
                                            <a class="text-primary" href="{{route('admin.requests.show',$ride->id)}}"><span class="underline">Ride Details</span></a>
                                        @else
                                            <span>No Details Found </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-muted">{{$ride->created_at->diffForHumans()}}</span>
                                    </td>
                                    <td>
                                        {{ currency($ride->payment['total'])}}
                                    </td>
                                    <td>
                                        @if($ride->status == "COMPLETED")
                                            <span class="tag tag-success">{{$ride->status}}</span>
                                        @elseif($ride->status == "CANCELLED")
                                            <span class="tag tag-danger">{{$ride->status}}</span>
                                        @else
                                            <span class="tag tag-info">{{$ride->status}}</span>
                                        @endif
                                    </td>
                                </tr>
                                <?php if ($index == 10) break; ?>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

@endsection
