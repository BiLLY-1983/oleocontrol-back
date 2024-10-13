<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'date' => $this->date,
            'sender' => [
                'id' => $this->sender->id,
                'username' => $this->sender->username,
                'name' => $this->sender->first_name . ' ' . $this->sender->last_name,
            ],
            'receiver' => [
                'id' => $this->receiver->id,
                'username' => $this->receiver->username,
                'name' => $this->receiver->first_name . ' ' . $this->receiver->last_name,
            ],
        ];
    }
}
