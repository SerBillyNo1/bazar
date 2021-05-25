<?php

namespace Bazar\Contracts\Models;

use Bazar\Contracts\Taxable;
use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Shipping extends Taxable
{
    /**
     * Get the shippable model for the shipping.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function shippable(): MorphTo;

    /**
     * Get the driver attribute.
     *
     * @param  string|null  $value
     * @return string
     */
    public function getDriverAttribute(?string $value = null): string;

    /**
     * Get the total attribute.
     *
     * @return float
     */
    public function getTotalAttribute(): float;

    /**
     * Get the formatted total attribute.
     *
     * @return string
     */
    public function getFormattedTotalAttribute(): string;

    /**
     * Get the net total attribute.
     *
     * @return float
     */
    public function getNetTotalAttribute(): float;

    /**
     * Get the formatted net total attribute.
     *
     * @return string
     */
    public function getFormattedNetTotalAttribute(): string;

    /**
     * Get the quantity attribute.
     *
     * @return int
     */
    public function getQuantityAttribute(): int;

    /**
     * Get the price attribute.
     *
     * @return float
     */
    public function getPriceAttribute(): float;

    /**
     * Get the name of the shipping method.
     *
     * @return string
     */
    public function getDriverNameAttribute(): string;

    /**
     * Get the shipping's total.
     *
     * @return float
     */
    public function getTotal(): float;

    /**
     * Get the shipping's formatted total.
     *
     * @return string
     */
    public function getFormattedTotal(): string;

    /**
     * Get the shipping's net total.
     *
     * @return float
     */
    public function getNetTotal(): float;

    /**
     * Get the shipping's formatted net total.
     *
     * @return string
     */
    public function getFormattedNetTotal(): string;

    /**
     * Calculate the cost.
     *
     * @param  bool  $update
     * @return float
     */
    public function cost(bool $update = true): float;
}
