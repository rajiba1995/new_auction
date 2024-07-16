<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Contracts\BuyerDashboardContract;
use App\Helper\helper;
use App\Models\Inquiry;
use App\Models\GroupWatchList;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class BuyerDashboardRepository implements BuyerDashboardContract{
  
    private function getAuthenticatedUserId() {
        if (Auth::guard('web')->check()) {
            return Auth::guard('web')->user()->id;
        }
        return null;
    }
    public function get_all_existing_inquiries_by_user($id){
        return Inquiry::where('created_by', $id)->where('inquiry_id', '!=', null)->where('status', 1)->get();
    }
    public function group_wise_inquiries_by_user($id){
        return GroupWatchList::orderBy('group_name', 'ASC')->where('created_by',$id)->get();
    }
    public function saved_inquiries_by_user($id){
        return Inquiry::where('created_by',$id)->where('inquiry_id', null)->get();
    }
    public function live_inquiries_by_user($id){
        return Inquiry::orderBy('id', 'DESC')->with('ParticipantsData')->where('created_by',$id)->where('inquiry_id', '!=', null)->where('status', 1)->get();
    }
    public function pending_inquiries_by_user($start_date = null, $end_date = null, $seller_id = null, $keyword = null) {
        $user_id = $this->getAuthenticatedUserId();
       
        $query = Inquiry::where('created_by', $user_id);
        
        if ($start_date) {
            $query->whereDate('start_date', '>=', $start_date);
        }
    
        if ($end_date) {
            $query->whereDate('end_date', '<=', $end_date);
        }
    
        // if ($seller_id) {
        //     $query->whereHas('ParticipantsData.SellerData', function($q) use ($seller_id) {
        //         $q->where('user_id', $seller_id);
        //     });
        // }
    
        if ($keyword) {
            $query->where('inquiry_id', 'LIKE', '%' . $keyword . '%')
                ->orWhere('title', 'LIKE', '%' .$keyword. '%')
                ->orWhere('category', 'LIKE', '%' .$keyword. '%')
                ->orWhere('sub_category','LIKE', '%'.$keyword.'%')
                ->orWhere('inquiry_type','LIKE','%'.$keyword.'%')
                ->orWhere('inquiry_amount','LIKE','%'.$keyword.'%')
                ->orWhere('location','LIKE','%'.$keyword.'%');
        }
    
        return $query->where('inquiry_id', '!=', null)->where('status', 2)->get();
    }
    public function confirmed_inquiries_by_search($user_id, $start_date = null, $end_date = null, $seller_id = null, $keyword = null) {
        $query = Inquiry::where('created_by', $user_id)->where('status', 3);
        
        if ($start_date) {
            $query->whereDate('start_date', '>=', $start_date);
        }
    
        if ($end_date) {
            $query->whereDate('end_date', '<=', $end_date);
        }
    
        // if ($seller_id) {
        //     $query->whereHas('ParticipantsData.SellerData', function($q) use ($seller_id) {
        //         $q->where('user_id', $seller_id);
        //     });
        // }
    
        if ($keyword) {
            $query->where('inquiry_id', 'LIKE', '%' . $keyword . '%')
                ->orWhere('title', 'LIKE', '%' .$keyword. '%')
                ->orWhere('category', 'LIKE', '%' .$keyword. '%')
                ->orWhere('sub_category','LIKE', '%'.$keyword.'%')
                ->orWhere('inquiry_type','LIKE','%'.$keyword.'%')
                ->orWhere('inquiry_amount','LIKE','%'.$keyword.'%')
                ->orWhere('location','LIKE','%'.$keyword.'%');
        }
    
        return $query->get();
    }
    
    public function confirmed_inquiries_by_user($id){
        return Inquiry::with('ParticipantsData')->latest('updated_at')->where('created_by',$id)->where('inquiry_id', '!=', null)->where('status', 3)->get();
    }
    public function cancelled_inquiries_by_user($id){
        return Inquiry::with('ParticipantsData')->where('created_by',$id)->where('inquiry_id', '!=', null)->where('status', 4)->get();
    }

    public function cancelled_inquiries_by_search($user_id, $start_date = null, $end_date = null, $seller_id = null, $keyword = null) {
        $query = Inquiry::where('created_by', $user_id)->where('status', 4);
    
        if ($start_date) {
            $query->whereDate('start_date', '>=', $start_date);
        }
    
        if ($end_date) {
            $query->whereDate('end_date', '<=', $end_date);
        }
    
        // if ($seller_id) {
        //     $query->whereHas('ParticipantsData.SellerData', function($q) use ($seller_id) {
        //         $q->where('id', $seller_id);
        //     });
        // }
    
        if ($keyword) {
            $query->where('inquiry_id', 'LIKE', '%' . $keyword . '%')
                ->orWhere('title', 'LIKE', '%' .$keyword. '%')
                ->orWhere('category', 'LIKE', '%' .$keyword. '%')
                ->orWhere('sub_category','LIKE', '%'.$keyword.'%')
                ->orWhere('inquiry_type','LIKE','%'.$keyword.'%')
                ->orWhere('inquiry_amount','LIKE','%'.$keyword.'%')
                ->orWhere('location','LIKE','%'.$keyword.'%');
        }
    
        return $query->get();
    }
    
    
}
