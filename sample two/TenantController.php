<?php

namespace App\Http\Controllers\Api\V1\TenantsManagement;


use App\Http\Controllers\Controller;
use App\Http\Requests\TenantsManagement\CreateTenantRequest;
use App\Http\Resources\TenantsManagement\TenantResource;
use App\Services\Auth\AuthenticationService;
use App\Services\TenantManagement\TenantService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * @OA\Tag(
 *     name="Tenants",
 *     description="Endpoints for managing roles"
 * )
 */
class TenantController extends Controller
{
    use ApiResponse;

    protected $tenantService;
    protected $authenticationService;


    public function __construct(TenantService $tenantService, AuthenticationService $authenticationService)
    {
        $this->tenantService = $tenantService;
        $this->authenticationService = $authenticationService;
    }
    /**
     * Create a new tenant.
     *
     * @OA\Post(
     *   path="v1/sp/api/tenants",
     *   summary="Create a new tenant",
     *   tags={"Tenants"},
     *    @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              ref="#/components/schemas/CreateTenantRequest"
     *          )
     *      ),
     *   @OA\Response(
     *     response="200",
     *     description="Tenant has been created successfully",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="example message"),
     *     )
     *   ),
     *   @OA\Response(
     *     response="400",
     *     description="Bad Request",
     *     @OA\JsonContent(
     *       @OA\Property(property="success", type="boolean", example=false),
     *       @OA\Property(property="error", type="string")
     *     )
     *   )
     * )
     */

    public function create(CreateTenantRequest $request)
    {
        try {
             
            $this->tenantService->create($request);
            return $this->successResponse(null, 'Tenant has been created successfully.');
        } catch (Throwable $e) {
            // Handle the exception
            return $e;
            // return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Get(
     *     path="v1/sp/api/tenants",
     *     summary="Get all tenants",
     *     tags={"Tenants"},
     *     @OA\Response(
     *         response=200,
     *         description="Get all tenants",
     *         @OA\JsonContent(@OA\Property(property="success", type="bool", example=true),
     *         @OA\Property(property="message", type="string", example="example message"),
     *         @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/TenantResource")))
     *     ),
     *      @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="bool", example=false),
     *              @OA\Property(property="message", type="string", example="example message"),
     *          )
     *     )
     * )
     */

    public function index()
    {
        $getAllTenants = $this->tenantService->getAllTenants();
        return $this->successResponse($getAllTenants, 'Tenants All');
    }


    // /**
    //  * @OA\Put(
    //  *     path="/api/v1/tenants/status-change",
    //  *     summary="Update existing status",
    //  *     tags={"tenants"},
    //  *     @OA\RequestBody(
    //  *          required=true,
    //  *          @OA\JsonContent(
    //  *              type="object",
    //  *              ref="#/components/schemas/CreateTenantRequest")
    //  *      ),
    //  *     @OA\Response(
    //  *         response=200,
    //  *         description="You have successfully changed the user status to active",
    //  *         @OA\JsonContent(
    //  *              @OA\Property(property="success", type="bool", example=true),
    //  *              @OA\Property(property="message", type="string", example="example message"),
    //  *              @OA\Property(property="data", type="object", ref="#/components/schemas/TenantResource")
    //  *          )
    //  *     ),
    //  *     @OA\Response(
    //  *         response=500,
    //  *         description="Server error",
    //  *         @OA\JsonContent(
    //  *              @OA\Property(property="success", type="bool", example=false),
    //  *              @OA\Property(property="message", type="string", example="example message"),
    //  *          )
    //  *     )
    //  * )
    //  */
    public function status(Request $request)
    {

        $upateStatus = $this->tenantService->status($request);
        if ($upateStatus->status) {
            return $this->successResponse($upateStatus, 'You have successfully changed the user status to active');
        } else {
            return $this->successResponse($upateStatus, 'You have successfully changed the user status to inactive');
        }
    }

    public function delete($id)
    {
        $this->tenantService->delete($id);
        return $this->successResponse(null, 'Tenant has been deleted successfully');
    }


}