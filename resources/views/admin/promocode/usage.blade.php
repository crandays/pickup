@extends('admin.layout.base')

@section('title', 'Promocodes ')

@section('content')

    <div class="content-area py-1">
        <div class="container-fluid">

            <div class="box box-block bg-white">
                <h5 class="mb-1">Promocodes Usage</h5>
                <table class="table table-striped table-bordered dataTable" id="table-2">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User Name</th>
                            <th>Promocode </th>
                            <th>Used Date</th>
                            <th>Status</th>
                            <th>Used Count</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($promocodes as $index => $promo)
                        <tr>
                            <td>{{$index + 1}}</td>
                            <td>{{$promo->user->first_name}} {{$promo->user->last_name}}</td>
                            <td>{{ $promo->promocode['promo_code'] }}</td>
                            <td>
                                {{date('d-m-Y',strtotime($promo->created_at))}}
                            </td>
                            <td>
                                <?php
                                $pro = \App\Promocode::where('id',$promo->promocode_id)->where('status','ADDED')->first();
                                $pro_count = \App\PromocodeUsage::where('promocode_id',$promo->promocode_id)->where('status','USED')->count();
                                ?>
                                @if($pro->max_count <= $pro_count)
                                    <span class="tag tag-danger">Expired</span>
                                @elseif(date("Y-m-d") <= $pro->expiration )
                                    <span class="tag tag-success">Valid</span>
                                @else
                                    <span class="tag tag-danger">Expiration</span>
                                @endif
                            </td>
                            <td>
                                {{ promo_used_count($promo->promocode['id']) }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                        <th>ID</th>
                            <th>User Name</th>
                            <th>Promocode </th>
                            <th>Expiration</th>
                            <th>Status</th>
                            <th>Used Count</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
@endsection
