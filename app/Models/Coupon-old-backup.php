<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_order',
        'max_uses', 'used_count', 'expires_at', 'is_active'
    ];

    protected $casts = [
        'expires_at' => 'date',
    ];

    public function isValid($orderAmount): array
    {
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'Coupon is inactive.'];
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return ['valid' => false, 'message' => 'Coupon has expired.'];
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return ['valid' => false, 'message' => 'Coupon usage limit reached.'];
        }

        if ($orderAmount < $this->min_order) {
            return ['valid' => false, 'message' => "Minimum order of ₹{$this->min_order} required."];
        }

        return ['valid' => true, 'message' => 'Coupon applied successfully!'];
    }

    public function getDiscount($orderAmount): float
    {
        if ($this->type === 'percent') {
            return round(($this->value / 100) * $orderAmount, 2);
        }
        return min($this->value, $orderAmount); // fixed discount can't exceed order
    }
}