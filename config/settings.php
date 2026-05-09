<?php

$json = file_get_contents(__DIR__ . '/settings.json');

return json_decode($json, true) ?? [];
