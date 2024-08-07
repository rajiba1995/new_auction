<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Contracts\UserContract;
use App\Contracts\MasterContract;
use App\Contracts\BuyerDashboardContract;
use App\Models\Product;
use App\Models\Notification;
use App\Models\Category;
use App\Models\WatchList;
use App\Models\MyBadge;
use App\Models\Badge;
use App\Models\User;
use App\Models\MySellerWallet;
use App\Models\OutsideParticipant;
use App\Models\MySellerPackage;
use App\Models\Package;
use App\Models\ReviewRating;
use App\Models\GroupWatchList;
use App\Models\UserImage;
use App\Models\SellerReport;
use App\Models\Transaction;
use App\Models\WebsiteLogs;
use App\Models\UserDocument;
use App\Models\MyBuyerWallet;
use App\Models\InquiryOutsideParticipant;
use App\Models\Collection;
use App\Models\MyBuyerOpeningPackage;
use App\Models\MyBuyerPackage;
use App\Models\UserAdditionalDocument;
use App\Models\RequirementConsumption;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use App\Mail\welcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use PDF;
use Hash;


class UserController extends Controller{

    protected $userRepository;

    public function __construct(UserContract $userRepository, MasterContract $MasterRepository, BuyerDashboardContract $BuyerDashboardRepository) {
        $this->userRepository = $userRepository;
        $this->MasterRepository = $MasterRepository;
        $this->BuyerDashboardRepository = $BuyerDashboardRepository;
    }

    public function AuthCheck(){
        if(Auth::guard('web')->check()){
            return Auth::guard('web')->user();
        } else{
           return "";
        }
    }

    public function profile(){
        $data = $this->AuthCheck();
        $location = '';
        $keyword = '';
        return view('front.user.profile', compact('data', 'location', 'keyword'));
    }
    public function ProfileEdit(){
        $data = $this->AuthCheck();
        $business_data = $this->userRepository->getAllBusiness();
        $legal_status_data = $this->userRepository->getAllLegalStatus();
        $states = $this->userRepository->getAllStates();
        $cities = $this->userRepository->getAllCities();
        // dd($states);
        return view('front.user.profile_update', compact('data','business_data','legal_status_data','states','cities'));
    }
    public function ProfileUpdate(Request $request){
        // dd($request->all());
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'profile_image'=>'nullable|mimes:jpg,jpeg,png,webp,jfif,gif',
            'short_bio'=>'required',
            'business_name' => 'required',
            'address' => 'required',
            'state' => 'required',
            'city' => 'required',
            'pincode' => 'required',
            'employee' => 'nullable',
            'Establishment_year' => 'nullable',
            'email' => 'required|email',
            'phone_number' => 'required|numeric',
            'legal_status' => 'nullable',
            'email1' => 'nullable|email', // Added email validation if these are supposed to be emails
            'email2' => 'nullable|email',
            'email3' => 'nullable|email',
            // Add more rules for other fields as needed
        ];
        
        // Define custom error messages
        $customMessages = [
            'first_name.required' => 'The first name field is required.',
            'last_name.required' => 'The last name field is required.',
            'profile_image.mimes' => 'Profile Image must be an image file',
            'short_bio.required' => 'The short bio field is required.',
            'business_name.required' => 'The business name field is required.',
            'business_type.required' => 'Please select a business type.',
            'address.required' => 'The address field is required.',
            'state.required' => 'Please select a state.',
            'city.required' => 'The city field is required.',
            'pincode.required' => 'The pincode field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone_number.required' => 'The phone number field is required.',
            'phone_number.numeric' => 'Please enter a valid phone number.',
            'employee.required' => 'Please enter total no, of Employee.',
            'Establishment_year.required' => 'Please enter establishment year.',
            // Add more custom messages for specific fields and rules as needed
        ];
        
        // Validate the data
        $validator = Validator::make($request->all(), $rules, $customMessages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            $params = $request->except('_token');
            $data = $this->userRepository->updateUser($request, $params);
            return redirect()->route('user.profile')->with('success', 'Information added successfully');
        }
    }
    public function ProductAndService(){
        $data = $this->AuthCheck();
        $Product = Product::where('user_id', $this->AuthCheck()->id)->get();
        return view('front.user.product_and_service', compact('Product', 'data'));
    }
    public function ProductAndServiceAdd(){
        $data = $this->AuthCheck();
        $AllCollection = $this->MasterRepository->getAllActiveCollections();
        $AllCategory = $this->MasterRepository->getAllActiveCategories();
        return view('front.user.product_and_service_add', compact('AllCollection', 'AllCategory', 'data'));
    }
    public function ProductAndServiceEdit($id){
        $data = $this->AuthCheck();
        $Product = Product::findOrFail($id);
        $AllCollection = $this->MasterRepository->getAllActiveCollections();
        $AllCategory = $this->MasterRepository->getAllActiveCategories();
        return view('front.user.product_and_service_edit', compact('AllCollection', 'AllCategory', 'data', 'Product'));
    }
    public function CollectionWiseCategory(Request $request){
        $data = $this->MasterRepository->CollectionWiseCategoryData($request->category);
        return response()->json(["status"=>200, 'data'=>$data]);
    }
    public function CollectionWiseCategoryBytitle(Request $request){
        $Collection = Collection::where('title', $request->category)->first();
        $data = $this->MasterRepository->CollectionWiseCategoryDataByTitle($Collection->id);
        return response()->json(["status"=>200, 'data'=>$data]);
    }
    public function ProductAndServiceStore(Request $request){
        // dd($request->all());
        $rules = [
            // Define validation rules for product details
            'product_image' => 'nullable:prodserv,productdetails|image',
            'product_name' => 'required_if:prodserv,productdetails',
            // 'category' => 'required_if:prodserv,productdetails',
            // 'sub_category' => 'required_if:prodserv,productdetails',
            'product_description' => 'required_if:prodserv,productdetails',
            // 'price' => 'required_if:prodserv,productdetails',
        
            // Define validation rules for service details
            'service_image' => 'nullable:prodserv,servicedetails|image|mimes:jpeg,png,jpg,gif',
            'service_name' => 'required_if:prodserv,servicedetails',
            // 'service_category' => 'required_if:prodserv,servicedetails',
            // 'service_sub_category' => 'required_if:prodserv,servicedetails',
            'service_description' => 'required_if:prodserv,servicedetails',
            'service_price' => 'nullable:prodserv,servicedetails',
        ];
        //for product
        if($request->prodserv == "productdetails"){ 
            if (request()->has('others_doc_product')) {
                $rules['other_category_product'] = 'required';
                $rules['other_sub_category_product'] = 'required';
            } else {
                // If 'others_doc_product' doesn't exist, require 'category' and 'sub_category'
                $rules['category'] = 'required';
                $rules['sub_category'] = 'required';
            }
        }
        // For Service 
        if($request->prodserv == "servicedetails"){
            if (request()->has('others_doc_service')) {
                $rules['other_category_service'] = 'required';
                $rules['other_sub_category_service'] = 'required';
            } else {
                // If 'others_doc_service' doesn't exist, require 'category' and 'sub_category'
                $rules['service_category'] = 'required';
                $rules['service_sub_category'] = 'required';
            }
        }
        
        
        $customMessages = [
            // Custom messages for product details validation
            'product_image.required_if' => 'The product image is required',
            'product_image.image' => 'The product image must be an image file.',
            'product_name.required_if' => 'The product name field is required',
            'product_name.regex' => 'The product name field should only contain letters, numbers, spaces, and hyphens.',
            'category.required_if' => 'The category field is required',
            'sub_category.required_if' => 'The sub category field is required',
            'product_description.required_if' => 'The product description field is required',
            'price.required_if' => 'The price field is required',
        
            // Custom messages for service details validation
            'service_image.required_if' => 'The service image is required',
            'service_image.image' => 'The service image must be an image file.',
            'service_image.mimes' => 'The service image must be a file of type: jpeg, png, jpg, gif.',
            'service_name.required_if' => 'The service name field is required',
            'service_name.regex' => 'The service name field should only contain letters, numbers, spaces, and hyphens.',
            'service_category.required_if' => 'The service category field is required',
            'service_sub_category.required_if' => 'The service sub category field is required',
            'service_description.required_if' => 'The service description field is required',
            // 'service_price.required_if' => 'The service price field is required',
        ];        
    
        // Validate the request
        $validator = Validator::make($request->all(), $rules, $customMessages);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('prodserv', $request->prodserv);
        }else{
            // try {
                    // $User = $this->AuthCheck();
                    // Start a database transaction
                // DB::beginTransaction();

                    
                $data =new Product;
                $data->type = $request->prodserv=="productdetails"?"Product":"Service";
                $data->title = $request->prodserv=="productdetails"?$request->product_name:$request->service_name;
                $data->slug = slugGenerate($data->title, 'products'); 
                //checking during product insert
                if (request()->has('others_doc_product')) {
                    $category = Collection::where('title', $request->other_category_product)->first();
                    if($category){
                        return redirect()->back()->with('error', 'This category already exists');
                    }
            
                    $category = new Collection;
                    $category->title = $request->other_category_product;
                    $category->image = asset('frontend/assets/images/building.png');
                    $category->created_by = 2;
                    $category->status = 3;
                    $category->save();
            
                    $sub_category = new Category;
                    $sub_category->title = $request->other_sub_category_product;
                    $sub_category->image = asset('frontend/assets/images/building.png');
                    $sub_category->collection_id = $category->id;
                    $sub_category->created_by = 2;
                    $sub_category->save();

                    $data->category_id = $category->id;
                    $data->sub_category_id= $sub_category->id;
                }else{
                    $data->category_id = $request->category?$request->category:$request->service_category;
                    $data->sub_category_id = $request->sub_category?$request->sub_category:$request->service_sub_category;
                }

                //checking during service insert
                if (request()->has('others_doc_service')) {
                    $category = Collection::where('title', $request->other_category_service)->first();
                    if ($category) {
                        return redirect()->back()->with('error', 'This category already exists');
                    }
            
                    $category = new Collection;         
                    $category->title = $request->other_category_service;
                    $category->image = 'frontend/assets/images/building.png';
                    $category->created_by = 2;
                    $category->status = 3;
                    $category->save();
            
                    $sub_category = new Category;
                    $sub_category->title = $request->other_sub_category_service;
                    $sub_category->image = 'frontend/assets/images/building.png';
                    $sub_category->collection_id = $category->id;
                    $sub_category->created_by = 2;
                    $sub_category->save();

                    $data->category_id = $category->id;
                    $data->sub_category_id = $sub_category->id;
                }else{
                    $data->category_id = $request->category?$request->category:$request->service_category;
                    $data->sub_category_id = $request->sub_category?$request->sub_category:$request->service_sub_category;
                }

                
                $data->description = $request->prodserv=="productdetails"?$request->product_description:$request->service_description;
                $data->price = $request->prodserv=="productdetails"?$request->price:$request->service_price;
                $data->specifications = $request->specifications;
                $data->image = asset('frontend/assets/images/building.png');
                $data->user_id =$this->AuthCheck()->id;

                if ($request->hasFile('product_image')) {
                    $file = $request->file('product_image');
                    $fileName = time() . rand(10000, 99999) . '.' . $file->getClientOriginalExtension(); // Generate unique filename
                    $filePath = 'uploads/product/' . $fileName; // Construct full path
                    $file->move(public_path('uploads/product'), $fileName);
                    $data->image = $filePath;
                }
                if ($request->hasFile('service_image')) {
                    $file = $request->file('service_image');
                    $fileName = time() . rand(10000, 99999) . '.' . $file->getClientOriginalExtension(); // Generate unique filename
                    $filePath = 'uploads/product/' . $fileName; // Construct full path
                    $file->move(public_path('uploads/product'), $fileName);
                    $data->image = $filePath;
                }
                $data->save();
                // Commit the transaction if all operations succeed
                // DB::commit();
                

                return redirect()->route("user.product_and_service")->with('success', 'New '.$data->type . ' added successfully');
            // } catch (\Exception $e) {
            //     // Rollback the transaction and handle the exception
            //     // DB::rollBack();
            //     return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            // }
        }
    }
    public function ProductAndServiceUpdate(Request $request){
        // dd($request->all());
        $rules = [
            // Define validation rules for product details
            'product_image' => 'nullable|image',
            'service_name' => 'required_if:prodserv,productdetails',
            'category' => 'required_if:prodserv,productdetails',
            'sub_category' => 'required_if:prodserv,productdetails',
            'product_description' => 'required_if:prodserv,productdetails',
            'price' => 'required_if:prodserv,productdetails',
        
            // Define validation rules for service details
            'service_image' => 'nullable|image',
            'service_name' => 'required_if:prodserv,servicedetails',
            'service_category' => 'required_if:prodserv,servicedetails',
            'service_sub_category' => 'required_if:prodserv,servicedetails',
            'service_description' => 'required_if:prodserv,servicedetails',
            'service_price' => 'required_if:prodserv,servicedetails',
        ];
        
        $customMessages = [
            // Custom messages for product details validation
            'product_image.image' => 'The product image must be an image file.',
            'product_name.required_if' => 'The product name field is required',
            'product_name.regex' => 'The product name field should only contain letters, numbers, spaces, and hyphens.',
            'category.required_if' => 'The category field is required',
            'sub_category.required_if' => 'The sub category field is required',
            'product_description.required_if' => 'The product description field is required',
            'price.required_if' => 'The price field is required',
        
            // Custom messages for service details validation
            'service_image.image' => 'The service image must be an image file.',
            'service_name.required_if' => 'The service name field is required',
            'service_name.regex' => 'The product name field should only contain letters, numbers, spaces, and hyphens.',
            'service_category.required_if' => 'The service category field is required',
            'service_sub_category.required_if' => 'The service sub category field is required',
            'service_description.required_if' => 'The service description field is required',
            'service_price.required_if' => 'The service price field is required',
        ];
        
    
        // Validate the request
        $validator = Validator::make($request->all(), $rules, $customMessages);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('prodserv', $request->prodserv);
        }else{
            $data =Product::findOrFail($request->id);
            $data->type = $request->prodserv=="productdetails"?"Product":"Service";
            $data->title = $request->prodserv=="productdetails"?$request->product_name:$request->service_name;
            $data->slug = slugGenerate($data->title, 'products'); 
            $data->category_id = $request->prodserv=="productdetails"?$request->category:$request->service_category;
            $data->sub_category_id = $request->prodserv=="productdetails"?$request->sub_category:$request->service_sub_category;
            $data->description = $request->prodserv=="productdetails"?$request->product_description:$request->service_description;
            $data->price = $request->prodserv=="productdetails"?$request->price:$request->service_price;
            $data->specifications = $request->specifications;
            $data->user_id =$this->AuthCheck()->id;
            if ($request->hasFile('product_image')) {
                $file = $request->file('product_image');
                $fileName = time() . rand(10000, 99999) . '.' . $file->getClientOriginalExtension(); // Generate unique filename
                $filePath = 'uploads/product/' . $fileName; // Construct full path
                $file->move(public_path('uploads/product'), $fileName);
                $data->image = $filePath;
            }
            if ($request->hasFile('service_image')) {
                $file = $request->file('service_image');
                $fileName = time() . rand(10000, 99999) . '.' . $file->getClientOriginalExtension(); // Generate unique filename
                $filePath = 'uploads/product/' . $fileName; // Construct full path
                $file->move(public_path('uploads/product'), $fileName);
                $data->image = $filePath;
            }
            $data->save();
            return redirect()->back()->with('success', $data->type . ' updated successfully');
        }
    }

    public function ProductAndServiceDelete($id){
        $data =Product::findOrFail($id);
        $data->delete();
        return redirect()->back()->with('success', $data->type.' deleted successfully');
    }

    public function RatingAndReview(){
        $data = $this->AuthCheck();
        $review_rating = $this->userRepository->getUserAllReviewRating($data->id);
        $asBuyer = $this->userRepository->asBuyer($data->id);
        $on_time_payment_rating = $this->userRepository->on_time_payment_rating($data->id);
        $delivery_cooperation_rating = $this->userRepository->delivery_cooperation_rating($data->id);
        $genuiness_rating = $this->userRepository->genuiness_rating($data->id);
        $asBuyerOverallRatingPoint = $this->userRepository->asBuyerOverallRatingPoint($data->id);
        $asSeller = $this->userRepository->asSeller($data->id);
        $on_time_delivery_rating = $this->userRepository->on_time_delivery_rating($data->id);
        $right_product_rating = $this->userRepository->right_product_rating($data->id);
        $post_delivery_service_rating = $this->userRepository->post_delivery_service_rating($data->id);
        $asSellerOverallRatingPoint = $this->userRepository->asSellerOverallRatingPoint($data->id);
        $old_location="";
        $old_keyword="";
        return view('front.user.rating', compact('data','old_location','old_keyword','review_rating','asBuyer','on_time_payment_rating','delivery_cooperation_rating','genuiness_rating', 'asSeller','on_time_delivery_rating','right_product_rating','post_delivery_service_rating','asBuyerOverallRatingPoint','asSellerOverallRatingPoint'));
    }
    public function RatingAndReviewComment(Request $request){
        $data = $this->AuthCheck();
        $request->validate([
            'comment' => 'required',
        ]);

        $comment = ReviewRating::findOrFail($request->revirew_id);
        $comment->replied_comment_author =$request->comment ;
        $comment->save();
        if($comment){
            return redirect()->back()->with('success','You replied on this review successfully');
        }

    }
    public function RConsumption(){ 
        $data = $this->AuthCheck();
        $consumption = RequirementConsumption::where('user_id', $data->id)->get();
        return view('front.user.requirement_consumption', compact('data', 'consumption'));
    }
    public function RConsumptionAdd(){
        $data = $this->AuthCheck();
        $AllCollection = $this->MasterRepository->getAllActiveCollections();
        $AllCategory = $this->MasterRepository->getAllActiveCategories();
        return view('front.user.requirement_consumption_add', compact('data', 'AllCollection', 'AllCategory'));
    }
    public function RConsumptionStore(Request $request){
        // dd($request->all());
        $rules = [
            'product_name' => 'required',
        ];
        if (request()->has('others_doc')) {
            $rules['other_category'] = 'required';
            $rules['other_sub_category'] = 'required';
        } else {
            // If 'others_doc' doesn't exist, require 'category' and 'sub_category'
            $rules['category'] = 'required';
            $rules['sub_category'] = 'required';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            try {
                $User = $this->AuthCheck();
                // Start a database transaction
                DB::beginTransaction();
            
                $data = new RequirementConsumption();
                $data->type = $request->consumption;
                $data->name = $request->product_name;
            
                if (request()->has('others_doc')) {
                    $category = Collection::where('title', $request->other_category)->first();
                    if ($category) {
                        // Rollback the transaction and return with error message if category already exists
                        DB::rollBack();
                        return redirect()->back()->with('error', 'This category already exists');
                    }
            
                    $category = new Collection;
                    $category->title = $request->other_category;
                    $category->created_by = 2;
                    $category->status = 3;
                    $category->save();
            
                    $sub_category = new Category;
                    $sub_category->title = $request->other_sub_category;
                    $sub_category->collection_id = $category->id;
                    $sub_category->created_by = 2;
                    $sub_category->save();
            
                    $data->category = $category->id;
                    $data->sub_category = $sub_category->id;
                } else {
                    $data->category = $request->category;
                    $data->sub_category = $request->sub_category;
                }
                $data->user_id = $User->id;
                $data->save();
            
                // Commit the transaction if all operations succeed
                DB::commit();
            
                return redirect()->route('user.requirements_and_consumption')->with('success', 'New Consumption added successfully');
            } catch (\Exception $e) {
                // Rollback the transaction and handle the exception
                DB::rollBack();
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }
        }
    }
    public function RConsumptionDelete($id){
        $WatchList = RequirementConsumption::findOrFail($id);
        $WatchList->delete();
        return redirect()->back();
    }

    
    public function performance_analytics(){
        $data = $this->AuthCheck();
        return view('front.user.performance_analytics', compact('data'));
    }
    public function photos_and_documents(){
        $data = $this->AuthCheck();
        $AllImages = $this->userRepository->getUserAllImages($data->id);
        $userData = $this->userRepository->getUserAllData($data->id);
        $user_document = $userData['userDocument'];
        $user_additional_document = $userData['userAdditionalDocument'];
        return view('front.user.photos_and_documents', compact('data', 'AllImages', 'user_document','user_additional_document'));
    }
    public function payment_management(){
        $data = $this->AuthCheck();
        $packages = $this->userRepository->getUserAllPackages();
        $seller_packages = $this->userRepository->getSellerAllPackages();
        $myBadges = $this->userRepository->getAllBadgesById($data->id);
        $myBadgesFullDetails = $this->userRepository->myBadgesFullDetails($data->id);
        $allBadges = $this->userRepository->getAllBadges($myBadges);
        $verifiedBadge = $this->userRepository->verifiedBadge();
        $trustedBadge = $this->userRepository->trustedBadge();
        $my_cuttent_seller_package = $this->userRepository->getCurrentSellerPackage($data->id);
        $my_cuttent_buyer_package = $this->userRepository->getCurrentBuyerPackage($data->id);
        return view('front.user.payment_management', compact('data','packages','seller_packages','myBadges','allBadges', 'my_cuttent_seller_package', 'my_cuttent_buyer_package','myBadgesFullDetails','verifiedBadge','trustedBadge'));
    }
    public function wallet_management(){
        $data = $this->AuthCheck();
        $my_current_seller_package = MySellerPackage::where('user_id',$data->id)->latest()->first();
        $my_current_buyer_package = MyBuyerPackage::where('user_id',$data->id)->latest()->first();
        $seller_walletBalance = MySellerWallet::where(["user_id"=>$data->id])->latest()->first();
        $buyer_walletBalance = MyBuyerWallet::where(["user_id"=>$data->id])->latest()->first();
        $buyer_credit_left = MyBuyerWallet::where(["user_id"=>$data->id])->sum('credit_unit');
        $buyer_credit_used = MyBuyerWallet::where(["user_id"=>$data->id])->sum('debit_unit');
        $buyer_credit_left = $buyer_credit_left-$buyer_credit_used;
        $seller_credit_left = MySellerWallet::where(["user_id"=>$data->id])->sum('credit_unit');
        $seller_credit_used = MySellerWallet::where(["user_id"=>$data->id])->sum('debit_unit');
        $seller_credit_left = $seller_credit_left-$seller_credit_used;
        return view('front.user.wallet_management',compact('data','my_current_seller_package','seller_walletBalance','my_current_buyer_package','buyer_walletBalance', 'buyer_credit_left', 'buyer_credit_used', 'seller_credit_left', 'seller_credit_used'));
    }
    public function package_payment_management(Request $request){
        // dd($request->all());
        $data = $this->AuthCheck();
        if($data){
            try {
                $negotiable_amount = 0;
                $my_current_package = MySellerPackage::with('getPackageDetails')->where('user_id', $data->id)->first();
                if($request->form_type=='upgrade'){
                    if($my_current_package){
                        $current_package_duration = $my_current_package->package_duration;
                        $monthly_package_price = $my_current_package->monthly_package_price;
                        $negotiable_amount = $monthly_package_price*($current_package_duration-$my_current_package->usage_months);
                    }
                }
                // Start a database transaction
                // DB::beginTransaction();
                $duration = 30*$request->package_duration;
                $monthly_package_price = $request->package_value/$request->package_duration;
                
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
                $my_current_package->package_id = $request->package_id;
                $my_current_package->user_id = $data->id;
                $my_current_package->monthly_package_price = $monthly_package_price;
                $my_current_package->package_duration = $request->package_duration;
                $my_current_package->monthly_credit = $request->monthly_credit;
                $my_current_package->next_credit_date = $next_credit_date;
                $my_current_package->purchase_amount = $request->package_value;    
                $my_current_package->expiry_date = $expiryDate;
                $my_current_package->save();
                $package_name = $my_current_package->getPackageDetails?$my_current_package->getPackageDetails->package_name:"Package";

                // Retrieve the latest wallet record for the user
                $latest_wallet = MySellerWallet::where('user_id', $data->id)->latest()->first();
                // Calculate the current balance based on the latest wallet record
                $current_balance = $latest_wallet ? $latest_wallet->current_unit : 0;
                // Update Wallet
                $monthly_credit = $request->monthly_credit;
                $my_wallet = new MySellerWallet();
                $my_wallet->user_id = $data->id;
                $my_wallet->type = 1; //Credit
                $my_wallet->purpose = $package_name; 
                $my_wallet->credit_unit = $request->monthly_credit;
                $my_wallet->current_unit = $current_balance + $monthly_credit;
                $my_wallet->save();
                // For Amount Transaction
                $transaction = new Transaction();
                $transaction->user_id = $data->id;
                $transaction->unique_id = GenerateYearWiseTransaction(8, 'transactions', date('Y'));
                $transaction->transaction_type = 1; //Online
                $transaction->status = 1; //Paid
                $transaction->user_type = 1; //Seller
                $transaction->transaction_id = $request->seller_razorpay_payment_id; // Adjusted range for 8-digit number
                $transaction->purpose = $request->form_type=='upgrade'?'Upgrade seller package':'New seller package';
                $transaction->actual_amount = $request->package_value;
                $transaction->negotiable_amount = $negotiable_amount;
                $transaction->amount = $request->package_value-$negotiable_amount;
                $transaction->seller_package_id = $request->package_id;
                $transaction->transaction_source = $request->seller_payment_method;
                $transaction->save();
                if($transaction){
                    $websiteLog =new WebsiteLogs();
                    $websiteLog->logs_type ="INSERTED";
                    $websiteLog->table_name ="transactions";
                    $websiteLog->response =json_encode($transaction);
                    $websiteLog->save();
                }
                DB::commit();
            } catch (\Exception $e) {
                // Rollback the transaction and handle the exception
                DB::rollBack();
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }
            //OrderID Generate
            $razorpayKey = env('RAZORPAY_KEY');
            $razorpaySecret = env('RAZORPAY_SECRET');

            // Prepare the data for the order
            $orderData = [
                'receipt'         => $transaction->unique_id,
                'amount'          => $transaction->amount * 100, // amount in the smallest currency unit
                'currency'        => 'INR',
                'payment_capture' => 1 // auto capture
            ];
            // Encode the order data
            $jsonData = json_encode($orderData);
            
            // Initialize cURL
            $ch = curl_init();

            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode("$razorpayKey:$razorpaySecret")
            ]);

            // Execute the cURL request
            $response = curl_exec($ch);
            // Check for errors
            if ($response === false) {
                $errorMessage = curl_error($ch);
                // $order_id = "";
            }

            $data_pdf=[
                'user'=>$data,
                'package'=>$package_name,
                'transaction'=>$transaction,
            ];
            
            // Generate the PDF
            $PDFOptions = ['enable_remote' => true];
            $pdf = PDF::setOptions($PDFOptions)->loadView('mail.pdf', $data_pdf);
            // Save the PDF to a file on the local server
            $pdfPath = storage_path('app/public/' . date('His') . '_invoice.pdf');
           

            $pdf->save($pdfPath);

            $data_array=[
                'cc'=>[],
                'user'=>$data,
                'attachment'=>$pdfPath,
                'transaction_type'=>'Seller: '.$package_name,
                'start_date'=>date('d-m-Y', strtotime($my_current_package->created_at)),
                'end_date'=>date('d-m-Y', strtotime($my_current_package->expiry_date)),
                'transaction'=>$transaction,
                'type'=>'PAYMENT_TRANSACTION',
            ];
            
            $sender = env('SMS_SENDER');
            $amount = "Rs.".$transaction->amount?number_format($transaction->amount,2):"";
            $expiry_date = date('d-m-Y',strtotime($my_current_package->expiry_date));
            $url = 'https://milaapp.in';
            $myMessage = urlencode("Payment of ".$amount." for the subscription of ".$package_name." is confirmed. Valid till ".$expiry_date." For details: www.milaapp.in Sarv Megh Technology OPC Private Limited");
            $customer_mobile_no = $data->mobile?$data->mobile:null;
            $checkPhoneNumberValid = checkPhoneNumberValid($customer_mobile_no);
            if($checkPhoneNumberValid){
                sendSMS($sender, $customer_mobile_no, $myMessage);
            }
            sendMail($data_array, $data->email, 'Confirmation of Payment Transaction on Milaapp');
           
            if (Session::has('url.intended')) {
                $intendedUrl = Session::get('url.intended');
                // Forget the intended URL from the session after using it
                Session::forget('url.intended');
                return redirect($intendedUrl);
            }else{
                return redirect()->route('user.payment_management')->with('success', 'Package has been successfully purchased');  
            }
        }else{
            return redirect()->route('login');
        }
    }
    public function seller_package_check(Request $request){
        $negotiable_amount = 0;
        $my_current_package = MySellerPackage::with('getPackageDetails')->where('user_id', $request->id)->first();
        if($my_current_package){
            $current_package_duration = $my_current_package->package_duration;
            $monthly_package_price = $my_current_package->monthly_package_price;
            $negotiable_amount = $monthly_package_price*($current_package_duration-$my_current_package->usage_months);
        }
        $amount = $request->amount-$negotiable_amount;
        if ($amount > 0) {
            return response()->json(['status' => 200, 'amount' => $amount]);
        } else {
            return response()->json(['status' => 500, 'error' => 'Something went wrong! Please contact the support team!']);
        }
       
        
    }
    public function buyer_package_store(Request $request){
        // dd($request->all());
        $data = $this->AuthCheck();
        $razorpayKey = env('RAZORPAY_KEY');
        $razorpaySecret = env('RAZORPAY_SECRET');
        $payment_id = $request->buyer_razorpay_payment_id;
        // Log the exception to the web_logs table
        $ch = curl_init();

        // Set cURL options
        curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/payments/$payment_id");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPGET, true); // Use GET method to fetch payment details
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode("$razorpayKey:$razorpaySecret")
        ]);

        // Execute the cURL request
        $response = curl_exec($ch);
        // Check for errors
        if ($response === false) {
            $errorMessage = curl_error($ch);
            return redirect()->route('user.payment_management')->with('error', $errorMessage);
        } else {
            $responseData = json_decode($response, true);
            // Check if response is valid
            if (isset($responseData['id'])) {
                // Payment details fetched successfully
                $Package = Package::findOrFail($request->package_id);
                $paymentAmount = $responseData['amount']/100;
                if($paymentAmount == $Package->package_price){
                    try {
                        DB::beginTransaction();
                        $negotiable_amount = 0;
                        $duration = 30*$request->package_duration;
                        $expiryDate = Carbon::now()->addDays($duration);
                        $my_basic_checking = MyBuyerOpeningPackage::where('user_id', $data->id)->first();
                        if($my_basic_checking && $request->package_type=="premium" || $my_basic_checking && $request->package_type=="basic"){
                            $my_current_package = MyBuyerPackage::with('package_data')->where('user_id', $data->id)->first();
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
                                $my_current_package->package_id = $request->package_id;
                                $my_current_package->user_id = $data->id;
                                $my_current_package->package_type = $request->package_type;
                                $my_current_package->package_amount =$request->package_amount;
                                $my_current_package->package_duration =$request->package_duration;
                                $my_current_package->package_credit =$request->package_credit;
                                $my_current_package->expiry_date =$expiryDate;
                                $my_current_package->save();
            
                        }else{
                            if($request->package_type=="basic"){
                                $basic_package = new MyBuyerOpeningPackage;
                                $basic_package->package_id = $request->package_id;
                                $basic_package->user_id = $data->id;
                                $basic_package->package_type = $request->package_type;
                                $basic_package->package_amount =$request->package_amount;
                                $basic_package->package_duration =$request->package_duration;
                                $basic_package->package_credit =$request->package_credit;
                                $basic_package->expiry_date =$expiryDate;
                                $basic_package->save();
                                if($basic_package){
                                    $my_current_package = new MyBuyerPackage;
                                    $my_current_package->package_id = $request->package_id;
                                    $my_current_package->user_id = $data->id;
                                    $my_current_package->package_type = $request->package_type;
                                    $my_current_package->package_amount =$request->package_amount;
                                    $my_current_package->package_duration =$request->package_duration;
                                    $my_current_package->package_credit =$request->package_credit;
                                    $my_current_package->expiry_date =$expiryDate;
                                    $my_current_package->save();
                                }
                            }else{
                                DB::commit();
                                return redirect()->back()->with('warning', 'Please purchase the basic package first, one time only.');
                            }
                        }
            
                        // Retrieve the latest wallet record for the user
                        $latest_wallet = MyBuyerWallet::where('user_id', $data->id)->latest()->first();
                        // Calculate the current balance based on the latest wallet record
                        $current_balance = $latest_wallet ? $latest_wallet->current_unit : 0;
                        // Update Wallet
                        $package_credit = $request->package_credit;
                        $my_wallet = new MyBuyerWallet();
                        $my_wallet->user_id = $data->id;
                        $my_wallet->type = 1; //Credit
                        // $my_wallet->inquiry_id = null;
                        $my_wallet->purpose = "For purchase package";
                        $my_wallet->credit_unit = $request->package_credit;
                        $my_wallet->current_unit = $current_balance + $package_credit;
                        $my_wallet->save();
            
                        $transaction = new Transaction();
                        $transaction->user_id = $data->id;
                        $transaction->unique_id = GenerateYearWiseTransaction(8, 'transactions', date('Y')); // Adjusted range for 8-digit number
                        $transaction->transaction_type = 1; //Online
                        $transaction->status = 1; //Paid
                        $transaction->user_type = 2; //Buyer
                        $transaction->transaction_id = $request->buyer_razorpay_payment_id; // Adjusted range for 8-digit number
                        $transaction->purpose = $request->form_type=='upgrade'?'Upgrade buyer package':'New buyer package';
                        $transaction->actual_amount = $request->package_amount;
                        $transaction->negotiable_amount = $negotiable_amount;
                        $transaction->amount = $request->package_amount-$negotiable_amount;
                        $transaction->buyer_package_id = $request->package_id;
                        $transaction->transaction_source = $request->buyer_payment_method;
                        $transaction->remarks = NULL;
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
                            $package_name = $my_current_package->package_data?$my_current_package->package_data->package_name:"Package";
                        }
                        DB::commit();
                    } catch (\Exception $e) {
                        // Rollback the transaction and handle the exception
                        DB::rollBack();
                        // dd($e->getMessage());
                        // return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
                        return redirect()->back()->with('error', 'Something went wrong, please try again later!');
                    }
                    if($transaction){
                        //OrderID Generate
                        $razorpayKey = env('RAZORPAY_KEY');
                        $razorpaySecret = env('RAZORPAY_SECRET');
            
                        // Prepare the data for the order
                        $orderData = [
                            'receipt'         => $transaction->unique_id,
                            'amount'          => $transaction->amount * 100, // amount in the smallest currency unit
                            'currency'        => 'INR',
                            'payment_capture' => 1 // auto capture
                        ];
                        // Encode the order data
                        $jsonData = json_encode($orderData);
            
                        // Initialize cURL
                        $ch = curl_init();
            
                        // Set cURL options
                        curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'Content-Type: application/json',
                            'Authorization: Basic ' . base64_encode("$razorpayKey:$razorpaySecret")
                        ]);
            
                        // Execute the cURL request
                        $response = curl_exec($ch);
                        // Check for errors
                        if ($response === false) {
                            $errorMessage = curl_error($ch);
                            // $order_id = "";
                        }
                        if($request->buyer_razorpay_payment_id){
                            $paymentId = $request->buyer_razorpay_payment_id; // Example payment ID, replace with actual payment ID
                            $amount = $paymentAmount * 100; // Amount in paise (for INR)

                            // Prepare the data for the capture
                            $captureData = [
                                'amount'   => $amount,
                                'currency' => 'INR'
                            ];

                            // Encode the capture data to JSON
                            $jsonData = json_encode($captureData);

                            // Initialize cURL
                            $ch = curl_init();

                            // Set cURL options
                            curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/payments/$paymentId/capture");
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                'Content-Type: application/json',
                                'Authorization: Basic ' . base64_encode("$razorpayKey:$razorpaySecret")
                            ]);

                            // Execute the cURL request
                            $response = curl_exec($ch);
                            // Decode the response
                            $responseData = json_decode($response, true);
                            // Close cURL
                            curl_close($ch);
                        }
                        $data_pdf=[
                            'user'=>$data,
                            'package'=>$package_name,
                            'transaction'=>$transaction,
                        ];
            
                        // Generate the PDF
                        $PDFOptions = ['enable_remote' => true];
                        $pdf = PDF::setOptions($PDFOptions)->loadView('mail.pdf', $data_pdf);
                        // Save the PDF to a file on the local server
                        $pdfPath = storage_path('app/public/' . date('His') . '_invoice.pdf');
                        $pdf->save($pdfPath);
            
                        $data_array=[
                            'cc'=>[],
                            'user'=>$data,
                            'attachment'=>$pdfPath,
                            'transaction_type'=>'Buyer: '.$package_name,
                            'start_date'=>date('d-m-Y', strtotime($my_current_package->created_at)),
                            'end_date'=>date('d-m-Y', strtotime($my_current_package->expiry_date)),
                            'transaction'=>$transaction,
                            'type'=>'PAYMENT_TRANSACTION',
                        ];
                        // FOR MESSAGE
                        $sender = env('SMS_SENDER');
                        $amount = "Rs.".$transaction->amount?number_format($transaction->amount,2):"";
                        $expiry_date = date('d-m-Y', strtotime($my_current_package->expiry_date));
                        $url = 'https://milaapp.in';
                        $package_name = $package_name.' package';
                        $myMessage = urlencode("Payment of ".$amount." for the subscription of ".$package_name." is confirmed. Valid till ".$expiry_date." For details: www.milaapp.in Sarv Megh Technology OPC Private Limited");
                        $customer_mobile_no = $data->mobile?$data->mobile:null;
                        $checkPhoneNumberValid = checkPhoneNumberValid($customer_mobile_no);
                        if($checkPhoneNumberValid){
                            sendSMS($sender, $customer_mobile_no, $myMessage);
                        }
                        // FOR MAIL
                        sendMail($data_array, $data->email, 'Confirmation of Payment Transaction on Milaapp');
                        // For Amount Transaction
                        if (Session::has('url.intended')) {
                            $intendedUrl = Session::get('url.intended');
                            // Forget the intended URL from the session after using it
                            Session::forget('url.intended');
                            return redirect($intendedUrl);
                        }else{
                            return redirect()->route('user.payment_management')->with('success', 'Package has been successfully purchased');  
                        }
                    }else{
                        return redirect()->route('user.payment_management')->with('success', 'Package has been successfully purchased'); 
                    }
                }else{
                    return redirect()->back()->with('error', 'Transaction amount does not match the package amount!');
                }
            }else{
                 return redirect()->back()->with('error', 'Something went wrong, please contact with admin!');
            }
        }
       
        
    }
    public function buyer_package_check(Request $request){
        $my_basic_checking = MyBuyerOpeningPackage::where('user_id', $request->id)->first();
        if($my_basic_checking && $request->package_type=="premium" || $my_basic_checking && $request->package_type=="basic"){
            return response()->json(['status'=>200]);
        }else{
            if($request->package_type=="basic"){
                return response()->json(['status'=>200]);
            }else{
                return response()->json(['status'=>500]);
            }
        }
    }
    public function settings(){
        $data = $this->AuthCheck();
        $package = MySellerPackage::where('user_id',$data->id)->latest()->first();
        $walletBalance = MySellerWallet::where(["user_id"=>$data->id])->latest()->first();
        return view('front.user.settings', compact('data','package','walletBalance'));
    }

    public function transaction(Request $request){
        $data = $this->AuthCheck();
        $startDate = $request->start_date ?? '';
        $endDate = $request->end_date ?? '';
        $mode = $request->mode ?? '';
        $purpose = $request->purpose ?? '';
        // Check if any of the parameters are provided
        // If keyword is provided or both start_date and end_date are provided
        if (!empty($mode) || !empty($startDate) || !empty($endDate)|| !empty($purpose)) {  
            $transactions = $this->userRepository->getSearchTransactionByUserId($data->id,$purpose,$mode,$startDate, $endDate);
        }else{
            $transactions = $this->userRepository->getAllTransactionByUserId($data->id);
        }
        $purpose_array = [];
        if(count($transactions)>0){
            foreach($transactions as $k=>$item){
                $purpose_array[] = $item->purpose;
            }
            $purpose_array = array_unique($purpose_array);
        }
        return view('front.user.transaction',compact('data','transactions', 'purpose_array'));
    }
    public function notifications(Request $request){
        $data = $this->AuthCheck();
        // $startDate = $request->start_date ?? '';
        // $endDate = $request->end_date ?? '';
        // $mode = $request->mode ?? '';
        // $purpose = $request->purpose ?? '';
        // // Check if any of the parameters are provided
        // // If keyword is provided or both start_date and end_date are provided
        // if (!empty($mode) || !empty($startDate) || !empty($endDate)|| !empty($purpose)) {  
        //     $transactions = $this->userRepository->getSearchTransactionByUserId($data->id,$startDate, $endDate,$mode,$purpose);
        // }else{
        //     $transactions = $this->userRepository->getAllTransactionByUserId($data->id);
        // }
        // return view('front.user.transaction',compact('data','transactions'));
        $oldData = Notification::where('buyer_id', $data->id)->where('seller_id', $data->id)->get();
        foreach($oldData as $k =>$item){
            $update = Notification::findOrFail($item->id);
            $update->read_at =1;
            $update->save();
        }
        $notification = Notification::where('seller_id',$data->id)->latest('id')->get();
        return view('front.user.notifiction',compact('data','notification'));
    }
    public function notifications_marks_read(Request $request){
        $notification = Notification::findOrFail($request->id);
        $notification->read_at =1;
        $notification->save();
        return response()->json(['status'=>200]);
    }
   
    public function seller_wallet_transaction(){
        $data = $this->AuthCheck();
        $seller_wallet_transactions = $this->userRepository->getSellerAllWalletTransactionByUserId($data->id);
        return view('front.user.seller_wallet',compact('data','seller_wallet_transactions'));
    }
    public function buyer_wallet_transaction(){
        $data = $this->AuthCheck();
        $buyer_wallet_transactions = $this->userRepository->getBuyerAllWalletTransactionByUserId($data->id);
        return view('front.user.buyer_wallet',compact('data','buyer_wallet_transactions'));
    }
    public function seller_package_history(){
        $data = $this->AuthCheck();
        $seller_package_history = $this->userRepository->getSellerPackagehistory($data->id);
        return view('front.user.seller_package_history',compact('data','seller_package_history'));
    }
    public function buyer_package_history(){
        $data = $this->AuthCheck();
        $buyer_package_history = $this->userRepository->getBuyerPackagehistory($data->id);
        // dd($buyer_package_history);
        return view('front.user.buyer_package_history',compact('data','buyer_package_history'));
    }
    public function changePassword(){
        $data = $this->AuthCheck();
        return view('front.user.change_password',compact('data'));
    }
    public function changePasswordUpdate(Request $request){
        // dd($request->all());
        $data = $this->AuthCheck();
        // dd($data);
        $rules = [
            'old_password'=>'required|min:6|max:12',
            'password' => 'required|min:6|max:12',
            'confirm_password' => 'required_with:password|same:password',
        ];
    
        $customMessages = [
            'old_password.required' => 'The old password field is required.',
            'old_password.min' => 'The old password should be at least 6 characters long.',
            'old_password.max' => 'The old password should not exceed 12 characters.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password should be at least 6 characters long.',
            'password.max' => 'The password should not exceed 12 characters.',
            'confirm_password.required_with' => 'The confirm password field is required when password is present.',
            'confirm_password.same' => 'The confirm password must match the password.',
        ];
        
            // Validate the data
            $validator = Validator::make($request->all(), $rules, $customMessages);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }else{
                // Check if the old password matches the user's current password

                $user = User::findOrFail($data->id);
                if(!Hash::check($request->old_password,$user->password)){
                    return redirect()->back()->withErrors(['old_password' => 'The old password does not match our records.'])->withInput();
                }
                $user->password=Hash::make($request->password);
                $user->save();
                return redirect()->route('user.settings')->with('success', 'Your profile has been data updated successfully');
            }
    }


    public function MyWatchlist(Request $request){
        $data = $this->AuthCheck();
        $group_slug = $request->group ? $request->group : '';
        if($group_slug){
            $ExistGroupWatch = GroupWatchList::where('slug', $group_slug)->where('created_by', $data->id)->first();
            if(empty($ExistGroupWatch)){
                return abort(404);
            }
        }
       
        $existing_inquiries= $this->BuyerDashboardRepository->get_all_existing_inquiries_by_user($data->id);
        $WatchList = WatchList::with('SellerData')->where('buyer_id', $data->id)->where('group_id', null)->get();
        $groupWatchList = GroupWatchList::orderBy('group_name', 'ASC')->where('created_by',$data->id)->get();
        $verifiedBadge = $this->userRepository->verifiedBadge();
        $trustedBadge = $this->userRepository->trustedBadge();
        return view('front.user.watchlist', compact('WatchList','groupWatchList', 'existing_inquiries','verifiedBadge','trustedBadge'));
    }

    public function seller_buk_upload_on_group_watchlist(Request $request){
        $data = $this->AuthCheck();
        $GroupWatchList = GroupWatchList::where('slug', $request->group_slug)->where('created_by', $data->id)->first();
        if($GroupWatchList){
            foreach($request->seller as $key =>$item){
                $WatchList = WatchList::where('buyer_id', $data->id)->where('seller_id', $item)->where('group_id', null)->first();
                $WatchList->group_id = $GroupWatchList->id;
                $WatchList->save();
            }
            $route = route('user.watchlist.my_watchlist_by_group', $GroupWatchList->slug);
            return response()->json(['status'=>200, 'route'=>$route]);
        }else{
            return response()->json(['status'=>400]);
        }
    }
    public function MyWatchlistDataSore(Request $request){
        $fetch = WatchList::where('buyer_id', $request->buyer_id)->where('seller_id', $request->seller_id)->where('group_id', null)->first();
        if($fetch){
            return redirect()->back()->with('warning', 'The seller is already on the watchlist..');
        }else{
            $WatchList = new WatchList;
            $WatchList->seller_id =$request->seller_id;
            $WatchList->buyer_id =$request->buyer_id;
            $WatchList->save();

            if($WatchList){
                // Retrieve the buyer's name
                $buyer = User::find($request->buyer_id);
                $buyerName = $buyer ? $buyer->first_name : '';
                $title = $buyerName . ' added you to a watchlist';
                notification_push(NULL,$request->buyer_id,$request->seller_id,$title,NULL,NULL);

            }
            return redirect()->back()->with('success', 'Seller has been successfully added to the watchlist..');
        }
    }
    public function UserToSellerReportStore(Request $request)
    {
        $request->validate([
            'report_message' => 'required'
        ], [
            'report_message.required' => 'You must leave a report message before submitting.'
        ]);
    
        $report = new SellerReport();   
        $report->seller_id = $request->seller_id;    
        $report->report_by = $request->user_id;
        $report->content = $request->report_message;
        $report->save();
    
        return back()->with('success', 'Report Submitted successfully.');
    }
    
    public function MyGroupWatchlistDataSore(Request $request){
        $fetch = WatchList::where('buyer_id', $request->buyer_id)->where('seller_id', $request->seller_id)->where('group_id',$request->group_id)->first();
        if($fetch){
            return redirect()->back()->with('warning', 'The seller is already on the watchlist..');
        }else{
            $WatchList = new WatchList;
            $WatchList->seller_id =$request->seller_id;
            $WatchList->buyer_id =$request->buyer_id;
            $WatchList->group_id =$request->group_id;
            $WatchList->status =2;
            $WatchList->save();

            if($WatchList){
                // Retrieve the buyer's name
                $buyer = User::find($request->buyer_id);
                $buyerName = $buyer ? $buyer->first_name : '';
                $group = GroupWatchList::find($request->group_id);
                $groupName = $group ? $group->group_name : '';
                $title = $buyerName . ' added you to '.'<strong>'.$groupName.'</strong>'.' group watchlist';
                $link = route('user.watchlist');


                notification_push(NULL,$request->buyer_id,$request->seller_id,$title,NULL,$link);

            }

            return redirect()->back()->with('success', 'Seller has been successfully added to the watchlist..');
        }
    }

    public function CreateGroupWatchlist(Request $request){
    $data = $this->AuthCheck();
    $groupName = ucfirst($request->group_watchlist_name);
    $fetch = GroupWatchList::where('created_by', $data->id)->where('group_name',$groupName)->first();
    if($fetch){
        return response()->json(['status'=>400]);
    }else{
        $groupWatchList = new GroupWatchList;
        $groupWatchList->created_by =$data->id;
        $groupWatchList->group_name =$groupName;
        $groupWatchList->slug =GroupslugGenerate($groupName, 'group_watchlist');
        $groupWatchList->save();
        return response()->json(['status'=>200]);
    }
    }
    public function UpdateGroupWatchlist(Request $request){
        $data = $this->AuthCheck();
        $groupName = ucfirst($request->group_watchlist_name);
        $fetch = GroupWatchList::where('created_by', $data->id)->where('group_name',$groupName)->where('id', '!=', $request->id)->first();
        if($fetch){
            return response()->json(['status'=>400]);
        }else{
            $update = GroupWatchList::findOrFail($request->id);
            $update->group_name = $groupName;
            $update->slug =GroupslugGenerateUpdate($groupName, 'group_watchlist', $update->id);
            $update->save();
            return response()->json(['status'=>200]);
        }
    }
    public function DeleteGroupWatchlist(Request $request){
        $groupWatchList = GroupWatchList::findOrFail($request->id);
        // Check if the GroupWatchList is found
        if ($groupWatchList) {
            $watchList = WatchList::where('group_id', $groupWatchList->id)->get();
            // Delete related WatchList records
            $watchList->each->delete();
            // Delete the GroupWatchList record
            $groupWatchList->delete();
        }
        return response()->json(['status'=>200]);     
    }
    public function DeleteSingleWatchlist(Request $request)
    {
        // Find the watchlist entry or fail if not found
        $watchList = WatchList::findOrFail($request->id);
        
        // Retrieve the buyer's name before deleting the watchlist entry
        $buyer = User::find($watchList->buyer_id);
        $buyerName = $buyer ? $buyer->first_name : '';

        $wasDeleted = $watchList->delete();
        if ($wasDeleted) {
            $title = $buyerName . ' removed you from his watchlist';
            notification_push(NULL,$watchList->buyer_id,$watchList->seller_id,$title,NULL,NULL);
        }
    
        // Return a JSON response indicating success
        return response()->json(['status' => 200]);
    }
    public function AddSingleWatchlist(Request $request){
        $user = $this->AuthCheck();
        $exist_user = WatchList::with('SellerData')->where('buyer_id', $user->id)->where('seller_id', $request->seller_id)->first();
        if(!isset($exist_user)){
            $WatchList = new WatchList;
            $WatchList->seller_id =$request->seller_id;
            $WatchList->buyer_id =$user->id;
            $WatchList->save();
    
            if($WatchList){
                // Retrieve the buyer's name
                $buyer = User::find($user->id);
                $buyerName = $buyer ? $buyer->first_name : '';
                $title = $buyerName . ' added you to a watchlist';
                notification_push(NULL,$user->id,$request->seller_id,$title,NULL,NULL);
            }
            // Return a JSON response indicating success
            return response()->json(['status' => 200, 'item_id'=>$WatchList->id]);
        }else{
            // Return a JSON response indicating success
            return response()->json(['status' => 400]);    
        }
        
         
    }
    
    public function MyWatchlistDataDelete($id){
        $WatchList = WatchList::findOrFail($id);
        $WatchList->delete();
        return redirect()->back();
    }
    public function OutSideParticipantsDelete(Request $request){
        $outSideParticipant = OutsideParticipant::findOrFail($request->id);
        $outSideParticipant->delete();
        return response()->json(['status'=>200]);     
    }
    public function ExsistingOutSideParticipantsDelete(Request $request){
        $outSideParticipant = InquiryOutsideParticipant::findOrFail($request->id);
        $outSideParticipant->delete();
        return response()->json(['status'=>200]);     
    }
    public function photos_and_documents_edit(){
        $data = $this->AuthCheck();
        $AllImages = $this->userRepository->getUserAllImages($data->id);
        $userData = $this->userRepository->getUserAllData($data->id);
        $user_document = $userData['userDocument'];
        $user_additional_document = $userData['userAdditionalDocument'];
        return view('front.user.photos_and_documents_edit', compact('data','user_document', 'AllImages','user_additional_document'));
    }

    public function photos_and_documents_update(Request $request){  
      
        $user = $this->AuthCheck();
        if ($request->hasFile('user_images')) {
            $request->validate([
                'user_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:110240',
            ], [
                'user_images.*.image' => 'The file must be an image.',
                'user_images.*.mimes' => 'The file must be a jpeg, png, jpg, or gif.',
                'user_images.*.max' => 'The file may not be greater than 10 MB in size.',
            ]);
            foreach ($request->file('user_images') as $image) {
                $user_image = new UserImage();
                $filename = "User-images" . rand(10000, 99999) . time() . "." . $image->getClientOriginalExtension();
                $image->move('uploads/userData', $filename);
                $user_image->image = 'uploads/userData/' . $filename;
                $user_image->user_id = $user->id;
                $user_image->save();
            }
        }

        $request->validate([
            'gst_file' => 'nullable|file|mimes:pdf,jpeg,png,jpg,jfif|max:2048',
            'gst_number' => 'nullable|string|max:255',
            'pan_file' => 'nullable|file|mimes:pdf,jpeg,png,jpg,jfif|max:2048',
            'pan_number' => 'nullable|string|max:255',
            'adhar_file' => 'nullable|file|mimes:pdf,jpeg,png,jpg,jfif|max:2048',
            'adhar_number' => 'nullable|string|max:255',
            'trade_license_file' => 'nullable|file|mimes:pdf,jpeg,png,jpg,jfif|max:2048',
            'trade_license_number' => 'nullable|string|max:255',
            'cancelled_cheque_file' => 'nullable|file|mimes:pdf,jpeg,png,jpg,jfif|max:2048',
            'account_number' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:255',
        ], [
            'gst_file.file' => 'The uploaded GST file is invalid.',
            'gst_file.mimes' => 'The GST file must be a PDF or image (jpeg, png, jpg).',
            'gst_file.max' => 'The GST file may not be greater than 2MB in size.',
            'gst_number.string' => 'The GST number must be a string.',
            'gst_number.max' => 'The GST number may not be greater than 255 characters.',
            'pan_file.file' => 'The uploaded PAN file is invalid.',
            'pan_file.mimes' => 'The PAN file must be a PDF or image (jpeg, png, jpg).',
            'pan_file.max' => 'The PAN file may not be greater than 2MB in size.',
            'pan_number.string' => 'The PAN number must be a string.',
            'pan_number.max' => 'The PAN number may not be greater than 255 characters.',
            'adhar_file.file' => 'The uploaded Aadhar file is invalid.',
            'adhar_file.mimes' => 'The Aadhar file must be a PDF or image (jpeg, png, jpg).',
            'adhar_file.max' => 'The Aadhar file may not be greater than 2MB in size.',
            'adhar_number.string' => 'The Aadhar number must be a string.',
            'adhar_number.max' => 'The Aadhar number may not be greater than 255 characters.',
            'trade_license_file.file' => 'The uploaded Trade License file is invalid.',
            'trade_license_file.mimes' => 'The Trade License file must be a PDF or image (jpeg, png, jpg).',
            'trade_license_file.max' => 'The Trade License file may not be greater than 2MB in size.',
            'trade_license_number.string' => 'The Trade License number must be a string.',
            'trade_license_number.max' => 'The Trade License number may not be greater than 255 characters.',
            'cancelled_cheque_file.file' => 'The uploaded Cancelled Cheque file is invalid.',
            'cancelled_cheque_file.mimes' => 'The Cancelled Cheque file must be a PDF or image (jpeg, png, jpg).',
            'cancelled_cheque_file.max' => 'The Cancelled Cheque file may not be greater than 2MB in size.',
            'account_number.string' => 'The Account number must be a string.',
            'account_number.max' => 'The Account number may not be greater than 255 characters.',
            'ifsc_code.string' => 'The IFSC code must be a string.',
            'ifsc_code.max' => 'The IFSC code may not be greater than 255 characters.',
        ]);
        

        $ExistData = UserDocument::where('user_id', $user->id)->first();
        if($ExistData){
            $user_document = UserDocument::findOrFail($ExistData->id);
        }else{
            $user_document = new UserDocument;
        }
       
        if (isset($request->gst_file)) {
            $file = $request->gst_file;
            $filename = rand(10000, 99999) . time() . "." . $file->getClientOriginalName();
            $file->move('uploads/userData', $filename);
            $user_document->gst_file = 'uploads/userData/' . $filename;
        }
        if (isset($request->pan_file)) {
            $file = $request->pan_file;
            $filename = rand(10000, 99999) . time() . "." . $file->getClientOriginalName();
            $file->move('uploads/userData', $filename);
            $user_document->pan_file = 'uploads/userData/' . $filename;
        }
        if (isset($request->adhar_file)) {
            $file = $request->adhar_file;
            $filename = rand(10000, 99999) . time() . "." . $file->getClientOriginalName();
            $file->move('uploads/userData', $filename);
            $user_document->adhar_file = 'uploads/userData/' . $filename;
        }
        if (isset($request->trade_license_file)) {
            $file = $request->trade_license_file;
            $filename = rand(10000, 99999) . time() . "." . $file->getClientOriginalName();
            $file->move('uploads/userData', $filename);
            $user_document->trade_license_file = 'uploads/userData/' . $filename;
        }
        if (isset($request->cancelled_cheque_file)) {
            $file = $request->cancelled_cheque_file;
            $filename = rand(10000, 99999) . time() . "." . $file->getClientOriginalName();
            $file->move('uploads/userData', $filename);
            $user_document->cancelled_cheque_file = 'uploads/userData/' . $filename;
        }
        $user_document->gst_number = $request->gst_number;
        $user_document->user_id = $user->id;
        $user_document->pan_number = $request->pan_number;
        $user_document->adhar_number = $request->adhar_number;
        $user_document->trade_license_number = $request->trade_license_number;
        $user_document->account_number = $request->account_number;
        $user_document->ifsc_code = $request->ifsc_code;
        $user_document->save();
        if(count($request->additional_documents)>0){
            foreach ($request->additional_documents as $key => $value) {
                if($value){
                    $AddiData = new UserAdditionalDocument;
                    $AddiData->user_id = $user->id;
                    if (isset($request->additional_document_file[$key])) {
                        $file = $request->additional_document_file[$key];
                        $filename = rand(10000, 99999) . time() . "." . $file->getClientOriginalName();
                        $file->move('uploads/userData', $filename);
                        $AddiData->additional_document_file = 'uploads/userData/' . $filename;
                    }
                    $AddiData->additional_documents = $value;
                    $AddiData->save();
                }
                
            }
        }
        return response()->json(['status'=>'success']);
    }

    public function photos_and_documents_delete(Request $request){
        $UserImage = UserImage::findOrFail($request->id);
        $UserImage->delete();
        return response()->json(['status'=>200]);
    }

    public function additional_photos_and_documents_delete(Request $request){
        // dd($request->all());
        $UserAdditionalDocument = UserAdditionalDocument::findOrFail($request->id);
        $filePath = $UserAdditionalDocument->additional_document_file;
        if(file_exists($filePath)){
            if (!unlink($filePath)) {
                return response()->json(['status'=>500, 'message' => 'File could not be deleted.']);
            }
        }
        
        $UserAdditionalDocument->delete();
        return response()->json(['status'=>200]);
    }

    public function my_watchlist_by_group($slug){
        $User = $this->AuthCheck();
        $GroupWatchList = GroupWatchList::where('slug', $slug)->where('created_by', $User->id)->first();
        if($GroupWatchList){
            $existing_inquiries= $this->BuyerDashboardRepository->get_all_existing_inquiries_by_user($User->id);
            $WatchList =WatchList::with('SellerData')->where('group_id', $GroupWatchList->id)->get();
            $outSideParticipats =OutsideParticipant::with('BuyerDetails')->where('group_id', $GroupWatchList->id)->get();
            $verifiedBadge = $this->userRepository->verifiedBadge();
            $trustedBadge = $this->userRepository->trustedBadge();



            // dd($outSideParticipats);
             return view('front.user.watchlist_by_group', compact('WatchList', 'GroupWatchList', 'existing_inquiries','outSideParticipats','verifiedBadge','trustedBadge'));
        }else{
            return redirect()->route('user.watchlist')->with('warning', 'Group not found in your panel. Please enter a valid group name.');
        }
    }
    public function purchase(Request $request){
        if($request->razorpay_payment_id){
            // Log the exception to the web_logs table
            $payment_id = $request->razorpay_payment_id;
            $razorpayKey = env('RAZORPAY_KEY');
            $razorpaySecret = env('RAZORPAY_SECRET');
            $ch = curl_init();

            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/payments/$payment_id");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPGET, true); // Use GET method to fetch payment details
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Basic ' . base64_encode("$razorpayKey:$razorpaySecret")
            ]);

            // Execute the cURL request
            $response = curl_exec($ch);
            // Check for errors
            if ($response === false) {
                $errorMessage = curl_error($ch);
                return response()->json(['status' => 400, 'error'=>'Sorry, something went wrong!']);
            } else {
                $responseData = json_decode($response, true);
                // Check if response is valid
                if (isset($responseData['id'])) {
                    // Payment details fetched successfully
                    $Badge = Badge::findOrFail($request->id);
                    $paymentAmount = $responseData['amount']/100;
                    if($paymentAmount == $Badge->price){
                        try{
                            DB::beginTransaction();
                            $duration =isset($request->duration)?$request->duration:12;
                            $data = $this->AuthCheck();
                            // Create a new transaction
                            
                            $package = $Badge?$Badge->title:"";
                            $transaction = new Transaction();
                            $transaction->user_id = $data->id;
                            $transaction->unique_id = GenerateYearWiseTransaction(8, 'transactions', date('Y'));
                            $transaction->transaction_type = 1;
                            $transaction->status = 1;
                            $transaction->transaction_id = $request->razorpay_payment_id;
                            $transaction->purpose = 'Badge: '.$package;
                            $transaction->amount = $request->amount;
                            $transaction->transaction_source = $request->payment_method;
                            $transaction->save();
                            
                            // Create a new MyBadge
            
                            $myBadge = new MyBadge();
                            $myBadge->user_id = $data->id;
                            $myBadge->badge_id = $request->id;
                            $myBadge->duration = $duration;
                            $myBadge->expiry_date = Carbon::now()->addDays($duration * 30);
                            $myBadge->save();
                            DB::commit();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            return response()->json(['status' => 400, 'error'=>'Sorry, something went wrong!']);
                        }
                        // PDF Generate
                        $data_pdf=[
                            'user'=>$data,
                            'package'=>$package,
                            'transaction'=>$transaction,
                        ];
                        
                        // Generate the PDF
                        $PDFOptions = ['enable_remote' => true];
                        $pdf = PDF::setOptions($PDFOptions)->loadView('mail.pdf', $data_pdf);
                        // Save the PDF to a file on the local server
                        $pdfPath = storage_path('app/public/' . date('His') . '_invoice.pdf');
                        $pdf->save($pdfPath);
                        
                        // Return success response
            
                        //OrderID Generate
                        $razorpayKey = env('RAZORPAY_KEY');
                        $razorpaySecret = env('RAZORPAY_SECRET');
            
                        // Prepare the data for the order
                        $orderData = [
                            'receipt'         => $transaction->unique_id,
                            'amount'          => $transaction->amount * 100, // amount in the smallest currency unit
                            'currency'        => 'INR',
                            'payment_capture' => 1 // auto capture
                        ];
                        // Encode the order data
                        $jsonData = json_encode($orderData);
                        
                        // Initialize cURL
                        $ch = curl_init();
            
                        // Set cURL options
                        curl_setopt($ch, CURLOPT_URL, 'https://api.razorpay.com/v1/orders');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POST, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, [
                            'Content-Type: application/json',
                            'Authorization: Basic ' . base64_encode("$razorpayKey:$razorpaySecret")
                        ]);
            
                        // Execute the cURL request
                        $response = curl_exec($ch);
                        // Check for errors
                        if ($response === false) {
                            $errorMessage = curl_error($ch);
                            // $order_id = "";
                        }
                        if($request->razorpay_payment_id){// Example payment ID, replace with actual payment ID
                            $amount = $paymentAmount * 100; // Amount in paise (for INR)

                            // Prepare the data for the capture
                            $captureData = [
                                'amount'   => $amount,
                                'currency' => 'INR'
                            ];

                            // Encode the capture data to JSON
                            $jsonData = json_encode($captureData);

                            // Initialize cURL
                            $ch = curl_init();

                            // Set cURL options
                            curl_setopt($ch, CURLOPT_URL, "https://api.razorpay.com/v1/payments/$payment_id/capture");
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                                'Content-Type: application/json',
                                'Authorization: Basic ' . base64_encode("$razorpayKey:$razorpaySecret")
                            ]);

                            // Execute the cURL request
                            $response = curl_exec($ch);
                            // Decode the response
                            $responseData = json_decode($response, true);
                            // Close cURL
                            curl_close($ch);
                        }
            
                        // Decode the response
                        // $responseData = json_decode($response, true);
                        $package = 'Badge: '.$package;
                        $data_array=[
                            'cc'=>[],
                            'user'=>$data,
                            'attachment'=>$pdfPath,
                            'transaction_type'=>$package,
                            'start_date'=>date('d-m-Y', strtotime($myBadge->created_at)),
                            'end_date'=>date('d-m-Y', strtotime($myBadge->expiry_date)),
                            'transaction'=>$transaction,
                            'type'=>'PAYMENT_TRANSACTION',
                        ];
                        $sender = env('SMS_SENDER');
                        
                        $amount = "Rs.".$transaction->amount?number_format($transaction->amount,2):"";
                        $expiry_date = date('d-m-Y',strtotime($myBadge->expiry_date));
                        $url = 'https://milaapp.in';
                        $myMessage = urlencode("Payment of ".$amount." for the subscription of ".$package." is confirmed. Valid till ".$expiry_date." For details: www.milaapp.in Sarv Megh Technology OPC Private Limited");
                        $customer_mobile_no = $data->mobile?$data->mobile:null;
                        $checkPhoneNumberValid = checkPhoneNumberValid($customer_mobile_no);
                        if($checkPhoneNumberValid){
                            sendSMS($sender, $customer_mobile_no, $myMessage);
                        }
                        sendMail($data_array, $data->email, 'Confirmation of Payment Transaction on Milaapp');
                        return response()->json(['status' => 200]);
                    }else{
                        return response()->json(['status' => 400, 'error'=>'Transaction amount does not match the package amount!']);
                    }
                }else{
                    return response()->json(['status' => 400, 'error'=>'Sorry, something went wrong!']);
                }
            }
        }else{
            return response()->json(['status' => 400, 'error'=>'Sorry, something went wrong!']);
        }
    }
    public function verify_badge_price(Request $request){
        $Badge = Badge::where('id', $request->id)->first();
        if($Badge){
            return response()->json(['status' => 200, 'price'=>$Badge->price]);
        }else{
            return response()->json(['status' => 400]);
        }
        
    }

    public function StateWiseCity(Request $request){
        $data = $this->MasterRepository->StateWiseCityData($request->state);
        return response()->json(["status"=>200, 'data'=>$data]);
    }

    public function InviteOutSideParticipants(Request $request){
    $data = $this->AuthCheck();
        // Make sure both arrays have the same number of elements
      if(count($request->name) !== count($request->phone)) {
        return response()->json(['status' => 400, 'error'=>'Names and phones arrays must have the same number of elements']);
      }
        $goupId = $request->groupId;
        // Iterate over the arrays and save each participant
        $seller_data = [];

        foreach ($request->name as $key => $name) {
            $phone = $request->phone[$key]; // Get corresponding phone number

            if ($name == null) {
                return response()->json(['status' => 400, 'error' => 'Please enter name.']);
            }

            if ($phone == null) {
                return response()->json(['status' => 400, 'error' => 'Please enter phone number.']);
            }

            if (checkPhoneNumberValid($phone) == false) {
                return response()->json(['status' => 400, 'error' => 'Phone number must be exactly 10 digits.']);
            }

            $User = User::where('mobile', $phone)->first();

            if ($User) {
                if ($request->groupId) {
                    $exist_user = WatchList::with('SellerData')
                        ->where('buyer_id', $data->id)
                        ->where('seller_id', $User->id)
                        ->where('group_id', $request->groupId)
                        ->first();
                } else {
                    $exist_user = WatchList::with('SellerData')
                        ->where('buyer_id', $data->id)
                        ->where('seller_id', $User->id)
                        ->first();
                }
                if (!isset($exist_user)) {
                    $WatchList = new WatchList;
                    $WatchList->buyer_id = $data->id;
                    $WatchList->seller_id = $User->id;
                    $WatchList->group_id = $request->groupId ? $request->groupId : null;
                    $WatchList->save();

                    $exist_user_data = [
                        'type' => 'inside',
                        'id' => $WatchList->id,
                        'user_id' => $User->id,
                        'group_id' => $WatchList->group_id,
                        'name' => ucwords($User->business_name),
                    ];
                } else {
                    return response()->json(['status' => 400, 'error' => $name . ' seller already exists']);
                }
            } else {
                $OutsideParticipant = OutsideParticipant::where('buyer_id', $data->id)->where('mobile', $phone)->first();
                if($OutsideParticipant){
                    return response()->json(['status' => 400, 'error' => $phone . ' this phone number already exists']);
                }else{
                    $outSide_participants = new OutsideParticipant();
                    $outSide_participants->group_id = $request->groupId; // Use groupId directly
                    $outSide_participants->buyer_id = $data->id;
                    $outSide_participants->name = $name;
                    $outSide_participants->mobile = $phone;
                    $outSide_participants->save();
    
                    $exist_user_data = [
                        'type' => 'outside',
                        'id' => $outSide_participants->id,
                        'group_id' => $outSide_participants->group_id,
                        'name' => ucwords($name),
                    ];
                }
            }

            // Add the exist_user_data to the seller_data array
            $seller_data[] = $exist_user_data;
        }
        return response()->json(['status' => 200, 'seller'=>$seller_data]);
        }

    // public function mail(){
    //     // dd('mail');
    //     $data =['name'=>'amit','data'=>'hellow sir'];
    //     $user['to']='amit.s@techmantra.co';
    //     $response = Mail::raw('front.user.mail',$data,function($message) use ($user){
    //         $message->to($user['to']);
    //         $message->subject('hellow dev');
    //     });
    //     dd($response);

    //     }
//     public function mail()
// {
//     $data = ['name' => 'amit', 'data' => 'hello sir'];
//     $user = ['to' => 'amit.s@techmantra.co'];
    
//     Mail::send('front.user.mail', $data, function($message) use ($user) {
//         $message->to($user['to'])
//                 ->subject('Hello Dev');
//     });

//     dd("Mail sent successfully!"); // Optional: Check if the mail sending is successful
// }
    public function mail(){
        Mail::to('amit.s@techmantra.co')
        ->send(new welcomeMail());
        dd("Mail sent successfully!"); // Optional: Check if the mail sending is successful
    }


}