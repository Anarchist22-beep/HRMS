<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return[
        'id'=>$this->id,
        'name'=>$this->name,
        'description'=>$this->description,
        'document' => $this->document
            ? asset('storage/' . $this->document)
            : null,
        'documents' => $this->relationLoaded('documents') ? $this->documents->map(function ($doc) {
            return [
                'id' => $doc->id,
                'document' => asset('storage/' . $doc->document),
                'project_id' => $doc->project_id,
            ];
        }) : null,
        'payments' => $this->relationLoaded('payments') ? $this->payments->map(function ($payment) {
            return [
                'id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'amount' => $payment->amount,
                'project_id' => $payment->project_id,
                'amount_paid' => $payment->amount_paid,
                'amount_received'=>$payment->amount_received,
                'created_at' => $payment->created_at,
            ];
        }) : null,
        // ── Payment totals ──
'total_amount'          => $this->relationLoaded('payments') ? $this->payments->sum('amount') : 0,
'total_amount_paid'     => $this->relationLoaded('payments') ? $this->payments->where('amount_paid', true)->sum('amount') : 0,
'total_amount_received' => $this->relationLoaded('payments') ? $this->payments->where('amount_received', true)->sum('amount') : 0,
        'status'        => $this->status

       ];
    }
}
