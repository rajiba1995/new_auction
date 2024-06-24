<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auction Pdf</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    {{-- <section class="crop_section" style="padding: 30px 0px;">
        <div class="container-fluid">
            <div class="check_img" style="text-align: center;margin-bottom: 20px;">
                <img style="width: 100px; height: 100px;" src="https://images.vexels.com/media/users/3/157931/isolated/preview/604a0cadf94914c7ee6c6e552e9b4487-curved-check-mark-circle-icon.png" alt="">
            </div>
            <h5
                style="text-align: center;font-size: 20px; font-weight: 600;color: rgb(44, 159, 98);margin-bottom: 10px;">
                Thank You For Your Order!</h5>
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptatem quisquam, at obcaecati amet ipsa
                ipsam</p>
            <ul style="padding: 0;margin-bottom: 40px;">
                <li
                    style="display: flex;justify-content: space-between;padding: 15px 20px; list-style: none; font-size: 14px;font-weight: 500; background-color: rgb(221, 243, 251);padding-left: 10px;">
                    <span>Order Confirmation No</span>
                    <span>#2345678</span>
                </li>
                <li
                    style="display: flex;justify-content: space-between;padding: 15px 20px ; list-style: none; font-size: 14px;font-weight: 500; text-align: left;padding-left: 10px;">
                    <span>Purchased Item (1)</span>
                    <span>$100.00</span>
                </li>
                <li
                    style="display: flex;justify-content: space-between;padding: 15px 20px; list-style: none; font-size: 14px;font-weight: 500; text-align: left;padding-left: 10px;">
                    <span>Shipping + Handling</span>
                    <span>$10.00</span>
                </li>
                <li
                    style="display: flex;justify-content: space-between;padding: 15px 20px; list-style: none; font-size: 14px;font-weight: 500; text-align: left;padding-left: 10px;">
                    <span>Sales Tax</span>
                    <span>$55.00</span>
                </li>
                <li
                    style="display: flex;justify-content: space-between;padding: 15px 20px; list-style: none; font-size: 16px;font-weight: 600; text-align: left;border-top: 1px solid #d1d0d0;border-bottom: 1px solid #d1d0d0;padding-left: 10px;">
                    <span>TOTAL</span>
                    <span>$115.00</span>
                </li>
            </ul>
            <div class="address_info" style="display: flex;margin-bottom: 20px;">
                <div class="address" style="margin-right: 70px;">
                    <h5 style="font-size: 16px;font-weight: 500;">Delivery Address</h5>
                    <p style="max-width: 200px; font-size: 14px; font-weight: 400;">675 Massachusetts Avenue 11th Floor
                        Cambridge, MA 02139</p>
                </div>
                <div class="deliverydate">
                    <h5 style="font-size: 16px;font-weight: 500;">Estimated Delivery Date</h5>
                    <p style="max-width: 200px; font-size: 14px; font-weight: 400;">January 1st, 2024</p>
                </div>
            </div>

        </div>
        <div class="button_sec" style="height: 200px; background-color: #003B6B; text-align: center; ">
            <h5
                style="padding-top: 50px;color: #fff;font-size: 18px;font-weight: 500;padding-bottom: 25px;text-transform: capitalize;">
                Get 25%
                off your next order.</h5>
            <a href="#"
                style="text-decoration: none;color: #fff;padding: 10px 20px;background-color:#007aff ;font-size: 14px;font-weight: 500;border-radius: 4px;">Awesome</a>
        </div>
        <div class="link_sec" style="text-align: center;margin: 20px 0px;">
            <a href="#" style="text-decoration: none; color: #000;">Any address information, legal, terms etc to be
                added here</a>
            <div class="all_link" style="margin-top: 20px;">
                <a href="#" style="padding: 0px 5px; color: #000;">Email Preferences</a>
                <a href="#" style="padding: 0px 5px; color: #000;">Unsubscribe</a>
                <a href="#" style="padding: 0px 5px; color: #000;">View Online</a>
            </div>
        </div>
    </section> --}}
    @if($type=="REG_OTP")
    {{dd($user->otp)}}
        <section class="otp_section" style="padding: 30px 0px;">
            <div class="container-fluid">
                <div class="check_img" style="text-align: center; margin-bottom: 20px;">
                    <img style="width: 100px; height: 100px;" src="https://images.vexels.com/media/users/3/157931/isolated/preview/604a0cadf94914c7ee6c6e552e9b4487-curved-check-mark-circle-icon.png" alt="Check Mark Icon">
                </div>
                <h5 style="text-align: center; font-size: 20px; font-weight: 600; color: rgb(44, 159, 98); margin-bottom: 10px;">
                    Your One Time Password ({{$user->otp}})</h5>
                <p style="text-align: center; font-size: 16px;">Dear Sir/Madam,</p>
                <p style="text-align: center; font-size: 16px;">Your One Time Password to log in to MILAAPP is <strong>{{$user->otp}}</strong>.</p>
                <p style="text-align: center; font-size: 16px;">Please do not share this OTP with anyone for security reasons.</p>
                <div class="button_sec" style="height: 200px; background-color: #003B6B; text-align: center;">
                    <a href="#" style="text-decoration: none; color: #fff; padding: 10px 20px; background-color:#007aff; font-size: 14px; font-weight: 500; border-radius: 4px;">Awesome</a>
                </div>
                <div class="link_sec" style="text-align: center; margin: 20px 0px;">
                    <a href="#" style="text-decoration: none; color: #000;">Any address information, legal terms, etc. to be added here</a>
                    <div class="all_link" style="margin-top: 20px;">
                        <a href="mailto:Email:helpdesk@milaapp.in" style="padding: 0px 5px; color: #000;">Email:helpdesk@milaapp.in</a>
                        <a href="#" style="padding: 0px 5px; color: #000;">Unsubscribe</a>
                        <a href="#" style="padding: 0px 5px; color: #000;">View Online</a>
                    </div>
                </div>
            </div>
        </section>
    @endif
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>