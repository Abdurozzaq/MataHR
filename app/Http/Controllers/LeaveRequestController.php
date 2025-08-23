<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Leave;

class LeaveRequestController extends Controller
{
    /**
     * Get leave requests for the current logged-in user by employee_id.
     */
    public function me(Request $request)
    {
        $user = Auth::user();
        // Assuming employee_id is a field in the users table
        $employeeId = $user->employee_id;
        $leaveRequests = Leave::where('employee_id', $employeeId)
            ->orderByDesc('created_at')
            ->get();
        return response()->json($leaveRequests);
    }
}
