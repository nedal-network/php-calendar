<?php

declare(strict_types=1);

namespace NedalNetwork\phpCalendar;

use Carbon\Carbon;

/** @package NedalNetwork\phpCalendar */
class Event
{
    public string $box_classes;

    public string $classes;

    /**
     * @param string|list<string> $classes
     * @param string|list<string> $box_classes
     */
    public function __construct(
        public Carbon $start,
        public Carbon $end,
        public string $summary = '',
        public bool $mask = false,
        string|array $classes = '',
        string|array $box_classes = [],
    ) {
        $this->classes = is_array($classes) ? implode(' ', $classes) : $classes;
        $this->box_classes = is_array($box_classes) ? implode(' ', $box_classes) : $box_classes;
    }
}
