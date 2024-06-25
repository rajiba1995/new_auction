<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\PackageContract;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Contracts\UserDetailsContract;
use App\Contracts\UserContract;
use App\Models\MyBuyerWallet;
use App\Models\MySellerWallet;
use App\Models\WebsiteLogs;
use App\Models\MyBuyerOpeningPackage;
use App\Models\MyBuyerPackage;
use App\Models\MySellerPackage;
use App\Models\Transaction;
use Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;


class UserDetailsController extends Controller
{
    protected $userDetailsRepository;
    protected $userRepository;
    protected $packageRepository;


    public function __construct(UserDetailsContract $userDetailsRepository,UserContract $userRepository,PackageContract $packageRepository)
    {
        $this->userDetailsRepository = $userDetailsRepository;
        $this->userRepository = $userRepository;
        $this->packageRepository = $packageRepository;

    }
    public function AuthCheck(){
        if(Auth::guard('web')->check()){
            return Auth::guard('web')->user();
        } else{
           return "";
        }
    }

    // Banner
    public function UserDetailsIndex(Request $request)
    {   
        $startDate = $request->start_date ?? '';
        $endDate = $request->end_date ?? '';
        $keyword = $request->keyword ?? '';
               // Check if any of the parameters are provided
        // If keyword is provided or both start_date and end_date are provided
        if (!empty($keyword) || !empty($startDate) || !empty($endDate)) {  
        $data = $this->userDetailsRepository->getSearchUser($keyword,$startDate,$endDate);
        }else{
        $data = $this->userDetailsRepository->getAllUsers();
         }
        return view('admin.user.index', compact('data'));
        }
        public function UserDetailsView(int $id)
        {
            $data = $this->userDetailsRepository->getUserDetailsById($id);
            $AllImages = $this->userDetailsRepository->getAllUsersImages($id);
            $badges = $this->userDetailsRepository->getAllBadgesByUserId($id);
            $products = $this->userDetailsRepository->getAllProductsByUserId($id);
            return view('admin.user.view', compact('data', 'AllImages','badges','products'));
        }
            public function UserDocumentView(int $id)
            { 
                $data = $this->userDetailsRepository->getUserAllDocumentsById($id);
                $Additional_data = $this->userDetailsRepository->getAllAddiDocByUserId($id);
                return view('admin.user.userDoc', compact('data', 'Additional_data'));
                }
                public function UserDocumentStatus(Request $request)
                {
                    // dd($request->all());
        $data = $this->userDetailsRepository->StatusUserDocument($request);
        // dd($data);
        return response()->json(['status'=>200]);
    }
    public function UserStatus($id)
    {
        // dd($request->all());
        $data = $this->userDetailsRepository->StatusUser($id);
        return redirect()->back();
        
        // dd($data);
        return response()->json(['status'=>200]);
        }
        public function UserBlockStatus($id)
        {
            // dd($id);
            $data = $this->userDetailsRepository->StatusUserBlock($id);
            return redirect()->back();
            
            
            }
            public function UserReportStatus(int $id)
            {
                $data = $this->userDetailsRepository->StatusUserReport($id);
                return redirect()->back();
                }
                public function UserReportView(int $id)
                {
                    // $seller_id = $id;
        $block_status =$this->userDetailsRepository->getBlockStatusOfUserById($id);
        // dd($block_status);
        $data = $this->userDetailsRepository->getAllReportsById($id);
        return view('admin.user.report',compact('data','block_status'));
        // $data = $this->userDetailsRepository->StatusUserDocument($id);
        // return redirect()->back();
    }
    public function UserDetailsExport(Request $request)
    {
        //  dd($request->all());
        $start_date = $request->start_date ?? '';
        $end_date = $request->end_date ?? '';
        $keyword = $request->keyword ?? '';
        $query = User::query();
        
        $query->when($start_date && $end_date, function($query) use ($start_date, $end_date) {
             $query->where('created_at', '>=', $start_date." 00:00:00")
             ->where('created_at', '<=', date("Y-m-d 23:59:59",strtotime($end_date)));
             });
             
         $query->when($keyword, function ($query) use ($keyword) {
             $query->where('name', 'like', '%' . $keyword . '%')
             ->orWhere('email', 'like', '%' . $keyword . '%')
             ->orWhere('mobile', 'like', '%' . $keyword . '%')
             ->orWhere('business_name', 'like', '%' . $keyword . '%')
             ->orWhere('state', 'like', '%' . $keyword . '%')
             ->orWhere('business_type', 'like', '%' . $keyword . '%');
             });
             $data = $query->latest('id')->get();
             
             
             if(count($data)>0){
                 $delimiter = ",";
                 $fileName = "Users Details-".date('d-m-Y').".csv";
                 // Create a file pointer
                 $f = fopen('php://memory', 'w');
                 
                 // Set Column Headers
                 $header = array("First Name","Last Name","Email","Mobile","Gender","Address","Pincode","Short Bio","Business Name","Business Type","No of Employee","Establish Year","Legal Status","added_by","Date");
            fputcsv($f,$header,$delimiter);
            
            $count =1;
            foreach($data as $key => $row){
                $exportData = array(
                    $row->first_name ? $row->first_name : '',
                    $row->last_name ? $row->last_name : '',
                    $row->email ? $row->email : '',
                    $row->mobile ? $row->mobile : '',      
                    $row->gender ? $row->gender : '',      
                    $row->address ? $row->address : '',      
                    $row->pincode ? $row->pincode : '',      
                    $row->short_bio ? $row->short_bio : '',      
                    $row->business_name ? $row->business_name : '',      
                    $row->business_type ? $row->business_type : '',      
                    $row->employee ? $row->employee : '',      
                    $row->Establishment_year ? $row->Establishment_year : '',      
                    $row->legal_status ? $row->legal_status : '',      
                    $row->added_by ? $row->getEmployeeName->name : '',      
                    date("Y-m-d h:i a",strtotime($row->created_at)) ? date("d-m-Y h:i a",strtotime($row->created_at)) : ''
                    
                    );
                    // dd($exportData);
                    fputcsv($f,$exportData,$delimiter);
                    $count++;
                    }
                    fseek($f,0);
                    // Set headers to download file rather than displayed
                    header('Content-Type: text/csv');
                    header('Content-Disposition: attachment; filename="' . $fileName . '";');
                    
                    //output all remaining data on a file pointer
                    fpassthru($f);
                    
                    }
                    }
                    
                    public function UserTransactionView(Request $request,int $id)
                    { 
                        $startDate = $request->start_date ?? '';
                        $endDate = $request->end_date ?? '';
        $keyword = $request->keyword ?? '';
        $status = $request->status ?? '';
        if (!empty($keyword) || !empty($startDate) || !empty($endDate) || !empty($status)) {  
            $data = $this->userDetailsRepository->getSearchUsersTransaction($keyword,$startDate,$endDate,$status,$id);
            }else{
                $data = $this->userDetailsRepository->getUserAllTransactionById($id);
                }
                return view('admin.user.userTransaction', compact('data','id','status'));
                }
                public function UserWalletView(Request $request,int $user_id)
                { 
        $seller_wallet_transactions = $this->userRepository->getSellerAllWalletTransactionByUserId($user_id);
        $buyer_wallet_transactions = $this->userRepository->getBuyerAllWalletTransactionByUserId($user_id);
        return view('admin.user.userWallet', compact('seller_wallet_transactions','buyer_wallet_transactions','user_id'));
        
        }
        public function GiftBuyerCredit(Request $request)
    { 
     
        $request->validate([
            'buyer_gift_credit' => 'required|numeric',    
        ]);
        // Retrieve the latest wallet record for the user
        $latest_wallet = MyBuyerWallet::where('user_id', $request->user_id)->latest()->first();
        // Calculate the current balance based on the latest wallet record
        $current_balance = $latest_wallet ? $latest_wallet->current_unit : 0;
        // Update Wallet
        $wallet_credit = $request->buyer_gift_credit;
        $buyer_gift_credit = new MyBuyerWallet();
        $buyer_gift_credit->user_id = $request->user_id;
        $buyer_gift_credit->type = 1;
        // $buyer_gift_credit->inquiry_id = null;
        $buyer_gift_credit->purpose = $wallet_credit.' Credit gift from admin';
        $buyer_gift_credit->debit_unit = 0;
        $buyer_gift_credit->credit_unit = $request->buyer_gift_credit;
        $buyer_gift_credit->current_unit = $current_balance + $wallet_credit;
        $buyer_gift_credit->status = 1;
        $buyer_gift_credit->save();
        
        if($buyer_gift_credit){
            $title = "Admin give you ".$wallet_credit." buyer free credits";
            $link = route('user.buyer_wallet_transaction');
            notification_push("Admin",NULL,$request->user_id,$title,NULL,$link); //admin,buyer,seller,title,desc,link
            
            $json_data = [
                'gifted_by' =>'Admin',
                'gifted_to_buyer' =>$request->user_id ,
                'credit_amount' => $request->buyer_gift_credit,
                
                ];
                
                $websiteLog =new WebsiteLogs();
                $websiteLog->logs_type ="GIFT CREDIT TO BUYER";
                $websiteLog->table_name ="my_buyer_wallets";
                $websiteLog->response =json_encode($json_data);
                $websiteLog->save();
                }
    return redirect()->back()->with('success',"You gift free credit to this buyer");
    
    }
    public function GiftSellerCredit(Request $request)
    { 
     
        $request->validate([
            'seller_gift_credit' => 'required|numeric',    
            ]);
            // Retrieve the latest wallet record for the user
            $latest_wallet = MySellerWallet::where('user_id', $request->user_id)->latest()->first();
            // Calculate the current balance based on the latest wallet record
        $current_balance = $latest_wallet ? $latest_wallet->current_unit : 0;
        // Update Wallet
        $wallet_credit = $request->seller_gift_credit;
        $seller_gift_credit = new MySellerWallet();
        $seller_gift_credit->user_id = $request->user_id;
        $seller_gift_credit->type = 1;
          // $seller_gift_credit->inquiry_id = null;
          $seller_gift_credit->purpose = $wallet_credit.' Credit gift from admin';
          $seller_gift_credit->debit_unit = 0;
        $seller_gift_credit->credit_unit = $request->seller_gift_credit;
        $seller_gift_credit->current_unit = $current_balance + $wallet_credit;
        $seller_gift_credit->status = 1;
        $seller_gift_credit->save();
        
        if($seller_gift_credit){
            $title = "Admin give you ".$wallet_credit." seller free credits";
            $link = route('user.seller_wallet_transaction');
            notification_push("Admin",NULL,$request->user_id,$title,NULL,$link); //admin,buyer,seller,title,desc,link
            $json_data = [
                'gifted_by' =>'Admin' ,
                'gifted_to_seller' =>$request->user_id ,
                'credit_amount' => $request->seller_gift_credit,
                
                ];
                
                $websiteLog =new WebsiteLogs();
                $websiteLog->logs_type ="GIFT CREDIT TO SELLER";
                $websiteLog->table_name ="my_seller_wallets";
                $websiteLog->response =json_encode($json_data);
                $websiteLog->save();
                }
                return redirect()->route('admin.user.wallet.view', ['id' => $request->user_id, 'package' => 'seller'])->with('success', "You gifted free credit to this seller");

                
                }
                
    public function UserPackageDetailsView(Request $request,int $id)
    { 
        $seller_currernt_package = $this->userDetailsRepository->getUserSellerCurrentPackageById($id);
        $seller_old_package = $this->userDetailsRepository->getUserSellerOldPackageById($id);
        $buyer_currernt_package = $this->userDetailsRepository->getUserBuyerCurrentPackageById($id);
        $buyer_old_package = $this->userDetailsRepository->getUserBuyerOldPackageById($id);
        $allBuyerPackages = $this->packageRepository->getAllBuyerPackages();
        $allSellerPackages = $this->packageRepository->getAllSellerPackages();
        return view('admin.user.userPackageDetails', compact('seller_currernt_package','seller_old_package','buyer_currernt_package','buyer_old_package','id','allBuyerPackages','allSellerPackages'));
        
        
        }
        public function UserAddByEmployee()
    { 
        $data = $this->userDetailsRepository->getAllUsersByEmployee();
        return view('admin.user.user_index_by_emp', compact('data'));
        
    }
        public function AdminBuyBuyerPackage(Request $request)
    { 
    //    dd($request->all());
        
       try {
        DB::beginTransaction();
        $negotiable_amount = 0;
        $duration = 30*$request->buyer_package_duration;
        $expiryDate = Carbon::now()->addDays($duration);
        $my_basic_checking = MyBuyerOpeningPackage::where('user_id', $request->user_id)->first();
        if($my_basic_checking && $request->buyer_package_type=="premium" || $my_basic_checking && $request->buyer_package_type=="basic"){
            $my_current_package = MyBuyerPackage::with('package_data')->where('user_id', $request->user_id)->first();
                if(!$my_current_package){
                    $my_current_package = new MyBuyerPackage();
                }else{
                    $old_package = DB::table('old_buyer_packages')->insert([
                        'package_id' => $my_current_package->package_id,
                        'user_id' => $my_current_package->user_id,
                        'package_duration' => $my_current_package->package_duration, 
                        'package_amount' => $my_current_package->package_amount,
                        'package_credit' => $my_current_package->package_credit,
                        'purchase_date' => $my_current_package->created_at,
                        'expiry_date' => $my_current_package->expiry_date,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $my_current_package->package_id = $request->user_buyer_package;
                $my_current_package->user_id = $request->user_id;
                $my_current_package->package_type = $request->buyer_package_type;
                $my_current_package->package_amount =$request->buyer_package_price;
                $my_current_package->package_duration =$request->buyer_package_duration;
                $my_current_package->package_credit =$request->buyer_package_credits;
                $my_current_package->expiry_date =$expiryDate;
                $my_current_package->save();

        }else{
            if($request->buyer_package_type=="basic"){
                $basic_package = new MyBuyerOpeningPackage;
                $basic_package->package_id = $request->user_buyer_package;
                $basic_package->user_id = $request->user_id;
                $basic_package->package_type = $request->buyer_package_type;
                $basic_package->package_amount =$request->buyer_package_price;
                $basic_package->package_duration =$request->buyer_package_duration;
                $basic_package->package_credit =$request->buyer_package_credits;
                $basic_package->expiry_date =$expiryDate;
                $basic_package->save();
                if($basic_package){
                    $my_current_package = new MyBuyerPackage;
                    $my_current_package->package_id = $request->user_buyer_package;
                    $my_current_package->user_id = $request->user_id;
                    $my_current_package->package_type = $request->buyer_package_type;
                    $my_current_package->package_amount =$request->buyer_package_price;
                    $my_current_package->package_duration =$request->buyer_package_duration;
                    $my_current_package->package_credit =$request->buyer_package_credits;
                    $my_current_package->expiry_date =$expiryDate;
                    $my_current_package->save();
                }
            }else{
                DB::commit();
                return redirect()->back()->with('warning', 'Please purchase the basic package first, one time only.');
            }
        }

        // Retrieve the latest wallet record for the user
        $latest_wallet = MyBuyerWallet::where('user_id', $request->user_id)->latest()->first();
        // Calculate the current balance based on the latest wallet record
        $current_balance = $latest_wallet ? $latest_wallet->current_unit : 0;
        // Update Wallet
        $package_credit = $request->buyer_package_credits;
        $my_wallet = new MyBuyerWallet();
        $my_wallet->user_id = $request->user_id;
        $my_wallet->type = 1; //Credit
        // $my_wallet->inquiry_id = null;
        $my_wallet->purpose = "For purchase package";
        $my_wallet->credit_unit = $request->buyer_package_credits;
        $my_wallet->current_unit = $current_balance + $package_credit;
        $my_wallet->save();

        $transaction = new Transaction();
        $transaction->user_id = $request->user_id;
        $transaction->unique_id = rand(100000, 999999).''.time(); // Adjusted range for 8-digit number
        $transaction->transaction_type = 0; //offline
        $transaction->user_type = 2; //Buyer
        $transaction->transaction_id = rand(100000, 999999).''.time(); // Adjusted range for 8-digit number
        $transaction->purpose = $request->form_type=='upgrade'?'Upgrade buyer package':'New buyer package';
        $transaction->actual_amount = $request->buyer_package_price;
        $transaction->negotiable_amount = $negotiable_amount;
        $transaction->amount = $request->buyer_package_price-$negotiable_amount;
        $transaction->buyer_package_id = $request->user_buyer_package;
        $transaction->transaction_source = "Buy from Admin";
        $transaction->remarks = $request->buyer_package_notes;
        $transaction->save();
        
        if($transaction){
            $json_data = [
                'transaction' => $transaction,
                'my_wallet' => $my_wallet,
                'my_current_package' => $my_current_package,
            ];
          
            $websiteLog =new WebsiteLogs();
            $websiteLog->logs_type ="INSERTED";
            $websiteLog->table_name ="transactions, my_buyer_wallets, my_buyer_packages";
            $websiteLog->response =json_encode($json_data);
            $websiteLog->save();
        }
         // For Amount Transaction
         DB::commit();
        if (Session::has('url.intended')) {
            $intendedUrl = Session::get('url.intended');
            // Forget the intended URL from the session after using it
            Session::forget('url.intended');
            return redirect($intendedUrl);
        }else{
            return redirect()->route('admin.user.package.view',$request->user_id)->with('success', 'Package has been successfully purchased');  
        }
        // Start a database transaction
       
    } catch (\Exception $e) {
        // Rollback the transaction and handle the exception
        DB::rollBack();
        dd($e->getMessage());
        // return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Something went wrong, please try again later!');
    }

    }

    public function AdminBuySellerPackage(Request $request){
        // dd($request->all());

        try {
            $negotiable_amount = 0;
            $my_current_package = MySellerPackage::with('getPackageDetails')->where('user_id', $request->user_id)->first();
            if($request->form_type=='upgrade'){
                if($my_current_package){
                    $current_package_duration = $my_current_package->package_duration;
                    $monthly_package_price = $my_current_package->monthly_package_price;
                    $negotiable_amount = $monthly_package_price*($current_package_duration-$my_current_package->usage_months);
                }
            }
            // Start a database transaction
            DB::beginTransaction();
            $duration = 30*$request->seller_package_duration;
            $monthly_package_price = $request->seller_package_price/$request->seller_package_duration;
            
                // Set expiry date 30 days later from today
            $expiryDate = Carbon::now()->addDays($duration);
            $next_credit_date = Carbon::now()->addDays(30);
            if(!$my_current_package){
                $my_current_package = new MySellerPackage();
            }else{
                $old_package = DB::table('old_seller_packages')->insert([
                    'package_id' => $my_current_package->package_id,
                    'user_id' => $my_current_package->user_id,
                    'monthly_package_price' => $my_current_package->monthly_package_price,
                    'package_duration' => $my_current_package->package_duration, // Corrected key
                    'monthly_credit' => $my_current_package->monthly_credit,
                    'next_credit_date' => $my_current_package->next_credit_date,
                    'purchase_amount' => $my_current_package->purchase_amount,
                    'purchase_date' => $my_current_package->created_at,
                    'expiry_date' => $my_current_package->expiry_date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
            }
            $my_current_package->package_id = $request->user_seller_package;
            $my_current_package->user_id = $request->user_id;
            $my_current_package->monthly_package_price = $monthly_package_price;
            $my_current_package->package_duration = $request->seller_package_duration;
            $my_current_package->monthly_credit = $request->seller_package_credits;
            $my_current_package->next_credit_date = $next_credit_date;
            $my_current_package->purchase_amount = $request->seller_package_price;    
            $my_current_package->expiry_date = $expiryDate;
            $my_current_package->save();

            // Retrieve the latest wallet record for the user
            $latest_wallet = MySellerWallet::where('user_id', $request->user_id)->latest()->first();
            // Calculate the current balance based on the latest wallet record
            $current_balance = $latest_wallet ? $latest_wallet->current_unit : 0;
            // Update Wallet
            $monthly_credit = $request->seller_package_credits;
            $my_wallet = new MySellerWallet();
            $my_wallet->user_id = $request->user_id;
            $my_wallet->type = 1; //Credit
            $my_wallet->purpose = "For purchase package"; 
            $my_wallet->credit_unit = $request->seller_package_credits;
            $my_wallet->current_unit = $current_balance + $monthly_credit;
            $my_wallet->save();

            // For Amount Transaction

            $transaction = new Transaction();
            $transaction->user_id = $request->user_id;
            $transaction->unique_id = rand(100000, 999999).''.time(); // Adjusted range for 8-digit number
            $transaction->transaction_type = 0; //offline
            $transaction->user_type = 1; //Seller
            $transaction->transaction_id = rand(100000, 999999).''.time(); // Adjusted range for 8-digit number
            $transaction->purpose = $request->form_type=='upgrade'?'Upgrade seller package':'New seller package';
            $transaction->actual_amount = $request->seller_package_price;
            $transaction->negotiable_amount = $negotiable_amount;
            $transaction->amount = $request->seller_package_price-$negotiable_amount;
            $transaction->seller_package_id = $request->user_seller_package;
            $transaction->transaction_source = "Buy from Admin";
            $transaction->remarks = $request->seller_package_notes;
            $transaction->save();
            if($transaction){
                $websiteLog =new WebsiteLogs();
                $websiteLog->logs_type ="INSERTED";
                $websiteLog->table_name ="transactions";
                $websiteLog->response =json_encode($transaction);
                $websiteLog->save();
            }
            DB::commit();
            if (Session::has('url.intended')) {
                $intendedUrl = Session::get('url.intended');
                // Forget the intended URL from the session after using it
                Session::forget('url.intended');
                return redirect($intendedUrl);
            }else{
                return redirect()->route('admin.user.package.view',$request->user_id)->with(['success' => 'Package has been successfully purchased', 'package_type' => 'seller']);  
            }
        } catch (\Exception $e) {
            // Rollback the transaction and handle the exception
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    
    }
    