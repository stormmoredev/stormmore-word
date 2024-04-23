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

function profile_photo($username, $photo): void
{
    if ($photo != null) {
        ?>
            <img class="h-10 w-10 rounded-md" src="/media/profile/<?php echo $photo ?>" />
        <?php
    } else {
        $initials = $username[0];
        if (str_contains($username, ' ')) {
            $p = explode(' ', $username);
            $initials = $p[0][0] . $p[1][0];
        }
        ?>
        <div class="inline-flex h-10 w-10 items-center justify-center rounded-md bg-gray-500">
            <span class="text-xl font-medium leading-none text-white"><?php echo $initials ?></span>
        </div>
        <?php
    }
}