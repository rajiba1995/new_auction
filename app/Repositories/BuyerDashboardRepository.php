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
  
    public function pending_inquiries_by_user($id){
        return Inquiry::with('ParticipantsData')->latest('updated_at')->where('created_by',$id)->where('inquiry_id' ,'!=', null)->where('status',2)->get();
    }
    
    public function confirmed_inquiries_by_user($id){
        return Inquiry::with('ParticipantsData', 'OfflineData')->latest('updated_at')->where('created_by',$id)->where('inquiry_id', '!=', null)->where('status', 3)->get();
    }
    public function cancelled_inquiries_by_user($id){
        return Inquiry::with('ParticipantsData')->where('created_by',$id)->where('inquiry_id', '!=', null)->where('status', 4)->orderBy('id', 'DESC')->get();
    }

    public function all_inquiries_by_search($user_id, $start_date = null, $end_date = null, $seller_id = null, $keyword = null, $status = null) {
        $query = Inquiry::query()->where('created_by', $user_id);

        $query->when($start_date, function ($q) use ($start_date) {
            return $q->whereDate('start_date', '>=', $start_date);
        });
        
        $query->when($end_date, function ($q) use ($end_date) {
            return $q->whereDate('start_date', '<=', $end_date);
        });
        
        $query->when($seller_id, function ($q) use ($seller_id) {
            return $q->whereHas('participantsData.sellerData', function($q) use ($seller_id) {
                $q->where('id', $seller_id);
            });
        });
        
        $query->when($keyword, function ($q) use ($keyword) {
            return $q->where(function ($q) use ($keyword) {
                $q->where('inquiry_id', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('title', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('category', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('sub_category', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('inquiry_type', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('inquiry_amount', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('location', 'LIKE', '%' . $keyword . '%');
            });
        });
        
        return $query->where('status', $status)->latest()->get();
    }
    public function all_save_inquiries_by_search($user_id, $start_date = null, $end_date = null, $keyword = null) {
        $query = Inquiry::query()->where('created_by', $user_id);

        $query->when($start_date, function ($q) use ($start_date) {
            return $q->whereDate('start_date', '>=', $start_date);
        });
        
        $query->when($end_date, function ($q) use ($end_date) {
            return $q->whereDate('start_date', '<=', $end_date);
        });
        $query->when($keyword, function ($q) use ($keyword) {
            return $q->where(function ($q) use ($keyword) {
                $q->where('title', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('category', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('sub_category', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('inquiry_type', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('inquiry_amount', 'LIKE', '%' . $keyword . '%')
                  ->orWhere('location', 'LIKE', '%' . $keyword . '%');
            });
        });
        
        return $query->where('inquiry_id', null)->latest()->get();
    }
    
    
}
