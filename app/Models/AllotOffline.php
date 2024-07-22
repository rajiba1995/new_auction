<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllotOffline extends Model{
    use HasFactory;
    protected $table = 'allot_offlines';
    public function InquriesData(){
        return $this->belongsTo('App\Models\Inquiry','inquiry_id','id');
    }
}