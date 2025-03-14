@extends('admin.layout.base')


@section('title', 'Loyality Point Gifts ')



@section('content')

    <div class="content-area py-1">

        <div class="container-fluid">

            <div class="box box-block bg-white">

                <h5 class="mb-1">

                    Loyality Point Gifts

                    @if(Setting::get('demo_mode', 0) == 1)

                        <span class="pull-right">(*personal information hidden in demo)</span>

                    @endif

                </h5>

                <a href="{{ route('admin.loyality-point-gifts.create') }}" style="margin-left: 1em;" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add New
                    Gift</a>

                <table class="table table-striped table-bordered dataTable" id="table-2">

                    <thead>

                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Image</th>
                        <th>Description</th>
                        <th>Price in Points</th>
                        <th>Status</th>
                        <th>Action</th>

                    </tr>

                    </thead>
                    <tbody>
                    @foreach($providers as $index => $provider)

                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $provider->title }}</td>
                            <td>
                                @if(isset($provider->image))
                                    <img style="height: 70px; margin-bottom: 15px; border-radius:2em;" src=" {{  URL::to('/') }}/storage/app/public/{{$provider->image}}">
                                @endif
                            </td>
                            <td>{{ $provider->description }}</td>
                            <td>{{ $provider->price_in_points }}</td>
                            <td>
                                @if($provider->status == 1)
                                    <label class="btn btn-block btn-primary">Active</label>
                                @else
                                    <label class="btn btn-block btn-warning">Deactive</label>
                                @endif
                            </td>
                            <td>
                                <div class="input-group-btn">
                                    <button type="button" class="btn btn-info btn-block dropdown-toggle" data-toggle="dropdown">Action<span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ url('admin/loyality-point-gifts/'.$provider->id.'/history') }}" class="btn btn-default"><i class="fa
                                            fa-search"></i>
                                                History</a>
                                        </li>
                                        <li>
                                            <a href="{{ url('admin/loyality-point-gifts/'.$provider->id.'/edit' ) }}" class="btn btn-default"><i class="fa fa-pencil"></i>
                                                Edit</a>
                                        </li>
                                        <li>
                                            <form action="{{ url('admin/loyality-point-gifts/'.$provider->id) }}" method="POST">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button class="btn btn-default look-a-like" onclick="return confirm('Are you sure?')"><i class="fa fa-trash"></i> Delete
                                                </button>
                                            </form>
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
                        <th>Title</th>
                        <th>Image</th>
                        <th>Description</th>
                        <th>Price in Points</th>
                        <th>Status</th>
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

    function checkForm(form) {
        form.bttn_submit.disabled = true;
        return true;
    }

    function updateWallet(balance, id) {

        $('#wallet_balance').text(balance);
        $('#id').val(id);
        $('#updateWallet').modal('show');
    }
</script>
