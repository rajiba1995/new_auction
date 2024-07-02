@extends('front.layout.app')

@section('section')
<style>
    .contact-info-list {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }
    .contact-info-list li {
        display: flex;
        align-items: flex-start;
        font-size: 14px;
        font-weight: 400;
        line-height: 23px;

    }
</style>
    <div class="main">
        <div class="inner-page">
            <div class="container my-4" style="text-align: justify;">
                    <h3>Contact Us</h3>
                    <ul class="contact-info-list">
                        <li>
                            <img src="{{asset('frontend/assets/images/map-pin.png')}}" alt="">
                            9B school row bhawanipore, kolkata 700025
                        </li>
                        <li>
                            <img src="{{asset('frontend/assets/images/phone-call.png')}}" alt="">
                            <a href="#">+91-9330921674</a>
                        </li>
                        <li>
                            <img src="{{asset('frontend/assets/images/mail.png')}}" alt="">
                            <a href="#">customercare@milaapp.in</a>
                        </li>
                    </ul>
                
            </div>
        </div>
    </div>
@endsection