<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'News pages',
    'description' => 'Create news articles as "normal" pages in TYPO3. Easy to install and ready to use straight away.',
    'category' => 'plugin',
    'author' => 'Raphael Thanner',
    'author_email' => 'r.thanner@zeroseven.de',
    'author_company' => 'zeroseven design studios GmbH',
    'state' => 'stable',
    'clearCacheOnLoad' => 1,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.4.99',
            'pagebased' => '1.2.0-1.99.99'
        ]
    ]
];
