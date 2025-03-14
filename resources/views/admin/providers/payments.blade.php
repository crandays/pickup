@extends('admin.layout.base')
<?php $i=0;?>

@section('title', 'Providers ')



@section('content')

<div class="content-area py-1">

    <div class="container-fluid">

        <div class="box box-block bg-white">

            <h5 class="mb-1">

                Deposit Provider Money History

                @if(Setting::get('demo_mode', 0) == 1)

                <span class="pull-right">(*personal information hidden in demo)</span>

                @endif

            </h5>

            <table class="table table-striped table-bordered dataTable" id="table-2">

                <thead>

                    <tr>
					<th>ID</th>
                        <th>reports</th>
                    </tr>

                </thead>

                <tbody>

                @foreach($payments as $payment)

                    <tr>
                        <td>{{++$i}}</td>
                        @if($payment['type']=="discount")
                            <td>+{{ $payment['amount'] }} DA coupon discount id of ride {{$payment['booking_id']}} at {{$payment['created_at']}}</td>
                        @endif
						@if($payment['type']=="deposit")
							<td>+{{ $payment['amount'] }} DA recharged at {{$payment['created_at']}}</td>
						@endif
						@if($payment['type']=="deduction")
							<td>-{{ $payment['amount'] }} DA fee id of ride {{$payment['booking_id']}} at {{$payment['created_at']}}</td>
						@endif


                    </tr>

                @endforeach

                </tbody>


            </table>

        </div>

    </div>

</div>
@include('admin.providers.modal')
@endsection
<script>
function updateWallet(balance,id)
{

	$('#wallet_balance').text(balance);
	$('#id').val(id);
	$('#updateWallet').modal('show');
}
</script>
