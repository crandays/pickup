@extends('admin.layout.base')


@section('title', 'Loyality Point Gifts Purchases ')



@section('content')

    <div class="content-area py-1">

        <div class="container-fluid">

            <div class="box box-block bg-white">

                <h5 class="mb-1">
                    Loyality Point Gifts History
                </h5>


                <table class="table table-striped table-bordered dataTable" id="table-2">

                    <thead>

                    <tr>
                        <th>ID</th>
                        <th>User / Provider</th>
                        <th>Buyer</th>
                        <th>Gift</th>
                        <th>Points on Purchase</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>

                    </thead>
                    <tbody>
                    @foreach($requests as $index => $r)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if($r->buyer == 1)
                                    User
                                @else
                                    Provider
                                @endif
                            </td>
                            <td>
                                @if($r->buyer == 1)
                                    <a href="{{ url('admin/user/'.$r->user($r->buyer_id)->id.'/edit') }}">{{ $r->user
                                    ($r->buyer_id)->first_name
                                    }}</a>
                                @else

                                    <a href="{{ url('admin/provider/'.$r->provider($r->buyer_id)->id.'/edit') }}">{{ $r->provider($r->buyer_id)->first_name }}</a>

                                @endif
                            </td>
                            <td>
                                {{ $r->gift->title }}
                            </td>
                            <td>{{ $r->points_on_purchase }}</td>
                            <td>
                                @if($r->status == 1)
                                    <span class="tag tag-info">Ordered</span>
                                @elseif($r->status ==2)
                                    <span class="tag tag-success">Delivered</span>
                                @else
                                    <span class="tag tag-danger">Cancelled</span>
                                @endif
                            </td>
                            <td>
                                {{ $r->created_at }}
                            </td>
                            <td>
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown">Action<span class="caret"></span></button>
                                    <ul class="dropdown-menu py-0">
                                        <li>
                                            @if($r->status == 1)
                                            <form class="mb-0" action="{{ url('admin/loyality-point-gifts-purchases/'.$r->id.'/delivered') }}" method="POST">
                                                {{ csrf_field() }}
                                                <button class="btn btn-success w-100" onclick="return confirm('Are you sure?')"><i class="fa fa-shipping"></i>
                                                    Delivered
                                                </button>
                                            </form>
                                            @endif
                                            @if($r->status == 1)
                                            <form class="mb-0" action="{{ url('admin/loyality-point-gifts-purchases/'.$r->id.'/cancel') }}" method="POST">
                                                {{ csrf_field() }}
                                                <button class="btn btn-danger w-100" onclick="return confirm('Are you sure?')"><i class="fa fa-times"></i> Cancel
                                                </button>
                                            </form>
                                            @endif
                                        </li>
                                    </ul>
                                </div>
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
                        <th>Action</th>
                    </tr>

                    </tfoot>

                </table>

            </div>

        </div>

    </div>
    @include('admin.providers.modal')
@endsection
<script>


</script>
