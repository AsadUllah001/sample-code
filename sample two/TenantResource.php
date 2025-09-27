<?php

namespace App\Http\Resources\TenantsManagement;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    /**
        @OA\Schema(
     *     schema="TenantResource",
     *     title="TenantResource",
     *     description="Tenant fields",
     *     type="object",
     *     @OA\Property(property="name", type="integer", example=1),
     *     @OA\Property(property="domain", type="string", example="dummy"),
     *     @OA\Property(property="subscription_start_time", type="string", example="2023-06-06 05:24:39"),
     *     @OA\Property(property="subscription_end_time", type="string", example="2024-06-06 05:24:39"),
     *     @OA\Property(property="permissions", type="array", @OA\Items(ref="#/components/schemas/TenantResource") )
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'domain' => $this->domain,
            'plan' => $this->plan,
            'subscription_start_time' => $this->subscription_start_time,
            'subscription_end_time' => $this->subscription_end_time,
            'created_at' => $this->created_at

        ];
    }
}
