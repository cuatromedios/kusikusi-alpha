<?php

return [
    'presets' => [
        'preview' => [
            'quality' => 95,
            'width' => 1200,
            'height' =>  1200,
            'background' => '#ffffff',
            'alignment' => 'center',
            'scale' => 'contain',
            'format' => 'jpg',
            'effects' => []
        ],
        'thumb' => [
            'quality' => 80,
            'width' => 128,
            'height' =>  128,
            'background' => 'crop',
            'alignment' => 'center',
            'scale' => 'cover',
            'format' => 'jpg',
            'effects' => []
        ]
    ]
];
