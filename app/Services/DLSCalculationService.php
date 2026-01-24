<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class DLSCalculationService
{
    private $resourceTable;

    public function __construct()
    {
        $this->resourceTable = config('DLS'); // Load the resource table from config
    }

    /**
     * Get the resource percentage based on overs remaining and wickets lost.
     */
    public function getResource(float $oversRemaining, int $wicketsLost): float
    {
        // Validate inputs
        if ($oversRemaining < 0 || $wicketsLost < 0 || $wicketsLost > 9) {
            throw new \InvalidArgumentException('Invalid inputs: overs must be non-negative and wickets between 0 and 9.');
        }

        $floorOvers = (int)floor($oversRemaining);
        $partialOver = $oversRemaining - $floorOvers;

        // Handle edge cases for overs beyond the table
        if (!isset($this->resourceTable[$floorOvers])) {
            $floorOvers = max(array_keys($this->resourceTable)); // Use the maximum overs defined
        }

        // Get resource values for current and next overs
        $currentResource = $this->resourceTable[$floorOvers][$wicketsLost] ?? 0;
        $nextResource = $this->resourceTable[$floorOvers + 1][$wicketsLost] ?? $currentResource;

        // Interpolate for fractional overs
        return round($currentResource + ($partialOver * ($nextResource - $currentResource)), 1);
    }

    /**
     * Calculate the revised target based on the DLS method.
     */
    public function calculateTarget(
        int $firstInningsRuns,
        float $initialOvers,
        float $revisedOvers,
        int $wicketsLost,
        int $remainngOversLeft
    ): int {
        if ($firstInningsRuns < 0 || $initialOvers <= 0 || $revisedOvers < 0) {
            throw new \InvalidArgumentException('Invalid inputs: runs and overs must be non-negative.');
        }

        // Get initial and available resources
        $initialResources = $this->getResource($initialOvers, 0);

        $availableResources = $this->getResource((int) $remainngOversLeft, (int) $wicketsLost);

        Log::info("Initial resources: $initialResources, Available resources: $availableResources");

        if ($initialResources == 0) {
            throw new \InvalidArgumentException('Initial resources cannot be zero.');
        }

        // Calculate the revised target
        $calculatedTarget = floor(($firstInningsRuns * $availableResources) / $initialResources);

        Log::info("Calculated target (without +1): $calculatedTarget");

        return max($calculatedTarget, 1); // Ensure target is at least 1
    }

}
