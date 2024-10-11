<?php

declare(strict_types=1);

namespace NedalNetwork\phpCalendar\Views;

use DateTimeInterface;
use NedalNetwork\phpCalendar\Calendar;
use NedalNetwork\phpCalendar\Config;
use NedalNetwork\phpCalendar\Event;
use Carbon\CarbonInterface;

abstract class View
{
    public function __construct(
        protected Config $config,
        protected Calendar $calendar,
    ) {
    }

    /**
     * Find an event from the internal pool.
     *
     * @return array<Event> either an array of events or false
     */
    abstract protected function findEvents(CarbonInterface $start, CarbonInterface $end): array;

    /**
     * Returns the calendar as a month view.
     */
    abstract public function render(DateTimeInterface|string|null $startDate = null, string $color = ''): string;
}
