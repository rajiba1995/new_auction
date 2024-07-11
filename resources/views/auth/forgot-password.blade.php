<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{env('APP_NAME')}}</title>

    <link rel="stylesheet" href="{{asset('frontend/assets/css/fontawesome.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontend/assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontend/assets/css/swiper-bundle.min.css')}}">
    <link rel="stylesheet" href="{{asset('frontend/assets/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('frontend/assets/css/responsive.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>
    <div class="main">
        <div class="outer-page-wrapper">
            <div class="page-content">
                <div class="form-content">
                    <div class="login-logo">
                        <img src="https://milaapp.in/frontend/assets/images/logo.png">
                    </div>
                    <h2>Forgot Password</h2>
                    <form id="forgotPasswordForm">
                        @csrf <!-- CSRF protection -->
                        <div class="form-group">
                            <label class="form-label">Please Enter Email ID *</label>
                            <input type="email" name="email" class="form-control border-red @error('email') is-invalid @enderror" placeholder="Ex, info@auction.com" value="{{ old('email') }}">
                            <div class="mt-2" id="forgot_error">
                               
                            </div>
                        </div>
                        <button type="submit" class="btn btn-animated">Send OTP</button>
                    </form>
    
                    <form id="verifyOtpForm" style="display:none;">
                        @csrf <!-- CSRF protection -->
                        <div class="form-group">
                            <label class="form-label">Enter OTP *</label>
                            <input type="text" name="otp" class="form-control" placeholder="Enter OTP" required>
                            <div class="mt-2" id="otp_error">
                               
                            </div>
                        </div>
                        <button type="submit" class="btn btn-animated">Verify OTP</button>
                    </form>
    
                    <form id="resetPasswordForm" style="display:none;">
                        @csrf <!-- CSRF protection -->
                        <input type="hidden" name="email">
                        <input type="hidden" name="otp">
                        <div class="form-group">
                            <label class="form-label">New Password *</label>
                            <input type="password" name="password" class="form-control" placeholder="Enter new password" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Confirm New Password *</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm new password" required>
                        </div>
                        <div class="mt-2" id="reset_error">
                               
                        </div>
                        <button type="submit" class="btn btn-animated">Reset Password</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle forgot password form submission
            $('#forgotPasswordForm').on('submit', function(e) {
                e.preventDefault();
                let submitButton = $(this).find('button[type="submit"]');
                toggleButton(submitButton, true, 'Sending OTP...')
                $.ajax({
                    url: "{{ route('forgot.password.sendOtp') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if(response.status==200){
                            toastr.success(response.message);
                            $('#forgotPasswordForm').hide();
                            $('#verifyOtpForm').show();
                        }
                        if (response.errors) {
                            displayErrors(response.errors, '#forgot_error', 'danger');
                        }
                        
                        toggleButton(submitButton, false, 'Send OTP');
                    },
                    error: function(response) {
                        displayErrors(response.responseJSON.errors, '#forgot_error', 'danger');
                        toggleButton(submitButton, false, 'Send OTP');
                    }
                });
            });

            // Handle OTP verification form submission
            $('#verifyOtpForm').on('submit', function(e) {
                e.preventDefault();
                let submitButton = $(this).find('button[type="submit"]');
                toggleButton(submitButton, true, 'Verifying...')
                $.ajax({
                    url: "{{ route('forgot.password.verifyOtp') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if(response.status==400){
                            let html = `<div class="alert alert-danger" role="alert">
                                ${response.message}
                            </div>`;
                            $('#otp_error').html(html);
                            setTimeout(function() {
                                $('#otp_error').html('');
                            }, 3000);
                            toggleButton(submitButton, false, 'Verify OTP');
                        }
                        if(response.status==200){
                            toastr.success(response.message);
                            $('#verifyOtpForm').hide();
                            $('input[name="email"]').val($('input[name="email"]').val());
                            $('input[name="otp"]').val($('input[name="otp"]').val());
                            $('#resetPasswordForm').show();
                            toggleButton(submitButton, false, 'Verify OTP');
                        }
                    },
                    error: function(response) {
                        displayErrors(response.responseJSON.errors, '#otp_error', 'danger');
                        toggleButton(submitButton, false, 'Verify OTP');
                    }
                });
            });

            // Handle password reset form submission
            $('#resetPasswordForm').on('submit', function(e) {
                e.preventDefault();
                let submitButton = $(this).find('button[type="submit"]');
                toggleButton(submitButton, true, 'Please wait..')
                $.ajax({
                    url: "{{ route('forgot.password.reset') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if(response.status==400){
                            let html = `<div class="alert alert-danger" role="alert">
                                ${response.message}
                            </div>`;
                            $('#reset_error').html(html);
                            setTimeout(function() {
                                $('#reset_error').html('');
                            }, 3000);
                        }
                        if(response.status==200){
                            toastr.success(response.message);
                            window.location.href = "{{ route('login') }}";
                        }
                        toggleButton(submitButton, false, 'Reset Password');
                    },
                    error: function(response) {
                        displayErrors(response.responseJSON.errors, '#reset_error', 'danger');
                        toggleButton(submitButton, false, 'Reset Password');
                    }
                });
            });
            
        });
        function displayErrors(errors, selector, alert) {
            let errorMessages = '<div class="alert alert-'+alert+'" role="alert">';
            $.each(errors, function(key, value) {
                errorMessages += value[0] + '<br>';
            });
            errorMessages += '</div>';
            $(selector).html(errorMessages);

            setTimeout(function() {
                $(selector).html('');
            }, 3000);
        }
        function toggleButton(button, disable, text) {
            if (disable) {
                button.prop('disabled', true);
                button.text(text);
            } else {
                button.prop('disabled', false);
                button.text(text);
            }
        }
    </script>
</body>
</html>
