<?php

declare(strict_types=1);

/**
 * Editorial content for the website. Change copy here without touching page
 * templates or database code.
 */
$services = [
    [
        'name' => 'Market Entry',
        'short' => 'Evidence-led entry plans for new products, sectors and geographies.',
        'icon' => 'pin',
    ],
    [
        'name' => 'Growth Strategy',
        'short' => 'A clear path from current performance to durable commercial growth.',
        'icon' => 'spark',
    ],
    [
        'name' => 'Commercial Advisory',
        'short' => 'Pricing, proposition and channel decisions grounded in market reality.',
        'icon' => 'dashboard',
    ],
    [
        'name' => 'Partnerships',
        'short' => 'The right alliances, distribution relationships and joint ventures.',
        'icon' => 'users',
    ],
    [
        'name' => 'Operating Models',
        'short' => 'Structures and ways of working that turn strategy into execution.',
        'icon' => 'gear',
    ],
];

$sectors = [
    [
        'slug' => 'strategy',
        'number' => '01',
        'name' => 'Strategy',
        'short' => 'Clear strategic direction for complex markets — from market entry and growth to partnerships and execution.',
        'icon' => 'diamond',
        'lead' => 'Clarity before action.',
        'description' => [
            'White Sea Star works with founders, investors and leadership teams to turn complex market questions into clear, executable strategy. Our work spans market entry, growth planning, commercial partnerships and operating models.',
            'Every engagement starts with listening and evidence. We map the market, test the assumptions and build a practical path forward — then stay close enough to help our partners execute with confidence.',
        ],
        'bullets' => [
            'Market entry and expansion strategy',
            'Growth and commercial planning',
            'Partnership and distribution strategy',
            'Operating model and execution support',
        ],
    ],
];

$markets = [
    [
        'name' => 'UAE Domestic Market',
        'code' => 'AE',
        'description' => 'Our home market and operational base, with direct access to decision-makers in Dubai.',
    ],
    [
        'name' => 'GCC Region',
        'code' => 'GCC',
        'description' => 'Established client and partner relationships across the Gulf Cooperation Council states.',
    ],
    [
        'name' => 'Africa',
        'code' => 'AF',
        'description' => 'Growing client and partner networks across key African markets.',
    ],
    [
        'name' => 'South Asia',
        'code' => 'SA',
        'description' => 'Commercial insight and connections across the Indian subcontinent.',
    ],
    [
        'name' => 'Emerging Global Markets',
        'code' => 'GL',
        'description' => 'Selective work in high-potential international markets.',
    ],
];

return [
    'company' => [
        'name' => 'White Sea Star General Trading L.L.C',
        'short_name' => 'White Sea Star',
        'tagline' => 'Built on trust. Driven by strategy.',
        'established' => '2009',
        'years' => '17+',
        'email' => 'info@whiteseastar.com',
        'phone' => '+971 4 264 9406',
        'phone_href' => '+97142649406',
        'address' => 'Al Reem Tower, Office 1404, Al Maktoum Road, Riggat Al Buteen, Deira, Dubai, UAE',
        'address_short' => 'Al Reem Tower, Office 1404, Deira, Dubai',
        'hours' => 'Sunday – Thursday · 9:00 – 18:00 GST',
        'maps_url' => 'https://maps.google.com/?q=Al+Reem+Tower+Al+Maktoum+Road+Deira+Dubai',
    ],

    'services' => $services,

    'sectors' => $sectors,

    'values' => [
        ['name' => 'Integrity', 'text' => 'Straight counsel and transparent advice, in every engagement.'],
        ['name' => 'Clarity', 'text' => 'Complex questions translated into a direction people can act on.'],
        ['name' => 'Partnership', 'text' => 'We work alongside our clients, not at a distance.'],
        ['name' => 'Discipline', 'text' => 'Evidence-led thinking, practical plans and accountable execution.'],
        ['name' => 'Growth', 'text' => 'A focus on durable commercial progress, not short-term noise.'],
    ],

    'markets' => $markets,

    'advantages' => [
        [
            'title' => 'Seventeen years of perspective',
            'text' => 'Operating since 2009, with a long view of what makes commercial relationships endure.',
        ],
        [
            'title' => 'Cross-sector insight',
            'text' => 'Experience across multiple industries, brought together in one clear strategic view.',
        ],
        [
            'title' => 'Market intelligence',
            'text' => 'Direct relationships and on-the-ground research across Asia, Europe and the Middle East.',
        ],
        [
            'title' => 'Execution discipline',
            'text' => 'Strategy only matters when it ships. We stay close through implementation.',
        ],
        [
            'title' => 'Regional fluency',
            'text' => 'A Dubai base with practical understanding of UAE regulation and cross-border business.',
        ],
    ],

    'stats' => [
        ['value' => '2009', 'label' => 'Established in Dubai'],
        ['value' => '01', 'label' => 'Strategy practice'],
        ['value' => '17+', 'label' => 'Years of experience'],
        ['value' => '05', 'label' => 'Markets served'],
    ],

    'markets_meta' => [
        'heading' => 'From Dubai, to the world.',
        'text' => 'Our location at the crossroads of Asia, Africa and Europe gives our clients a practical vantage point on markets, partners and opportunity.',
    ],

    'resource_categories' => [
        'Company Documents',
        'Insights',
        'Reports',
        'Compliance',
        'Partner Notices',
    ],
];
