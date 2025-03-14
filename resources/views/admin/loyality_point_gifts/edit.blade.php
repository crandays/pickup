@extends('admin.layout.base')

@section('title', 'Update Loyality Point Gifts ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
        <div class="box box-block bg-white">
            <a href="{{ route('admin.loyality-point-gifts.index') }}" class="btn btn-default pull-right"><i class="fa fa-angle-left"></i> Back</a>

            <h5 style="margin-bottom: 2em;">Update Loyality Point Gift</h5>

            <form class="form-horizontal" action="{{route('admin.loyality-point-gifts.update', $provider->id )}}" method="POST" enctype="multipart/form-data" role="form">
                {{csrf_field()}}
                <input type="hidden" name="_method" value="PATCH">

                <div class="form-group row">
                    <label for="first_name" class="col-xs-12 col-form-label">Title</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" value="{{ old('title',$provider->title) }}" name="title" required id="title" placeholder="Title">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="first_name" class="col-xs-12 col-form-label">Price in Points</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="number" value="{{ old('price_in_points',$provider->price_in_points) }}" name="price_in_points" required id="price_in_points"
                               placeholder="price in points">
                    </div>
                </div>
                <div class="form-group row">

                    <label for="picture" class="col-xs-12 col-form-label">Image</label>
                    <div class="col-xs-10">

                        <input type="file" accept="image/*" name="image" class="dropify form-control-file" id="s" aria-describedby="fileHelp">
                    </div>
                    <div class="col-xs-2">
                        @if(isset($provider->image))
                            <img style="height: 90px; margin-bottom: 15px; border-radius:2em;" src="{{  URL::to('/') }}/storage/app/public/{{$provider->image}}">
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="mobile" class="col-xs-12 col-form-label">Description</label>
                    <div class="col-xs-10">
                        <textarea class="form-control" name="description" required id="description" placeholder="description">{{ old('description',$provider->description) }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-xs-2 col-form-label">
                        <label for="UPI_key" class="col-form-label">
                            Status
                        </label>
                    </div>
                    <div class="col-xs-10">
                        <input {{ (old('status',$provider->status) == 1? "checked":"") }} value="1" name="status" id="status"
                               type="checkbox" class="js-switch" data-color="#43b968">
                    </div>
                </div>




                <div class="form-group row">
                    <label for="zipcode" class="col-xs-2 col-form-label"></label>
                    <div class="col-xs-10">
                        <button type="submit" class="btn btn-primary">Update Loyality Point Gifts</button>
                        <a href="{{route('admin.loyality-point-gifts.index')}}" class="btn btn-default">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
