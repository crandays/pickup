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
					<label for="site_icon" class="col-xs-2 col-form-label">Site Icon</label>
					<div class="col-xs-10">
						@if(Setting::get('site_icon')!='')
	                    <img style="height: 90px; margin-bottom: 15px;" src="{{ Setting::get('site_icon') }}">
	                    @endif
						<input type="file" accept="image/*" name="site_icon" class="dropify form-control-file" id="site_icon" aria-describedby="fileHelp">
					</div>
				</div>

				<div class="form-group row">
					<label for="provider_select_timeout" class="col-xs-2 col-form-label">Provider Accept Timeout</label>
					<div class="col-xs-10">
						<input class="form-control" type="number" value="{{ Setting::get('provider_select_timeout', '60')  }}" name="provider_select_timeout" required id="provider_select_timeout" placeholder="Provider Timout">
					</div>
				</div>

				<div class="form-group row">
					<label for="provider_search_radius" class="col-xs-2 col-form-label">Provider Search Radius</label>
					<div class="col-xs-10">
						<input class="form-control" type="number" value="{{ Setting::get('provider_search_radius', '100')  }}" name="provider_search_radius" required id="provider_search_radius" placeholder="Provider Search Radius">
					</div>
				</div>

				<div class="form-group row">
					<label for="sos_number" class="col-xs-2 col-form-label">SOS Number</label>
					<div class="col-xs-10">
						<input class="form-control" type="number" value="{{ Setting::get('sos_number', '911')  }}" name="sos_number" required id="sos_number" placeholder="SOS Number">
					</div>
				</div>

				<div class="form-group row">
                    <label for="map_key" class="col-xs-2 col-form-label">Google Map Api Key</label>
                    <div class="col-xs-10">
                        <input class="form-control" type="text" value="{{ Setting::get('map_key') }}" name="map_key" id="map_key" placeholder="Site Copyright">
                    </div>
                </div>

				<div class="form-group row">
					<label for="android_user_fcm_key" class="col-xs-2 col-form-label">User App FCM Key</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('android_user_fcm_key', '')  }}" name="android_user_fcm_key" required id="android_user_fcm_key" placeholder="User App FCM Key">
					</div>
				</div>

				<div class="form-group row">
					<label for="android_user_driver_key" class="col-xs-2 col-form-label">Driver App FCM Key</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('android_user_driver_key', '')  }}" name="android_user_driver_key" required id="android_user_driver_key" placeholder="Driver App FCM Key">
					</div>
				</div>

				<div class="form-group row">
					<label for="verification" class="col-xs-2 col-form-label">Phone Verification In App</label>
					<div class="col-xs-10">
						<select class="form-control" id="verification" name="verification">
							<option value="1" @if(Setting::get('verification', 1) == 1) selected @endif>Enable</option>
							<option value="0" @if(Setting::get('verification', 0) == 0) selected @endif>Disable</option>
						</select>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-xs-2 col-form-label">
						<label for="UPI_key" class="col-form-label">
							App Maintenance
						</label>
					</div>
					<div class="col-xs-10">
						<input @if(Setting::get('appmaintain') == 1) checked  @endif  name="appmaintain" id="appmaintain_check" onchange="appmaintainselect()" type="checkbox" class="js-switch" data-color="#43b968">
					</div>
				</div>

				</br></br><h5>Other Settings</h5></br></br>

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

				</br></br><h5>Contact Page Settings</h5></br></br>

				<div class="form-group row">
					<label for="Website Link" class="col-xs-2 col-form-label">Website Link</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('site_link', '')  }}" name="site_link"  id="site_link" placeholder="Website Link">
					</div>
				</div>

				<div class="form-group row">
					<label for="Website Link" class="col-xs-2 col-form-label">FaceBook Page Link</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_f_link', '')  }}" name="f_f_link"  id="f_f_link" placeholder="FaceBook Page Link">
					</div>
				</div>


				<div class="form-group row">
					<label for="Twitter Link" class="col-xs-2 col-form-label">Twitter Link</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_t_link', '')  }}" name="f_t_link"  id="f_t_link" placeholder="Twitter Link">
					</div>
				</div>

				<div class="form-group row">
					<label for="Website Link" class="col-xs-2 col-form-label">linkedin Link</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_l_link', '')  }}" name="f_l_link"  id="f_l_link" placeholder="linkedin Link">
					</div>
				</div>

                <div class="form-group row">
					<label for="Website Link" class="col-xs-2 col-form-label">Instagram Link</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_i_link', '')  }}" name="f_i_link"  id="f_i_link" placeholder="Instagram Link">
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
				<div class="form-group row">
					<label for="Loyality Point Per Ride" class="col-xs-2 col-form-label">Loyality Point Per Ride</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('loyality_point_per_ride', '')  }}" name="loyality_point_per_ride"  id="loyality_point_per_ride" placeholder="Loyality Point Per Ride">
					</div>
				</div>
			
                <h5>Frontend Text Settings</h5></br>


                <div class="form-group row">
					<label for="Contact Phone" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text1', '')  }}" name="f_text1"  id="f_text1" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<label for="Contact Phone" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text2', '')  }}" name="f_text2"  id="f_text2" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<label for="Contact Phone" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text6', '')  }}" name="f_text6"  id="f_text6" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<label for="Contact Phone" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text7', '')  }}" name="f_text7"  id="f_text7" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<label for="Contact Phone" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text8', '')  }}" name="f_text8"  id="f_text8" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<label for="Contact Phone" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text9', '')  }}" name="f_text9"  id="f_text9" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<label for="Contact Phone" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text10', '')  }}" name="f_text10"  id="f_text10" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<label for="Contact Phone" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text11', '')  }}" name="f_text11"  id="f_text11" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<label for="Contact Phone" class="col-xs-2 col-form-label"></label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="{{ Setting::get('f_text12', '')  }}" name="f_text12"  id="f_text12" placeholder="Enter Text Here">
					</div>
				</div>
                <div class="form-group row">
					<label for="Contact Phone" class="col-xs-2 col-form-label"></label>
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
