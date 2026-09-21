<?php

namespace App\Services;

use Laravel\Ai\Responses\ClassificationResponse;
use Laravel\Ai\Responses\Data\BooleanAnswer;
use Laravel\Ai\Responses\Data\ChoiceAnswer;
use Laravel\Ai\Responses\Data\ScoreAnswer;

final class PreflightPolicy
{
    public const VERSION = 'policy-v1';

    public function recommend(ClassificationResponse $response): string
    {
        $yes = $response->answers['delivers'] ?? null;
        $format = $response->answers['format'] ?? null;
        $steps = $response->answers['actionability'] ?? null;

        if (! $yes instanceof BooleanAnswer
            || ! $format instanceof ChoiceAnswer
            || ! $steps instanceof ScoreAnswer) {
            return 'needs-review';
        }

        foreach ([$yes->probability, $format->confidence, $steps->confidence] as $n) {
            if (! $this->unit($n)) {
                return 'needs-review';
            }
        }

        if (! $this->distribution($format->probabilities, ['tutorial', 'announcement', 'opinion', 'other'])
            || ! $this->distribution($steps->probabilities, [0, 1, 2])
            || $steps->legend !== CheckContent::LEVELS
            || ! is_finite($steps->score) || $steps->score < 0 || $steps->score > 2
            || ! array_key_exists($format->choice, $format->probabilities)) {
            return 'needs-review';
        }

        if ($format->confidence < 0.70 || $steps->confidence < 0.70
            || $format->choice !== 'tutorial') {
            return 'needs-review';
        }

        if ($yes->probability >= 0.85 && $steps->score >= 1.60) {
            return 'ready-for-editor';
        }

        if ($yes->probability <= 0.20 || $steps->score <= 0.60) {
            return 'revise';
        }

        return 'needs-review';
    }

    private function unit(mixed $value): bool
    {
        return (is_int($value) || is_float($value))
            && is_finite((float) $value) && $value >= 0 && $value <= 1;
    }

    private function distribution(array $values, array $keys): bool
    {
        if (count($values) !== count($keys)
            || array_diff($keys, array_keys($values)) !== []) {
            return false;
        }
        foreach ($values as $value) {
            if (! $this->unit($value)) {
                return false;
            }
        }

        return abs(array_sum($values) - 1.0) <= 0.001;
    }
}
