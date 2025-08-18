<?php
namespace App\Policies;

use App\Models\EmergencyContact;
use App\Models\User;

class EmergencyContactPolicy
{
    public function update(User $user, EmergencyContact $emergencyContact)
    {
        return $user->id === $emergencyContact->user_id;
    }

    public function delete(User $user, EmergencyContact $emergencyContact)
    {
        return $user->id === $emergencyContact->user_id;
    }
}
