@extends('admin.layout.base')


@section('title', 'Loyality Point Gifts History ')



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
                        <th>Created At</th>
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
                                {{ $r->created_at }}
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
                        <th>Created At</th>
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
