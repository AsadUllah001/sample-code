<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use stdClass;

class FieldMappingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'object' => $this->object,
            'sf_field' => $this->sf_field,
            'sf_label' => $this->sf_label,
            'system_field' => $this->system_field,
            'system_label' => $this->system_label,
            'type' => $this->type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'entry_name' => $this->entry_name,
            'converted_to_usd' => $this->converted_to_usd,
            'is_required' => $this->is_required,
            'sf_required' => $this->sf_required ,
            'is_reference_field' => $this->is_reference_field,
            'is_lookup' => $this->is_lookup,
            'splittable' => $this->splittable,
            'controller_name' => $this->controller_name,
            'custom' => $this->custom,
            'picklist' => $this->picklist ?? [],
            'reference_to' => $this->reference_to ?? new stdClass,
            'should_show' => $this->should_show
        ];
    }
}
