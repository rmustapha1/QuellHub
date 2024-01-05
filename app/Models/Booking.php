<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = "bookings";
    protected $primaryKey = "id";

    public function rooms(){
        return $this->belongsTo(Room::class,'room_id');
    }

    public function hostels(){
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }
}
