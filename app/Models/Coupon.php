<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'type', 'value', 'min_order',
        'max_uses', 'used_count', 'expires_at',
        'is_active', 'valid_days', 'valid_from_time', 'valid_until_time'
    ];

    protected $casts = [
        'expires_at' => 'date',
        'valid_days' => 'array',
    ];

    public function isValid($orderAmount, $bookingDate = null): array
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

        // Check day restriction based on booking date
        if ($this->valid_days && count($this->valid_days) > 0) {
            $checkDate = $bookingDate 
                ? Carbon::parse($bookingDate, 'Asia/Kolkata') 
                : Carbon::now('Asia/Kolkata');
            
            $dayOfWeek = $checkDate->format('l'); // e.g. "Wednesday"
            $validDays = array_map('strtolower', $this->valid_days);
            
            if (!in_array(strtolower($dayOfWeek), $validDays)) {
                $days = implode(', ', array_map('ucfirst', $validDays));
                return ['valid' => false, 'message' => "Coupon valid only on: {$days}."];
            }
        }

        // Check time restriction (IST) - still based on current time
        if ($this->valid_from_time && $this->valid_until_time) {
            $nowIST = Carbon::now('Asia/Kolkata');
            $currentTime = $nowIST->format('H:i:s');

            if ($currentTime < $this->valid_from_time || $currentTime > $this->valid_until_time) {
                $from = Carbon::parse($this->valid_from_time)->format('h:i A');
                $until = Carbon::parse($this->valid_until_time)->format('h:i A');
                return ['valid' => false, 'message' => "Coupon valid only between {$from} - {$until} IST."];
            }
        }

        return ['valid' => true, 'message' => 'Coupon applied successfully!'];
    }

    public function getDiscount($orderAmount): float
    {
        if ($this->type === 'percent') {
            return round(($this->value / 100) * $orderAmount, 2);
        }
        return min($this->value, $orderAmount);
    }
}