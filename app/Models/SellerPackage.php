<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerPackage extends Model
{
    use HasFactory;
    protected $table ="seller_packages";
    protected $primaryKey ="id";
}
