<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model{
    use HasFactory;
    protected $table = 'inquiries';
    protected $fillable = [
        'inquiry_id',
        'title',
        'category',
        'sub_category',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'execution_date',
        'minimum_quote_amount',
        'maximum_quote_amount',
        'inquiry_type',
        'allot_seller',
    ];

    public function ParticipantsData(){
        return $this->hasMany('App\Models\InquiryParticipant','inquiry_id','id');
    }
    public function SellerQuotesData(){
        return $this->hasMany('App\Models\InquirySellerQuotes','inquiry_id','id');
    }
    public function SellerCommentData(){
        return $this->hasMany('App\Models\InquirySellerComments','inquiry_id','id');
    }
    public function BuyerData(){
        return $this->belongsTo('App\Models\User','created_by','id');
    }
    public function SellerData(){
        return $this->belongsTo('App\Models\User','allot_seller','id');
    }
    public function OfflineData(){
        return $this->belongsTo('App\Models\AllotOffline','allot_seller','id');
    }
    
}
