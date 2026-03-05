<header id="header"><!--header-->
	<div class="header_top"><!--header_top-->
		<div class="container">
			<div class="row">
				<div class="col-sm-6">
					<div class="contactinfo">
						<ul class="nav nav-pills">
							<li><a href=""><i class="fa fa-phone"></i> +2 95 01 88 821</a></li>
							<li><a href=""><i class="fa fa-envelope"></i> info@domain.com</a></li>
						</ul>
					</div>
				</div>
				<div class="col-sm-6">
					<div class="social-icons pull-right">
						<ul class="nav navbar-nav">
							<li><a href=""><i class="fa fa-facebook"></i></a></li>
							<li><a href=""><i class="fa fa-twitter"></i></a></li>
							<li><a href=""><i class="fa fa-linkedin"></i></a></li>
							<li><a href=""><i class="fa fa-dribbble"></i></a></li>
							<li><a href=""><i class="fa fa-google-plus"></i></a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div><!--/header_top-->

	<div class="header-middle"><!--header-middle-->
		<div class="container">
			<div class="row">
				<div class="col-md-4 clearfix">
					<div class="logo pull-left">
						<a href="{{ route('home') }}">
							<img src="{{ asset('frontend/images/home/logo.png') }}" alt="">
						</a>
					</div>

					<div class="btn-group pull-right clearfix">
						<div class="btn-group">
							<button type="button" class="btn btn-default dropdown-toggle usa" data-toggle="dropdown">
								USA <span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<li><a href="">Canada</a></li>
								<li><a href="">UK</a></li>
							</ul>
						</div>

						<div class="btn-group">
							<button type="button" class="btn btn-default dropdown-toggle usa" data-toggle="dropdown">
								DOLLAR <span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<li><a href="">Canadian Dollar</a></li>
								<li><a href="">Pound</a></li>
							</ul>
						</div>
					</div>
				</div>

				<div class="col-md-8 clearfix">
					<div class="shop-menu clearfix pull-right">
						<ul class="nav navbar-nav">
							<li>
								<a href="{{ route('member.profile') }}">
									<i class="fa fa-user"></i> Account
								</a>
							</li>

							<li><a href=""><i class="fa fa-star"></i> Wishlist</a></li>
							<li><a href="checkout.html"><i class="fa fa-crosshairs"></i> Checkout</a></li>
							<li>
								<a href="{{ route('cart.index') }}">
									<i class="fa fa-shopping-cart"></i>
									Cart
									<span id="cart-count">
										{{ session()->has('cart')
											? array_sum(array_column(session('cart'), 'qty'))
											: 0 }}
									</span>
								</a>
							</li>

							@guest
								<li>
									<a href="{{ route('member.login') }}">
										<i class="fa fa-lock"></i> Login
									</a>
								</li>
							@endguest

							@auth
								<li>
									<a href="{{ route('member.logout') }}"
									onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
										<i class="fa fa-sign-out"></i> Logout
									</a>

									<form id="logout-form"
										action="{{ route('member.logout') }}"
										method="POST"
										style="display: none;">
										@csrf
									</form>
								</li>
							@endauth

						</ul>
					</div>
				</div>

			</div>
		</div>
	</div><!--/header-middle-->

	<div class="header-bottom"><!--header-bottom-->
		<div class="container">
			<div class="row">
				<div class="col-sm-9">
					<div class="navbar-header">
						<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
					</div>
					<div class="mainmenu pull-left">
						<ul class="nav navbar-nav collapse navbar-collapse">
							<li>
								<a href="{{ route('home') }}">Home</a>
							</li>

							<li class="dropdown"><a href="#">Shop<i class="fa fa-angle-down"></i></a>
								<ul role="menu" class="sub-menu">
									<li><a href="shop.html">Products</a></li>
									<li><a href="product-details.html">Product Details</a></li>
									<li><a href="checkout.html">Checkout</a></li>
									<li><a href="cart.html">Cart</a></li>
									<li><a href="login.html">Login</a></li>
								</ul>
							</li>
							<li class="dropdown"><a href="#">Blog<i class="fa fa-angle-down"></i></a>
								<ul role="menu" class="sub-menu">
									<li>
										<a href="{{ route('blog.index') }}">Blog List</a>
									</li>

									<li><a href="blog-single.html">Blog Single</a></li>
								</ul>
							</li>
							<li><a href="404.html">404</a></li>
							<li><a href="contact-us.html">Contact</a></li>
						</ul>
					</div>
				</div>

				<form action="{{ route('search') }}" method="GET">
					<div class="search_box pull-right">
						<input type="text"
							name="keyword"
							placeholder="Search"
							value="{{ request('keyword') }}">
					</div>
				</form>
			</div>
		</div>
	</div><!--/header-bottom-->
</header><!--/header-->
