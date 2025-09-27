<?php

namespace App\Http\Controllers\API\V1\Board;

use App\Http\Requests\BoardFieldMappingUpdateRequest;
use App\Models\Board;
use App\Http\Controllers\Controller;
use App\Services\FieldMappingService;
use App\Http\Requests\BoardFieldMappingRequest;
use App\Http\Resources\FieldMappingResource;
use App\Models\FieldMapping;

class BoardFieldMappingController extends Controller
{
    private $fieldMappingService;

    public function __construct(FieldMappingService $fieldMappingService)
    {
        $this->fieldMappingService = $fieldMappingService;
    }

    public function index(Board $board)
    {
        $fieldMappings = $this->fieldMappingService
            ->getAll($board);

        return response()->json([
            'data' => [
                'lineItemsExists' => $board->lineItems()->exists(),
                'mappings' => FieldMappingResource::collection($fieldMappings)
            ]
        ]);
    }

    public function show(Board $board, FieldMapping $fieldMapping)
    {
        return new FieldMappingResource($fieldMapping);
    }

    public function store(BoardFieldMappingRequest $request, Board $board)
    {
        $fieldMappings = $this->fieldMappingService
            ->sync($board, collect($request->validated()['mappings']));
        return response()->json([
            'message' => 'Board Field mappings have been saved successfully',
            'data' => FieldMappingResource::collection($fieldMappings)
        ], 201);
    }

    public function destroy(Board $board, FieldMapping $fieldMapping)
    {
        $this->fieldMappingService
            ->delete($fieldMapping);

        return response()->json([
            'message' => 'Mapping has been deleted successfully.'
        ]);
    }
}
