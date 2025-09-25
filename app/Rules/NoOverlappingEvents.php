<?php

namespace App\Rules;

use App\Models\Event;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class NoOverlappingEvents implements ValidationRule
{
    protected $fromDate;
    protected $toDate;
    protected $excludeEventId;

    public function __construct($fromDate, $toDate, $excludeEventId = null)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->excludeEventId = $excludeEventId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->fromDate || !$this->toDate) {
            return; // Skip validation if dates are not provided
        }

        $query = Event::where(function ($q) {
            // Check if the new event's start date falls within any existing event's date range
            $q->where(function ($subQ) {
                $subQ->where('from_date', '<=', $this->fromDate)
                     ->where('to_date', '>', $this->fromDate);
            })
            // Check if the new event's end date falls within any existing event's date range
            ->orWhere(function ($subQ) {
                $subQ->where('from_date', '<', $this->toDate)
                     ->where('to_date', '>=', $this->toDate);
            })
            // Check if the new event completely encompasses an existing event
            ->orWhere(function ($subQ) {
                $subQ->where('from_date', '>=', $this->fromDate)
                     ->where('to_date', '<=', $this->toDate);
            })
            // Check if an existing event completely encompasses the new event
            ->orWhere(function ($subQ) {
                $subQ->where('from_date', '<=', $this->fromDate)
                     ->where('to_date', '>=', $this->toDate);
            });
        });

        // Exclude current event when updating
        if ($this->excludeEventId) {
            $query->where('id', '!=', $this->excludeEventId);
        }

        // Debug logging
        \Log::info('Overlap validation debug', [
            'from_date' => $this->fromDate,
            'to_date' => $this->toDate,
            'exclude_event_id' => $this->excludeEventId,
            'query_count' => $query->count(),
            'query_sql' => $query->toSql(),
            'query_bindings' => $query->getBindings(),
        ]);

        if ($query->exists()) {
            $fail('The event dates overlap with an existing event. Please choose different dates.');
        }
    }
}
