<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{env('APP_NAME')}}</title>
    <style>
        .hand_sign{
            cursor: pointer!important;
        }
    </style>
    <link rel="stylesheet" href="{{asset('frontend/assets/css/fontawesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontend/assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontend/assets/css/swiper-bundle.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontend/assets/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('frontend/assets/css/responsive.css')}}">
</head>
<body>
    <div class="main">
        <div class="outer-page-wrapper">
            <div class="page-content">
                <div class="form-content">
                    <div class="login-logo">
                        <img src="https://milaapp.in/frontend/assets/images/logo.png">
                    </div>
                    <h2>Sign In</h2>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf <!-- CSRF protection -->
    
                        <div class="form-group">
                            <label class="form-label">Please Enter Email ID *</label>
                            <input type="email" name="email" class="form-control border-red @error('email') is-invalid @enderror" placeholder="Ex, info@auction.com" value="{{ old('email') }}">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password*</label>
                            <input type="password" name="password" class="form-control border-red @error('password') is-invalid @enderror" placeholder="............."> 
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-animated">Login</button>
                        <div class="resend-block mt-4">
                            {{-- <label>If you haven't registered yet,</label> --}}
                            <a href="{{route('forgot-password')}}" class="hand_sign">Forgot your password?</a>
                            <a href="{{route('register')}}" class="hand_sign">Register Here</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{asset('frontend/assets/js/jquery.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/fontawesome.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/swiper-bundle.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/custom.js')}}"></script>
</body>
</html>
