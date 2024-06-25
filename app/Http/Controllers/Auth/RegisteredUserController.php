<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
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
                        'user'=>$exist_User,
                        'type'=>'REG_OTP',
                    ];
                    $mail = sendMail($data, 'OTP Verification'); // Assuming sendMail function exists
                    DB::commit(); // Commit the transaction
                    $route = route('front.otp_validation');
                    $endTime = Carbon::now()->addSeconds(60);
                    session(['counter_timer' => $endTime->format('Y-m-d H:i:s')]);
                    return response()->json(['status' => 200, 'route' => $route]);
                }
                DB::rollBack();
                return response()->json(['status' => 500]); // User already exists
            } else {
                $User = new User;
                $User->name = $request->full_name;
                $User->email = $request->email;
                $User->mobile = $request->phone;
                $User->email_status = 1;
                $User->otp = rand(1111,9999);
                $User->password = Hash::make($request->password);
                $User->save();
                // You may perform additional operations within the transaction here
    
                session(['user' => $User]);
                $data=[
                    'user'=>$User,
                    'type'=>'REG_OTP',
                ];
                $mail = sendMail($data, 'OTP Verification'); // Assuming sendMail function exists
                
                DB::commit(); // Commit the transaction
    
                $route = route('front.otp_validation');
                $endTime = Carbon::now()->addSeconds(60);
                session(['counter_timer' => $endTime->format('Y-m-d H:i:s')]);
                return response()->json(['status' => 200, 'route' => $route]);
            }
        } catch (\Exception $e) {
            DB::rollBack(); // Roll back the transaction on exception
            // Handle the exception as per your application's requirement
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
                'type'=>'REG_OTP',
            ];
            $mail = sendMail($data, 'OTP Verification'); // Assuming sendMail function exists
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
                    foreach($Exist_outside_participant as $k =>$item){
                        $outside_participant_data = InquiryOutsideParticipant::where('mobile', $item->mobile)->where('inquiry_id', $item->inquiry_id)->first();
                        $inqOutParti =  new InquiryParticipant;
                        $inqOutParti->inquiry_id = $item->inquiry_id;
                        $inqOutParti->user_id = $userUpdate->id;
                        $inqOutParti->selected_from = 1;//1 for Close Inquiry
                        $inqOutParti->save();
                        if($inqOutParti){
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
}
