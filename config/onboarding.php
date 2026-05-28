<?php

/*
|--------------------------------------------------------------------------
| Client Onboarding Templates
|--------------------------------------------------------------------------
| Each template = an array of task definitions that get auto-created when
| a project is created (or when the user clicks "Apply onboarding tasks").
| Keys map to the project's `description` (service type) so the right
| checklist is used for the right service.
|
| Each task: title, description (optional), priority, offset_days from
| the project's start_date, ai_tag (purely cosmetic).
*/

return [

    'Web Development' => [
        ['title' => '📞 Kickoff call with client',                                  'priority' => 'high',     'offset_days' => 1,  'description' => 'Schedule and run intro call. Capture goals, timeline, success criteria.'],
        ['title' => '🔑 Collect site credentials (live + staging)',                'priority' => 'high',     'offset_days' => 2,  'description' => 'Get cPanel / WP-admin / DNS access. Store in the project\'s Site Credentials card.'],
        ['title' => '🎨 Receive brand assets (logo, colours, fonts)',              'priority' => 'medium',   'offset_days' => 3,  'description' => 'Get high-res logo, brand guidelines if any, brand colour palette.'],
        ['title' => '🏗 Set up staging environment',                                'priority' => 'high',     'offset_days' => 4,  'description' => 'Spin up Cloudways/wp-engine staging instance. Verify SSL.'],
        ['title' => '📐 Design wireframes / page architecture',                    'priority' => 'high',     'offset_days' => 7,  'description' => 'Sitemap + key page wireframes for client approval.'],
        ['title' => '✅ Get design approval',                                       'priority' => 'critical', 'offset_days' => 10, 'description' => 'Walk client through wireframes, capture written approval.'],
        ['title' => '💻 Build pages on staging',                                    'priority' => 'high',     'offset_days' => 20, 'description' => 'Page-by-page implementation against approved designs.'],
        ['title' => '🐞 Install the Gesture Bug Widget on staging',                'priority' => 'medium',   'offset_days' => 22, 'description' => 'Generate widget key + drop snippet so the client can leave feedback during review.'],
        ['title' => '👀 Client review round',                                       'priority' => 'high',     'offset_days' => 28, 'description' => 'Demo on staging, collect feedback via the widget, list change requests.'],
        ['title' => '🚀 Final QA + go-live',                                        'priority' => 'critical', 'offset_days' => 35, 'description' => 'Cross-browser test, mobile test, lighthouse audit, DNS cutover.'],
        ['title' => '📈 Post-launch handover + analytics setup',                    'priority' => 'medium',   'offset_days' => 38, 'description' => 'GA4, Search Console, training video, support handover.'],
    ],

    'SEO' => [
        ['title' => '📞 Kickoff call with client',                                  'priority' => 'high',     'offset_days' => 1,  'description' => 'Discuss target keywords, geos, conversion goals.'],
        ['title' => '🔑 Get GSC + GA4 + GTM access',                                'priority' => 'high',     'offset_days' => 2,  'description' => 'Request property access. Verify in our agency account.'],
        ['title' => '📊 Baseline audit (technical, on-page, content)',             'priority' => 'high',     'offset_days' => 5,  'description' => 'Crawl with Screaming Frog. Document Core Web Vitals, indexation, hreflang, canonical issues.'],
        ['title' => '🔍 Keyword research + topic clusters',                        'priority' => 'high',     'offset_days' => 7,  'description' => 'Use Ahrefs/Semrush. Map keywords to existing or new pages.'],
        ['title' => '🧱 Technical SEO fixes (round 1)',                            'priority' => 'high',     'offset_days' => 14, 'description' => 'Schema, sitemap, robots.txt, redirects, page speed quick-wins.'],
        ['title' => '✍️ Content briefs for top 5 pages',                            'priority' => 'high',     'offset_days' => 21, 'description' => 'SERP analysis + content briefs for writers.'],
        ['title' => '📝 Publish optimized content',                                'priority' => 'medium',   'offset_days' => 35, 'description' => 'Write, review, optimize, publish. Internal linking.'],
        ['title' => '🔗 Backlink outreach (round 1)',                              'priority' => 'medium',   'offset_days' => 42, 'description' => 'Build 3-5 high-quality links from relevant Australian sites.'],
        ['title' => '📈 Month-1 reporting',                                         'priority' => 'medium',   'offset_days' => 30, 'description' => 'Rankings, traffic, indexation changes vs baseline.'],
    ],

    'Google Ads' => [
        ['title' => '📞 Kickoff call with client',                                  'priority' => 'high',     'offset_days' => 1,  'description' => 'Goals, target CPL, monthly budget, geographies, current funnel.'],
        ['title' => '🔑 Google Ads + GA4 + Tag Manager access',                    'priority' => 'high',     'offset_days' => 2,  'description' => 'Request manager-link to client account. Verify in MCC.'],
        ['title' => '🎯 Conversion tracking setup',                                'priority' => 'critical', 'offset_days' => 3,  'description' => 'GTM + GA4 events + Ads conversions. Test with Tag Assistant.'],
        ['title' => '🔍 Keyword research + negatives',                             'priority' => 'high',     'offset_days' => 4,  'description' => 'Build keyword themes (exact + phrase), seed negatives list.'],
        ['title' => '🧱 Account structure (campaigns + ad groups)',                'priority' => 'high',     'offset_days' => 5,  'description' => 'Search + PMax. Geo-target Australia. Set bid strategy.'],
        ['title' => '✍️ Ad copy + assets',                                          'priority' => 'high',     'offset_days' => 6,  'description' => 'Headlines, descriptions, sitelinks, callouts, images.'],
        ['title' => '📐 Landing page review',                                       'priority' => 'high',     'offset_days' => 7,  'description' => 'Check load speed, form simplicity, mobile UX, trust signals.'],
        ['title' => '🚀 Launch campaigns + monitor day 1-3',                       'priority' => 'critical', 'offset_days' => 8,  'description' => 'Watch for early CPC spikes or quality-score issues.'],
        ['title' => '📊 Week-1 optimization',                                       'priority' => 'high',     'offset_days' => 14, 'description' => 'Add negatives, adjust bids, pause underperforming keywords.'],
        ['title' => '📈 Month-1 report + strategy review',                          'priority' => 'medium',   'offset_days' => 30, 'description' => 'Conversions, CPL, ROAS. Decide what to scale.'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stage routing
    |--------------------------------------------------------------------------
    | All new onboarding tasks land in this stage (looked up by name within
    | the project's workspace; falls back to is_default stage).
    */
    'initial_stage' => 'To Do',

];
