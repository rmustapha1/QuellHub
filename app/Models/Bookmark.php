<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Bookmark extends Model {
    use HasFactory;

    protected $table = "bookmarks";
    protected $primaryKey = "id";

    public function hostels(){
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }
}
