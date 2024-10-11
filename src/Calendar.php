<?php

declare(strict_types=1);

namespace NedalNetwork\phpCalendar;

use BadMethodCallException;
use DateTimeInterface;
use NedalNetwork\phpCalendar\Views\Month;
use NedalNetwork\phpCalendar\Views\Week;
use Carbon\Carbon;

/**
 * Simple PHP Calendar Class.
 *
 * @license https://github.com/NedalNetwork/php-calendar
 *
 * @method $this hideSundays()
 * @method $this hideMondays()
 * @method $this hideTuesdays()
 * @method $this hideWednesdays()
 * @method $this hideThursdays()
 * @method $this hideFridays()
 * @method $this hideSaturdays()
 */
class Calendar
{
    private Config $config;

    public function __construct()
    {
        $this->config = new Config();
    }

    public function setLocale(string $locale): static
    {
        $this->config->locale = $locale;

        return $this;
    }

    /**
     * @param array<string,mixed> $args
     *
     * @return $this
     */
    public function __call(string $method, array $args): static
    {
        if (str_starts_with($method, 'hide')) {
            $this->config->hiddenDays[] = rtrim(ltrim($method, 'hide'), 's');

            return $this;
        }

        throw new BadMethodCallException(sprintf('Method "%s" does not exist.', $method));
    }

    /**
     * Sets the day format flag to return initial day names. This is the default behaviour.
     */
    public function useInitialDayNames(): static
    {
        $this->config->day_format = 'initials';

        return $this;
    }

    /**
     * Sets the day format flag to return full day names instead of initials by default.
     */
    public function useFullDayNames(): static
    {
        $this->config->day_format = 'full';

        return $this;
    }

    /**
     * Changes the weekly start date to Sunday.
     */
    public function useSundayStartingDate(): static
    {
        $this->config->starting_day = 0;

        return $this;
    }

    /**
     * Changes the weekly start date to Monday.
     */
    public function useMondayStartingDate(): static
    {
        $this->config->starting_day = 1;

        return $this;
    }

    /**
     * The events array.
     *
     * @var array<Event>
     */
    private array $events = [];

    /**
     * Add an event to the current calendar instantiation.
     *
     * @param string|DateTimeInterface $start the start date in Y-m-d format
     * @param string|DateTimeInterface $end the end date in Y-m-d format
     * @param string $summary the summary string of the event
     * @param bool $mask the masking class
     * @param string|list<string> $classes (optional) A list of classes to use for the event
     * @param string|list<string> $box_classes (optional) A list of classes to apply to the event summary box
     */
    public function addEvent(
        string|DateTimeInterface $start,
        string|DateTimeInterface $end,
        string $summary = '',
        bool $mask = false,
        string|array $classes = [],
        string|array $box_classes = [],
    ): static {
        $this->events[] = new Event(
            Carbon::parse($start),
            Carbon::parse($end),
            $summary,
            $mask,
            $classes,
            $box_classes
        );

        return $this;
    }

    /**
     * Add an array of events using $this->addEvent();.
     *
     * Each array element must have the following:
     *     'start'  =>   start date in Y-m-d format.
     *     'end'    =>   end date in Y-m-d format.
     *     (optional) 'mask' => a masking class name.
     *     (optional) 'classes' => custom classes to include.
     *
     * @param array<int, array{start: (string | DateTimeInterface), end: (string | DateTimeInterface), summary?: string, classes?: (string | list<string>), mask?: bool, event_box_classes?: (string | list<string>)}> $events the events array
     */
    public function addEvents(array $events): static
    {
        foreach ($events as $event) {
            $classes = $event['classes'] ?? '';
            $mask = (bool) ($event['mask'] ?? false);
            $summary = $event['summary'] ?? '';
            $box_classes = $event['event_box_classes'] ?? '';
            $this->addEvent($event['start'], $event['end'], $summary, $mask, $classes, $box_classes);
        }

        return $this;
    }

    /**
     * Remove all events tied to this calendar.
     */
    public function clearEvents(): static
    {
        $this->events = [];

        return $this;
    }

    /**
     * Use Month View.
     */
    public function useMonthView(): static
    {
        $this->config->type = 'month';

        return $this;
    }

    /**
     * Use Week View.
     */
    public function useWeekView(): static
    {
        $this->config->type = 'week';

        return $this;
    }

    /**
     * Add any custom table classes that should be injected into the calendar table header.
     *
     * This can be a space separated list, or an array of classes.
     *
     * @param string|list<string> $classes
     */
    public function addTableClasses(string|array $classes): static
    {
        $classes = is_array($classes) ? implode(' ', $classes) : $classes;

        $this->config->table_classes = $classes;

        return $this;
    }

    /**
     * Find an event from the internal pool.
     *
     * @return array<Event> either an array of events or false
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * Returns the calendar as a month view.
     */
    public function asMonthView(DateTimeInterface|string|null $startDate = null, string $color = ''): string
    {
        return (new Month($this->config, $this))->render($startDate, $color);
    }

    /**
     * Returns the calendar as a month view.
     */
    public function asWeekView(DateTimeInterface|string|null $startDate = null, string $color = ''): string
    {
        return (new Week($this->config, $this))->render($startDate, $color);
    }

    /**
     * Draw the calendar and return HTML output.
     *
     * @param string|DateTimeInterface|null $date the date of this calendar
     *
     * @return string The calendar
     */
    public function draw(DateTimeInterface|string|null $date = null, string $color = ''): string
    {
        return 'week' === $this->config->type ? $this->asWeekView($date, $color) : $this->asMonthView($date, $color);
    }

    /**
     * Returns, or prints, the default stylesheet.
     */
    public function stylesheet(bool $print = true): ?string
    {
        $styles = '<style>.weekly-calendar{min-width:850px;}.calendar{background:#2ca8c2;color:#fff;width:100%;font-family:Oxygen;table-layout:fixed}.calendar.purple{background:#913ccd}.calendar.pink{background:#f15f74}.calendar.orange{background:#f76d3c}.calendar.yellow{background:#f7d842}.calendar.green{background:#98cb4a}.calendar.grey{background:#839098}.calendar.blue{background:#5481e6}.calendar-title th{font-size:22px;font-weight:700;padding:20px;text-align:center;text-transform:uppercase;background:rgba(0,0,0,.05)}.calendar-header th{padding:10px;text-align:center;background:rgba(0,0,0,.1)}.calendar tbody tr td{text-align:center;vertical-align:top;width:14.28%}.calendar tbody tr td.pad{background:rgba(255,255,255,.1)}.calendar tbody tr td.day div:first-child{padding:4px;line-height:17px;height:25px}.calendar tbody tr td.day div:last-child{font-size:10px;padding:4px;min-height:25px}.calendar tbody tr td.today{background:rgba(0,0,0,.25)}.calendar tbody tr td.mask,.calendar tbody tr td.mask-end,.calendar tbody tr td.mask-start{background:#c23b22}.calendar .cal-weekview-time{padding:4px 2px 2px 4px;}.calendar .cal-weekview-time > div{background:rgba(0,0,0,0.03);padding:10px;min-height:50px;}.calendar .cal-weekview-event.mask-start,.calendar .cal-weekview-event.mask,.calendar .cal-weekview-event.mask-end{background:#C23B22;margin-bottom:3px;padding:5px;}.calendar .cal-weekview-time-th{background:rgba(0,0,0,.1);}.calendar .cal-weekview-time-th > div{padding:10px;min-height:50px;}.calendar .event-summary-row{display:block;}</style>';
        $styles .= '<style>@media screen and (max-width:768px){#weekly-calendar-container{display: block;overflow-x: scroll;overflow-y: hidden;white-space: nowrap;}}</style>';

        if ($print) {
            echo $styles;

            return null;
        }

        return $styles;
    }

    /**
     * Shortcut helper to print the calendar output.
     */
    public function display(string|DateTimeInterface|null $date = null, string $color = ''): void
    {
        echo $this->stylesheet();
        echo $this->draw($date, $color);
    }
}
