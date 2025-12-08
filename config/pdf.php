<?php

return [
    'mode'                 => 'utf-8',
    'format'               => 'A4',
    'default_font_size'    => '14',
    'margin_left'          => 10,
    'margin_right'         => 10,
    'margin_top'           => 10,
    'margin_bottom'        => 10,
    'orientation'          => 'P',
    'title'                => 'Invoice PDF',
    'display_mode'         => 'fullpage',
    'tempDir'              => base_path('storage/app'),

    'custom_font_dir' => resource_path('fonts/'), // مسار الخطوط
    'custom_font_data' => [
        'amiri' => [
            'R' => 'Amiri-Regular.ttf',
            'B' => 'Amiri-Bold.ttf',
            'useOTL' => 0xFF,
            'useKashida' => 75,
        ],
    ],

    'default_font' => 'amiri', // هنا الفونت العربي
];