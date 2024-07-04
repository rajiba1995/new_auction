<?php

namespace App\Http\Controllers;

use App\Models\MyBuyerPackage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MySellerPackage;
use App\Models\MySellerWallet;
use App\Models\MyBuyerWallet;
use App\Models\WebsiteLogs;
use App\Models\MyBadge;
use App\Models\Inquiry;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException; // Import Exception class

use function GuzzleHttp\json_encode;

class CornController extends Controller
{
    public function SellerMothlyPackageCheckCron(){
        $today = Carbon::today()->toDateString(); 
        // $today = '2024-07-19';
         // Get today's date in 'Y-m-d' format
        $data = MySellerPackage::where('package_duration','!=','usage_months')->whereDate('next_credit_date',$today)->whereDate('expiry_date','>=',$today)->latest()->get()->toArray();
        // dd($data);
        try{
            DB::beginTransaction();

            if (!empty($data)) { // Check if the collection is not empty
            foreach($data as $item){
                $wallet = MySellerWallet::where('user_id',$item['user_id'])->latest('id')->first();
                    if($wallet){
                        if($wallet->current_unit>0){
                            //debit the current_unit>0 of MysellerWallet 
                            $mySellerWalletDebit = new MySellerWallet();
                            $mySellerWalletDebit->inquiry_id = NULL;
                            $mySellerWalletDebit->purpose = "Monthly Credit Units Expired";
                            $mySellerWalletDebit->user_id = $wallet->user_id;
                            $mySellerWalletDebit->type = 0;//debit
                            $mySellerWalletDebit->credit_unit = 0;
                            $mySellerWalletDebit->debit_unit = $wallet->current_unit;
                            $mySellerWalletDebit->current_unit = 0;
                            $mySellerWalletDebit->save();
                            // Credit the new monthly units
                            $mySellerWalletCredit = new MySellerWallet();
                            $mySellerWalletCredit->inquiry_id = NULL;
                            $mySellerWalletCredit->purpose = "Monthly Credit Units Credited";
                            $mySellerWalletCredit->user_id = $wallet->user_id;
                            $mySellerWalletCredit->type = 1;//credit
                            $mySellerWalletCredit->credit_unit = $item['monthly_credit'];
                            $mySellerWalletCredit->debit_unit = 0;
                            $mySellerWalletCredit->current_unit = $item['monthly_credit'];
                            $mySellerWalletCredit->save();
                        
                        }else{
                            // if current_unit == 0 then no debit only monthly credit
                            $mySellerWalletCredit = new MySellerWallet();
                            $mySellerWalletCredit->inquiry_id = NULL;
                            $mySellerWalletCredit->purpose = "Monthly Credit Units Credited";
                            $mySellerWalletCredit->user_id = $wallet->user_id;
                            $mySellerWalletCredit->type = 1;
                            $mySellerWalletCredit->credit_unit = $item['monthly_credit'];
                            $mySellerWalletCredit->debit_unit = 0;
                            $mySellerWalletCredit->current_unit = $item['monthly_credit'];
                            $mySellerWalletCredit->save();
                        }

                        $mySellerPackageUpdate = MySellerPackage::findOrFail($item['id']);
                        $mySellerPackageUpdate->next_credit_date = Carbon::now()->addDays(30)->toDateString();
                        // dd($mySellerPackageUpdate->next_credit_date);
                        $mySellerPackageUpdate->usage_months = $item['usage_months']+1;
                        // dd($mySellerPackageUpdate->usage_months);
                        $mySellerPackageUpdate->save();
                    }   
                }
            } else {
                return false;
            }   
            DB::commit();

            // Log the operation  
        
            if (!empty($data)){
            $websiteLog =new WebsiteLogs();
            $websiteLog->emp_id = NULL;
            $websiteLog->logs_type ="Seller package mothly debit & credit";
            $websiteLog->table_name ="my_seller_package && my_seller_wallet";
            $websiteLog->response =json_encode($data);
            // dd('json');
            $websiteLog->save();
            }
        }
            catch (QueryException $exception) {
            throw new InvalidArgumentException($exception->getMessage());
                // $websiteLog =new WebsiteLogs();
                // $websiteLog->emp_id = NULL;
                // $websiteLog->logs_type ="Seller package mothly debit & credit";
                // $websiteLog->table_name ="mysellerpackage && mysellerwallet";
                // $websiteLog->response =json_encode($exception->getMessage());
                // $websiteLog->save();
        }

    }


    public function SellerExpiryPackageCheckCron()
    {
        // Get the current datetime in 'Y-m-d H:i:00' format (excluding seconds)
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:00');
        
        // Calculate the range for 1 minute before and 1 minute after the current datetime
        $oneMinuteBefore = Carbon::parse($currentDateTime)->subMinute()->format('Y-m-d H:i:00');
        $oneMinuteAfter = Carbon::parse($currentDateTime)->addMinute()->format('Y-m-d H:i:00');
        
        // Query MySellerPackage where expiry_date is within the 1-minute range
        $data = MySellerPackage::whereBetween('expiry_date', [$oneMinuteBefore, $oneMinuteAfter])->get()->toArray();
        // dd($data);
        $myBadge = MyBadge::whereBetween('expiry_date', [$oneMinuteBefore, $oneMinuteAfter])->get()->toArray();
        // dd($myBadge);

        try{
            DB::beginTransaction();

                if (!empty($data)) { // Check if the collection is not empty
                    foreach($data as $item){
                    $purchaseDate = Carbon::parse($item['created_at'])->format('Y-m-d H:i:s');
                // Insert into my_old_seller_package
                DB::table('old_seller_packages')->insert([
                    'package_id' => $item['package_id'],
                    'user_id' => $item['user_id'],
                    'package_duration' => $item['package_duration'],
                    'monthly_package_price' => $item['monthly_package_price'],
                    'monthly_credit' => $item['monthly_credit'],
                    'purchase_amount' => $item['purchase_amount'],
                    'next_credit_date' => $item['next_credit_date'],
                    'usage_months' => $item['usage_months'],
                    'purchase_date' => $purchaseDate,
                    'expiry_date' => $item['expiry_date'],

                    ]);

                // Delete from MySellerPackage
                MySellerPackage::where('id', $item['id'])->delete();
                    }
                }

                DB::commit();

                // Log the operation  
            
                if (!empty($data)){
                $websiteLog =new WebsiteLogs();
                $websiteLog->emp_id = NULL;
                $websiteLog->logs_type ="Seller package expiry";
                $websiteLog->table_name ="my_seller_package && old_seller_package";
                $websiteLog->response =json_encode($data);
                // dd('json');
                $websiteLog->save();
                }
            }
            catch (QueryException $exception) {
            throw new InvalidArgumentException($exception->getMessage());
                // $websiteLog =new WebsiteLogs();
                // $websiteLog->emp_id = NULL;
                // $websiteLog->logs_type ="Seller package mothly debit & credit";
                // $websiteLog->table_name ="mysellerpackage && mysellerwallet";
                // $websiteLog->response =json_encode($exception->getMessage());
                // $websiteLog->save();
        }


        try{
            DB::beginTransaction();

                if (!empty($myBadge)) { // Check if the collection is not empty
                    foreach($myBadge as $item){
                    $purchaseDate = Carbon::parse($item['created_at'])->format('Y-m-d H:i:s');
                // Insert into my_old_seller_package
                DB::table('my_old_badges')->insert([
                    'user_id' => $item['user_id'],
                    'badge_id' => $item['badge_id'],
                    'duration' => $item['duration'],
                    'expiry_date' => $item['expiry_date'],
                    'purchase_date' => $purchaseDate,
                    ]);

                // Delete from MyBadge
                MyBadge::where('id', $item['id'])->delete();
                    }
                }

                DB::commit();

                // Log the operation  
            
                if (!empty($myBadge)){
                $websiteLog =new WebsiteLogs();
                $websiteLog->emp_id = NULL;
                $websiteLog->logs_type ="Badge expiry";
                $websiteLog->table_name ="my_badges && my_old_badges";
                $websiteLog->response =json_encode($myBadge);
                // dd('json');
                $websiteLog->save();
                }
            }
            catch (QueryException $exception) {
            throw new InvalidArgumentException($exception->getMessage());
                // $websiteLog =new WebsiteLogs();
                // $websiteLog->emp_id = NULL;
                // $websiteLog->logs_type ="Seller package mothly debit & credit";
                // $websiteLog->table_name ="mysellerpackage && mysellerwallet";
                // $websiteLog->response =json_encode($exception->getMessage());
                // $websiteLog->save();
        }

    }
    public function BuyerPackageCurrentUnitCheckCron()
    {
        $latestRecords = MyBuyerWallet::select('user_id', DB::raw('MAX(id) as latest_id'))
        ->where('current_unit', 0)
        ->groupBy('user_id');

        // Join the main table with the subquery to get the latest records for each user_id
        $data = MyBuyerWallet::joinSub($latestRecords, 'latest_records', function($join) {
        $join->on('my_buyer_wallets.id', '=', 'latest_records.latest_id');
        })
        ->orderByDesc('created_at')
        ->get()
        ->toArray();
        // dd($data);
        try{
            DB::beginTransaction();

                if(!empty($data)){
                    foreach($data as $item){
                    $myCurrentBuyerPackage = MyBuyerPackage::where('user_id',$item['user_id'])->latest()->first();
                    // dd($myCurrentBuyerPackage);
                    $old_buyer_package = DB::table('old_buyer_packages')->insert([
                        'package_id' => $myCurrentBuyerPackage->package_id,
                        'package_type' => $myCurrentBuyerPackage->package_type,
                        'user_id' => $myCurrentBuyerPackage->user_id,
                        'package_duration' => $myCurrentBuyerPackage->package_duration,
                        'package_amount' => $myCurrentBuyerPackage->package_amount,
                        'package_credit' => $myCurrentBuyerPackage->package_credit,
                        'purchase_date' => $myCurrentBuyerPackage->created_at,
                        'expiry_date' => $myCurrentBuyerPackage->expiry_date,
                    ]);

                        // Delete from MyBuyerPackage
                        MyBuyerPackage::where('id', $myCurrentBuyerPackage->id)->delete();
                    }
                }
                DB::commit();

                // Log the operation  
            
                if (!empty($data)){
                $websiteLog =new WebsiteLogs();
                $websiteLog->emp_id = NULL;
                $websiteLog->logs_type ="Seller package expiry for current unit 0";
                $websiteLog->table_name ="my_buyer_package && old_buyer_package";
                $websiteLog->response =json_encode($data);
                // dd('json');
                $websiteLog->save();
                }
            }
            catch (QueryException $exception) {
            throw new InvalidArgumentException($exception->getMessage());
                // $websiteLog =new WebsiteLogs();
                // $websiteLog->emp_id = NULL;
                // $websiteLog->logs_type ="Seller package mothly debit & credit";
                // $websiteLog->table_name ="mysellerpackage && mysellerwallet";
                // $websiteLog->response =json_encode($exception->getMessage());
                // $websiteLog->save();
            }


    }
    public function BuyerExpiryPackageCheckCron()
    {
        // Get the current datetime in 'Y-m-d H:i:00' format (excluding seconds)
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:00');
        
        // Calculate the range for 1 minute before and 1 minute after the current datetime
        $oneMinuteBefore = Carbon::parse($currentDateTime)->subMinute()->format('Y-m-d H:i:00');
        $oneMinuteAfter = Carbon::parse($currentDateTime)->addMinute()->format('Y-m-d H:i:00');
        
        // Query MySellerPackage where expiry_date is within the 1-minute range
        $data = MyBuyerPackage::whereBetween('expiry_date', [$oneMinuteBefore, $oneMinuteAfter])->get()->toArray();
        // dd($data);

        
        try{
            DB::beginTransaction();


                if (!empty($data)) { // Check if the collection is not empty
                    foreach($data as $item){
                    $purchaseDate = Carbon::parse($item['created_at'])->format('Y-m-d H:i:s');
                // Insert into my_old_seller_package
                DB::table('old_buyer_packages')->insert([
                    'package_id' => $item['package_id'],
                    'package_type' => $item['package_type'],
                    'user_id' => $item['user_id'],
                    'package_duration' => $item['package_duration'],
                    'package_amount' => $item['package_amount'],
                    'package_credit' => $item['package_credit'],
                    'purchase_date' => $purchaseDate,
                    'expiry_date' => $item['expiry_date'],

                    ]);

                        // Delete from MySellerPackage
                        MyBuyerPackage::where('id', $item['id'])->delete();
                    }
                }
                DB::commit();

                // Log the operation  
            
                if (!empty($data)){
                $websiteLog =new WebsiteLogs();
                $websiteLog->emp_id = NULL;
                $websiteLog->logs_type ="Buyer package expiry";
                $websiteLog->table_name ="my_buyer_package && old_buyer_package";
                $websiteLog->response =json_encode($data);
                // dd('json');
                $websiteLog->save();
                }
            }
            catch (QueryException $exception) {
            throw new InvalidArgumentException($exception->getMessage());
                // $websiteLog =new WebsiteLogs();
                // $websiteLog->emp_id = NULL;
                // $websiteLog->logs_type ="Seller package mothly debit & credit";
                // $websiteLog->table_name ="mysellerpackage && mysellerwallet";
                // $websiteLog->response =json_encode($exception->getMessage());
                // $websiteLog->save();
        }
    }

    public function BeforeStartAuction()
    {
        // Get the current datetime
        $now = Carbon::now();
        
        // Extract the current date in 'Y-m-d' format
        $currentDate = $now->format('Y-m-d');
        
        // Extract the current time in 'H:i:00' format (excluding seconds)
        $currentTime = $now->format('H:i');
        
        // Calculate the datetime 5 minutes after the current datetime
        $fiveMinutesAfter = Carbon::parse($currentTime)->addMinutes(5)->format('H:i');
        // Query Inquiry where expiry_date is within the next 5 minutes
        $data = Inquiry::with('ParticipantsData', 'BuyerData')->where('start_date',$currentDate)->whereBetween('start_time', [$currentTime, $fiveMinutesAfter])->get()->toArray();
        if (count($data) > 0) {
            foreach ($data as $inquiry) {
                // Check if there are any participants data
                if (isset($inquiry['participants_data']) && count($inquiry['participants_data']) > 0) {
                    foreach ($inquiry['participants_data'] as $item) {
                        // Fetch user email based on user_id
                        $user = User::find($item['user_id']);
                        if ($user) {
                            $data=[
                                'user'=>$user,
                            ];
                            $customer_mobile_no = $user->mobile; 
                            $checkPhoneNumberValid = checkPhoneNumberValid($customer_mobile_no);
                            if($checkPhoneNumberValid){
                                $sender = env('SMS_SENDER');
                                $company = $inquiry['buyer_data']?$inquiry['buyer_data']['business_name']:"a company";
                                $url = 'https://milaapp.in/seller/inquiries';
                                // Mobile number to send the SMS to
                                $myMessage = urlencode("Auction starting for ".$company." Details: ".$url.". (owned by SMTPL) -Sarv Megh Technology (OPC) Private Limited");
                                // New URL format
                                sendSMS($sender, $customer_mobile_no, $myMessage);
                            }
                            $email = $user->email;
                            sendMail($data, $email, 'INQUIRY_START'); 
                        }
                    }
                }
            }
        }
    }
    public function SendToSellerMail()
    {
        // Fetch the first 10 entries from the mail_logs table
        $data = DB::table('mail_logs')->take(10)->get()->toArray();
    
        // Check if there are any entries to process
        if (count($data) > 0) {
            DB::beginTransaction();
            try {
                // Loop through each entry
                foreach ($data as $item) {
                    // dd($item->response);
                         // Convert the response to an array
                    $messageData = json_decode($item->response, true);
                
                    $messageData['user'] = new User($messageData['user']);
                    $messageData['inquiry_data'] = new Inquiry($messageData['inquiry_data']);
                    $messageData['Buyer_data'] = new User($messageData['Buyer_data']);
                    // Send the email
                    sendMail($messageData, $item->email, $item->subject);
    
                    // Delete the entry from the mail_logs table after sending the email
                    DB::table('mail_logs')->where('id', $item->id)->delete();
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                // Handle exception or log error
            }
        }
    }
}