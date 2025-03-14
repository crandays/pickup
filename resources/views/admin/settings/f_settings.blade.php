@extends('admin.layout.base')

@section('title', 'Site Settings ')

@section('content')

<div class="content-area py-1">
    <div class="container-fluid">
    	<div class="box box-block bg-white">
			<h5>Site Settings</h5>

            <form class="form-horizontal" action="{{ route('admin.settings.store') }}" method="POST" enctype="multipart/form-data" role="form">
            	{{csrf_field()}}

				<div class="form-group row">
					<label for="site_title" class="col-xs-2 col-form-label">Site Name</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('site_title', 'Tranxit')  }}" name="site_title" required id="site_title" placeholder="Site Name">
					</div>
				</div>

				<div class="form-group row">
					<label for="site_logo" class="col-xs-2 col-form-label">Site Logo</label>
					<div class="col-xs-10">
						@if(Setting::get('site_logo')!='')
	                    <img style="height: 90px; margin-bottom: 15px;" src="{{ Setting::get('site_logo', asset('logo-black.png')) }}">
	                    @endif
						<input type="file" accept="image/*" name="site_logo" class="dropify form-control-file" id="site_logo" aria-describedby="fileHelp">
					</div>
				</div>

                <div class="form-group row">
                    <label for="tax_percentage" class="col-xs-2 col-form-label">Copyright Content</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" value="{{ Setting::get('site_copyright', '&copy; '.date('Y').' Appoets') }}" name="site_copyright" id="site_copyright" placeholder="Site Copyright">
                    </div>
                </div>

				<div class="form-group row">
					<label for="f_u_url" class="col-xs-2 col-form-label">User App PlayStore Link</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_u_url', '')  }}" name="f_u_url"  id="f_u_url" placeholder="User App PlayStore Link">
					</div>
				</div>

				<div class="form-group row">
					<label for="f_p_url" class="col-xs-2 col-form-label">Driver App PlayStore Link</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_p_url', '')  }}" name="f_p_url"  id="f_p_url" placeholder="Driver App PlayStore Link">
					</div>
				</div>
                <h5>Contact Page Settings</h5></br>
                <div class="form-group row">
					<label for="Website Link" class="col-xs-2 col-form-label">User App PlayStore Link</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('site_link', '')  }}" name="site_link"  id="site_link" placeholder="Website Link">
					</div>
				</div>
                
                <div class="form-group row">
					<label for="Contact Message" class="col-xs-2 col-form-label">Contact Message</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('contact_message', '')  }}" name="contact_message"  id="contact_message" placeholder="Contact Message">
					</div>
				</div>

                <div class="form-group row">
					<label for="Contact City" class="col-xs-2 col-form-label">Contact City</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('contact_city', '')  }}" name="contact_city"  id="contact_city" placeholder="Contact City">
					</div>
				</div>
                
                <div class="form-group row">
					<label for="Contact Address" class="col-xs-2 col-form-label">Contact Address</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('contact_address', '')  }}" name="contact_address"  id="contact_address" placeholder="Contact Address">
					</div>
				</div>
                <div class="form-group row">
					<label for="Contact Email" class="col-xs-2 col-form-label">Contact Email</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('contact_email', '')  }}" name="contact_email"  id="contact_email" placeholder="Contact Email">
					</div>
				</div>
                <div class="form-group row">
					<label for="Contact Phone" class="col-xs-2 col-form-label">Contact Phone</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('contact_number', '')  }}" name="contact_number"  id="contact_number" placeholder="Contact Phone">
					</div>
				</div>
                <h5>Frontend Text Settings</h5></br>


                <div class="form-group row">
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text1', '')  }}" name="f_text1"  id="f_text1" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text2', '')  }}" name="f_text2"  id="f_text2" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text6', '')  }}" name="f_text6"  id="f_text6" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text7', '')  }}" name="f_text7"  id="f_text7" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text8', '')  }}" name="f_text8"  id="f_text8" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text9', '')  }}" name="f_text9"  id="f_text9" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text10', '')  }}" name="f_text10"  id="f_text10" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text11', '')  }}" name="f_text11"  id="f_text11" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text12', '')  }}" name="f_text12"  id="f_text12" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text13', '')  }}" name="f_text13"  id="f_text13" placeholder="Enter Text Here">
					</div>
				</div>
                
				<div class="form-group row">
					<label for="zipcode" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<button type="submit" class="btn btn-primary">Update Site Settings</button>
					</div>
				</div>

			</form>
		</div>
    </div>
</div>
@endsection
