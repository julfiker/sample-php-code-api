<?php

return [
    'page_size' => 20,
    'date_time_format' => 'Y-m-d\TH:i:sP', // can't use format 'c' for ISO 8601 format as date_parse_from_format() can't parse 'c'
];