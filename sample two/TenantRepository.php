<?php

namespace App\Http\Repositories\TenantManagement;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class TenantRepository
{

    public function createTenant($request)
    {
        $checking = Tenant::create([
            'name' => $request['name'],
            'plan' => $request['plan'] ?? null,
            'subscription_start_time' => $request['subscription_start_time'],
            'subscription_end_time' => $request['subscription_end_time']
        ]);
        return $checking;
    }

    public function createTenantUser($tenant, $tenantData)
    {
        if ($tenant) {
            $tenant->run(function ($tenant) use ($tenantData) {
                $user = User::create(
                    [
                        'name' => $tenantData['user_name'],
                        'user_name' => $tenantData['user_name'],
                        'email' => $tenantData['email'],
                        'password' => Hash::make($tenantData['password'])
                    ]
                );
                $role =  Role::whereIn('name', ['Super Admin'])->get();
                $user->assignRole($role);
            });
            return $tenant;
        }
    }
    public function getAllTenants()
    {
        return Tenant::with('domain')->latest()->get();
    }
    public function status($request)
    {
        Tenant::where('id', $request->id)->update(['status' => $request->status]);
        return Tenant::where('id', $request->id)->first();
    }

    public function findAndUpdate($id, $parameter)
    {
        return  Tenant::where('id', $id)->update($parameter);
    }

    public function delete($id)
    {
        return Tenant::where('id', $id)->delete();
    }
}
