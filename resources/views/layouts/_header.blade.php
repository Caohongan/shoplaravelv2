<nav class="navbar shadow navbar-expand-lg navbar-light bg-light navbar-static-top sticky-top">
  <div class="container">
    <!-- Branding Image -->
    <a class="navbar-brand " href="{{ url('/') }}">
      Laravel Shop
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <!-- Left Side Of Navbar -->
      <ul class="navbar-nav mr-auto">

      </ul>

      <!-- Right Side Of Navbar -->
      <ul class="navbar-nav navbar-right">
        <!-- 登录注册链接开始 -->
        @guest
        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Đăng Nhập</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('register') }}"><i class="fas fa-user-plus"></i>Đăng Kí</a></li>
        @else
        <li class="nav-item">
          <a class="nav-link mt-2" href="{{ route('cart.index') }}"><i class="fa fa-shopping-cart"></i></a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img src="https://pbs.twimg.com/media/Dk5rZlqXsAA4Qq6.jpg" class="img-responsive rounded circle" width="40px" height="40px">
            {{ Auth::user()->name }}
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a href="{{ route('user_addresses.index') }}" class="dropdown-item"><i class="fas fa-address-card"></i>  Địa chỉ của bạn</a>
            <a href="{{ route('orders.index') }}" class="dropdown-item"><i class="fas fa-shopping-cart"></i>  Những sản phẩm đã mua</a>  
            <a href="{{ route('products.favorites') }}" class="dropdown-item"><i class="fas fa-star"></i>  Những sản phẩm yêu thích</a>
            <a class="dropdown-item" id="logout" href="#"
               onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i>  Đăng Xuất</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              {{ csrf_field() }}
            </form>
          </div>
        </li>
        @endguest
        <!-- 登录注册链接结束 -->
      </ul>
    </div>
  </div>
</nav>
