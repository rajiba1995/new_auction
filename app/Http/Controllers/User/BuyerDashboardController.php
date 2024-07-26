<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Contracts\UserContract;
use App\Contracts\BuyerDashboardContract;
use App\Contracts\MasterContract;
use App\Models\Inquiry;
use App\Models\InquiryAllotmentData;
use App\Models\InquiryParticipant;
use App\Models\User;
use App\Models\AllotOffline;
use App\Models\InquirySellerQuotes;
use App\Models\InquirySellerComments;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log;



class BuyerDashboardController extends Controller
{
    protected $userRepository;
    protected $MasterRepository;
    protected $BuyerDashboardRepository;

    public function __construct(UserContract $userRepository, MasterContract $MasterRepository, BuyerDashboardContract $BuyerDashboardRepository) {
        $this->userRepository = $userRepository;
        $this->MasterRepository = $MasterRepository;
        $this->BuyerDashboardRepository = $BuyerDashboardRepository;

    }
    private function getAuthenticatedUserId() {
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user()->id;
        }
        return null;
    }

    public function index(Request $request){
        $existing_inquiries= $this->BuyerDashboardRepository->get_all_existing_inquiries_by_user($this->getAuthenticatedUserId());
        $group_wise_list =  $this->BuyerDashboardRepository->group_wise_inquiries_by_user($this->getAuthenticatedUserId());
        $saved_inquiries =  $this->BuyerDashboardRepository->saved_inquiries_by_user($this->getAuthenticatedUserId());
        $live_inquiries =  $this->BuyerDashboardRepository->live_inquiries_by_user($this->getAuthenticatedUserId());
        $confirmed_inquiry_data =  $this->BuyerDashboardRepository->confirmed_inquiries_by_user($this->getAuthenticatedUserId());
        $pending_inquiries_data =  $this->BuyerDashboardRepository->pending_inquiries_by_user($this->getAuthenticatedUserId());
        $cancelled_inquiry_data =  $this->BuyerDashboardRepository->cancelled_inquiries_by_user($this->getAuthenticatedUserId());


        return view('front.user_dashboard.index', compact('group_wise_list', 'existing_inquiries','saved_inquiries','live_inquiries','confirmed_inquiry_data','pending_inquiries_data','cancelled_inquiry_data'));
    }

    public function saved_inquiries(Request $request){
       

        // Check if any search parameters are provided
        $isSearch = $request->has('start_date') || $request->has('end_date') || $request->has('seller') || $request->has('keyword');
    
        if ($isSearch) {
            $saved_inquiries = $this->BuyerDashboardRepository->all_save_inquiries_by_search(
                $this->getAuthenticatedUserId(),
                $request->input('start_date'),
                $request->input('end_date'),
                $request->input('keyword')
            );
        }else{
            $saved_inquiries =  $this->BuyerDashboardRepository->saved_inquiries_by_user($this->getAuthenticatedUserId());
        }
        $group_wise_list =  $this->BuyerDashboardRepository->group_wise_inquiries_by_user($this->getAuthenticatedUserId());
        $live_inquiries =  $this->BuyerDashboardRepository->live_inquiries_by_user($this->getAuthenticatedUserId());
        $confirmed_inquiry_data =  $this->BuyerDashboardRepository->confirmed_inquiries_by_user($this->getAuthenticatedUserId());
        $pending_inquiries_data =  $this->BuyerDashboardRepository->pending_inquiries_by_user($this->getAuthenticatedUserId());
        $cancelled_inquiry_data =  $this->BuyerDashboardRepository->cancelled_inquiries_by_user($this->getAuthenticatedUserId());
        return view('front.user_dashboard.saved_inquireis', compact('saved_inquiries','group_wise_list','live_inquiries','confirmed_inquiry_data','pending_inquiries_data','cancelled_inquiry_data'));
    }
    public function live_inquiries(Request $request){
        $live_inquiries =  $this->BuyerDashboardRepository->live_inquiries_by_user($this->getAuthenticatedUserId());
        $group_wise_list =  $this->BuyerDashboardRepository->group_wise_inquiries_by_user($this->getAuthenticatedUserId());
        $saved_inquiries =  $this->BuyerDashboardRepository->saved_inquiries_by_user($this->getAuthenticatedUserId());
        $confirmed_inquiry_data =  $this->BuyerDashboardRepository->confirmed_inquiries_by_user($this->getAuthenticatedUserId());
        $pending_inquiries_data =  $this->BuyerDashboardRepository->pending_inquiries_by_user($this->getAuthenticatedUserId());
        $cancelled_inquiry_data =  $this->BuyerDashboardRepository->cancelled_inquiries_by_user($this->getAuthenticatedUserId());



        return view('front.user_dashboard.live_inquireis', compact('live_inquiries','saved_inquiries','group_wise_list','confirmed_inquiry_data','pending_inquiries_data','cancelled_inquiry_data'));
    }
    public function pending_inquiries(Request $request){
      
        $user_id = $this->getAuthenticatedUserId();
    
    // Check if any search parameters are provided
    $isSearch = $request->has('start_date') || $request->has('end_date') || $request->has('seller') || $request->has('keyword');
    
    if ($isSearch) {
        $pending_inquiries_data = $this->BuyerDashboardRepository->all_inquiries_by_search(
            $user_id,
            $request->input('start_date'),
            $request->input('end_date'),
            $request->input('seller'),
            $request->input('keyword'),2
        );

    } else {
        $pending_inquiries_data =  $this->BuyerDashboardRepository->pending_inquiries_by_user($user_id);
    }

       
        $group_wise_list =  $this->BuyerDashboardRepository->group_wise_inquiries_by_user($user_id);
        $confirmed_inquiry_data =  $this->BuyerDashboardRepository->confirmed_inquiries_by_user($user_id);
        $cancelled_inquiry_data =  $this->BuyerDashboardRepository->cancelled_inquiries_by_user($user_id);
        $live_inquiries =  $this->BuyerDashboardRepository->live_inquiries_by_user($user_id);
        $saved_inquiries =  $this->BuyerDashboardRepository->saved_inquiries_by_user($user_id);

        $pending_inquiries = [];
        $suppliers = [];
        $suppliers_data = [];
        
        if(count($pending_inquiries_data)>0){
            foreach ($pending_inquiries_data as $key => $value) {
                $seller_data = [];
                $all_inquiries = [];
               
                $all_inquiries['id'] = $value->id;
                $all_inquiries['inquiry_id'] = $value->inquiry_id;
                $all_inquiries['created_by'] = $value->BuyerData->name;
                $all_inquiries['title'] = $value->title;
                $all_inquiries['start_date_time'] = date('d M, Y h:i A', strtotime($value->start_date.' '.$value->start_time));
                
                $all_inquiries['end_date_time'] = date('d M, Y h:i A', strtotime($value->end_date.' '.$value->end_time));
               
                $all_inquiries['category'] = $value->category;
                $all_inquiries['sub_category'] = $value->sub_category;
                $all_inquiries['description'] = $value->description;
                $all_inquiries['execution_date'] = $value->execution_date;
                $all_inquiries['quotes_per_participants'] = $value->quotes_per_participants;
                $all_inquiries['minimum_quote_amount'] = $value->minimum_quote_amount;
                $all_inquiries['maximum_quote_amount'] = $value->maximum_quote_amount;
                $all_inquiries['inquiry_type'] = $value->inquiry_type;
                $all_inquiries['inquiry_amount'] = $value->inquiry_amount;
                $all_inquiries['location'] = $value->location;
                $all_inquiries['status'] = $value->status;
                $sub_suppliers = [];
                if($value->ParticipantsData){
                    foreach($value->ParticipantsData as $k =>$item){
                        if($item->SellerData){
                            $all_inquiries['participants'][]=$item->SellerData?$item->SellerData->business_name:"";
                            $Suppliers_data['id']= $item->SellerData?$item->SellerData->id:"";
                            $Suppliers_data['name']= $item->SellerData?$item->SellerData->business_name:"";
                           
                            // if($item->status==1){
                                $all_inquiries['invted_participants'][]= $item->SellerData?$item->SellerData->business_name:"";
                            // }
                            $sub_suppliers[] = $Suppliers_data;
                        }
                       
                    }
                }
                $all_inquiries['invted_participants_count'] = 0;
                if (isset($all_inquiries['participants'])) {
                    $all_inquiries['invted_participants_count'] = count($all_inquiries['participants']);
                }
                $getAllSellerQuotes = getAllSellerQuotes($value->id);
                $all_inquiries['participants_count'] = 0;
                if (isset($all_inquiries['participants'])) {
                    $all_inquiries['participants_count'] = count($all_inquiries['invted_participants']);
                }
                    if(count($getAllSellerQuotes)>0){
                        foreach($getAllSellerQuotes as $k =>$itemk){
                            $seller = [];
                            $seller['id'] = $itemk->id;
                            $seller['inquiry_id'] = $itemk->inquiry_id;
                            $seller['seller_id'] = $itemk->seller_id;
                            $seller['name'] = $itemk->name;
                            $seller['country_code'] = $itemk->country_code;
                            $seller['mobile'] = $itemk->mobile;
                            $seller['business_name'] = $itemk->business_name;
                            $seller['last_three_quotes'] = [];
                            foreach(get_last_three_quotes($itemk->inquiry_id,$itemk->seller_id) as $index=> $qItem){
                                $seller['last_three_quotes'][]=$qItem->quotes; 
                                if($index==0){
                                    $seller['quotes'] = $itemk->quotes;
                                }
                            }
                            $seller['last_three_quotes'] = array_reverse($seller['last_three_quotes']);
                            $SellerCommentsData = SellerCommentsData($itemk->inquiry_id, $itemk->seller_id);
                            $SellerFileData = SellerFileData($itemk->inquiry_id, $itemk->seller_id);
                            $seller['seller_comments_data'] = $SellerCommentsData;
                            $seller['seller_file_data'] = $SellerFileData;
                            $seller_data[]= $seller;
                        }
                    }
                    $all_inquiries['seller_data'] = $seller_data;
                
                $pending_inquiries[] = $all_inquiries;
                $suppliers[] = $sub_suppliers;
               
                
            }
            // For Suppliers data filter
            $main_suppliers = [];
            if(count($suppliers)>0){
                foreach($suppliers as $k=>$item){
                    foreach($item as $index=>$value){
                        $semi_suppliers['name'] = $value['name'];
                        $semi_suppliers['id'] = $value['id'];
                        $main_suppliers[]=$semi_suppliers;
                    }
                }
            }
            // Extract unique IDs
            $uniqueIds = array_unique(array_column($main_suppliers, 'id'));

            // Filter the original array to include only unique items
            $suppliers_data = array_intersect_key($main_suppliers, $uniqueIds);
           
        }
        return view('front.user_dashboard.pending_inquireis', compact('pending_inquiries','live_inquiries','saved_inquiries','group_wise_list','confirmed_inquiry_data','pending_inquiries_data','cancelled_inquiry_data', 'suppliers_data'));
    }
    public function confirmed_inquiries(Request $request){
        $user_id = $this->getAuthenticatedUserId();
    
    // Check if any search parameters are provided
    $isSearch = $request->has('start_date') || $request->has('end_date') || $request->has('seller') || $request->has('keyword');
    
    if ($isSearch) {
        $confirmed_inquiry_data = $this->BuyerDashboardRepository->all_inquiries_by_search(
            $user_id,
            $request->input('start_date'),
            $request->input('end_date'),
            $request->input('seller'),
            $request->input('keyword'),3
        );
    } else {
        $confirmed_inquiry_data = $this->BuyerDashboardRepository->confirmed_inquiries_by_user($user_id);
    }
        // $confirmed_inquiry_data =  $this->BuyerDashboardRepository->confirmed_inquiries_by_user($user_id);
        $group_wise_list =  $this->BuyerDashboardRepository->group_wise_inquiries_by_user($user_id);
        $pending_inquiries_data =  $this->BuyerDashboardRepository->pending_inquiries_by_user($user_id);
        $cancelled_inquiry_data =  $this->BuyerDashboardRepository->cancelled_inquiries_by_user($user_id);
        $live_inquiries =  $this->BuyerDashboardRepository->live_inquiries_by_user($user_id);
        $saved_inquiries =  $this->BuyerDashboardRepository->saved_inquiries_by_user($user_id);


        $confirmed_inquiries = [];
        $suppliers_data = [];
        if(count($confirmed_inquiry_data)>0){
            foreach ($confirmed_inquiry_data as $key => $value) {
                $seller_data = [];
                $all_inquiries = [];
                $all_inquiries['id'] = $value->id;
                $all_inquiries['allotment_type'] = $value->allotment_type;
                $all_inquiries['allot_seller'] = $value->allot_seller;
                $all_inquiries['inquiry_id'] = $value->inquiry_id;
                $all_inquiries['bill'] = $value->bill;
                $all_inquiries['bill_at'] = $value->bill_at;
                $all_inquiries['created_by'] = $value->BuyerData->name;
                $all_inquiries['title'] = $value->title;
                $all_inquiries['start_date_time'] = date('d M, Y h:i A', strtotime($value->start_date.' '.$value->start_time));
                
                $all_inquiries['end_date_time'] = date('d M, Y h:i A', strtotime($value->end_date.' '.$value->end_time));
               
                $all_inquiries['category'] = $value->category;
                $all_inquiries['sub_category'] = $value->sub_category;
                $all_inquiries['description'] = $value->description;
                $all_inquiries['execution_date'] = $value->execution_date;
                $all_inquiries['quotes_per_participants'] = $value->quotes_per_participants;
                $all_inquiries['minimum_quote_amount'] = $value->minimum_quote_amount;
                $all_inquiries['maximum_quote_amount'] = $value->maximum_quote_amount;
                $all_inquiries['inquiry_type'] = $value->inquiry_type;
                $all_inquiries['inquiry_amount'] = $value->inquiry_amount;
                $all_inquiries['location'] = $value->location;
                $all_inquiries['status'] = $value->status;
                $sub_suppliers = [];
                if($value->ParticipantsData){
                    foreach($value->ParticipantsData as $k =>$item){
                        $all_inquiries['participants'][]= $item->SellerData?$item->SellerData->business_name:"";
                        $Suppliers_data['id']= $item->SellerData?$item->SellerData->id:"";
                        $Suppliers_data['name']= $item->SellerData?$item->SellerData->business_name:"";
                       
                        // if($item->status==1){
                            $all_inquiries['invted_participants'][]= $item->SellerData?$item->SellerData->business_name:"";
                        // }
                        $sub_suppliers[] = $Suppliers_data;
                    }
                }
                $all_inquiries['invted_participants_count'] = 0;
                if (isset($all_inquiries['participants'])) {
                    $all_inquiries['invted_participants_count'] = count($all_inquiries['participants']);
                }
                $getAllSellerQuotes = getAllSellerQuotes($value->id);
                $all_inquiries['participants_count'] = 0;
                if (isset($all_inquiries['participants'])) {
                    $all_inquiries['participants_count'] = count($all_inquiries['invted_participants']);
                }
                    if(count($getAllSellerQuotes)>0){
                        foreach($getAllSellerQuotes as $k =>$itemk){
                            $seller = [];
                            $seller['id'] = $itemk->id;
                            $seller['inquiry_id'] = $itemk->inquiry_id;
                            $seller['seller_id'] = $itemk->seller_id;
                            $seller['quotes'] = $itemk->quotes;
                            $seller['name'] = $itemk->name;
                            $seller['country_code'] = $itemk->country_code;
                            $seller['mobile'] = $itemk->mobile;
                            $seller['business_name'] = $itemk->business_name;
                            $seller['last_three_quotes'] = [];
                            foreach(get_last_three_quotes($itemk->inquiry_id,$itemk->seller_id) as $qItem){
                                $seller['last_three_quotes'][]=$qItem->quotes; 
                            }
                            $seller['last_three_quotes'] = array_reverse($seller['last_three_quotes']);
                            $SellerCommentsData = SellerCommentsData($itemk->inquiry_id, $itemk->seller_id);
                            $SellerFileData = SellerFileData($itemk->inquiry_id, $itemk->seller_id);
                            $seller['seller_comments_data'] = $SellerCommentsData;
                            $seller['seller_file_data'] = $SellerFileData;
                            $seller_data[]= $seller;
                        }
                    }
                    
                    $all_inquiries['seller_data'] = $seller_data;
                
                $confirmed_inquiries[] = $all_inquiries;
                $suppliers[] = $sub_suppliers;
            }
             // For Suppliers data filter
             $main_suppliers = [];
             if(count($suppliers)>0){
                 foreach($suppliers as $k=>$item){
                     foreach($item as $index=>$value){
                         $semi_suppliers['name'] = $value['name'];
                         $semi_suppliers['id'] = $value['id'];
                         $main_suppliers[]=$semi_suppliers;
                     }
                 }
             }
              // Extract unique IDs
            $uniqueIds = array_unique(array_column($main_suppliers, 'id'));
             // Filter the original array to include only unique items
            $suppliers_data = array_intersect_key($main_suppliers, $uniqueIds);
           
        }
        return view('front.user_dashboard.confirmed_inquireis', compact('confirmed_inquiries','live_inquiries','saved_inquiries','group_wise_list','confirmed_inquiry_data','pending_inquiries_data','cancelled_inquiry_data','suppliers_data'));
    }
    // confirm_enquiry_export
    public function exportConfirmInquiries(Request $request){
        $user_id = $this->getAuthenticatedUserId();
        
        // Retrieve confirmed inquiries based on search parameters
        $confirmed_inquiry_data = $this->BuyerDashboardRepository->all_inquiries_by_search(
            $user_id,
            $request->input('start_date'),
            $request->input('end_date'),
            $request->input('seller'),
            $request->input('keyword'),
            3 // Assuming '3' is the status code for confirmed inquiries
        );
    
        // Determine if the search includes participants
        $includesParticipants = !is_null($request->input('seller'));
    
            $delimiter = ",";
            $fileName = "Inquiry Details-".date('d-m-Y').".csv";
            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Conditional header based on whether participants are included
            if ($includesParticipants) {
                $header = array('Sl.No', 'Inquiry ID', 'Title', 'Start Date Time', 'End Date Time', 'Category', 
                    'Sub Category', 'Description', 'Execution Date',
                    'Minimum Quote Amount', 'Maximum Quote Amount', 'Participant Details', 'Last Quote', 
                    'Inquiry Type', 'Allot Seller', 'Final Allot Amount', 'Location', 'Remarks'
                );
            } else {
                $header = array('Sl.No', 'Inquiry ID', 'Title', 'Start Date Time', 'End Date Time', 'Category', 
                    'Sub Category', 'Description', 'Execution Date',
                    'Minimum Quote Amount', 'Maximum Quote Amount', 
                    'Inquiry Type', 'Allot Seller', 'Final Allot Amount', 'Location', 'Remarks');
            }
            fputcsv($f,$header,$delimiter);
    
            // Add the data of the CSV
            foreach ($confirmed_inquiry_data as $index => $inquiry) {
                $description = strip_tags($inquiry->description);
                $allot_seller = User::where('id', $inquiry->allot_seller)->first();
                if ($includesParticipants) {
                    $seller_id = $request->input('seller');
                    $InquirySellerQuotes = InquirySellerQuotes::with('SellerData')->where('inquiry_id', $inquiry->id)->where('seller_id', $seller_id)->latest()->first();
                    $seller_info = 'Name: ' . ($InquirySellerQuotes->SellerData ? $InquirySellerQuotes->SellerData->business_name : '') 
                    . ' Mobile No: ' . ($InquirySellerQuotes->SellerData ? $InquirySellerQuotes->SellerData->mobile : '');
        
                    $exportData = array(
                        $index+1,
                        $inquiry->inquiry_id, 
                        $inquiry->title, 
                        date('d M, Y h:i A', strtotime($inquiry->start_date.' '.$inquiry->start_time)), 
                        date('d M, Y h:i A', strtotime($inquiry->end_date.' '.$inquiry->end_time)), 
                        $inquiry->category, 
                        $inquiry->sub_category, 
                        $description, 
                        $inquiry->execution_date,
                        $inquiry->minimum_quote_amount, 
                        $inquiry->maximum_quote_amount, 
                        $seller_info, 
                        $InquirySellerQuotes->quotes, 
                        $inquiry->inquiry_type, 
                        $allot_seller?$allot_seller->business_name:"", 
                        $inquiry->inquiry_amount, 
                        $inquiry->location, 
                        $inquiry->status == 3 ? "Confirmed" : ''
                    );
                } else {
                    $exportData = array(
                       $index+1, 
                        $inquiry->inquiry_id, 
                        $inquiry->title, 
                        date('d M, Y h:i A', strtotime($inquiry->start_date.' '.$inquiry->start_time)), 
                        date('d M, Y h:i A', strtotime($inquiry->end_date.' '.$inquiry->end_time)), 
                        $inquiry->category, 
                        $inquiry->sub_category, 
                        $description, 
                        $inquiry->execution_date, 
                        $inquiry->minimum_quote_amount, 
                        $inquiry->maximum_quote_amount, 
                        $inquiry->inquiry_type, 
                        $allot_seller?$allot_seller->business_name:"", 
                        $inquiry->inquiry_amount, 
                        $inquiry->location, 
                        $inquiry->status ==3 ? "Confirmed" : ''
                    );
                }
                fputcsv($f,$exportData,$delimiter);
            }
            fseek($f,0);
            // Set headers to download file rather than displayed
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $fileName . '";');

            //output all remaining data on a file pointer
            fpassthru($f);
    }
    
    public function cancelled_inquiries(Request $request){
        $user_id = $this->getAuthenticatedUserId();

        // Check if any search parameters are provided
        $isSearch = $request->has('start_date') || $request->has('end_date') || $request->has('seller') || $request->has('keyword');
    
        if ($isSearch) {
            $cancelled_inquiry_data = $this->BuyerDashboardRepository->all_inquiries_by_search(
                $user_id,
                $request->input('start_date'),
                $request->input('end_date'),
                $request->input('seller'),
                $request->input('keyword'),4
            );
        } else {
            $cancelled_inquiry_data = $this->BuyerDashboardRepository->cancelled_inquiries_by_user($user_id);
        }

        // $cancelled_inquiry_data =  $this->BuyerDashboardRepository->cancelled_inquiries_by_user($this->getAuthenticatedUserId());
        $group_wise_list =  $this->BuyerDashboardRepository->group_wise_inquiries_by_user($user_id);
        $saved_inquiries =  $this->BuyerDashboardRepository->saved_inquiries_by_user($user_id);
        $live_inquiries =  $this->BuyerDashboardRepository->live_inquiries_by_user($user_id);
        $confirmed_inquiry_data =  $this->BuyerDashboardRepository->confirmed_inquiries_by_user($user_id);
        $pending_inquiries_data =  $this->BuyerDashboardRepository->pending_inquiries_by_user($user_id);

        $cancelled_inquiries = [];
        $suppliers_data = [];

        if(count($cancelled_inquiry_data)>0){
            foreach ($cancelled_inquiry_data as $key => $value) {
                $seller_data = [];
                $all_inquiries = [];
                $all_inquiries['id'] = $value->id;
                $all_inquiries['allot_seller'] = $value->allot_seller;
                $all_inquiries['inquiry_id'] = $value->inquiry_id;
                $all_inquiries['created_by'] = $value->BuyerData->name;
                $all_inquiries['title'] = $value->title;
                $all_inquiries['start_date_time'] = date('d M, Y h:i A', strtotime($value->start_date.' '.$value->start_time));
                
                $all_inquiries['end_date_time'] = date('d M, Y h:i A', strtotime($value->end_date.' '.$value->end_time));
               
                $all_inquiries['category'] = $value->category;
                $all_inquiries['sub_category'] = $value->sub_category;
                $all_inquiries['description'] = $value->description;
                $all_inquiries['execution_date'] = $value->execution_date;
                $all_inquiries['quotes_per_participants'] = $value->quotes_per_participants;
                $all_inquiries['minimum_quote_amount'] = $value->minimum_quote_amount;
                $all_inquiries['maximum_quote_amount'] = $value->maximum_quote_amount;
                $all_inquiries['inquiry_type'] = $value->inquiry_type;
                $all_inquiries['inquiry_amount'] = $value->inquiry_amount;
                $all_inquiries['location'] = $value->location;
                $all_inquiries['status'] = $value->status;
                $sub_suppliers = [];
                if($value->ParticipantsData){
                    foreach($value->ParticipantsData as $k =>$item){
                        $all_inquiries['participants'][]= $item->SellerData?$item->SellerData->business_name:"";
                        $Suppliers_data['id']= $item->SellerData?$item->SellerData->id:"";
                        $Suppliers_data['name']= $item->SellerData?$item->SellerData->business_name:"";
                        // if($item->status==1){
                            $all_inquiries['invted_participants'][]= $item->SellerData?$item->SellerData->business_name:"";
                        // }
                        $sub_suppliers[] = $Suppliers_data;
                    }
                }
                $all_inquiries['invted_participants_count'] = 0;
                if (isset($all_inquiries['participants'])) {
                    $all_inquiries['invted_participants_count'] = count($all_inquiries['participants']);
                }
                $getAllSellerQuotes = getAllSellerQuotes($value->id);
                $all_inquiries['participants_count'] = 0;
                if (isset($all_inquiries['participants'])) {
                    $all_inquiries['participants_count'] = count($all_inquiries['invted_participants']);
                }
                    if(count($getAllSellerQuotes)>0){
                        foreach($getAllSellerQuotes as $k =>$itemk){
                            $seller = [];
                            $seller['id'] = $itemk->id;
                            $seller['inquiry_id'] = $itemk->inquiry_id;
                            $seller['seller_id'] = $itemk->seller_id;
                            $seller['quotes'] = $itemk->quotes;
                            $seller['name'] = $itemk->name;
                            $seller['country_code'] = $itemk->country_code;
                            $seller['mobile'] = $itemk->mobile;
                            $seller['business_name'] = $itemk->business_name;
                            $seller['last_three_quotes'] = [];
                            foreach(get_last_three_quotes($itemk->inquiry_id,$itemk->seller_id) as $qItem){
                                $seller['last_three_quotes'][]=$qItem->quotes; 
                            }
                            $seller['last_three_quotes'] = array_reverse($seller['last_three_quotes']);
                            $SellerCommentsData = SellerCommentsData($itemk->inquiry_id, $itemk->seller_id);
                            $SellerFileData = SellerFileData($itemk->inquiry_id, $itemk->seller_id);
                            $seller['seller_comments_data'] = $SellerCommentsData;
                            $seller['seller_file_data'] = $SellerFileData;
                            $seller_data[]= $seller;
                        }
                    }
                    $all_inquiries['seller_data'] = $seller_data;
                
                $cancelled_inquiries[] = $all_inquiries;
                $suppliers[] = $sub_suppliers;
            }
             // For Suppliers data filter
             $main_suppliers = [];
             if(count($suppliers)>0){
                 foreach($suppliers as $k=>$item){
                     foreach($item as $index=>$value){
                         $semi_suppliers['name'] = $value['name'];
                         $semi_suppliers['id'] = $value['id'];
                         $main_suppliers[]=$semi_suppliers;
                     }
                 }
             }
              // Extract unique IDs
            $uniqueIds = array_unique(array_column($main_suppliers, 'id'));
            // Filter the original array to include only unique items
           $suppliers_data = array_intersect_key($main_suppliers, $uniqueIds);
          
        }
        return view('front.user_dashboard.cancelled_inquireis', compact('cancelled_inquiries','group_wise_list','saved_inquiries','live_inquiries','confirmed_inquiry_data','pending_inquiries_data','cancelled_inquiry_data','suppliers_data'));
    }

    public function exportCancelledInquiries(Request $request){
        $user_id = $this->getAuthenticatedUserId();
        
        // Retrieve confirmed inquiries based on search parameters
        $cancelled_inquiry_data = $this->BuyerDashboardRepository->all_inquiries_by_search(
            $user_id,
            $request->input('start_date'),
            $request->input('end_date'),
            $request->input('seller'),
            $request->input('keyword'),
            4 // Assuming '4' is the status code for confirmed inquiries
        );
    
        // Determine if the search includes participants
        $includesParticipants = !is_null($request->input('seller'));
    
            $delimiter = ",";
            $fileName = "Inquiry Details-".date('d-m-Y').".csv";
            // Create a file pointer
            $f = fopen('php://memory', 'w');

            // Conditional header based on whether participants are included
            if ($includesParticipants) {
                $header = array('Sl.No', 'Inquiry ID', 'Title', 'Start Date Time', 'End Date Time', 'Category', 
                    'Sub Category', 'Description', 'Execution Date',
                    'Minimum Quote Amount', 'Maximum Quote Amount', 'Participant Details', 'Last Quote', 
                    'Inquiry Type', 'Allot Seller', 'Final Allot Amount', 'Location', 'Remarks'
                );
            } else {
                $header = array('Sl.No', 'Inquiry ID', 'Title', 'Start Date Time', 'End Date Time', 'Category', 
                    'Sub Category', 'Description', 'Execution Date',
                    'Minimum Quote Amount', 'Maximum Quote Amount', 
                    'Inquiry Type', 'Allot Seller', 'Final Allot Amount', 'Location', 'Remarks');
            }
            fputcsv($f,$header,$delimiter);
    
            // Add the data of the CSV
            foreach ($cancelled_inquiry_data as $index => $inquiry) {
                $description = strip_tags($inquiry->description);
                $allot_seller = User::where('id', $inquiry->allot_seller)->first();
                if ($includesParticipants) {
                    $seller_id = $request->input('seller'); 
                    $InquirySellerQuotes = InquirySellerQuotes::with('SellerData')->where('inquiry_id', $inquiry->id)->where('seller_id', $seller_id)->latest()->first();
                    $seller_info = 'Name: ' . ($InquirySellerQuotes->SellerData ? $InquirySellerQuotes->SellerData->business_name : '') 
                    . ' Mobile No: ' . ($InquirySellerQuotes->SellerData ? $InquirySellerQuotes->SellerData->mobile : '');
        
                    $exportData = array(
                        $index+1,
                        $inquiry->inquiry_id, 
                        $inquiry->title, 
                        date('d M, Y h:i A', strtotime($inquiry->start_date.' '.$inquiry->start_time)), 
                        date('d M, Y h:i A', strtotime($inquiry->end_date.' '.$inquiry->end_time)), 
                        $inquiry->category, 
                        $inquiry->sub_category, 
                        $description, 
                        $inquiry->execution_date,
                        $inquiry->minimum_quote_amount, 
                        $inquiry->maximum_quote_amount, 
                        $seller_info, 
                        $InquirySellerQuotes->quotes, 
                        $inquiry->inquiry_type, 
                        $allot_seller?$allot_seller->business_name:"", 
                        $inquiry->inquiry_amount, 
                        $inquiry->location, 
                        $inquiry->status == 4 ? "Cancelled" : ''
                    );
                } else {
                    $exportData = array(
                       $index+1, 
                        $inquiry->inquiry_id, 
                        $inquiry->title, 
                        date('d M, Y h:i A', strtotime($inquiry->start_date.' '.$inquiry->start_time)), 
                        date('d M, Y h:i A', strtotime($inquiry->end_date.' '.$inquiry->end_time)), 
                        $inquiry->category, 
                        $inquiry->sub_category, 
                        $description, 
                        $inquiry->execution_date, 
                        $inquiry->minimum_quote_amount, 
                        $inquiry->maximum_quote_amount, 
                        $inquiry->inquiry_type, 
                        $allot_seller?$allot_seller->business_name:"", 
                        $inquiry->inquiry_amount, 
                        $inquiry->location, 
                        $inquiry->status == 4 ? "Cancelled" : ''
                    );
                }
                fputcsv($f,$exportData,$delimiter);
            }
            fseek($f,0);
            // Set headers to download file rather than displayed
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $fileName . '";');

            //output all remaining data on a file pointer
            fpassthru($f);
    }

    public function live_inquiries_fetch_ajax(){
        $live_inquiries =  $this->BuyerDashboardRepository->live_inquiries_by_user($this->getAuthenticatedUserId());
        $inquiries = [];
        if(count($live_inquiries)>0){
            foreach ($live_inquiries as $key => $value) {
                $seller_data = [];
                $all_inquiries = [];
                $all_inquiries['buyer_bit_for_open_auction'] = $value->buyer_bit_for_open_auction;
                $all_inquiries['id'] = $value->id;
                $all_inquiries['inquiry_id'] = $value->inquiry_id;
                $all_inquiries['created_by'] = $value->BuyerData->name;
                $all_inquiries['title'] = $value->title;
                $all_inquiries['start_date_time'] = date('d M, Y h:i A', strtotime($value->start_date.' '.$value->start_time));
                 // Calculate remaining time until start date/time
                 $startDateTime = Carbon::parse($value->start_date . ' ' . $value->start_time)->timezone(env('APP_TIMEZONE'));
                 $currentDateTime = Carbon::now();
                 
                 if ($currentDateTime < $startDateTime) {
                     $startRemainingTime = $startDateTime->diff($currentDateTime);
                     $days = $startRemainingTime->days;
                     $hours = $startRemainingTime->h;
                     $minutes = $startRemainingTime->i;
                     $seconds = $startRemainingTime->s;
                     
                     $all_inquiries['start_remaining_time'] = "Starts in: $days d $hours h $minutes m $seconds s";
                 } else {
                     $all_inquiries['start_remaining_time'] = null;
                 }
                 
                 // Calculate remaining time until end date/time
                 $endDateTime = Carbon::parse($value->end_date . ' ' . $value->end_time)->timezone(env('APP_TIMEZONE'));
                 if ($currentDateTime < $endDateTime) {
                     $endRemainingTime = $endDateTime->diff($currentDateTime);
                     $days = $endRemainingTime->days;
                     $hours = $endRemainingTime->h;
                     $minutes = $endRemainingTime->i;
                     $seconds = $endRemainingTime->s;
                     $all_inquiries['end_date_time'] = date('d M, Y h:i A', strtotime($value->end_date.' '.$value->end_time));
                     $all_inquiries['end_remaining_time'] = "Ends in: $days d $hours h $minutes m $seconds s";
                 } else {
                     $store = Inquiry::find($value->id);
                     $store->status = 2;
                     $store->save();
                     $all_inquiries['end_remaining_time'] = "";
                 }
                $all_inquiries['category'] = $value->category;
                $all_inquiries['sub_category'] = $value->sub_category;
                $all_inquiries['description'] = $value->description;
                $all_inquiries['execution_date'] = $value->execution_date;
                $all_inquiries['quotes_per_participants'] = $value->quotes_per_participants;
                $all_inquiries['minimum_quote_amount'] = $value->minimum_quote_amount;
                $all_inquiries['maximum_quote_amount'] = $value->maximum_quote_amount;
                $all_inquiries['inquiry_type'] = $value->inquiry_type;
                $all_inquiries['inquiry_amount'] = $value->inquiry_amount;
                $all_inquiries['location'] = $value->location;
                $all_inquiries['status'] = $value->status;

                if($value->ParticipantsData){
                    foreach($value->ParticipantsData as $k =>$item){
                        $all_inquiries['participants'][]= $item->SellerData->business_name;
                        if($item->status==1){
                            $all_inquiries['invted_participants'][]= $item->SellerData->business_name;
                        }
                    }
                }
                $all_inquiries['invted_participants_count'] = 0;
                if (isset($all_inquiries['participants'])) {
                    $all_inquiries['invted_participants_count'] = count($all_inquiries['participants']);
                }
                $getAllSellerQuotes = getAllSellerQuotes($value->id);
                $all_inquiries['participants_count'] = 0;
                if (isset($all_inquiries['participants'])) {
                    $all_inquiries['participants_count'] = count($all_inquiries['invted_participants']);
                }
                    if(count($getAllSellerQuotes)>0){
                        foreach($getAllSellerQuotes as $k =>$itemk){
                            $InquiryParticipant = InquiryParticipant::where('inquiry_id', $itemk->inquiry_id)->where('user_id', $itemk->seller_id)->first();
                            $seller = [];
                            $seller['id'] = $itemk->id;
                            $seller['inquiry_id'] = $itemk->inquiry_id;
                            $seller['seller_id'] = $itemk->seller_id;
                            $seller['selected_from'] = $InquiryParticipant?$InquiryParticipant->selected_from:"1";
                            // $seller['quotes'] = $itemk->quotes;
                            $seller['name'] = $itemk->name;
                            $seller['country_code'] = $itemk->country_code;
                            $seller['mobile'] = $itemk->mobile;
                            $seller['business_name'] = $itemk->business_name;
                            $seller['last_three_quotes'] = [];
                            foreach($get_last_three_quotes = get_last_three_quotes($itemk->inquiry_id,$itemk->seller_id) as $index=> $qItem){
                                $seller['last_three_quotes'][]=$qItem->quotes;
                            }
                            $seller['quotes'] = $get_last_three_quotes[0]->quotes;
                            
                            $seller['last_three_quotes'] = array_reverse($seller['last_three_quotes']);
                            $SellerCommentsData = SellerCommentsData($itemk->inquiry_id, $itemk->seller_id);
                            $SellerFileData = SellerFileData($itemk->inquiry_id, $itemk->seller_id);
                           
                            $seller['seller_comments_data'] = $SellerCommentsData;
                            $seller['seller_file_data'] = $SellerFileData;
                            $seller_data[]= $seller;
                        }
                    }
                    $all_inquiries['seller_data'] = $seller_data;
                
                $inquiries[] = $all_inquiries;
            }
        }
        // dd($inquiries);
        if(count($inquiries)>0){
            return response()->json(['status'=>200, 'data'=>$inquiries]);
        }else{
            return response()->json(['status'=>400]);
        }
    }
    public function CreditBuyerBit(Request $request){
        $validator = Validator::make($request->all(), [
            'unlock_inquiry_id' => 'required|exists:inquiries,id', // Ensure unlock_inquiry_id exists in 'inquiries' table
            'credit_bit' => 'required|numeric|min:1', // Validate unlock_bit is numeric and at least 1
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $Inquiry = Inquiry::findOrFail($request->unlock_inquiry_id);
        $Inquiry->buyer_bit_for_open_auction = $request->credit_bit;
        $Inquiry->save();
        if($Inquiry){
            return response()->json(['message' => 'Data has been successfully updated', 'status'=>200]);
        }else{
            return response()->json(['message' => 'Something wend wrong!', 'status'=>400]);
        }
        
    }

    public function live_inquiry_seller_allot(Request $request){
        // dd($request->all());
        if (!is_numeric($request->allot_amount)) {
            return redirect()->back()->with('warning', 'Please enter a numeric value.');
        }
        DB::beginTransaction();
        try {
           
            $InquiryAllotmentData = new InquiryAllotmentData;
            $InquiryAllotmentData->inquiry_id = $request->inquiry_id;
            $InquiryAllotmentData->user_id = $request->bidder_id;
            $InquiryAllotmentData->quote = $request->allot_amount;
            $InquiryAllotmentData->reason = $request->type == 'old' ? $request->reallot_reason : null;
            $InquiryAllotmentData->save();

            $inquiry= Inquiry::findOrFail($request->inquiry_id);
            $inquiry->allot_seller = $request->bidder_id;
            $inquiry->inquiry_amount = $request->allot_amount;
            $inquiry->status = 3; //Confirmed
            $inquiry->save();
            if ($inquiry) {
                $data = InquiryParticipant::where('inquiry_id', $inquiry->id)
                                          ->where('user_id', '!=', $inquiry->allot_seller)
                                          ->get();
                
                // Loop through each record and update the status to 3
                foreach ($data as $participant) {
                    $participant->update(['status' => 3, 'rejected_reason'=>'Buyer selected another supplier']);
                }
                $allot_seller = InquiryParticipant::with('SellerData')->where('inquiry_id', $inquiry->id)
                                          ->where('user_id', $inquiry->allot_seller)
                                          ->first();
                if($allot_seller){
                    $allot_seller->status = 4; //Allot
                    $allot_seller->rejected_reason = null;
                    $allot_seller->save();
                    if($allot_seller){
                        $Buyer_data = User::where('id', $inquiry->created_by)->first();
                        // ALLOTMENT & REALLOTMENT SMS
                        $myMessage ="";
                        $sender = env('SMS_SENDER');
                        $inquiry_data= Inquiry::with('BuyerData')->where('id', $request->inquiry_id)->first();
                        $inquiry_id = $inquiry_data->inquiry_id?$inquiry_data->inquiry_id:"...";
                        $company = $inquiry_data->BuyerData?$inquiry_data->BuyerData->business_name:"a company";
                        $execution_date = $inquiry_data->execution_date?$inquiry_data->execution_date:"...";
                        $amount = $request->allot_amount;
                        $url = 'https://milaapp.in/seller/confirmed';
                        $cc =[
                            0=>$allot_seller->SellerData->email1,
                            1=>$allot_seller->SellerData->email2,
                            2=>$allot_seller->SellerData->email3,
                        ];
                        if($request->type=="first" || $request->type=="new"){
                            $data=[
                                'cc'=>$cc,
                                'user'=>$allot_seller->SellerData,
                                'inquiry_data'=>$inquiry,
                                'Buyer_data'=>$Buyer_data,
                                'type'=>'INQUIRY_ALLOTMENT',
                                'user_type'=>'Seller',
                            ];
                            $type = "INQUIRY_ALLOTMENT";
                            $subject = 'Inquiry ALLOTMENT Notification for '.ucwords($Buyer_data->business_name);
                            $myMessage = urlencode("New auction ".$inquiry_id." is assigned to you from ".$company.". Amt ".$amount." . Expected date ".$execution_date.". Details: ".$url." (owned by SMTPL) Regards, Sarv Megh Technology (OPC) Private Limited");
                        }else{
                            $reason = $request->reallot_reason?$request->reallot_reason:"";
                            $data=[
                                'cc'=>$cc,
                                'user'=>$allot_seller->SellerData,
                                'inquiry_data'=>$inquiry,
                                'Buyer_data'=>$Buyer_data,
                                'reason'=>$reason,
                                'type'=>'INQUIRY_REALLOTMENT',
                                'user_type'=>'Seller',
                            ];
                            $type = "INQUIRY_REALLOTMENT";
                            $subject = 'Inquiry REALLOTMENT Notification for '.ucwords($Buyer_data->business_name);
                            $myMessage = urlencode("Auction ".$inquiry_id." from ".$company.". is REASSIGNED to you. Amt ".$amount.". Expected: ".$execution_date." Details: ".$url.". (owned by SMTPL) Regards, Sarv Megh Technology (OPC) Private Limited");
                        }
                        $customer_mobile_no = $allot_seller->SellerData?$allot_seller->SellerData->mobile:null;
                        // $customer_mobile_no = 9007083569;
                        $checkPhoneNumberValid = checkPhoneNumberValid($customer_mobile_no);
                        if($checkPhoneNumberValid){
                            sendSMS($sender, $customer_mobile_no, $myMessage);
                        }
                        $seller_email =$allot_seller->SellerData?$allot_seller->SellerData->email:null;
                        if($seller_email){
                            sendMail($data,$seller_email,$subject);
                        }
                        if($Buyer_data){
                            $exist_participants = InquiryParticipant::with('SellerData')->where('inquiry_id', $inquiry->id)->get();
                            $reason = $request->reallot_reason?$request->reallot_reason:"";
                            $data=[
                                'cc'=>[],
                                'user'=>$allot_seller->SellerData,
                                'inquiry_data'=>$inquiry,
                                'Buyer_data'=>$Buyer_data,
                                'reason'=>$reason,
                                'participants'=>$exist_participants,
                                'type'=>$type,
                                'user_type'=>'Buyer',
                            ];
                            sendMail($data,$Buyer_data->email,$subject);
                        }
                        
                    }
                }
            }
             // Commit the transaction
        DB::commit();
            return redirect()->route('buyer_confirmed_inquiries')->with('success', 'Seller has been successfully allocated.');
        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();
            dd($e->getMessage());
            // Log the error for debugging
            // Log::error('Error allotting seller: ' . $e->getMessage());
    
            return redirect()->back()->with('warning', 'Something went wrong. Please try again later.');
        }
    }

    // update_your_notes
    public function update_your_notes(Request $request){

    // Extract data from the request
    $description = $request->input('description');
    $inquiryId = $request->input('inquiry_id');
    $userId = (int) $request->input('user_id');
    // Update or Insert the note
    DB::table('buyer_notes')->updateOrInsert(
        [
            'inquiry_id' => $inquiryId,
            'user_id' => $userId,
        ],
        [
            'notes' => $description,
            'updated_at' => now(),  // Set the update timestamp
        ]
    );

    // Redirect back with a success message
    return redirect()->back()->with('success', 'Note updated successfully!');
       
    }

    public function cancelled_reason(Request $request){
        
        if(isset($request->cancelled_reason)){
            $inquiry= Inquiry::with('BuyerData')->where('id', $request->id)->first();
            if($inquiry->status==3){
                $sender = env('SMS_SENDER');
                $inquiry_id = $inquiry->inquiry_id?$inquiry->inquiry_id:"...";
                $company = $inquiry->BuyerData?ucwords($inquiry->BuyerData->business_name):"a company";
                $time = date('d-m-Y h:i a');
                if($inquiry->allotment_type==1){
                    $seller = AllotOffline::where('id', $inquiry->allot_seller)->first();
                 }else{
                     $seller = User::where('id',$inquiry->allot_seller)->first();
                 }
                $url = 'https://milaapp.in/seller/cancelled';
                $myMessage = urlencode("Auction ".$inquiry_id." has been cancelled by ".$company." on ".$time.". Details: ".$url." - Sarv-Megh Technology (OPC) Private Limited");
                $customer_mobile_no = $seller?$seller->mobile:null;
                $checkPhoneNumberValid = checkPhoneNumberValid($customer_mobile_no);
                if($checkPhoneNumberValid){
                    sendSMS($sender, $customer_mobile_no, $myMessage);
                }
                $exist_participants = InquiryParticipant::with('SellerData')->where('inquiry_id', $inquiry->id)->get();
                $reason = $request->cancelled_reason?$request->cancelled_reason:"";
                
                $subject = 'Inquiry CANCELLATION Notification for '.ucwords($inquiry->BuyerData->business_name).' '.$inquiry->inquiry_id.'';
                if($inquiry->allotment_type==0){//for only Online User
                    $seller_email =$seller?$seller->email:null;
                    $cc =[
                        0=>$seller->email1,
                        1=>$seller->email2,
                        2=>$seller->email3,
                    ];
                    $data=[
                        'cc'=>$cc,
                        'user'=>$seller,
                        'inquiry_data'=>$inquiry,
                        'Buyer_data'=>$inquiry->BuyerData,
                        'reason'=>$reason,
                        'type'=>'INQUIRY_CANCELLATION',
                        'user_type'=>'Seller',
                    ];
                    
                    sendMail($data,$seller_email,$subject);
                }
                $data=[
                    'cc'=>[],
                    'user'=>$seller,
                    'inquiry_data'=>$inquiry,
                    'Buyer_data'=>$inquiry->BuyerData,
                    'reason'=>$reason,
                    'participants'=>$exist_participants,
                    'type'=>"INQUIRY_CANCELLATION",
                    'user_type'=>'Buyer',
                ];
                sendMail($data,$inquiry->BuyerData->email,$subject);
            }
            $inquiry->cancelled_reason = $request->cancelled_reason;
            $inquiry->status = 4;  // here we do push evey paticipats staus ==2 in inquiry_participats table as buyer cancel the inquiry for reason
            $inquiry->save();

            return redirect()->back()->with('success','Inquiry cancelled successfull.');
        }else{
            return redirect()->back()->with('warning','Please select the cancell reason.');
    }
    
    }
    public function InquiryPdfGenarate($id){
        $inquiry = Inquiry::where('id',$id)->first();
        if($inquiry->allotment_type==1){
           $final_seller_details = AllotOffline::where('id', $inquiry->allot_seller)->first();
        }else{
            $final_seller_details = User::where('id',$inquiry->allot_seller)->first();
        }
        
        $buyer_details = User::where('id',$inquiry->created_by)->first();
        $max_rate = InquirySellerQuotes::where('inquiry_id', $id)->max('quotes');
        $min_rate = InquirySellerQuotes::where('inquiry_id', $id)->min('quotes');
        $seller_ids = InquirySellerQuotes::select('seller_id')
        ->where('inquiry_id', $id)
        ->groupBy('seller_id')
        ->pluck('seller_id')
        ->toArray();
        $seller_data = [];
        foreach($seller_ids as $key =>$seller_id){
            $latest_quote = InquirySellerQuotes::where('seller_id', $seller_id)->where('inquiry_id', $id)->latest()->first()->quotes;
            $first_quote = InquirySellerQuotes::where('seller_id', $seller_id)->where('inquiry_id', $id)->orderBy('id', 'ASC')->first()->quotes;
            $seller_comments = InquirySellerComments::where('seller_id', $seller_id)->where('inquiry_id', $id)->latest()->take(2)->get();
            // Get seller details from the User table
            $seller_details = User::find($seller_id);
            //  dd($seller_details);
        // Store the seller data
        $seller_data[$seller_id] = [
            'latest_quote' => $latest_quote,
            'first_quote' => $first_quote,
            'seller_details' => $seller_details,
            'seller_comments'=>$seller_comments
        ];
        
        }
        $data = [
            'inquiry' => $inquiry,
            'max_rate' => $max_rate,
            'min_rate' => $min_rate,
            'buyer_details' => $buyer_details,
            'final_seller_details' => $final_seller_details,
            'seller_data' => $seller_data,
            'seller_count' => count($seller_ids)
        ];
        $pdf = Pdf::loadView('admin.inquiry.generate-inquiry-pdf', $data);
        return $pdf->download('inquiry-pdf.pdf');
    }
    public function allot_offline_seller(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'mobile' => 'required|digits:10',
            'rate' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }
        // Process the form data (e.g., save to the database)
        // Example:
        if($request->input('id')){
            $formData = AllotOffline::where('inquiry_id', $request->input('id'))->first();
            if(!$formData){
                $formData = new AllotOffline();
            }
            $formData->inquiry_id  = $request->input('id');
            $formData->name = $request->input('name');
            $formData->mobile = $request->input('mobile');
            $formData->amount = $request->input('rate');
            $formData->save();
            $inquiry= Inquiry::findOrFail($request->id);
            $inquiry->allot_seller = $formData->id;
            $inquiry->inquiry_amount = $request->rate;
            $inquiry->allotment_type = 1; //for Offline Seller
            $inquiry->status = 3; //Confirmed
            $inquiry->save();
            if ($inquiry) {
                $data = InquiryParticipant::where('inquiry_id', $inquiry->id)
                                            ->get();
                
                // Loop through each record and update the status to 3
                foreach ($data as $participant) {
                    $participant->update(['status' => 3, 'rejected_reason'=>'Buyer selected another supplier']);
                }
            }
            $company = $inquiry->BuyerData?ucwords($inquiry->BuyerData->business_name):"a company";
            $execution_date = $inquiry->execution_date?$inquiry->execution_date:"...";
            $amount =number_format($request->input('rate'), 2, '.', ',');
            $url = 'https://milaapp.in/seller/confirmed';
            $myMessage = urlencode("Auction ".$inquiry->inquiry_id." from ".$company.". is ASSIGNED to you. Amt ".$amount.". Expected: ".$execution_date." Details: ".$url.". (owned by SMTPL) Regards, Sarv Megh Technology (OPC) Private Limited");
            $customer_mobile_no = $request->input('mobile');
            $checkPhoneNumberValid = checkPhoneNumberValid($customer_mobile_no);
            if($checkPhoneNumberValid){
                $sender = env('SMS_SENDER');
                sendSMS($sender, $customer_mobile_no, $myMessage);
            }
            return response()->json(['success' => true, 'message' => 'Form submitted successfully!']);
        }else{
            return response()->json(['success' => false, 'errors' => "Inquiry Id not found!"], 500);
        }
       
    }
}