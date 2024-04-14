<?php

function format_time_ago($date): string
{
    $now = new DateTime();
    $date = new DateTime($date);
    $diff = $now->diff($date);

    foreach(['y', 'm', 'd', 'h', 'i'] as $timeUnit) {
        $quantity = Getter::get($diff, $timeUnit);
        if ($quantity > 0) {
            $grammarNumber = $quantity > 1 ? "plural" : "singular";
            return _("date_interval_" . $timeUnit . "_" . $grammarNumber, $quantity);
        }
    }

    return _("date_interval_seconds_ago");
}