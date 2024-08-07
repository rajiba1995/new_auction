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
    <link rel="stylesheet" href="{{asset('frontend/assets/css/loader.css')}}">
    <link rel="stylesheet" href="{{asset('frontend/assets/css/responsive.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script type="text/javascript" src="{{ asset('frontend/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('frontend/ckeditor/adapters/jquery.js') }}"></script>
    <style>
        .bidder-box .badge-rating{
            margin-left: 0px !important;
        }
        .rating-star-values ul.solid-stars {
            left: 30px !important;
        }
        .rating-star-values ul.rating-stars {
            position: absolute;
            top: 0;
            left: 30px !important;
        }
        .notification_content_box h5 {
            white-space: break-spaces;
        }
    </style>
    @php
    // Get the client's IP address
        $ip = $_SERVER['REMOTE_ADDR'];
    
        // Use geoplugin.net to get location data based on the IP address
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
    
        // Check if the data was retrieved successfully
        if ($ipdat && $ipdat->geoplugin_status == 200) {
            $country = $ipdat->geoplugin_countryName;
            $city_name = $ipdat->geoplugin_city;
            $region = $ipdat->geoplugin_region;
            $latitude = $ipdat->geoplugin_latitude;
            $longitude = $ipdat->geoplugin_longitude;
        } else {
            $country = '';
            $city_name = '';
            $region = '';
        }
    
    @endphp
</head>
<body>

    @if (Auth::guard('web')->check())
        <header class="logged">
            <div class="container-fluid">
                <div class="header-inner">
                <div class="brand">
                    <a href="{{asset('')}}" class="rajib">
                        <img src="{{asset('frontend/assets/images/logo.png')}}" alt="">
                    </a>
                </div>

                <div class="search-section">
                    <div class="location-bar">
                        <img src="{{asset('frontend/assets/images/location.png')}}" alt="">
                        <input type="text" placeholder="Select Location" id="stateInput" name="global_state_name" autocomplete="off" value="@yield('location', $city_name)">
                        <div id="stateSuggestions"></div>
                    </div>
                    
                    
                    <div class="search-bar">
                        <form action="{{route('user.global.make_slug')}}" method="GET" id="Search_form">
                            <input type="hidden" name="location" id="hidden_location" value="@yield('location', $city_name)">
                            <!-- <input type="search" name="keyword" id="autocomplete-input" placeholder="Search for Service, Category, etc" value="@yield('keyword')"> -->
                            <input type="search" name="keyword" id="autocomplete-input" placeholder="Search" autocomplete="off">
                            
                            <button type="submit" class="btn-search btn-animated" id="global_form_submit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M20.9999 21.0004L16.6499 16.6504" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        <!-- <div id="autocomplete-list" class="autocomplete-items"></div> -->
                            <div id="autocomplete-suggestions" class="autocomplete-suggestions">
                                
                            </div>
                        </form>
                    </div>
                
                    <!-- <div id="filterSuggestions"></div> -->
                    
                </div>

                {{-- <a herf="{{route('user.profile')}}" class="btn btn-cta btn-animated btn-dashboard">Dashboard</a> --}}
                <div class="dropdown dashboard-dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Dashboard
                    </button>
                    <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{route('user_buyer_dashboard')}}">Buyer</a></li>
                    <li><a class="dropdown-item" href="{{route('user_seller_dashboard')}}">Seller</a></li>
                    </ul>
                </div>
                <a href="" class="menu-cta helpdesk">
                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_726_1406)">
                        <path d="M12.2453 7.39797C13.2299 7.39797 14.031 6.5969 14.031 5.61223C14.031 4.62756 13.2299 3.82654 12.2453 3.82654C11.2606 3.82654 10.4596 4.62761 10.4596 5.61223C10.4596 6.5969 11.2606 7.39797 12.2453 7.39797ZM8.67383 10.4592V12.5H10.9698C11.2516 12.5 11.48 12.7284 11.48 13.0102V20.9184L14.5418 20.9183L14.5412 10.4592H8.67383Z" fill="#ee2737"/>
                        <path d="M12.3103 0.00141447C5.49825 0.102977 -0.00824269 5.70273 9.26258e-06 12.5155C0.00396434 15.7859 1.26124 18.8741 3.54132 21.2127C3.73629 21.4126 3.73424 21.7322 3.53678 21.9296L0.735654 24.7308C0.636289 24.8301 0.70665 25 0.847177 25H12.3754C19.2956 25 25.0445 19.338 24.9998 12.418C24.955 5.49995 19.2619 -0.102199 12.3103 0.00141447ZM12.2449 2.80615C13.7922 2.80615 15.051 4.06494 15.051 5.61225C15.051 7.15957 13.7922 8.41841 12.2449 8.41841C10.6976 8.41841 9.43878 7.15962 9.43878 5.61225C9.43878 4.06494 10.6976 2.80615 12.2449 2.80615ZM15.5612 20.9184C15.5612 21.481 15.1035 21.9388 14.5408 21.9388H11.4796C10.917 21.9388 10.4592 21.481 10.4592 20.9184V13.5204H8.67345C8.1108 13.5204 7.65304 13.0626 7.65304 12.5V10.4592C7.65304 9.89653 8.1108 9.43877 8.67345 9.43877H14.5408C15.1034 9.43877 15.5612 9.89653 15.5612 10.4592V20.9184H15.5612Z" fill="#ee2737"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_726_1406">
                        <rect width="25" height="25" fill="white"/>
                        </clipPath>
                        </defs>
                    </svg>    
                    Helpdesk                
                </a>
                <a href="{{route('user.watchlist')}}" class="menu-cta watchlist">
                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_726_1397)">
                        <path d="M20.1224 5.29455C20.1609 5.33338 20.2068 5.36416 20.2574 5.38511C20.308 5.40607 20.3622 5.41678 20.4169 5.41664H20.4357C20.4935 5.41394 20.5502 5.39929 20.6021 5.37362C20.654 5.34795 20.7001 5.31181 20.7374 5.26747L22.8207 2.76747C22.8915 2.68249 22.9257 2.57285 22.9157 2.46267C22.9057 2.3525 22.8523 2.2508 22.7674 2.17997C22.6824 2.10914 22.5727 2.07496 22.4626 2.08496C22.3524 2.09496 22.2507 2.14832 22.1799 2.2333L20.3882 4.3833L20.294 4.28914C20.2154 4.21324 20.1102 4.17124 20.0009 4.17219C19.8917 4.17314 19.7872 4.21696 19.7099 4.29421C19.6327 4.37147 19.5889 4.47597 19.5879 4.58522C19.587 4.69447 19.629 4.79972 19.7049 4.8783L20.1224 5.29455ZM20.1224 14.0446C20.1609 14.0834 20.2068 14.1142 20.2574 14.1351C20.308 14.1561 20.3622 14.1668 20.4169 14.1666H20.4357C20.4935 14.1639 20.5502 14.1493 20.6021 14.1236C20.654 14.098 20.7001 14.0618 20.7374 14.0175L22.8207 11.5175C22.8915 11.4325 22.9257 11.3229 22.9157 11.2127C22.9057 11.1025 22.8523 11.0008 22.7674 10.93C22.6824 10.8591 22.5727 10.825 22.4626 10.835C22.3524 10.845 22.2507 10.8983 22.1799 10.9833L20.3882 13.1333L20.294 13.0391C20.2154 12.9632 20.1102 12.9212 20.0009 12.9222C19.8917 12.9231 19.7872 12.967 19.7099 13.0442C19.6327 13.1215 19.5889 13.226 19.5879 13.3352C19.587 13.4445 19.629 13.5497 19.7049 13.6283L20.1224 14.0446ZM20.1224 22.7946C20.1609 22.8334 20.2068 22.8642 20.2574 22.8851C20.308 22.9061 20.3622 22.9168 20.4169 22.9166H20.4357C20.4935 22.9139 20.5502 22.8993 20.6021 22.8736C20.654 22.8479 20.7001 22.8118 20.7374 22.7675L22.8207 20.2675C22.8558 20.2254 22.8822 20.1768 22.8985 20.1245C22.9148 20.0722 22.9206 20.0172 22.9157 19.9627C22.9107 19.9081 22.8951 19.8551 22.8696 19.8066C22.8442 19.7581 22.8094 19.715 22.7674 19.68C22.7253 19.6449 22.6767 19.6185 22.6244 19.6021C22.5721 19.5858 22.5171 19.58 22.4626 19.585C22.408 19.5899 22.355 19.6056 22.3065 19.631C22.2579 19.6565 22.2149 19.6912 22.1799 19.7333L20.3882 21.8833L20.294 21.7891C20.2154 21.7132 20.1102 21.6712 20.0009 21.6722C19.8917 21.6731 19.7872 21.717 19.7099 21.7942C19.6327 21.8715 19.5889 21.976 19.5879 22.0852C19.587 22.1945 19.629 22.2997 19.7049 22.3783L20.1224 22.7946Z" fill="#ee2737"/>
                        <path d="M14.5833 4.16667H17.5246C17.6311 5.11918 18.0982 5.99476 18.8301 6.61359C19.562 7.23242 20.5031 7.54752 21.46 7.4942C22.417 7.44087 23.3172 7.02315 23.9758 6.32683C24.6344 5.63051 25.0014 4.70845 25.0014 3.75001C25.0014 2.79156 24.6344 1.8695 23.9758 1.17318C23.3172 0.476862 22.417 0.0591434 21.46 0.00581412C20.5031 -0.0475152 19.562 0.267593 18.8301 0.886423C18.0982 1.50525 17.6311 2.38083 17.5246 3.33334H14.5833C14.2518 3.33334 13.9339 3.46503 13.6995 3.69945C13.465 3.93388 13.3333 4.25182 13.3333 4.58334V12.0833H10.8333V10C10.8316 9.01976 10.4847 8.07143 9.8534 7.32151C9.22212 6.5716 8.34685 6.06801 7.38125 5.89917C7.68084 5.62698 7.92028 5.29521 8.08423 4.92512C8.24818 4.55504 8.33303 4.15478 8.33333 3.75001V2.91667C8.33333 2.14312 8.02604 1.40126 7.47906 0.854277C6.93208 0.307295 6.19021 4.66855e-06 5.41667 4.66855e-06C4.64312 4.66855e-06 3.90125 0.307295 3.35427 0.854277C2.80729 1.40126 2.5 2.14312 2.5 2.91667V3.75001C2.5003 4.15478 2.58515 4.55504 2.7491 4.92512C2.91305 5.29521 3.15249 5.62698 3.45208 5.89917C2.48648 6.06801 1.61121 6.5716 0.979933 7.32151C0.348652 8.07143 0.00170504 9.01976 0 10L0 14.7917C1.95161e-05 15.0377 0.0622743 15.2797 0.180971 15.4952C0.299667 15.7107 0.470947 15.8926 0.67887 16.0241C0.886794 16.1556 1.1246 16.2323 1.37016 16.2472C1.61573 16.2621 1.86106 16.2146 2.08333 16.1092V23.125C2.08363 23.5126 2.20405 23.8906 2.42802 24.207C2.65199 24.5233 2.96851 24.7625 3.33401 24.8916C3.6995 25.0206 4.09602 25.0333 4.46899 24.9277C4.84195 24.8221 5.17303 24.6036 5.41667 24.3021C5.6603 24.6036 5.99138 24.8221 6.36435 24.9277C6.73731 25.0333 7.13383 25.0206 7.49933 24.8916C7.86483 24.7625 8.18134 24.5233 8.40531 24.207C8.62928 23.8906 8.7497 23.5126 8.75 23.125V16.1092C8.97227 16.2146 9.21761 16.2621 9.46317 16.2472C9.70873 16.2323 9.94654 16.1556 10.1545 16.0241C10.3624 15.8926 10.5337 15.7107 10.6524 15.4952C10.7711 15.2797 10.8333 15.0377 10.8333 14.7917V12.9167H13.3333V20.4167C13.3333 20.7482 13.465 21.0661 13.6995 21.3006C13.9339 21.535 14.2518 21.6667 14.5833 21.6667H17.5246C17.6311 22.6192 18.0982 23.4948 18.8301 24.1136C19.562 24.7324 20.5031 25.0475 21.46 24.9942C22.417 24.9409 23.3172 24.5231 23.9758 23.8268C24.6344 23.1305 25.0014 22.2084 25.0014 21.25C25.0014 20.2916 24.6344 19.3695 23.9758 18.6732C23.3172 17.9769 22.417 17.5591 21.46 17.5058C20.5031 17.4525 19.562 17.7676 18.8301 18.3864C18.0982 19.0053 17.6311 19.8808 17.5246 20.8333H14.5833C14.4728 20.8333 14.3668 20.7894 14.2887 20.7113C14.2106 20.6332 14.1667 20.5272 14.1667 20.4167V12.9167H17.5246C17.6311 13.8692 18.0982 14.7448 18.8301 15.3636C19.562 15.9824 20.5031 16.2975 21.46 16.2442C22.417 16.1909 23.3172 15.7731 23.9758 15.0768C24.6344 14.3805 25.0014 13.4584 25.0014 12.5C25.0014 11.5416 24.6344 10.6195 23.9758 9.92318C23.3172 9.22686 22.417 8.80914 21.46 8.75581C20.5031 8.70249 19.562 9.01759 18.8301 9.63642C18.0982 10.2553 17.6311 11.1308 17.5246 12.0833H14.1667V4.58334C14.1667 4.47283 14.2106 4.36685 14.2887 4.28871C14.3668 4.21057 14.4728 4.16667 14.5833 4.16667ZM21.25 0.833338C21.8269 0.833338 22.3908 1.0044 22.8704 1.32489C23.3501 1.64537 23.7239 2.10089 23.9446 2.63384C24.1654 3.1668 24.2232 3.75324 24.1106 4.31902C23.9981 4.8848 23.7203 5.4045 23.3124 5.8124C22.9045 6.2203 22.3848 6.49809 21.819 6.61063C21.2532 6.72317 20.6668 6.66541 20.1338 6.44465C19.6009 6.2239 19.1454 5.85006 18.8249 5.37042C18.5044 4.89077 18.3333 4.32687 18.3333 3.75001C18.3343 2.97676 18.6419 2.23547 19.1887 1.68871C19.7355 1.14194 20.4768 0.83433 21.25 0.833338ZM3.33333 3.75001V2.91667C3.33333 2.36414 3.55283 1.83423 3.94353 1.44353C4.33423 1.05283 4.86413 0.833338 5.41667 0.833338C5.9692 0.833338 6.4991 1.05283 6.88981 1.44353C7.28051 1.83423 7.5 2.36414 7.5 2.91667V3.75001C7.5 4.30254 7.28051 4.83244 6.88981 5.22314C6.4991 5.61384 5.9692 5.83334 5.41667 5.83334C4.86413 5.83334 4.33423 5.61384 3.94353 5.22314C3.55283 4.83244 3.33333 4.30254 3.33333 3.75001ZM4.94875 8.33334H5.88417L6.24583 11.1175L5.4125 11.6646L4.58333 11.1125L4.94875 8.33334ZM4.69875 6.66667H6.13125L5.92292 7.50001H4.90875L4.69875 6.66667ZM10 14.7917C10 14.9574 9.93415 15.1164 9.81694 15.2336C9.69973 15.3508 9.54076 15.4167 9.375 15.4167C9.20924 15.4167 9.05027 15.3508 8.93306 15.2336C8.81585 15.1164 8.75 14.9574 8.75 14.7917V10C8.75 9.8895 8.7061 9.78352 8.62796 9.70538C8.54982 9.62724 8.44384 9.58334 8.33333 9.58334C8.22283 9.58334 8.11685 9.62724 8.0387 9.70538C7.96056 9.78352 7.91667 9.8895 7.91667 10V23.125C7.91667 23.4013 7.80692 23.6662 7.61157 23.8616C7.41622 24.0569 7.15127 24.1667 6.875 24.1667C6.59873 24.1667 6.33378 24.0569 6.13843 23.8616C5.94308 23.6662 5.83333 23.4013 5.83333 23.125V16.6667C5.83333 16.5562 5.78943 16.4502 5.71129 16.372C5.63315 16.2939 5.52717 16.25 5.41667 16.25C5.30616 16.25 5.20018 16.2939 5.12204 16.372C5.0439 16.4502 5 16.5562 5 16.6667V23.125C5 23.4013 4.89025 23.6662 4.6949 23.8616C4.49955 24.0569 4.2346 24.1667 3.95833 24.1667C3.68207 24.1667 3.41711 24.0569 3.22176 23.8616C3.02641 23.6662 2.91667 23.4013 2.91667 23.125V10C2.91667 9.8895 2.87277 9.78352 2.79463 9.70538C2.71649 9.62724 2.61051 9.58334 2.5 9.58334C2.38949 9.58334 2.28351 9.62724 2.20537 9.70538C2.12723 9.78352 2.08333 9.8895 2.08333 10V14.7917C2.08333 14.9574 2.01749 15.1164 1.90028 15.2336C1.78306 15.3508 1.62409 15.4167 1.45833 15.4167C1.29257 15.4167 1.1336 15.3508 1.01639 15.2336C0.899181 15.1164 0.833333 14.9574 0.833333 14.7917V10C0.834453 9.17196 1.14335 8.3739 1.7 7.76088C2.25666 7.14786 3.02132 6.76366 3.84542 6.68292L4.16 7.94042L3.75667 11.0063C3.7366 11.1615 3.76048 11.3193 3.82559 11.4617C3.8907 11.604 3.99443 11.7253 4.125 11.8117L4.94792 12.3583C5.08629 12.4508 5.24898 12.5002 5.41542 12.5002C5.58185 12.5002 5.74455 12.4508 5.88292 12.3583L6.70833 11.8117C6.83939 11.7251 6.94346 11.6033 7.00866 11.4604C7.07387 11.3175 7.09758 11.1591 7.07708 11.0033L6.67417 7.94042L6.98875 6.68292C7.8127 6.76385 8.57716 7.14814 9.13364 7.76114C9.69012 8.37414 9.9989 9.17209 10 10V14.7917ZM21.25 18.3333C21.8269 18.3333 22.3908 18.5044 22.8704 18.8249C23.3501 19.1454 23.7239 19.6009 23.9446 20.1338C24.1654 20.6668 24.2232 21.2532 24.1106 21.819C23.9981 22.3848 23.7203 22.9045 23.3124 23.3124C22.9045 23.7203 22.3848 23.9981 21.819 24.1106C21.2532 24.2232 20.6668 24.1654 20.1338 23.9447C19.6009 23.7239 19.1454 23.3501 18.8249 22.8704C18.5044 22.3908 18.3333 21.8269 18.3333 21.25C18.3343 20.4768 18.6419 19.7355 19.1887 19.1887C19.7355 18.6419 20.4768 18.3343 21.25 18.3333ZM21.25 9.58334C21.8269 9.58334 22.3908 9.7544 22.8704 10.0749C23.3501 10.3954 23.7239 10.8509 23.9446 11.3838C24.1654 11.9168 24.2232 12.5032 24.1106 13.069C23.9981 13.6348 23.7203 14.1545 23.3124 14.5624C22.9045 14.9703 22.3848 15.2481 21.819 15.3606C21.2532 15.4732 20.6668 15.4154 20.1338 15.1947C19.6009 14.9739 19.1454 14.6001 18.8249 14.1204C18.5044 13.6408 18.3333 13.0769 18.3333 12.5C18.3343 11.7268 18.6419 10.9855 19.1887 10.4387C19.7355 9.89194 20.4768 9.58433 21.25 9.58334Z" fill="#ee2737"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_726_1397">
                        <rect width="25" height="25" fill="white"/>
                        </clipPath>
                        </defs>
                    </svg>
                    Watchlist                    
                </a>
                <a href="{{route('front.auction_inquiry_generation')}}" class="menu-cta start-auction">
                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_726_1389)">
                        <path d="M5.75078 8.51294C5.56035 8.32251 5.25078 8.32251 5.0604 8.51294C4.86948 8.70386 4.86948 9.01294 5.0604 9.20337C5.25083 9.39429 5.5604 9.39429 5.75078 9.20337C5.94165 9.01294 5.94165 8.70386 5.75078 8.51294ZM12.3632 21.8612C12.5785 21.5696 12.6949 21.2183 12.6949 20.8489C12.6949 19.9066 11.9283 19.14 10.9859 19.14H2.68545C2.22876 19.14 1.79961 19.3177 1.477 19.6404C1.15425 19.9631 0.976514 20.3922 0.976514 20.8489C0.976185 21.2131 1.09283 21.5678 1.30928 21.8606C0.531787 22.2694 0 23.0849 0 24.0226V24.5109C0 24.7806 0.218652 24.9992 0.488281 24.9992H13.1831C13.4527 24.9992 13.6714 24.7806 13.6714 24.5109V24.0226C13.6714 23.0853 13.1401 22.2702 12.3632 21.8612ZM2.16748 20.3309C2.23535 20.2626 2.31607 20.2086 2.40499 20.1718C2.4939 20.135 2.58922 20.1162 2.68545 20.1165H10.9859C11.3898 20.1165 11.7183 20.445 11.7183 20.8488C11.7183 21.0446 11.6421 21.2286 11.5039 21.3668C11.436 21.4351 11.3553 21.4891 11.2664 21.5259C11.1774 21.5627 11.0821 21.5815 10.9859 21.5812H2.68545C2.28159 21.5812 1.95308 21.2527 1.95308 20.8489C1.95308 20.6531 2.0292 20.4691 2.16748 20.3309ZM0.976514 24.0226C0.976514 23.2149 1.63364 22.5578 2.44131 22.5578H11.2301C12.0377 22.5578 12.6949 23.2149 12.6949 24.0226H0.976514Z" fill="#ee2737"/>
                        <path d="M24.5465 20.5138C24.3807 20.2568 24.1828 20.0296 23.9584 19.8384L21.6769 17.8485C21.6588 17.8212 21.638 17.7959 21.6149 17.7728C21.5763 17.7344 21.5316 17.7027 21.4826 17.6791L20.1028 16.4757L13.7166 10.9049C13.978 10.6316 14.122 10.2742 14.122 9.89419C14.122 9.50269 13.9696 9.13476 13.6926 8.85781C13.575 8.7403 13.438 8.64389 13.2877 8.5728L13.8786 7.98198C14.0432 8.02589 14.2128 8.04819 14.3832 8.04834C14.8833 8.04829 15.3834 7.85796 15.764 7.47729C16.1331 7.1083 16.3363 6.61782 16.3363 6.09619C16.3363 5.57456 16.1331 5.08408 15.7641 4.71509L11.6211 0.572217C11.2521 0.203223 10.7616 0 10.2398 0C9.71826 0 9.22788 0.203223 8.85894 0.572217C8.34898 1.08218 8.1811 1.80625 8.35425 2.45771L2.45947 8.35244C2.29455 8.30853 2.12462 8.28629 1.95396 8.28628C1.43208 8.28628 0.941797 8.48936 0.573096 8.85801C-0.188428 9.61953 -0.188428 10.8586 0.573096 11.6202L4.71602 15.7632C5.09683 16.1439 5.59697 16.3343 6.09712 16.3343C6.59731 16.3343 7.09746 16.1439 7.47827 15.7632C7.98823 15.2532 8.1561 14.5292 7.98296 13.8777L8.57256 13.288C8.64444 13.4379 8.7413 13.5745 8.85898 13.6919C9.13564 13.9686 9.50347 14.1209 9.89487 14.1209C10.2745 14.1209 10.6317 13.9772 10.9054 13.7161L17.7075 21.5312C17.7262 21.5605 17.7481 21.5884 17.7737 21.614C17.7824 21.6228 17.7917 21.6307 17.8009 21.6386L19.8278 23.9674C19.8788 24.0276 19.9322 24.0856 19.988 24.1413C20.5447 24.6978 21.28 24.9998 22.052 24.9998C22.188 24.9998 22.3254 24.9904 22.4629 24.9714C23.3823 24.8441 24.1769 24.2941 24.6428 23.4622C25.1532 22.5504 25.1152 21.3929 24.5465 20.5138ZM9.54951 1.26274C9.63994 1.17177 9.74752 1.09964 9.86602 1.05051C9.98451 1.00139 10.1116 0.976256 10.2398 0.976562C10.3682 0.976217 10.4953 1.00133 10.6139 1.05046C10.7325 1.09958 10.8401 1.17173 10.9306 1.26274L15.0736 5.40566C15.1646 5.49614 15.2367 5.60376 15.2858 5.72229C15.3349 5.84083 15.3601 5.96793 15.3597 6.09624C15.3601 6.22455 15.3349 6.35166 15.2858 6.4702C15.2367 6.58874 15.1645 6.69635 15.0735 6.78682C14.6929 7.16753 14.0735 7.16753 13.6929 6.78682L9.54976 2.6437L9.54951 2.64346C9.1688 2.26279 9.1688 1.64341 9.54951 1.26274ZM6.78774 15.0727C6.40698 15.4534 5.7873 15.4535 5.40654 15.0727L1.26362 10.9297C0.882813 10.5489 0.882813 9.92939 1.26362 9.54853C1.4478 9.36431 1.69302 9.26284 1.95396 9.26284C2.21484 9.26284 2.45991 9.36426 2.64404 9.54834L6.7877 13.692C7.16846 14.0726 7.16846 14.692 6.78774 15.0727ZM8.84351 11.6361L7.47822 13.0015L3.33486 8.8581L8.85894 3.33394L13.0022 7.47729L11.6345 8.84512C11.63 8.84932 11.6257 8.85342 11.621 8.85806L8.86035 11.6187C8.85464 11.6244 8.84912 11.6302 8.84351 11.6361ZM10.587 12.6547L10.5841 12.6576L10.2402 13.0015C10.195 13.0469 10.1411 13.083 10.0818 13.1075C10.0226 13.1321 9.95899 13.1446 9.89482 13.1444C9.83066 13.1446 9.76709 13.1321 9.70781 13.1075C9.64853 13.083 9.59471 13.0469 9.54946 13.0014C9.50392 12.9561 9.46782 12.9023 9.44324 12.8429C9.41867 12.7836 9.40611 12.72 9.4063 12.6558C9.4063 12.5279 9.45508 12.4077 9.5436 12.3169L12.3171 9.54346C12.5072 9.35859 12.8145 9.36089 13.0023 9.54853C13.0479 9.59382 13.084 9.64769 13.1085 9.70703C13.1331 9.76636 13.1456 9.82997 13.1455 9.89419C13.1457 9.95834 13.1331 10.0219 13.1085 10.0812C13.084 10.1404 13.0479 10.1942 13.0023 10.2394L12.6604 10.5812C12.6581 10.5835 12.6558 10.5858 12.6535 10.5882L10.587 12.6547ZM11.5981 13.0246L13.0251 11.5976L19.0672 16.8678L16.8625 19.0727L11.5981 13.0246ZM17.5051 19.811L19.8048 17.5112L20.5425 18.1546L18.1477 20.5495L17.5051 19.811ZM23.7908 22.9852C23.4757 23.5478 22.9429 23.9191 22.3291 24.0041C21.7167 24.0889 21.115 23.8872 20.6784 23.4507C20.6411 23.4135 20.6054 23.3747 20.5714 23.3345L20.5667 23.329L18.7903 21.2878L21.2801 18.798L23.3187 20.5762L23.3233 20.5802C23.4759 20.71 23.6113 20.8658 23.7262 21.0438C24.0955 21.6144 24.1213 22.3946 23.7908 22.9852Z" fill="#ee2737"/>
                        <path d="M9.20404 5.06036C9.01332 4.86969 8.70424 4.86969 8.51351 5.06036L6.44183 7.13204C6.25116 7.32272 6.25116 7.6319 6.44183 7.82252C6.48712 7.86793 6.54094 7.90394 6.60019 7.92849C6.65944 7.95303 6.72296 7.96562 6.7871 7.96554C6.85123 7.96561 6.91475 7.95302 6.974 7.92847C7.03325 7.90393 7.08707 7.86792 7.13236 7.82252L9.20404 5.75084C9.39471 5.56022 9.39471 5.25109 9.20404 5.06036Z" fill="#ee2737"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_726_1389">
                        <rect width="25" height="25" fill="white"/>
                        </clipPath>
                        </defs>
                    </svg>                    
                    Start Inquiry
                </a>
                <div class="notification_menu dropdown-toggle"  id="notificationDropdown">
                        <a href="#" class="notification" role="button" id="Link" data-bs-toggle="dropdown"
                    aria-expanded="false">
                            <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M22.5 10C22.5 8.01088 21.7098 6.10322 20.3033 4.6967C18.8968 3.29018 16.9891 2.5 15 2.5C13.0109 2.5 11.1032 3.29018 9.6967 4.6967C8.29018 6.10322 7.5 8.01088 7.5 10C7.5 18.75 3.75 21.25 3.75 21.25H26.25C26.25 21.25 22.5 18.75 22.5 10Z"
                                    stroke="#ee2737" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M17.1629 26.25C16.9431 26.6288 16.6277 26.9433 16.2482 27.1619C15.8687 27.3805 15.4384 27.4956 15.0004 27.4956C14.5624 27.4956 14.1321 27.3805 13.7526 27.1619C13.3731 26.9433 13.0577 26.6288 12.8379 26.25"
                                    stroke="#ee2737" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            @if(Auth::guard('web')->check())
                            <span class="indicator">
                                {{$notificationCount}}
                            </span>
                            @else
                                <span class="indicator">0</span>
                            @endif
                        </a>
                        <ul class="dropdown-menu notification_menu_ul" aria-labelledby="Link">
                            @if(count($notificationData)>0)
                            @foreach($notificationData as $data)
                            <li class="notification_all">
                                <a href="#" onclick="ViewNotification('{{$data->link?$data->link:route('user.notifications')}}', {{$data->id}})">
                                    <div class="notification_img_info">
                                        <div class="notification_content_box">
                                            <h5>{{$data->title}}</h5>
                                        </div>
                                        <ul class="notification_update">
                                            <li class="notified_td_date">{{date('d/m/y' ,strtotime($data->created_at))}}</li>
                                            <li class="notified_td">{{date('h:i A' ,strtotime($data->created_at))}}</li>
                                        </ul>
                                    </div>
                                </a>
                            </li>
                            @endforeach
                            <li class="dropdown-item-all">
                                <a class="dropdown-item" href="{{route('user.notifications')}}">Show All Notification</a>
                            </li>
                            @else
                            <li class="dropdown-item-all">
                                <a class="dropdown-item" href="{{route('user.notifications')}}">View Notification</a>
                            </li>
                            @endif
                        </ul>
                </div>
                <!-- <a href="#" class="notification">
                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22.5 10C22.5 8.01088 21.7098 6.10322 20.3033 4.6967C18.8968 3.29018 16.9891 2.5 15 2.5C13.0109 2.5 11.1032 3.29018 9.6967 4.6967C8.29018 6.10322 7.5 8.01088 7.5 10C7.5 18.75 3.75 21.25 3.75 21.25H26.25C26.25 21.25 22.5 18.75 22.5 10Z" stroke="#ee2737" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M17.1629 26.25C16.9431 26.6288 16.6277 26.9433 16.2482 27.1619C15.8687 27.3805 15.4384 27.4956 15.0004 27.4956C14.5624 27.4956 14.1321 27.3805 13.7526 27.1619C13.3731 26.9433 13.0577 26.6288 12.8379 26.25" stroke="#ee2737" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>                    
                    <span class="indicator"></span>
                </a> -->
                <div class="myaccount-dropdown">
                    <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="inner">
                            <div class="icon">
                                <img src="{{ Auth::guard('web')->user()->image ? asset(Auth::guard('web')->user()->image) : asset('frontend/assets/images/user.png') }}" alt="">
                            </div>
                            <!-- <div class="info">
                                <p class="name">John Doe</p>
                                <p class="user-type">Auctioneer</p>
                            </div>
                            <span class="indicator">
                                <img src="assets/images/angle-down.png" alt="">
                            </span> -->
                        </div>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <li>
                            <a class="dropdown-item" href="tel:{{Auth::guard('web')->user()->mobile ?? null}}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M11.4033 6.86696C12.1047 7.00354 12.7493 7.34592 13.2547 7.85028C13.76 8.35463 14.103 8.99801 14.2399 9.69808M11.4033 4C12.8606 4.16158 14.2195 4.81291 15.2569 5.84704C16.2943 6.88118 16.9486 8.23665 17.1123 9.69091M16.3942 15.4105V17.5607C16.395 17.7603 16.354 17.9579 16.2739 18.1408C16.1938 18.3237 16.0763 18.4879 15.9289 18.6228C15.7815 18.7578 15.6076 18.8605 15.4181 18.9244C15.2286 18.9884 15.0279 19.0121 14.8287 18.9942C12.619 18.7545 10.4963 18.0009 8.63141 16.7938C6.89632 15.6934 5.42528 14.2251 4.32274 12.4934C3.10911 10.6235 2.35385 8.49467 2.11813 6.27923C2.10019 6.08103 2.12379 5.88127 2.18743 5.69267C2.25107 5.50407 2.35337 5.33076 2.48779 5.18378C2.62222 5.0368 2.78584 4.91937 2.96823 4.83896C3.15062 4.75855 3.34779 4.71693 3.54718 4.71674H5.70151C6.05001 4.71332 6.38788 4.83649 6.65212 5.06331C6.91636 5.29012 7.08896 5.6051 7.13773 5.94953C7.22866 6.63765 7.3973 7.31329 7.64041 7.96357C7.73703 8.22011 7.75794 8.49891 7.70067 8.76695C7.64339 9.03498 7.51034 9.28101 7.31726 9.47589L6.40526 10.3861C7.42753 12.1805 8.9161 13.6663 10.7139 14.6866L11.6259 13.7763C11.8212 13.5836 12.0677 13.4508 12.3362 13.3936C12.6048 13.3365 12.8841 13.3574 13.1411 13.4538C13.7927 13.6964 14.4696 13.8648 15.159 13.9555C15.5079 14.0046 15.8265 14.18 16.0542 14.4483C16.2819 14.7165 16.4029 15.059 16.3942 15.4105Z" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                {{Auth::guard('web')->user()->mobile ?? null}}
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{route('user.profile')}}">
                                <i class="fa-solid fa-user"></i>
                            Profile
                            </a>
                        </li>
                    
                        <li>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                    <path d="M6.75 15.75H3.75C3.35218 15.75 2.97064 15.592 2.68934 15.3107C2.40804 15.0294 2.25 14.6478 2.25 14.25V3.75C2.25 3.35218 2.40804 2.97064 2.68934 2.68934C2.97064 2.40804 3.35218 2.25 3.75 2.25H6.75" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 12.75L15.75 9L12 5.25" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M15.75 9H6.75" stroke="black" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="hamburger">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42" fill="none">
                            <path d="M36.75 17.5H12.25" stroke="#014397" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M36.75 10.5H5.25" stroke="#014397" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M36.75 24.5H5.25" stroke="#014397" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div>
            </div>
            </div>
        </header>
    @else
        <header>
            <div class="container">
                <div class="header-inner without-login">
                <div class="brand">
                    <a href="{{asset('')}}">
                        <img src="{{asset('frontend/assets/images/logo.png')}}">
                    </a>
                </div>
                <div class="search-section">
                    <div class="location-bar">
                        <img src="{{asset('frontend/assets/images/location.png')}}">
                        <input type="text" placeholder="Search Location..." id="stateInput" name="global_state_name" value="@yield('location', $city_name)">
                        <div id="stateSuggestions"></div>
                    </div>
                    
                    <div class="search-bar">
                        <form action="{{route('user.global.make_slug')}}" method="GET" id="Search_form">
                            <input type="hidden" name="location" id="hidden_location" value="@yield('location', $city_name)">
                            <input type="search" name="keyword" id="autocomplete-input" placeholder="Search" value="@yield('keyword')">
                            <button type="submit" class="btn-search btn-animated" id="global_form_submit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                    <path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M20.9999 21.0004L16.6499 16.6504" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <div id="autocomplete-suggestions" class="autocomplete-suggestions"></div>
                        </form>
                    </div>
                    {{-- <div id="filterSuggestions"></div> --}}
                </div>
                
                <!-- <a href="{{route('register')}}" class="btn btn-cta btn-animated btn-start">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <g clip-path="url(#clip0_221_2270)">
                        <path d="M8.4 12.5998C3.72 12.5998 0 16.3198 0 20.9998V22.1998H16.8V20.9998C16.8 16.3198 13.08 12.5998 8.4 12.5998ZM2.52 19.7998C3.12 17.0398 5.52 14.9998 8.4 14.9998C11.28 14.9998 13.68 17.0398 14.28 19.7998H2.52ZM8.4 11.3998C11.04 11.3998 13.2 9.23981 13.2 6.5998C13.2 3.9598 11.04 1.7998 8.4 1.7998C5.76 1.7998 3.6 3.9598 3.6 6.5998C3.6 9.23981 5.76 11.3998 8.4 11.3998ZM8.4 4.1998C9.72 4.1998 10.8 5.2798 10.8 6.5998C10.8 7.91981 9.72 8.9998 8.4 8.9998C7.08 8.9998 6 7.91981 6 6.5998C6 5.2798 7.08 4.1998 8.4 4.1998ZM22.32 7.1998L18.36 11.1598L16.44 9.35981L14.76 11.0398L18.36 14.6398L24 8.87981L22.32 7.1998Z" fill="white"/>
                        </g>
                        <defs>
                        <clipPath id="clip0_221_2270">
                            <rect width="24" height="24" fill="white"/>
                        </clipPath>
                        </defs>
                    </svg>
                    &nbsp;&nbsp;Get Started
                </a> -->
                <a href="{{route('login')}}" class="btn btn-cta btn-normal btn-login">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M15 3H19C19.5304 3 20.0391 3.21071 20.4142 3.58579C20.7893 3.96086 21 4.46957 21 5V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H15" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10 17L15 12L10 7" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15 12H3" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    &nbsp;&nbsp;Login
                </a>
                <!-- <div class="hamburger">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42" fill="none">
                            <path d="M36.75 17.5H12.25" stroke="#014397" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M36.75 10.5H5.25" stroke="#014397" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M36.75 24.5H5.25" stroke="#014397" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                </div> -->
            </div>
            </div>
        </header>
    @endif
    <div class="page-loader " style="display: none;">
        <div class="spinner"></div>
        <p class="txt">Please Wait...</p>
    </div>
    @yield('section')
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>
    <footer>
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="content">
                            <h2>Start Your Journey Today</h2>
                            <a href="#" class="btn btn-cta btn-animated btn-yellow">Request a free demo</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <div class="row">
                            <div class="col-md-7 col-12 first-col">
                                <div class="footer-logo">
                                    <a href="#">
                                        <img src="{{asset('frontend/assets/images/Auction-white.png')}}" alt="">
                                    </a>
                                </div>
                                <div class="footer-description">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form</div>
                                <h4>Contact Info</h4>
                                {{-- {{dd($settings)}} --}}
                                <ul class="contact-info-list">
                                    <li>
                                        <img src="{{asset('frontend/assets/images/map-pin.png')}}" alt="">
                                         {{$settings['company_full_address'] ?? 'Address Not available'}}
                                    </li>
                                    <li>
                                        <img src="{{asset('frontend/assets/images/phone-call.png')}}" alt="">
                                        <a href="tel:{{$settings['official_phone1'] ?? '#'}}" >{{$settings['official_phone1'] ?? 'Phone number not available'}}</a>
                                    </li>
                                    <li>
                                        <img src="{{asset('frontend/assets/images/mail.png')}}" alt="">
                                        <a href="mailto:{{$settings['official_email'] ?? '#'}}" >{{$settings['official_email'] ?? 'Email not available'}}</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-5 col-12 second-col">
                                <h4>Company</h4>
                                <ul class="footer-menu">
                                    <li><a href="{{route('front.about-us')}}">About Us</a></li>
                                    <li><a href="{{route('front.contact-us')}}">Contact Us</a></li>
                                    {{-- <li><a href="#">SP TMS</a></li> --}}
                                    <li><a href="#">Career</a></li>
                                    <li><a href="{{route('front.terms-and-conditions')}}">Terms of Use</a></li>
                                    <li><a href="{{route('front.privacy_policy')}}">Privacy & Policy</a></li>
                                    {{-- <li><a href="#">Disclaim</a></li> --}}
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-12">
                        <div class="row">
                            <div class="col-xl-7 col-12 third-col">
                                <h4>Popular Category</h4>
                                <div class="row">
                                    <div class="col-sm-6 col-12">
                                        <ul class="footer-menu">
                                            @foreach ($categories as $k =>$category)
                                            @if($k<7)
                                              <li><a href="#">{{$category->title}} </a></li>
                                            @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div class="col-sm-6 col-12">
                                        <ul class="footer-menu">
                                            @foreach ($categories as $k =>$category)
                                                @if($k>7)
                                                <li><a href="#">{{$category->title}} </a></li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-5 col-12 fourth-col">
                                <h4>Follow us on</h4>
                                <ul class="footer-social-menu">
                                    @foreach ($socialmedias as $socialmedia)
                                    <li>
                                        <a href="{{$socialmedia->link}}" target="_blank">
                                            <img src="{{asset($socialmedia->logo)}}" alt="{{$socialmedia->title}}" width="40px">
                                        </a>
                                    </li>
                                    @endforeach
                                    
                                    {{-- <li>
                                        <a href="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                                                <path d="M28.75 3.74987C27.553 4.59422 26.2276 5.24001 24.825 5.66237C24.0722 4.79676 23.0717 4.18324 21.9588 3.90478C20.8459 3.62632 19.6744 3.69637 18.6026 4.10544C17.5308 4.51451 16.6106 5.24288 15.9662 6.19202C15.3219 7.14116 14.9846 8.26529 15 9.41237V10.6624C12.8033 10.7193 10.6266 10.2321 8.66376 9.24418C6.70093 8.25622 5.0129 6.79817 3.75 4.99987C3.75 4.99987 -1.25 16.2499 10 21.2499C7.42566 22.9973 4.35895 23.8735 1.25 23.7499C12.5 29.9999 26.25 23.7499 26.25 9.37487C26.2488 9.02669 26.2154 8.67937 26.15 8.33737C27.4258 7.07924 28.326 5.49077 28.75 3.74987Z" stroke="#FFB800" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                                                <path d="M21.25 2.5H8.75C5.29822 2.5 2.5 5.29822 2.5 8.75V21.25C2.5 24.7018 5.29822 27.5 8.75 27.5H21.25C24.7018 27.5 27.5 24.7018 27.5 21.25V8.75C27.5 5.29822 24.7018 2.5 21.25 2.5Z" stroke="#FFB800" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M20.0001 14.2129C20.1544 15.2532 19.9767 16.3156 19.4923 17.2491C19.0079 18.1826 18.2416 18.9396 17.3022 19.4125C16.3628 19.8853 15.2982 20.0499 14.2599 19.8828C13.2215 19.7157 12.2623 19.2255 11.5187 18.4818C10.775 17.7382 10.2848 16.779 10.1177 15.7406C9.95063 14.7023 10.1152 13.6377 10.588 12.6983C11.0609 11.7589 11.8179 10.9926 12.7514 10.5082C13.6849 10.0238 14.7473 9.84611 15.7876 10.0004C16.8488 10.1577 17.8312 10.6522 18.5897 11.4108C19.3483 12.1693 19.8428 13.1517 20.0001 14.2129Z" stroke="#FFB800" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M21.875 8.125H21.8875" stroke="#FFB800" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                                                <path d="M28.1752 8.025C28.0267 7.43176 27.7243 6.88821 27.2985 6.44926C26.8728 6.0103 26.3387 5.69148 25.7502 5.525C23.6002 5 15.0002 5 15.0002 5C15.0002 5 6.40023 5 4.25023 5.575C3.66179 5.74148 3.1277 6.0603 2.70191 6.49926C2.27612 6.93821 1.97371 7.48176 1.82523 8.075C1.43174 10.2569 1.23927 12.4704 1.25023 14.6875C1.2362 16.9213 1.42869 19.1516 1.82523 21.35C1.98893 21.9248 2.29811 22.4477 2.72291 22.8681C3.1477 23.2885 3.67375 23.5923 4.25023 23.75C6.40023 24.325 15.0002 24.325 15.0002 24.325C15.0002 24.325 23.6002 24.325 25.7502 23.75C26.3387 23.5835 26.8728 23.2647 27.2985 22.8257C27.7243 22.3868 28.0267 21.8432 28.1752 21.25C28.5657 19.0845 28.7581 16.8879 28.7502 14.6875C28.7643 12.4537 28.5718 10.2234 28.1752 8.025Z" stroke="#FFB800" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M12.1875 18.7746L19.375 14.6871L12.1875 10.5996V18.7746Z" stroke="#FFB800" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                    </li> --}}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-copyright">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="content">
                            <div class="copyright">Copyright &copy; {{date('Y')}} | All rights reserved.</div>
                            <ul class="policy-menu">
                                <li><a href="{{route('front.terms-and-conditions')}}">Terms & Conditions</a></li>
                                <li><a href="{{route('front.privacy_policy')}}">Privacy & Policy</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- Modal -->
    <div class="modal fade" id="profileModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            ...
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary">Understood</button>
            </div>
        </div>
        </div>
    </div>
    <script src="{{asset('frontend/assets/js/jquery.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/fontawesome.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/swiper-bundle.min.js')}}"></script>
    <script src="{{asset('frontend/assets/js/chart.min.js')}}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @yield('script')
    <script src="{{asset('frontend/assets/js/custom.js')}}"></script>
    <script>
         $(document).ready(function() {
            @if (session('success'))
                toastr.success('{{ session('success') }}');
            @endif
            @if (session('warning'))
                toastr.warning('{{ session('warning') }}');
            @endif
            @if (session('error'))
                toastr.error('{{ session('error') }}');
            @endif
        });

        $(document).ready(function() {
            @if(session('showProfileModal'))
            Swal.fire({
                title: "Please update your profile!",
                showClass: {
                    popup: `
                        animate__animated
                        animate__fadeInUp
                        animate__faster
                    `
                },
                hideClass: {
                    popup: `
                        animate__animated
                        animate__fadeOutDown
                        animate__faster
                    `
                },
                showCancelButton: true, // Show cancel button
                cancelButtonText: "Cancel",
                confirmButtonText: "OK"
                
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('user.profile.edit') }}";
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // Handle cancel button clicked
                    // You can add any specific action here if needed
                }
            });
            @endif
        });

        $('#autocomplete-input').on('keyup', function() {
            var keyword = $(this).val().toLowerCase();
            var location = $('#stateInput').val();
            const autocompleteList = $('#autocomplete-list');
            autocompleteList.empty();
            if (!keyword) {
                return false;
            }
                    // Reset border to default
            $('.location-bar').css('border', '1px solid #ced4da');
            $('.search-bar').css('border', '1px solid #ced4da');

            
            // Check if location is empty
            if(location.trim().length === 0){
                $('.location-bar').css('border', '1px solid red');
                return; // Stop further execution  
            }
            $.ajax({
                url: "{{route('user.suggestion')}}", // Replace this with your actual route
                type: 'GET',
                data: {
                    location: location,
                    keyword: keyword
                },
                success: function(response) {
                var html= "";
                response.forEach(function(element, index) {
                    html += `<div class="autocomplete-suggestion filter_data" data-value="${element.title}">
                        <div class="suggestion-icon">
                            <img src="{{asset('frontend/assets/images/${element.image}')}}" alt="" width="65%" height="65%">
                        </div>
                        <div class="suggestion-right">
                            <div class="autocomplete-business-name">${element.title}</div>
                            <div class="autocomplete-category-name">${element.sub_title}</div>
                        </div>
                    </div>`;
                });
                    $('#autocomplete-suggestions').html(html);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    // Handle errors if any
                }
            });
        });

        $('#global_form_submit').on('click', function(event) {
            event.preventDefault();

            //  var location = $('#stateInput').val();
             var location = $('#hidden_location').val();
             
             var keyword = $('#autocomplete-input').val();
             
             // Reset border to default
             $('.location-bar').css('border', '1px solid #ced4da');
             $('.search-bar').css('border', '1px solid #ced4da');
 
             // Check if location is empty
             if(location.trim().length === 0){
                 $('.location-bar').css('border', '1px solid red');
                 return; // Stop further execution  
             }
             
             // Check if keyword is empty
             if(keyword.trim().length === 0){
                 $('.search-bar').css('border', '1px solid red');
                 return; // Stop further execution
             }
             $('#Search_form').submit();
            //  $.ajax({
            //      url: "{{route('user.global.make_slug')}}", // Replace this with your actual route
            //      type: 'GET',
            //      data: {
            //          location: location,
            //          keyword: keyword
            //      },
            //      success: function(response) {
            //          if(response.status==200){
            //              window.location.href = response.route;
            //          }
                     
            //      },
            //      error: function(xhr, status, error) {
            //          console.error(error);
            //          // Handle errors if any
            //      }
            //  });
         });
 
 
             $(document).ready(function(){
                 // List of Indian states
                 var indianStates = {!! json_encode($global_filter_location) !!};
                 function showStateSuggestions(input) {
                     var filter = input.value.toLowerCase();
                     var suggestions = indianStates.filter(function(state) {
                     return state.toLowerCase().indexOf(filter) > -1;
                     });
                     var html = '<ul>';
                     suggestions.forEach(function(state) {
                     html += '<li>' + state + '</li>';
                     });
                     html += '</ul>';
                     $('#stateSuggestions').html(html);
                 }
 
                 // Handle keyup event on the input field
                 $('#stateInput').on('keyup', function() {
                     showStateSuggestions(this);
                 });
 
                 // Handle click event on state suggestion
                 $('#stateSuggestions').on('click', 'li', function() {
                     $('#stateInput').val($(this).text());
                     $('#hidden_location').val($(this).text());
                     $('#stateSuggestions').html('');
                 });
 
                 // Hide suggestions when clicking outside
                 $(document).on('click', function(e) {
                     if (!$(e.target).closest('#stateSuggestions').length && !$(e.target).is('#stateInput')) {
                     $('#stateSuggestions').html('');
                     }
                 });
             });
             $(document).ready(function() {
                $('.autocomplete-suggestions').html('');
                // Handle click event on state suggestion
                $('.autocomplete-suggestions').on('click', '.autocomplete-suggestion', function() {
                    var data_value = $(this).attr('data-value');
                    $('#autocomplete-input').val(data_value);
                    $('#Search_form').submit();
                    $('.autocomplete-suggestions').html(''); // Clear the suggestions list
                });
            });
 
     </script>
    <script>
         $(document).ready(function(){
             // Hide the success message after 5 seconds (5000 milliseconds)
             setTimeout(function() {
                 $('#successAlert').fadeOut('slow');
             }, 3000);
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
        // Fetch notifications and update count
        function ViewNotification(link, id) {
              $.ajax({
                 url: "{{route('user.notifications_marks_read')}}", // Replace this with your actual route
                 type: 'GET',
                 data: {
                    id: id,
                 },
                 success: function(response) {
                     if(response.status==200){
                         window.location.href = link;
                     }else{
                        // window.location.href = link;
                     }
                 },
                 error: function(xhr, status, error) {
                    // window.location.href = link;
                 }
             });
        }   
        // $(document).ready(function() {
        //     // Select button with type="submit"
        //     $('button[type="submit"]').on('click', function(event) {
        //         // Prevent form submission to demonstrate the change (if needed)
        //         event.preventDefault();

        //         // Change button text and disable it
        //         // $(this).text('Please wait...').prop('disabled', true);

        //         // You can also perform additional actions here, like submitting the form
        //         // For example, uncomment the line below to actually submit the form
        //         // $(this).closest('form').submit();
        //     });
        // });
    </script>
    
</body>
</html>