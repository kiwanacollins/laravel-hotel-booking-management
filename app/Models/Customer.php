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

        // Validate file
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $validExtensions)) {
            throw new \Exception('Invalid file type. Only images are allowed.');
        }

        // Max file size 2MB
        if ($file->getSize() > 2 * 1024 * 1024) {
            throw new \Exception('File size must be less than 2MB.');
        }

        $filename = 'customer_' . $this->id . '_' . time() . '.' . $extension;
        $path = $file->storeAs('public/profile_pictures', $filename);
        
        $this->profile_picture = str_replace('public/', 'storage/', $path);
        $this->save();

        return $this->profile_picture;
    }

    // Get Profile Picture URL with fallback
    public function getProfilePicture()
    {
        $defaultAvatars = [
            'male' => asset('img/default-male-profile.png'),
            'female' => asset('img/default-female-profile.png'),
            'default' => asset('img/default-profile.png')
        ];

        // Check if profile picture exists and is valid
        if ($this->profile_picture && \Storage::exists(str_replace('storage/', 'public/', $this->profile_picture))) {
            return asset($this->profile_picture);
        }

        // Use gender-based default if possible
        if (isset($this->gender)) {
            return strtolower($this->gender) == 'male' 
                ? $defaultAvatars['male'] 
                : $defaultAvatars['female'];
        }

        // Fallback to generic default
        return $defaultAvatars['default'];
    }

    // Telephone number validation helper
    public function formatTelephone()
    {
        if (!$this->telephone) return null;

        // Remove all non-digit characters
        $cleaned = preg_replace('/[^0-9]/', '', $this->telephone);

        // Validate and format Ugandan phone numbers
        if (strlen($cleaned) == 9) {
            return "+256 " . substr($cleaned, 0, 3) . " " . substr($cleaned, 3, 3) . " " . substr($cleaned, 6);
        } elseif (strlen($cleaned) == 10) {
            return "+256 " . substr($cleaned, 1, 3) . " " . substr($cleaned, 4, 3) . " " . substr($cleaned, 7);
        }

        return $this->telephone;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
