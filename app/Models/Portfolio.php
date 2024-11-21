<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable=[
        'contractor_id',
        'title'
    ];

    public function portfolio_image()
    {
        return $this->belongsTo(Portfolio_image::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }
}
