<?php

namespace App\Http\Requests\Tenant\Deals;

use App\Enums\BillingCycleEnum;
use App\Enums\DealTypeEnum;
use App\Enums\DiscountTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Settings\DealsSettings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DealRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // 'deal_type' => ['required', Rule::in(DealTypeEnum::values())],
            'deal_name' => 'required|string|max:255',
            'lead_id' => 'required|exists:leads,id',
            'sale_date' => 'required|date',
            'discount_type' => ['nullable', Rule::in(DiscountTypeEnum::values())],
            'discount_value' => 'required_with:discount_type|nullable|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0',
            'assigned_to_id' => 'required|exists:users,id,department_id,1',
            'payment_status' => ['required', Rule::in(PaymentStatusEnum::values())],
            'payment_method_id' => [
                'required',
                Rule::exists('payment_methods', 'id')->where(fn($q) => $q->where('is_checked', 1))
            ],
            'notes' => 'nullable|string|max:255',
            'partial_amount_paid' => 'required_if:payment_status,partial|nullable|numeric|min:0',
            'items' => 'required|array',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.variant_id' => 'nullable|exists:item_variants,id',
            'items.*.quantity' => 'sometimes|integer|min:1',
            'items.*.price' => 'required|numeric',
            'items.*.start_at' => 'nullable|date|after_or_equal:today',
            'items.*.end_at' => 'nullable|date|after:items.*.start_at',
            'items.*.billing_cycle' => ['nullable', Rule::in(BillingCycleEnum::values())],
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:png,jpg,jpeg,pdf,doc,docx|max:' . $this->getMaxAttachmentSize(),
        ];
    }

    /**
     * Get the maximum attachment size from settings
     */
    private function getMaxAttachmentSize(): int
    {
        $settings = app(DealsSettings::class);
        return $settings->attachment_size_limit_mb * 1024; // Convert MB to KB
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate partial payment amount against minimum percentage
            if ($this->input('payment_status') === PaymentStatusEnum::PARTIAL->value) {
                $partialAmountPaid = $this->input('partial_amount_paid');
                $totalAmount = $this->calculateTotalAmount();

                if ($partialAmountPaid !== null && $totalAmount !== null) {
                    // Check if partial amount paid is greater than total amount
                    if ($partialAmountPaid > $totalAmount) {
                        $validator->errors()->add(
                            'partial_amount_paid',
                            "Partial amount paid cannot exceed the total amount ({$totalAmount})."
                        );
                        return;
                    }

                    $settings = app(DealsSettings::class);
                    $minPercentage = $settings->min_payed_percentage;
                    $minRequiredAmount = $totalAmount * ($minPercentage / 100);

                    if ($partialAmountPaid < $minRequiredAmount) {
                        $validator->errors()->add(
                            'partial_amount_paid',
                            "Partial amount paid must be at least {$minPercentage}% of the total amount ({$minRequiredAmount})."
                        );
                    }
                }
            }

            $items = $this->input('items', []);

            foreach ($items as $index => $item) {
                if (isset($item['item_id']) && isset($item['variant_id'])) {
                    $this->validateItemVariantRelation($validator, $index, $item['item_id'], $item['variant_id']);
                }

                // Validate subscription fields
                $this->validateSubscriptionFields($validator, $index, $item);
            }
        });
    }

    /**
     * Validate subscription fields for an item
     */
    protected function validateSubscriptionFields($validator, $index, $item)
    {
        $hasStartAt = !empty($item['start_at']);
        $hasEndAt = !empty($item['end_at']);
        $hasBillingCycle = !empty($item['billing_cycle']);

        // If any subscription field is provided, all should be provided
        if ($hasStartAt || $hasEndAt || $hasBillingCycle) {
            if (!$hasStartAt) {
                $validator->errors()->add(
                    "items.{$index}.start_at",
                    "Start date is required when subscription fields are provided."
                );
            }

            if (!$hasEndAt) {
                $validator->errors()->add(
                    "items.{$index}.end_at",
                    "End date is required when subscription fields are provided."
                );
            }

            if (!$hasBillingCycle) {
                $validator->errors()->add(
                    "items.{$index}.billing_cycle",
                    "Billing cycle is required when subscription fields are provided."
                );
            }
        }

        // Validate date range consistency with billing cycle
        if ($hasStartAt && $hasEndAt && $hasBillingCycle) {
            $startAt = \Carbon\Carbon::parse($item['start_at']);
            $endAt = \Carbon\Carbon::parse($item['end_at']);
            $billingCycle = BillingCycleEnum::from($item['billing_cycle']);

            // Calculate expected end date based on billing cycle
            $expectedEndAt = $this->calculateExpectedEndDate($startAt, $billingCycle);

            // Allow some flexibility (±2 days) for billing cycle validation
            $daysDifference = $endAt->diffInDays($expectedEndAt);

            if ($daysDifference > 2) {
                $expectedEndAtFormatted = $expectedEndAt->format('Y-m-d');
                $validator->errors()->add(
                    "items.{$index}.end_at",
                    "End date should be approximately {$expectedEndAtFormatted} for {$billingCycle->getLabel()} billing cycle starting from {$startAt->format('Y-m-d')}."
                );
            }
        }
    }

    /**
     * Calculate expected end date based on start date and billing cycle
     */
    private function calculateExpectedEndDate(\Carbon\Carbon $startAt, BillingCycleEnum $billingCycle): \Carbon\Carbon
    {
        return match ($billingCycle) {
            BillingCycleEnum::MONTHLY => $startAt->copy()->addMonth(),
            BillingCycleEnum::QUARTERLY => $startAt->copy()->addMonths(3),
            BillingCycleEnum::YEARLY => $startAt->copy()->addYear(),
        };
    }

    /**
     * Validate that the variant belongs to the specified item
     */
    protected function validateItemVariantRelation($validator, $index, $itemId, $variantId)
    {
        $variant = \App\Models\Tenant\ItemVariant::find($variantId);

        if ($variant && $variant->product->item->id != $itemId) {
            $validator->errors()->add(
                "items.{$index}.variant_id",
                "The selected variant does not belong to the specified item."
            );
        }
    }

    /**
     * Calculate the total amount from items
     */
    private function calculateTotalAmount(): ?float
    {
        $items = $this->input('items', []);
        if (empty($items)) {
            return null;
        }

        $total = 0;
        foreach ($items as $item) {
            $quantity = $item['quantity'] ?? 1;
            $price = $item['price'] ?? 0;
            $total += $quantity * $price;
        }

        // Apply discount if provided
        $discountValue = $this->input('discount_value', 0);
        $discountType = $this->input('discount_type');

        if ($discountValue > 0) {
            if ($discountType === 'percentage') {
                $total = $total - ($total * $discountValue / 100);
            } else {
                $total = $total - $discountValue;
            }
        }

        // Apply tax
        $taxRate = $this->input('tax_rate', 0);
        $total = $total + ($total * $taxRate / 100);

        return $total;
    }
}
