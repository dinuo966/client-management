<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CustomerResource",
 *     description="客户资源响应结构"
 * )
 */
class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
    **/
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this -> id,
            'first_name'    => $this -> first_name,
            'last_name'     => $this -> last_name,
            'age'           => $this -> age,
            'dob'           => $this -> dob ? $this -> dob -> format('Y-m-d') : null, // 确保日期格式一致
            'email'         => $this -> email,
            'creation_date' => $this -> creation_date,
            'created_at'    => $this -> created_at,
            'updated_at'    => $this -> updated_at,
        ];
    }
}
