<?php

namespace App\Services\TenantManagement;

use App\Mail\SendConfirmationEmail;
use App\Mail\TenantCreationMail;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Traits\Constants;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;
use Illuminate\Support\Facades\Http;
use App\Http\Repositories\TenantManagement\TenantRepository;
use App\Jobs\CreateTenantJob;
use GuzzleHttp\Client;
use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Jobs\{CreateDatabase, MigrateDatabase, SeedDatabase};


class TenantService
{
    use ApiResponse;
    use Constants;
    protected $tenantRepository;
    protected  $domain, $link;

    public function __construct(TenantRepository $tenantRepository)
    {
        $this->tenantRepository = $tenantRepository;
    }
    public function create($request)
    {
        info(['37', $request->all()]);
        CreateTenantJob::dispatch($request->all());
    }
    public function sendGuzzleRequest($request)
    {
        // Replace the URL with the actual API endpoint of your second application
        $domainPrefix = strtolower(str_replace(' ', '', $request['name']));
        $apiUrl = config('constants.DOMAIN_PREFIX') . $domainPrefix . config('constants.DOMAIN_POSTFIX_API') . 'api/v1/tenant/run-tenant-seeder';
        $secretKey = 'PAOjvT3mxF3BB3c8kspmp2Afd7yEHcLjXkM43RlW'; // Replace with your actual secret key
        $apiUrl .= '?secret=' .$secretKey;
        $client = new Client();
        $header = [
            'Content-Type' => 'application/json',
        ];
        $response = $client->get($apiUrl, [
            'headers' => $header,
        ]);
        return $response;
    }

    public function createTenantDomain($request, $tenant)
    {
        $domainPrefix = strtolower(str_replace(' ', '', $request['name']));
        $domain = $tenant->domains()->create([
            'domain' => $domainPrefix . config('constants.DOMAIN'),
            'domain_url' => config('constants.DOMAIN_PREFIX') . $domainPrefix . config('constants.DOMAIN_POSTFIX')
        ]);
        $this->domain = $domainPrefix;
        $this->link = $domain->domain_url;
    }
    public  function prepareTenantInfo($request, $tenant)
    {
        $password = Str::random(10);
        return [
            'user_name' => $request['name'],
            'email' => $request['email'],
            'password' =>  $password,
            'tenancy_db_name' => $tenant->tenancy_db_name,
        ];
    }

    public function sendMail($tenantData)
    {
        try {
            Mail::to($tenantData['email'])->send(new TenantCreationMail($tenantData, $this->domain, $this->link));
        } catch (Throwable $e) {
            throw $e;   
        }
    }
    /**
     * Retrieve all tenants.
     *
     * @OA\Get(
     *   path="/api/v1/sp/tenants",
     *   summary="Retrieve all tenants",
     *   tags={"Tenants"},
     *   @OA\Response(
     *     response="200",
     *     description="Success"
     *   )
     * )
     */
    public function getAllTenants()
    {
        return $this->tenantRepository->getAllTenants();
    }

    public function status($request)
    {
        return $this->tenantRepository->status($request);
    }

    public function delete($id)
    {
        return $this->tenantRepository->delete($id);
    }

    protected function findAndUpdate($id, $parameter)
    {
        return  $this->tenantRepository->findAndUpdate($id, $parameter);
    }


    private function runNewTenantSeeder($tenant)
    {
        JobPipeline::make([
            Artisan::call('tenants:migrate', [
                '--tenants' => $tenant->id,
                '--force' => true
            ]),
            Artisan::call('tenants:seed', [
                '--tenants' => $tenant->id,
                '--class' => "Database\\Seeders\\tenant\\TenantDatabaseSeeder",
                '--force' => true
            ]),
        ]);
    }
}
