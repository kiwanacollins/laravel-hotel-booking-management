<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'address',
        'job',
        'birthdate',
        'user_id',
        'gender',
        'telephone',
        'profile_picture'
    ];

    // Profile Picture Upload Handling
    public function uploadProfilePicture($file)
    {
        if (!$file) return null;

        $filename = 'customer_' . $this->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('public/profile_pictures', $filename);
        
        $this->profile_picture = str_replace('public/', 'storage/', $path);
        $this->save();

        return $this->profile_picture;
    }

    // Get Profile Picture URL
    public function getProfilePicture()
    {
        return $this->profile_picture 
            ? asset($this->profile_picture) 
            : asset('img/default-profile.png');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
