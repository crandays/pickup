<head>

<meta charset="utf-8">

<meta name="description" content="VTC app for urban transport">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>{{Setting::get('site_title', '')}}</title>

<link rel="shortcut icon" type="image/png" href="{{ Setting::get('site_icon') }}"/>

<link href="mainindex/css/bootstrap.css" rel="stylesheet">

<link href="mainindex/css/style.css" rel="stylesheet">

<link href="mainindex/css/fontawesome-all.min.css" rel="stylesheet">

<link id="switcher" href="mainindex/css/color.css" rel="stylesheet">

<link href="mainindex/css/color-switcher.css" rel="stylesheet">

<link href="mainindex/css/owl.carousel.css" rel="stylesheet">

<link href="mainindex/css/responsive.css" rel="stylesheet">

<link href="mainindex/css/icomoon.css" rel="stylesheet">

<link href="mainindex/css/animate.css" rel="stylesheet">



</head>

<body>

<!--Wrapper Content Start-->

<div class="tj-wrapper">

	<!--Style Switcher Section End-->

	<header class="tj-header">

		<!--Header Content Start-->

		<div class="container">

			<div class="row">

				<!--Toprow Content Start-->

				<div class="col-md-3 col-sm-3 col-xs-12">

					<!--Logo Start-->

					<div class="tj-logo">

						<!-- <h1><a href="home-1.html">{{Setting::get('site_title', '')}}</a></h1> -->

						<img src="{{Setting::get('site_logo', '')}}" alt="" style="max-width: 80px;">

					</div>

					<!--Logo End-->

				</div>

				<div class="col-md-3 col-sm-2 col-xs-12">

				</div>

				<div class="col-md-3 col-sm-4 col-xs-12">

					<div class="info_box" >

						<i class="fa fa-envelope"></i>

						<div class="info_text">

							<span class="info_title">Email</span>

							<span><a href="mailto:contact@pickup-vtc.com">{{Setting::get('contact_email', '')}}</a></span>

						</div>

					</div>

				</div>

				<div class="col-md-3 col-sm-3 col-xs-12">

					<div class="phone_info">

						<div class="phone_icon">

							<i class="fas fa-phone-volume"></i>

						</div>

						<div class="phone_text">

							<span><a href="tel:0770151515">{{Setting::get('contact_number', '')}}</a></span>

						</div>

					</div>

				</div>

				<!--Toprow Content End-->

			</div>

		</div>



		<div class="tj-nav-row">

			<div class="container">

				<div class="row">

					<!--Nav Holder Start-->

					<div class="tj-nav-holder">

						<!--Menu Holder Start-->

						<nav class="navbar navbar-default">

							<div class="navbar-header">

								<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#tj-navbar-collapse" aria-expanded="false">

									<span class="sr-only">Menu</span>

									<span class="icon-bar"></span>

									<span class="icon-bar"></span>

									<span class="icon-bar"></span>

								</button>

							</div>

							<!-- Navigation Content Start -->

							<div class="collapse navbar-collapse" id="tj-navbar-collapse">

								<ul class="nav navbar-nav">

								<li>

									<a href="/">Home</a>

								</li>


								<li>

									<a href="contact">Contact</a>

								</li>

								</ul>

							</div>

							<!-- Navigation Content Start -->

						</nav>

						<!--Menu Holder End-->

						<div class="book_btn">



						</div>

					</div><!--Nav Holder End-->

				</div>

			</div>

		</div>

	</header>

	<!--Header Content End-->




			<!--Header Banner Content Start-->

	<section class="tj-banner-form" style="background: url('main/assets/img/photos-1/1.webp') no-repeat; background-size: cover;">

		<div class="container">

			<div class="row">

				<!--Header Banner Caption Content Start-->

				<div class="col-md-12 col-sm-12">

					<div class="banner-caption">

						<div class="banner-inner bounceInLeft animated delay-0s text-center">

							<strong>{{Setting::get('f_text1', '')}}</strong>

							<h2>{{Setting::get('f_text2', '')}}</h2>

							<div class="banner-btns">

								<a href="{{Setting::get('f_u_url', '')}}" class="btn-style-1"><i class="fab fa-android"></i> App Client</a>

								<a href="{{Setting::get('f_p_url', '')}}" class="btn-style-2"><i class="fab fa-android"></i> App Driver</a>

							</div>

						</div>

					</div>

				</div>

				<!--Header Banner Caption Content End-->

				<!--Header Banner Form Content Start-->

				<div class="col-md-4 hidden d-none col-sm-5">

					<div class="trip-outer">

						<!--Banner Tab Content Start-->

						<div class="tab-content">

							<div class="tab-pane active text-center" id="one-way">

								<!--Banner Form Content Start-->

								<form method="POST" class="trip-type-frm">

									<div class="field-outer">

										<span class="fas fa-search"></span>

										<input type="text" name="pick_loc" placeholder="Departure">

									</div>

									<div class="field-outer">

										<span class="fas fa-search"></span>

										<input type="text" name="drop_loc" placeholder="Arrival">

									</div>

									<a class="search-btn" href="login">Search Rides <i class="fa fa-arrow-circle-right" aria-hidden="true"></i></a>

								</form>

								<!--Banner Form Content End-->

							</div>

						</div>

						<!--Banner Tab Content End-->

					</div>

				</div>

				<!--Header Banner Form Content End-->

			</div>

		</div>

	</section>

	<!--Header Banner Content End-->



	<!--Offer Content Start-->

	<section class="tj-offers">

		<div class="row">

			<!--Offer Box Content Start-->

			<div class="col-md-3 col-sm-6">

				<div class="offer-box">

					<div class="offer-info">

						<h4>{{Setting::get('f_text6', '')}}</h4>

						<p>{{Setting::get('f_text7', '')}}</p>

					</div>

				</div>

			</div>

			<!--Offer Box Content End-->

			<!--Offer Box Content Start-->

			<div class="col-md-3 col-sm-6">

				<div class="offer-box">

					<div class="offer-info">

						<h4>{{Setting::get('f_text8', '')}}</h4>

						<p>{{Setting::get('f_text9', '')}}</p>

					</div>

				</div>

			</div>

			<!--Offer Box Content End-->

			<!--Offer Box Content Start-->

			<div class="col-md-3 col-sm-6">

				<div class="offer-box">

					<div class="offer-info">

						<h4>{{Setting::get('f_text10', '')}}</h4>

						<p>{{Setting::get('f_text11', '')}}</p>

					</div>

				</div>

			</div>

			<!--Offer Box Content End-->

			<!--Offer Box Content Start-->

			<div class="col-md-3 col-sm-6">

				<div class="offer-box">

					<div class="offer-info">

						<h4>{{Setting::get('f_text12', '')}}</h4>

						<p>{{Setting::get('f_text13', '')}}</p>

					</div>

				</div>

			</div>

			<!--Offer Box Content End-->

		</div>

	</section>

	<!--Offer Content End-->





	<section class="tj-footer">

		<div class="container">

			<div class="row">

				<div class="col-md-4 text-center">

					<div class="about-widget widget">

						<h3>About {{Setting::get('site_title', '')}}</h3>

						<p>{{Setting::get('f_text27', '')}}</p>

						<ul class="fsocial-links">

							<li><a href="{{Setting::get('f_f_link', '')}}"><i class="fab fa-facebook-f"></i></a></li>

							<li><a href="{{Setting::get('f_t_link', '')}}"><i class="fab fa-twitter"></i></a></li>

							<li><a href="{{Setting::get('f_l_link', '')}}"><i class="fab fa-linkedin-in"></i></a></li>

							<li><a href="{{Setting::get('f_i_link', '')}}"><i class="fab fa-instagram"></i></a></li>

						</ul>

					</div>

				</div>

				<div class="col-md-4 text-center">

					<div class="links-widget widget">

						<h3>Links</h3>

						<ul class="flinks-list">

							<li><a href="contact">Contact</a></li>

							<li><a href="privacy">Privacy policy</a></li>

							<li><a href="terms">Terms & Conditions</a></li>

						</ul>

					</div>

				</div>

				<div class="col-md-4 text-center">

					<div class="contact-info widget">

						<h3>Contact Info</h3>

						<ul class="contact-box">

							<li>

								<i class="fas fa-home" aria-hidden="true"></i>   {{Setting::get('contact_address', '')}}

							</li>

							<li>

								<i class="far fa-envelope-open"></i>

								<a href="mailto:contact@pickup-vtc.com">

								{{Setting::get('contact_email', '')}}</a>

							</li>

							<li>

								<i class="fas fa-phone-square"></i>

								{{Setting::get('contact_number', '')}}

							</li>

							<li>

								<i class="fas fa-globe-asia"></i>

								<a href="https://www.pickup-vtc.com">{{Setting::get('site_link', '')}}</a>

							</li>

						</ul>

					</div>

				</div>

			</div>

		</div>

	</section>

	<!--Footer Content End-->

	<!--Footer Copyright Start-->

	<section class="tj-copyright">

		<div class="container">

			<div class="row">

				<div class="col-md-6 col-sm-6">

					<p>{{Setting::get('site_copyright', '')}}</p>

				</div>



			</div>

		</div>

	</section>

	<!--Footer Copyright End-->

</div>

<!--Wrapper Content End-->



<!-- Js Files Start -->

<script src="mainindex/js/jquery-1.12.5.min.js"></script>

<script src="mainindex/js/bootstrap.min.js"></script>

<script src="mainindex/js/migrate.js"></script>

<script src="mainindex/js/owl.carousel.min.js"></script>

<script src="mainindex/js/color-switcher.js"></script>

<script src="mainindex/js/jquery.counterup.min.js"></script>

<script src="mainindex/js/waypoints.min.js"></script>

<script src="mainindex/js/tweetie.js"></script>

<script src="mainindex/js/custom.js"></script>

<!-- Js Files End -->

</body>
