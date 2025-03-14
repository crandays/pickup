@extends('admin.layout.auth')

@section('content')



<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">

<link rel="stylesheet" type="text/css" href="style.css">

  

<link href="../mainindex/css/fontawesome-all.min.css" rel="stylesheet">

<div class="sign-form">

    <div class="row" style="margin-top: 70px;">

        <div class="col-md-4 offset-md-4 px-3">

            <div class="d-flex justify-content-center h-100">

                <div class="card">

                    <div class="card-header">

                        <h3>Sign In (Admin)</h3>

                    </div>  

                    
                    <div class="card-body">

                        <form role="form" method="POST" action="{{ url('/admin/login') }}">

                        {{ csrf_field() }}

                            <div class="input-group form-group" style="margin-top: 50px;">

                                <div class="input-group-prepend">

                                    <span class="input-group-text"><i class="fas fa-user"></i></span>

                                </div>

                            <input type="email" name="email" required="true" class="form-control" id="email" placeholder="Email">

                                @if ($errors->has('email'))

                                    <span class="help-block" style="margin-left: 55px;color: red;">

                                        <strong>{{ $errors->first('email') }}</strong>

                                    </span>

                                @endif

                                

                            </div>

                            <div class="input-group form-group">

                                <div class="input-group-prepend">

                                    <span class="input-group-text"><i class="fas fa-key"></i></span>

                                </div>

                                <input type="password" name="password" required="true" class="form-control" id="password" placeholder="Password">

                                @if ($errors->has('password'))

                                    <span class="help-block" style="margin-left: 55px;color: red;">

                                        <strong>{{ $errors->first('password') }}</strong>

                                    </span>

                                @endif

                            </div>

                            <div class="row align-items-center remember">

                                 <input type="checkbox" name="remember"> Remember Me

                            </div>

                            <div class="form-group">

                                <input type="submit" value="Login" class="btn float-right login_btn">

                            </div>

                           

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>
  
</div>
        


@endsection

