<?php

namespace App\Http\Requests\TenantsManagement;

use Illuminate\Foundation\Http\FormRequest;

class CreateTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */ 
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */

    
      /**
        @OA\Schema(
    *     schema="CreateTenantRequest",
    *     title="Create Tenant Request",
    *     description="Create tenant request",
    *     type="object",
    *     @OA\Property(property="name", type="string", example="hello"),
    *     @OA\Property(property="plan", type="string", example="platinume"),
    *     @OA\Property(property="subscription_start_time", type="string", example="2023-06-06 05:24:39"),
    *     @OA\Property(property="subscription_end_time", type="string", example="2023-06-06 05:24:39"),



    * )
    */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s]+$/', 'unique:tenants,name'],
            'plan' => ['sometimes'],
            'subscription_start_time' => ['required'],
            'subscription_end_time' => ['required'],

        ];
    }
}
