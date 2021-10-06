<?php

/**
 * @file
 * Lists available colors and color schemes for the VaniPro theme.
 */

$info = [
  // Available colors and color labels used in theme.
  'fields' => [
    'headertop' => t('Header Top Blocks background'),
    'top' => t('Header and content background'),
    'bottom' => t('Footer background'),
    'bg' => t('Body background'),
    // 'sidebar' => t('Sidebar background'),
    // 'footer' => t('Footer background'),
    // 'titleslogan' => t('Title and slogan'),
    'text' => t('Text color'),
    'heading' => t('Heading color'),
    'border' => t('Border color'),
    'bordercontent' => t('Border color in content'),
    'footercolor' => t('Footer Text Color'),
    'footerborder' => t('Footer Border Color'),
    'themeone' => t('Theme Color First'),
    'themetwo' => t('Theme Color Second'),
    'circleone' => t('Header Circle One Color'),
    'circletwo' => t('Header Circle Two Color'),
    'circlethree' => t('Header Circle Three Color')    
  ],
  // Pre-defined color schemes.
  'schemes' => [
    'default' => [
      'title' => t('VaniPro Default'),
      'colors' => [
        'headertop' => '#f2f4fe',
        'top' => '#f2f4ff',
        'bottom' => '#232733',
        'bg' => '#ffffff',
        // 'sidebar' => '#f6f6f2',
        // 'sidebarborders' => '#1e222a',
        // 'footer' => '#1e222a',
        // 'titleslogan' => '#fffeff',
        'text' => '#2a2a2a',
        'heading' => '#1e2127',
        'border' => '#e9ebf5',
        'bordercontent' => '#ced1e4',
        'footercolor' => '#919296',
        'footerborder' => '#373b48',
        'themeone' => '#ff6347',
        'themetwo' => '#302e44',
        'circleone' => '#f3e7f0',
        'circletwo' => '#e0e2f6',
        'circlethree' => '#f9b6b4'
      ]
    ]
  ],

  // CSS files (excluding @import) to rewrite with new color scheme.
  'css' => [
    'css/colors.css',
  ],

  // Files to copy.
  'copy' => [
    'logo.svg',
  ],

  // Gradient definitions.
  'gradients' => [
    [
      // (x, y, width, height).
      'dimension' => [0, 0, 0, 0],
      // Direction of gradient ('vertical' or 'horizontal').
      'direction' => 'vertical',
      // Keys of colors to use for the gradient.
      'colors' => ['top', 'bottom'],
    ],
  ],

  // Preview files.
  'preview_library' => 'vanipro/color.preview',
  'preview_html' => 'color/preview.html',

  // Attachments.
  '#attached' => [
    'drupalSettings' => [
      'color' => [
        // Put the logo path into JavaScript for the live preview.
        'logo' => theme_get_setting('logo.url', 'vanipro'),
      ],
    ],
  ],
];
