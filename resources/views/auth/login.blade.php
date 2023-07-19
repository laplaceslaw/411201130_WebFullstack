<html>
@include('partials.head')

<link rel="shortcut icon" type="image/png" href="https://kucinkputih.com/.UNDIRA/Logo-UNDIRA.png"/>

<body class="hold-transition login-page">
    <div class="login-box">
    <img src="https://kucinkputih.com/.UNDIRA/Logo.jpg" style="width: 100%";></img>
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="/" class="h1">Aplikasi <b>Transaksi</b></a>
            </div>
            <div class="card-body"> 
                <p class="login-box-msg">Silahkan masuk untuk menggunakan :</p>
                <form action="/auth/login" method="post">
                    @csrf
                    @include('layouts.alert')
                    <div class="input-group mb-3">
                        <input type="username" name="username" class="form-control" placeholder="Username">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                </form>
            </div>
        </div>
    </div>
    @include('partials.script')
</body>

</html>
