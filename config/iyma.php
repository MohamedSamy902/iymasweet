<?php

return [

    'theme_defaults' => [
        /* Semantic (dashboard color pickers) — Sweet Boutique defaults from logo */
        '--color-primary' => '#C9477A',
        '--color-primary-hover' => '#A93566',
        '--color-primary-dark' => '#A93566',
        '--color-primary-soft' => '#FFE8EF',
        '--color-denim' => '#6C93BE',
        '--color-denim-soft' => '#DCE8F4',
        '--color-brown' => '#6B463C',
        '--color-cream' => '#FFFAF6',
        '--color-surface' => '#FFFFFF',
        '--color-surface-tint' => '#FFF1F5',
        '--color-bg' => '#FFFAF6',
        '--color-text' => '#3B2420',
        '--color-text-muted' => '#9A7B73',
        '--color-border' => '#F1E8E5',
        '--color-success' => '#1F7A52',
        '--color-warning' => '#9A6A12',
        '--color-danger' => '#B83A3A',
        '--color-info' => '#2B6CA3',
        '--color-coral' => '#FF9E8F',
        '--color-yellow' => '#FFD36E',
        '--color-sun' => '#FFD36E',
        '--color-mint' => '#8EDAB5',
        '--color-sky' => '#8CC8F2',
        '--color-lilac' => '#BDA6EA',
    ],

    'defaults' => [
        'guest_checkout' => true,
        'payment_timeout_hours' => 6,
        'reviews_manual_approval' => true,
        'sold_out_at_end' => true,
        'deposit_for_preorder' => false,
        'english_enabled' => true,
        'theme_mode' => 'light',
    ],

];
