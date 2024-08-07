<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Contracts\SellerDashboardContract;
use App\Helper\helper;
use App\Models\Inquiry;
use App\Models\InquiryParticipant;
use App\Models\GroupWatchList;
use App\Models\WatchList;
use Illuminate\Support\Str;
use Auth;
use Illuminate\Support\Facades\File;
use DB;

class SellerDashboardRepository implements SellerDashboardContract{
  
    private function getAuthenticatedUserId() {
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user()->id;
        }
        return null;
    }
    public function group_wise_inquiries_by_user($id){
        return DB::table('watchlists')
            ->select('watchlists.group_id', 'watchlists.seller_id', 'watchlists.buyer_id', 'sellers.name as seller_name', 'buyers.name as buyer_name','buyers.business_name as business_name','buyers.country_code as country_code','buyers.mobile as mobile', 'group_watchlist.group_name')
            ->join('users as sellers', 'sellers.id', '=', 'watchlists.seller_id')
            ->join('users as buyers', 'buyers.id', '=', 'watchlists.buyer_id')
            ->join('group_watchlist', 'group_watchlist.id', '=', 'watchlists.group_id')
            ->where('watchlists.seller_id', $id)
            ->whereNotNull('watchlists.group_id')
            ->get();
    }
    
    public function all_participants_inquiries_of_seller($id, $keyword = null, $start_date = null, $end_date = null, $buyer = null)
    {
        // Initialize the query
        $query = DB::table('inquiries')
            ->select('users.name as buyer_name', 'users.business_name as buyer_business_name', 'inquiry_participants.status as status', 'inquiry_participants.selected_from as selected_from', 'inquiry_participants.user_id as my_id', 'inquiries.*')
            ->join('inquiry_participants', 'inquiry_participants.inquiry_id', '=', 'inquiries.id')
            ->join('users', 'users.id', '=', 'inquiries.created_by')
            ->where('inquiry_participants.user_id', $id)
            ->where('inquiry_participants.status', 1)
            ->orderBy('inquiries.created_at', 'DESC');
    
        // Add conditions based on parameters
        if ($start_date) {
            $query->whereDate('inquiries.start_date', '>=', $start_date);
        }
    
        if ($end_date) {
            $query->whereDate('inquiries.start_date', '<=', $end_date);
        }
    
        if ($buyer) {
            $query->where('inquiries.created_by', $buyer);
        }
    
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('inquiries.inquiry_id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.title', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.category', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.sub_category', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.inquiry_type', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.inquiry_amount', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.location', 'LIKE', '%' . $keyword . '%');
            });
        }
    
        // Get the results
        return $query->get();
    }
    public function rejected_buyer($id){
        $buyerBusinessNames = Inquiry::join('inquiry_participants', 'inquiry_participants.inquiry_id', '=', 'inquiries.id')
            ->join('users', 'users.id', '=', 'inquiries.created_by')
            ->where('inquiry_participants.user_id', $id)
            ->where(function ($query) {
                $query->whereIn('inquiry_participants.status', [2, 3]) // 2: Seller own cancelled, 3: Buyer selected another seller
                    ->orWhere('inquiries.status', 4); // Buyer Cancelled inquiry
            })
            ->distinct()
            ->select('users.business_name', 'inquiries.created_by', 'inquiry_participants.user_id')
            ->get()
            ->unique('business_name');
        return $buyerBusinessNames;
    }
    

    public function rejected_inquiries_by_seller($id, $keyword = null, $start_date = null, $end_date = null, $buyer=null) {
        $query = Inquiry::with('BuyerData')
            ->select('users.business_name as buyer_business_name','inquiry_participants.user_id as my_id', 'inquiries.*', 'inquiry_participants.rejected_reason', 'inquiry_participants.status as participants_status')
            ->join('inquiry_participants', 'inquiry_participants.inquiry_id', '=', 'inquiries.id')
            ->join('users', 'users.id', '=', 'inquiries.created_by')
            ->where('inquiry_participants.user_id', $id)
            ->where(function ($query) {
                $query->whereIn('inquiry_participants.status', [2, 3]) // 2: Seller own cancelled, 3: Buyer selected another seller
                    ->orWhere('inquiries.status', 4); // Buyer Cancelled inquiry
            });
    
        // Add conditions based on parameters
        $query->whereNotNull('inquiries.inquiry_id')
            ->orderBy('inquiries.created_at', 'DESC');
        // Add conditions based on parameters
        $query->when($start_date, function ($q, $start_date) {
            return $q->whereDate('inquiries.start_date', '>=', $start_date);
        });

        $query->when($end_date, function ($q, $end_date) {
            return $q->whereDate('inquiries.start_date', '<=', $end_date);
        });
        $query->when($buyer, function ($q, $buyer) {
            return $q->where('inquiries.created_by', $buyer);
        });

        $query->when($keyword, function ($q, $keyword) {
            return $q->where(function ($q) use ($keyword) {
                $q->where('inquiries.inquiry_id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.title', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.category', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.sub_category', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.inquiry_type', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.inquiry_amount', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.location', 'LIKE', '%' . $keyword . '%');
            });
        });
    
        return $query->get();
    }
    
    
    public function all_inquiries_of_seller($id){
        return Inquiry::where('id',$id)->get();
    }
    public function live_inquiries_by_seller(){
        // dd($this->getAuthenticatedUserId());
        return Inquiry::with('buyerData') // Eager load the 'buyerData' relationship
            ->select(
                'inquiries.*', 
                'inquiry_participants.user_id as my_id', 
                'inquiry_participants.status as my_quote_status', 
                'users.business_name as buyer_business_name', 
                'users.name as buyer_name', 
                'users.country_code as country_code',
                'users.mobile as buyer_mobile'
            )
            ->whereNotNull('inquiries.inquiry_id') // Filter inquiries where 'inquiry_id' is not null
            ->where('inquiries.status', 1) // Filter inquiries with a status of 1
            ->join('inquiry_participants', 'inquiries.id', '=', 'inquiry_participants.inquiry_id') // Join 'inquiry_participants' table
            ->join('users', 'inquiries.created_by', '=', 'users.id') // Join 'users' table
            ->whereIn('inquiry_participants.status', [1,4,3]) 
            ->where('inquiry_participants.user_id', $this->getAuthenticatedUserId()) // Filter by authenticated user's ID
            ->get(); // Get the results
    }
    
    public function pending_inquiries_by_seller($keyword = null, $start_date = null, $end_date = null, $buyer=null)
    {
        $query = Inquiry::with('buyerData')
            ->select(
                'inquiries.*',
                'inquiry_participants.user_id as my_id',
                'users.business_name as buyer_business_name',
                'users.name as buyer_name',
                'users.country_code as country_code',
                'users.mobile as buyer_mobile'
            )
            ->whereNotNull('inquiries.inquiry_id')
            ->where('inquiries.status', 2)
            ->join('inquiry_participants', 'inquiries.id', '=', 'inquiry_participants.inquiry_id')
            ->join('users', 'inquiries.created_by', '=', 'users.id')
            ->where('inquiry_participants.user_id', $this->getAuthenticatedUserId());
    
        // Add conditions based on parameters
        $query->when($start_date, function ($q, $start_date) {
            return $q->whereDate('inquiries.start_date', '>=', $start_date);
        });
    
        $query->when($end_date, function ($q, $end_date) {
            return $q->whereDate('inquiries.start_date', '<=', $end_date);
        });
        $query->when($buyer, function ($q, $buyer) {
            return $q->where('inquiries.created_by', $buyer);
        });
    
        $query->when($keyword, function ($q, $keyword) {
            return $q->where(function ($q) use ($keyword) {
                $q->where('inquiries.inquiry_id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.title', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.category', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.sub_category', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.inquiry_type', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.inquiry_amount', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.location', 'LIKE', '%' . $keyword . '%');
            });
        });
    
        return $query->get();
    }
    
    public function confirmed_inquiries_by_seller($keyword = null, $start_date = null, $end_date = null, $buyer=null)
    {
        $query = Inquiry::with('buyerData')
            ->select('inquiries.*','users.business_name as buyer_business_name',)
            ->where('inquiries.inquiry_id', '!=', null)
            ->where('inquiries.status', 3)
            ->join('inquiry_participants', 'inquiries.id', '=', 'inquiry_participants.inquiry_id')
            ->join('users', 'inquiries.created_by', '=', 'users.id')
            ->where('inquiry_participants.user_id', $this->getAuthenticatedUserId())
            ->where('inquiry_participants.status', 4)
            ->where('inquiries.allot_seller', $this->getAuthenticatedUserId());
    
        // Add conditions based on parameters
        $query->when($start_date, function ($q, $start_date) {
            return $q->whereDate('inquiries.start_date', '>=', $start_date);
        });
    
        $query->when($end_date, function ($q, $end_date) {
            return $q->whereDate('inquiries.start_date', '<=', $end_date);
        });
        $query->when($buyer, function ($q, $buyer) {
            return $q->where('inquiries.created_by', $buyer);
        });
    
        $query->when($keyword, function ($q, $keyword) {
            return $q->where(function ($q) use ($keyword) {
                $q->where('inquiries.inquiry_id', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.title', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.category', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.sub_category', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.inquiry_type', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.inquiry_amount', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('inquiries.location', 'LIKE', '%' . $keyword . '%');
            });
        });
    
        return $query->get();
    }
    

    

}