<?php

return [
    'api_url' => env('PRINTING_API_URL', 'http://localhost:3001'),
    'default_printer' => env('PRINTING_DEFAULT_PRINTER', 'XPrinter XP-V320M'),
    'ticket_width_mm' => env('PRINTING_TICKET_WIDTH', 80)
];
