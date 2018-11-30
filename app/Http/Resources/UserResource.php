<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'address_books' => $this->address_books,
            'roles' => $this->roles,
            'login_providers' => $this->login_providers,
            'enable' => $this->enable
        ];
    }
}
