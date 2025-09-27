<?php

namespace App\Services;

use App\Exceptions\DBOperationException;
use App\Models\LineItemValue;
use Throwable;
use App\Models\Board;
use App\Models\FieldMapping;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FieldMappingService
{
    public function getAll(Board $board)
    {
        return $board->fieldMappings;
    }

    public function sync(Board $board, Collection $mappings)
    {
        DB::beginTransaction();

        try {
            $fieldMappings = $mappings->map(function ($mapping, $order) use ($board) {

                if (!isset($mapping['id'])) {
                    $mapp = $board->fieldMappings()->max('order');
                    $order = ++$mapp;
                    $mapping['order'] = $order;
                }
                $mapping['system_field'] = Str::snake($mapping['system_label']);
                $mapping['picklist'] = $mapping['picklist'] ?? [];
                $mapping['reference_to'] = $mapping['reference_to'] ?? null;

                /**
                 * If NO is set on the mapping data array means it is
                 * a new mappings so we create a new one and return
                 */
                if (!isset($mapping['id'])) {
                    return $board->fieldMappings()->create($mapping);
                }

                $fieldMapping = $board->fieldMappings()
                    ->find($mapping['id']);

                /**
                 * If there is id on field and the mapping already exists in database
                 * then we going to update that with the given data
                 */
                if ($fieldMapping) {
                    $fieldMapping->fill($mapping)->save();

                    if ($fieldMapping->wasChanged('type')) {
                        $fieldMapping->lineItemValues()->update([
                            'value' => null,
                            'meta' => null
                        ]);
                    }

                    return $fieldMapping;
                }

                /**
                 * Here if the id was set on the mapping request data but
                 * that is does not belong the the current board then we will
                 * create a new fieldmapping on the current board with the
                 * given data
                 */
                // unset($mapping['id']);
                // return $board->fieldMappings()->create($mapping);
                return null;
            });
            /**
             * Because there is no endpoint to delete a fieldmapping
             * so we are syncing database and the curent request
             * So if a fieldMappinng is not sent with this request
             * it means it's been deleted so we will delete those which are
             * not sent with this request
             */
            $currentFieldMappingIds = $fieldMappings->pluck('id');
            $fieldMapping = $fieldMappings[0];
            if ($fieldMapping->is_reference_field) {
                $board->fieldMappings()->where('id', $fieldMapping->id)->update(array("is_reference_field" => true));
                $board->fieldMappings()->whereNotIn('id', $currentFieldMappingIds)->update(array("is_reference_field" => false));
            }
            if ($fieldMapping->splittable) {
                $board->fieldMappings()->where('id', $fieldMapping->id)->update(array("splittable" => true));
                $board->fieldMappings()->whereNotIn('id', $currentFieldMappingIds)->update(array("splittable" => false));
            }
            if ($fieldMapping->start_date) {
                $board->fieldMappings()->where('id', $fieldMapping->id)->update(array("start_date" => true));
                $board->fieldMappings()->whereNotIn('id', $currentFieldMappingIds)->update(array("start_date" => false));
            }
            if ($fieldMapping->end_date) {
                $board->fieldMappings()->where('id', $fieldMapping->id)->update(array("end_date" => true));
                $board->fieldMappings()->whereNotIn('id', $currentFieldMappingIds)->update(array("end_date" => false));
            }
            if ($fieldMapping->entry_name) {
                $board->fieldMappings()->where('id', $fieldMapping->id)->update(array("entry_name" => true));
                $board->fieldMappings()->whereNotIn('id', $currentFieldMappingIds)->update(array("entry_name" => false));
            }

            $board->load('lineItems.lineItemValues'); //remeber me
            foreach ($board->lineItems as $lineItem)
            {
                $isExist = false;
                $lineItemValues = $lineItem->lineItemValues;
                if ($lineItemValues) {
                    foreach ($lineItemValues as $lineItemValue) {
                        foreach ($currentFieldMappingIds as $currentFieldMappingId) {
                            if (!$currentFieldMappingId)
                                continue;
                            if($lineItemValue->field_mapping_id == $currentFieldMappingId)
                                $isExist = true;
                        }
                    }
                    if (!$isExist) {
                        LineItemValue::create([
                            "line_item_id" => $lineItem->id,
                            "field_mapping_id" => $currentFieldMappingId,
                            "user_id" => request()->user()->uuid,
                            "value" => null,
                            "meta" => null,
                        ]);
                    }
                }
            }

            DB::commit();

            return $board->fieldMappings;
        } catch (Throwable $th) {
            DB::rollBack();

            report($th);

            throw new DBOperationException($th->getMessage());
        }
    }

    public function delete(FieldMapping $fieldMapping)
    {
        return $fieldMapping->delete();
    }

    public function createDefaultMappingsForBoard(Board $board)
    {
        $defaultMappings = [
            [
                'system_field' => 'amount',
                'system_label' => 'Amount',
                'type' => 'number',
                'is_required' => true,
                'is_visible' => true,
            ],
            [
                'system_field' => 'theater',
                'system_label' => 'Theater',
                'type' => 'string',
                'is_required' => true,
                'is_visible' => true,
            ],
            [
                'system_field' => 'region',
                'system_label' => 'Region',
                'type' => 'string',
                'is_required' => true,
                'is_visible' => true,
            ],
            [
                'system_field' => 'sub_region',
                'system_label' => 'Sub Region',
                'type' => 'string',
                'is_required' => true,
                'is_visible' => true,
            ],
        ];

        foreach ($defaultMappings as $order => $mapping) {
            $mapping['order'] = $order;

            $board->fieldMappings()
                ->create($mapping);
        }
    }

}
