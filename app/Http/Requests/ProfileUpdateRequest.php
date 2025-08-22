<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'id' => ['nullable', 'integer'],
            'supervisor_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()?->id)
            ],
            'role' => ['nullable', 'string', 'max:255'],
            'email_verified_at' => ['nullable', 'string', 'max:255'],
            'created_at' => ['nullable', 'string', 'max:255'],
            'updated_at' => ['nullable', 'string', 'max:255'],
            'full_name' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:30'],
            'personal_email' => ['nullable', 'string', 'email', 'max:255'],
            'marital_status' => ['nullable', 'in:single,married,divorced,widowed'],
            'nik' => ['nullable', 'string', 'max:30'],
        ];
        if ($this->user() && $this->user()->role !== 'karyawan') {
            $rules = array_merge($rules, [
                'employee_id' => ['nullable', 'string', 'max:30'],
                'position_id' => ['nullable', 'integer', 'exists:positions,id'],
                'department_id' => ['nullable', 'integer', 'exists:departments,id'],
                'start_date' => ['nullable', 'date'],
                'employment_status' => ['nullable', 'in:permanent,contract,intern'],
                'office_location' => ['nullable', 'string', 'max:255'],
                'supervisor' => ['nullable', 'string', 'max:255'],
                'salary' => ['nullable', 'numeric'],
                'benefits' => ['nullable', 'string'],
                'bank_account' => ['nullable', 'string', 'max:50'],
                'bank_name' => ['nullable', 'string', 'max:100'],
            ]);
        }
        return $rules;
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        if ($this->user() && $this->user()->role === 'karyawan') {
            unset($data['employee_id'], $data['position_id'], $data['department_id'], $data['start_date'], $data['employment_status'], $data['office_location'], $data['supervisor'], $data['salary'], $data['benefits'], $data['bank_account'], $data['bank_name']);
        }
        return $data;
    }
}
