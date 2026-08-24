<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Establishment extends Model
{
    //
    protected $fillable = [
    'owner_id',
    'name',
    'category',
    'address',
    'description',
];
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class);
    }
}
