<?php

/**
 * Model factory for Activity model
 */

$factory->define(App\Models\Eloquent\Activity\Activity::class, function (Faker\Generator $faker) {

    return [
        'title' => 'Event title',
        'start_time' => '2015-06-01T12:00:00+00:00',
        'end_time' => '2015-06-01T14:00:00+00:00',
        'description' => 'description',
        'recurring' => 'no',
        'privacy' => 'open',
        'max_participants' => 100,
        'lat' => 88.123456, // (+-90)
        'long' => -111.123456, //(+-180)
    ];
});
