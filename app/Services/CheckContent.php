<?php

namespace App\Services;

use Laravel\Ai\Classification;
use Laravel\Ai\Classification\Boolean;
use Laravel\Ai\Classification\Choice;
use Laravel\Ai\Classification\Score;
use Laravel\Ai\Responses\ClassificationResponse;

final class CheckContent
{
    public const MODEL = 'jev-1.13.0';

    public const RUBRIC = 'preflight-v1';

    public const LEVELS = [
        'No concrete step the reader can follow.',
        'At least one concrete step, but no clear check of the result.',
        'Concrete steps and an explicit way to check the result.',
    ];

    public function check(string $brief, string $draft): ClassificationResponse
    {
        return Classification::of(['brief' => $brief, 'draft' => $draft])
            ->questions([
                'delivers' => new Boolean(
                    'Does draft deliver the practical outcome promised in brief?',
                    [
                        'true' => 'The draft includes the route example and a browser check of its response.',
                        'false' => 'The route example or the browser check is missing.',
                    ],
                ),
                'format' => new Choice('What is the primary format of draft?', [
                    'tutorial' => 'Teaches the reader how to do something.',
                    'announcement' => 'Reports a launch, release, or other news.',
                    'opinion' => 'Argues for a view or preference.',
                    'other' => 'None of the listed formats fits the main purpose.',
                ]),
                'actionability' => new Score(
                    'How actionable is draft for a Laravel developer?',
                    self::LEVELS,
                ),
            ])
            ->classify(provider: 'typesafe', model: self::MODEL);
    }
}
