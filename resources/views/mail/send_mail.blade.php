<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MILAAPP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    @if($type=="REG_OTP")
        <section class="otp_section" style="padding: 30px 0px;">
            <div class="container-fluid">
                <h5 style="text-align: center; font-size: 20px; font-weight: 600; color: rgb(44, 159, 98); margin-bottom: 10px;">
                    Your One Time Password ({{$user->otp}})</h5>
                <p style="text-align: center; font-size: 16px;">Dear Sir/Madam,</p>
                <p style="text-align: center; font-size: 16px;">Your One Time Password to log in to MILAAPP is <strong>{{$user->otp}}</strong>.</p>
                <p style="text-align: center; font-size: 16px; color: #000;"><strong>Powered by MILAAPP</strong></p>
                <div class="link_sec" style="text-align: center; margin: 20px 0px;">
                   
                    <div class="all_link" style="margin-top: 20px;">
                        <a href="mailto:{{env('MAIL_CONTACT_ADDRESS')}}" style="padding: 0px 5px; color: #000;">Email: {{env('MAIL_CONTACT_ADDRESS')}}</a>
                        <a href="tel:{{env('MAIL_CONTACT_NUMBER')}}" style="padding: 0px 5px; color: #000;">Phone number: {{env('MAIL_CONTACT_NUMBER')}}</a>

                    </div>
                    <a href="#" style="text-decoration: none; color: #000;"> Marketplace + Auction = More Savings + More Business</a>
                </div>
            </div>
        </section>
    @endif
    @if($type==="INQUIRY_GENERATION")

        <section>
            <div style="max-width: 600px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                @if($inquiry_data->inquiry_type ==="open auction")
                    {{ $Buyer_data->business_name }} : INQUIRY POSTED
                @else
                    NEW INQUIRY POSTED
                @endif
                <p style="margin: 10px 0; color: #666;">Hey,</p>
                @if($inquiry_data->inquiry_type ==="open auction")
                    <p style="margin: 10px 0; color: #666;">Inquiry posted by {{ $Buyer_data->business_name }}</p>
                @endif
             
                
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 13px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Inquiry ID</th>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Title</th>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Category / Sub Category</th>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Start Date & Time</th>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">End Date & Time</th>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Service Delivery Date</th>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Min Amount</th>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Max Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Populate with actual inquiry details -->
                        <tr>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{ $inquiry_data->inquiry_id }}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{ $inquiry_data->title }}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{ $inquiry_data->category }}/{{ $inquiry_data->sub_category }}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{ date('d-m-Y', strtotime($inquiry_data->start_date)) }} {{ $inquiry_data->start_time }}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{ date('d-m-Y', strtotime($inquiry_data->end_date)) }} {{ $inquiry_data->end_time }}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{ date('d-m-Y', strtotime($inquiry_data->execution_date)) }}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{ $inquiry_data->minimum_quote_amount }}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{ $inquiry_data->maximum_quote_amount }}</td>
                        </tr>
                    </tbody>
                </table>
               
                <p style="margin: 10px 0; color: #666;">Description of service:</p>
                <p style="margin: 10px 0; color: #666;">{!! $inquiry_data->description !!}</p>
                {{-- For Participants --}}
                @if($user_type=="Buyer" && count($participants)>0)
                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 13px;">
                        <thead>
                            <tr>
                                <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">SL No</th>
                                <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Person Name</th>
                                <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Company Name</th>
                                <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Phone Number</th>
                                <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Rank</th>
                                <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Last Quoted Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Populate with actual inquiry details -->
                            @foreach ($participants as $k=> $item)
                           
                                <tr>
                                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $k+1 }}</td>
                                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $item->SellerData?$item->SellerData->name:"" }}</td>
                                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $item->SellerData?$item->SellerData->business_name:"" }}</td>
                                    <td style="border: 1px solid #ccc; padding: 8px;">{{ $item->SellerData?$item->SellerData->mobile:"" }}</td>
                                    <td style="border: 1px solid #ccc; padding: 8px;"></td>
                                    <td style="border: 1px solid #ccc; padding: 8px;"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                @endif
                <p style="margin: 10px 0; color: #666;">This is a system-generated mail so please do not reply to this mail.</p>
                @if($user_type=="Seller")
                    <p style="margin: 10px 0; text-align:center">
                        <a href="{{route('seller_all_inquiries')}}" style="display: inline-block;position: relative;color: #fff;border-radius: 50px; background-color: #014397;transition: all .3s ease-out !important;height: 37px;font-size: 12px;padding: 0 15px;line-height: 34px; text-decoration:none;"
                        target="_blank">BID NOW</a> <br> bidding link to be provided here
                    </p>
                @endif
                <p style="text-align: center; font-size: 16px; color: #000;"><strong>Powered by MILAAPP</strong></p>
                <div class="link_sec" style="text-align: center; margin: 20px 0px; background:#014397; padding:5px; color:#fff;">
                    <div class="all_link" style="margin-top: 20px;">
                        <a href="mailto:{{env('MAIL_CONTACT_ADDRESS')}}" style="padding: 0px 5px; color: #fff;">Email: {{env('MAIL_CONTACT_ADDRESS')}}</a>
                        <a href="tel:{{env('MAIL_CONTACT_NUMBER')}}" style="padding: 0px 5px; color: #fff;">Phone number: {{env('MAIL_CONTACT_NUMBER')}}</a>
                    </div>
                    
                    <a href="#" style="text-decoration: none; color: #fff;"> Marketplace + Auction = More Savings + More Business</a>
                    <p style="margin: 5px 0;">For registration please find the link: <a href="https://milaapp.in/register" style="color: #007bff; text-decoration: none;" target="_blank">registration link</a></p>
                </div>
            </div>
        </section>
    @endif
    @if($type=="INQUIRY_ALLOTMENT")
        <section>
            <div style="max-width: 600px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                <h2 style="font-size: 24px; color: #333; margin-top: 0;">
                    {{ $Buyer_data->business_name }} : INQUIRY ALLOTMENT
                </h2>
                <p style="margin: 10px 0; color: #666;">Hey,</p>
                <p style="margin: 10px 0; color: #666;">Dear  {{$user->business_name}} ,</p>
                <p style="margin: 10px 0; color: #666;">Please find inquiry allotment for {{ $Buyer_data->business_name }}</p>
                
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 13px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Inquiry ID</th>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Title</th>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Category / Sub Category</th>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Service Delivery</th>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Alloted Amount</th>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Commets</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{ $inquiry_data->inquiry_id }}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{ $inquiry_data->title }}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{ $inquiry_data->category }}/{{ $inquiry_data->sub_category }}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{date('d-m-Y', strtotime($inquiry_data->execution_date))}}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">Rs.{{$inquiry_data->inquiry_amount}}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;"></td>
                        </tr>
                    </tbody>
                </table>
                
                <p style="margin: 10px 0; color: #666;">Description of service:</p>
                <p style="margin: 10px 0; color: #666;">{!! $inquiry_data->description !!}</p>
                
                <p style="margin: 10px 0; color: #666;">This is a system-generated mail so please do not reply to this mail.</p>
                <p style="margin: 10px 0; color: #666;">{{$Buyer_data->business_name}}</p>
                <p style="margin: 10px 0; color: #666;">{{$Buyer_data->name}}</p>
                <p style="margin: 10px 0; color: #666;">{{$Buyer_data->mobile}}</p>
                
                <p style="text-align: center; font-size: 16px; color: #000;"><strong>Powered by MILAAPP</strong></p>
                
                <div class="link_sec" style="text-align: center; margin: 20px 0px; background:#014397; padding:5px; color:#fff;">
                    <div class="all_link" style="margin-top: 20px;">
                        <a href="mailto:{{ env('MAIL_CONTACT_ADDRESS') }}" style="padding: 0px 5px; color: #fff;">Email: {{ env('MAIL_CONTACT_ADDRESS') }}</a>
                        <a href="tel:{{ env('MAIL_CONTACT_NUMBER') }}" style="padding: 0px 5px; color: #fff;">Phone number: {{ env('MAIL_CONTACT_NUMBER') }}</a>
                    </div>
                    <a href="#" style="text-decoration: none; color: #fff;"> Marketplace + Auction = More Savings + More Business</a>
                    <p style="margin: 5px 0;">For registration please find the link: <a href="https://milaapp.in/register" style="color: #007bff; text-decoration: none;" target="_blank">registration link</a></p>
                </div>
            </div>
        </section>
    @endif
    @if($type=="INQUIRY_REALLOTMENT")
        <section>
            <div style="max-width: 600px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                <h2 style="font-size: 24px; color: #333; margin-top: 0;">
                    {{ $Buyer_data->business_name }} : INQUIRY REALLOTMENT
                </h2>
                <p style="margin: 10px 0; color: #666;">Hey,</p>
                <p style="margin: 10px 0; color: #666;">Dear  {{$user->business_name}} ,</p>
                <p style="margin: 10px 0; color: #666;">Please find inquiry reallotment for {{ $Buyer_data->business_name }}</p>
                
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 13px;">
                    <thead>
                        <tr>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Inquiry ID</th>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Title</th>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Category / Sub Category</th>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Service Delivery</th>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Realloted Amount</th>
                            <th style="border: 1px solid #ccc; padding: 8px; background-color: #f2f2f2;">Commets</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{ $inquiry_data->inquiry_id }}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{ $inquiry_data->title }}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{ $inquiry_data->category }}/{{ $inquiry_data->sub_category }}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{date('d-m-Y', strtotime($inquiry_data->execution_date))}}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">Rs.{{$inquiry_data->inquiry_amount}}</td>
                            <td style="border: 1px solid #ccc; padding: 8px;">{{$reason}}</td>
                        </tr>
                    </tbody>
                </table>
                
                <p style="margin: 10px 0; color: #666;">Description of service:</p>
                <p style="margin: 10px 0; color: #666;">{!! $inquiry_data->description !!}</p>
                
                <p style="margin: 10px 0; color: #666;">This is a system-generated mail so please do not reply to this mail.</p>
                <p style="margin: 10px 0; color: #666;">{{$Buyer_data->business_name}}</p>
                <p style="margin: 10px 0; color: #666;">{{$Buyer_data->name}}</p>
                <p style="margin: 10px 0; color: #666;">{{$Buyer_data->mobile}}</p>
                
                <p style="text-align: center; font-size: 16px; color: #000;"><strong>Powered by MILAAPP</strong></p>
                
                <div class="link_sec" style="text-align: center; margin: 20px 0px; background:#014397; padding:5px; color:#fff;">
                    <div class="all_link" style="margin-top: 20px;">
                        <a href="mailto:{{ env('MAIL_CONTACT_ADDRESS') }}" style="padding: 0px 5px; color: #fff;">Email: {{ env('MAIL_CONTACT_ADDRESS') }}</a>
                        <a href="tel:{{ env('MAIL_CONTACT_NUMBER') }}" style="padding: 0px 5px; color: #fff;">Phone number: {{ env('MAIL_CONTACT_NUMBER') }}</a>
                    </div>
                    <a href="#" style="text-decoration: none; color: #fff;"> Marketplace + Auction = More Savings + More Business</a>
                    <p style="margin: 5px 0;">For registration please find the link: <a href="https://milaapp.in/register" style="color: #007bff; text-decoration: none;" target="_blank">registration link</a></p>
                </div>
            </div>
        </section>
    @endif
    @if($type=="PAYMENT_TRANSACTION")
        <section>
            <div style="max-width: 600px; margin: 20px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
                <p><strong> Dear {{$user->name}},</strong></p>
                <p>We hope this email finds you well.</p>
                <p>We are writing to confirm the details of your recent transaction on Milaap. Please find the specifics of your payment below:</p>
                
               <p><strong>Transaction Details:</strong></p>
                
                <ul>
                    <li><strong>Transaction Type</strong> : {{$transaction_type}}</li>
                    <li><strong>Transaction Amount</strong> : {{$transaction->amount}}</li>
                    <li><strong>Validity Start Date</strong> : {{$start_date}}</li>
                    <li><strong>Expiry Date</strong> :{{$end_date}}</li>
                </ul>
                <p>Please note that the validity of your purchase starts from the date mentioned above. We encourage you to utilize the purchased services or products before the expiry date to ensure maximum benefit.</p>
                <p>For your convenience, an invoice for this transaction is attached to this email.</p>
                <p> Should you have any questions or require further assistance, please do not hesitate to contact our customer support team at [{{env('HELPLINE_MAIL')}}/{{env('HELPLINE_PHONE')}}]. We are here to help you with any queries you may have.</p>
                <p>Thank you for choosing Milaap for your shopping needs. We appreciate your trust in our platform and look forward to serving you again.</p>
                <p style="margin: 0px;">Best regards,</p>
                <p style="margin: 0px;"><strong>The Milaap Team</strong></p>
                <p style="margin: 0px;"><a href="https://milaapp.in/">https://milaapp.in/</a></p>
                <div class="link_sec" style="text-align: center; margin: 20px 0px; background:#014397; padding:5px; color:#fff;">
                    <div class="all_link" style="margin-top: 20px;">
                        <a href="mailto:{{ env('MAIL_CONTACT_ADDRESS') }}" style="padding: 0px 5px; color: #fff;">Email: {{ env('MAIL_CONTACT_ADDRESS') }}</a>
                        <a href="tel:{{ env('MAIL_CONTACT_NUMBER') }}" style="padding: 0px 5px; color: #fff;">Phone number: {{ env('MAIL_CONTACT_NUMBER') }}</a>
                    </div>
                    <a href="#" style="text-decoration: none; color: #fff;"> Marketplace + Auction = More Savings + More Business</a>
                    <p style="margin: 5px 0;">For registration please find the link: <a href="https://milaapp.in/register" style="color: #007bff; text-decoration: none;" target="_blank">registration link</a></p>
                </div>
            </div>
        </section>
    @endif
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>