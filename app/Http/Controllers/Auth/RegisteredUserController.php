<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Inquiry;
use App\Models\InquiryParticipant;
use App\Models\InquiryOutsideParticipant;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
    public function RegisterCheck(Request $request) {
        try {
            DB::beginTransaction(); // Start a transaction
    
            $User = User::where('email', $request->email)
                        ->orWhere('mobile', $request->phone)->first();
            if ($User) {
                $exist_User = User::where(function ($query) use ($request) {
                    $query->where('email', $request->email)
                          ->orWhere('mobile', $request->phone);
                })
                ->where('mobile_status', 0)
                ->first();
                if($exist_User){
                    $exist_User->otp = rand(1111,9999);
                    $exist_User->save();
                    session(['user' => $exist_User]);
                    $data=[
                        'cc'=>[],
                        'user'=>$exist_User,
                        'type'=>'REG_OTP',
                        'user_type'=>'Seller',
                    ];
                    
                    $mail = sendMail($data,$exist_User->email,'OTP Verification'); // Assuming sendMail function exists
                    DB::commit(); // Commit the transaction
                    $route = route('front.otp_validation');
                    $endTime = Carbon::now()->addSeconds(60);
                    session(['counter_timer' => $endTime->format('Y-m-d H:i:s')]);
                    return response()->json(['status' => 200, 'route' => $route]);
                }
                DB::rollBack();
                return response()->json(['status' => 500]); // User already exists
            } else {
                $fullName = $request->full_name;        
                $nameParts = explode(' ', $fullName);
                $firstName = $nameParts[0];   
                $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : null;  
                $User = new User;
                $User->name = $fullName;
                $User->first_name =  $firstName;
                $User->last_name =  $lastName;
                $User->email = $request->email;
                $User->mobile = $request->phone;
                $User->email_status = 1;
                $User->otp = rand(1111,9999);
                $User->password = Hash::make($request->password);
                $User->business_name = $request->business_name;
                $User->slug_business_name = slugGenerateForBusinessName($request->business_name,'users');
                $User->save();
                // You may perform additional operations within the transaction here
    
                session(['user' => $User]);
                
                $data=[
                    'cc'=>[],
                    'user'=>$User,
                    'type'=>'REG_OTP',
                    'user_type'=>'Seller',
                ];
               
                $sender = env('SMS_SENDER');
                $user_otp = $User->otp;
                $name = $User->name;
                $customer_mobile_no = $User->mobile; // Mobile number to send the SMS to
                $myMessage = urlencode("Dear ".$name." OTP for new registration is ".$user_otp." Please enter this to verify your identity and proceed with the registration request.Sarv-Megh Technology (OPC) Private Limited");
                // New URL format
                $checkPhoneNumberValid = checkPhoneNumberValid($customer_mobile_no);
                if($checkPhoneNumberValid){
                    sendSMS($sender, $customer_mobile_no, $myMessage);
                }
               
                $mail = sendMail($data, $User->email,'OTP Verification'); // Assuming sendMail function exists
                DB::commit(); // Commit the transaction
    
                $route = route('front.otp_validation');
                $endTime = Carbon::now()->addSeconds(60);
                session(['counter_timer' => $endTime->format('Y-m-d H:i:s')]);
                return response()->json(['status' => 200, 'route' => $route]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); // Roll back the transaction on exception
            // Handle the exception as per your application's requirement
            // dd($e->getMessage());
            return response()->json(['status' => 500, 'message' => $e->getMessage()]);
        }
    }
    
    public function UserVerifyData(Request $request){
        $user = Session::get('user');
        if($user->email_status==1 && $user->mobile_status==1){
            return redirect()->route('login');
        }else{
            $user_data = User::where('mobile', $user->mobile)->where('email', $user->email)->first();
            $otp = $user_data->otp;
            if($user_data->mobile_status=="0"){
                $user_data->otp = $otp; 
                $user_data->save();
                session(['user' => $user_data]);
                session(['otp' => $otp]);
            }
            if($user->email_status=="0"){
                $user->otp = $otp;
                $user->save();
                session(['user' => $user_data]);
                session(['otp' => $otp]);
            }
        }
        return view('auth.otp_validation');
    }
    public function resend_otp_validation(Request $request){
        $exist_User =User::where('id',$request->user_id)->first();
        if($exist_User){
            $exist_User->otp = rand(1111,9999);
            $exist_User->save();
            session(['user' => $exist_User]);
            $data=[
                'user'=>$exist_User,
                'cc'=>[],
                'type'=>'REG_OTP',
                'user_type'=>'Seller',
            ];
            $sender = env('SMS_SENDER');
            $user_otp = $exist_User->otp;
            $name = $exist_User->name;
            $customer_mobile_no = $exist_User->mobile; // Mobile number to send the SMS to
            $myMessage = urlencode("Dear ".$name." OTP for new registration is ".$user_otp." Please enter this to verify your identity and proceed with the registration request.Sarv-Megh Technology (OPC) Private Limited");
            // New URL format
            sendSMS($sender, $customer_mobile_no, $myMessage);
            $mail = sendMail($data,$exist_User->email,'OTP Verification'); // Assuming sendMail function exists
            $endTime = Carbon::now()->addSeconds(60);
            $counter = $endTime->format('Y-m-d H:i:s');
            session(['counter_timer' => $endTime->format('Y-m-d H:i:s')]);
            return response()->json(['status' => 200, 'counter'=>$counter]);
        }else{
            return response()->json(['status'=>500, 'message'=>"User not found!"]);
        }
        
    }
    public function UserVerifyDataCheck(Request $request){
        $main_otp = Session::get('otp');
        $user = Session::get('user');
        $otp = $request->otp;
        if($main_otp==$otp){
            $userUpdate  = User::findOrFail($user->id);
            $userUpdate->mobile_status = 1;
            $userUpdate->save();
            if($userUpdate){
                $Exist_outside_participant = InquiryOutsideParticipant::where('mobile', $userUpdate->mobile)->get();
                if(isset($Exist_outside_participant)){
                    if(count($Exist_outside_participant)>0){
                        $Inquiry_data = Inquiry::where('id', $Exist_outside_participant[0]->inquiry_id)->first();
                        $Buyer_data = User::where('id', $Inquiry_data->created_by)->first();
                    
                        $title = $Buyer_data->name . ' added you to a new inquiry '.$Inquiry_data->inquiry_id;
                        $link = route('seller_all_inquiries');
                        notification_push(NULL,$Inquiry_data->created_by,$userUpdate->id,$title,NULL,$link);
                    }
                    foreach($Exist_outside_participant as $k =>$item){
                        $outside_participant_data = InquiryOutsideParticipant::where('mobile', $item->mobile)->where('inquiry_id', $item->inquiry_id)->first();
                        $inqOutParti =  new InquiryParticipant;
                        $inqOutParti->inquiry_id = $item->inquiry_id;
                        $inqOutParti->user_id = $userUpdate->id;
                        $inqOutParti->selected_from = 1;//1 for Close Inquiry
                        $inqOutParti->save();
                        if($inqOutParti){
                            $userUpdate->trusted_id = $outside_participant_data->buyer_id;
                            $userUpdate->save();
                            $outside_participant_data->delete();
                        }
                    }
                    
                }
            }
            Session::forget('otp');
            session(['user' => $userUpdate]);
            return response()->json(['status'=>200]);
        }else{
            return response()->json(['status'=>500, 'message'=>"OTP does not match. Please try again!"]);
        }
    }
    public function ForgotPassword(Request $request){
        return view('auth.forgot-password');
    }
    public function ForgotPasswordSendOTP(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $otp = rand(1000, 9999);
        $user = User::where('email', $request->email)->first();
        $sender = env('SMS_SENDER');
        $user->otp = $otp;
        $user->save();
        $name = $user->name;
        $customer_mobile_no = $user->mobile; // Mobile number to send the SMS to
        $myMessage = urlencode("Dear ".$name." OTP for new password generation is ".$otp.". . -Sarv-Megh Technology (OPC) Private Limited");
        // New URL format
        $checkPhoneNumberValid = checkPhoneNumberValid($customer_mobile_no);
        if($checkPhoneNumberValid){
            sendSMS($sender, $customer_mobile_no, $myMessage);
        }
        Session::put('otp', $otp);
        Session::put('email', $request->email);
        return response()->json(['message' => 'OTP sent successfully', 'status'=>200]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required']);
        if ($request->otp == Session::get('otp')) {
            return response()->json(['message' => 'OTP verified', 'status'=>200]);
        } else {
            return response()->json(['message' => 'Invalid OTP', 'status'=>400]);
        }
    }

    public function passwordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
            'otp' => 'required'
        ]);

        if ($request->otp == Session::get('otp') && $request->email == Session::get('email')) {
            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            Session::forget('otp');
            Session::forget('email');

            return response()->json(['message' => 'Password reset successful', 'status'=>200]);
        } else {
            return response()->json(['message' => 'Invalid OTP or email', 'status'=>400]);
        }
    }
}
