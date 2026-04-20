<?php

/**
 * Demo posts (replace with a database when you are ready).
 * Keys are URL slugs; order is preserved for listing on the home page.
 *
 * @var array<string, array<string, mixed>>
 */
$defaultAuthor = 'Alex Rivera';

return [
  'shipping-a-smaller-first-version' => [
    'slug' => 'shipping-a-smaller-first-version',
    'title' => 'Shipping a smaller first version',
    'author' => $defaultAuthor,
    'dateDisplay' => 'Apr 12, 2026',
    'dateIso' => '2026-04-12',
    'tag' => 'Product',
    'excerpt' => 'Why constraints beat feature lists when you are still finding your voice.',
    'readingMinutes' => 6,
    'content' => [
      ['type' => 'p', 'text' => 'The hardest part of a new project is not choosing a stack—it is deciding what “done” means before you have users. A smaller first version gives you something real to react to: feedback, friction, and the shape of what comes next.'],
      ['type' => 'p', 'text' => 'Scope creep is rarely one big decision. It is a dozen small yeses: one more setting, one more integration, one more polish pass before launch. Each one feels reasonable alone; together they push the release line past the horizon.'],
      ['type' => 'h2', 'text' => 'Constraints as a feature'],
      ['type' => 'p', 'text' => 'Pick a narrow audience, a single workflow, or one pain point. Build only what that slice needs. You can always widen the lens later—what you cannot do is recover months spent building for hypothetical people.'],
      ['type' => 'blockquote', 'text' => 'Ship something embarrassingly small, then improve it in public.'],
      ['type' => 'p', 'text' => 'When the first version is intentionally tight, your roadmap writes itself from real usage instead of imagined requirements. That is how a blog, a product, or an API grows without collapsing under its own ambition.'],
    ],
  ],
  'notes-on-readable-typography' => [
    'slug' => 'notes-on-readable-typography',
    'title' => 'Notes on readable typography on the web',
    'author' => $defaultAuthor,
    'dateDisplay' => 'Apr 2, 2026',
    'dateIso' => '2026-04-02',
    'tag' => 'Design',
    'excerpt' => 'Line length, font choice, and contrast—small details readers actually feel.',
    'readingMinutes' => 8,
    'content' => [
      ['type' => 'p', 'text' => 'Readers rarely comment on typography when it is working. They notice when lines are too long, when type feels cramped, or when contrast fails in daylight. Good typography disappears into the experience and lets the words do the work.'],
      ['type' => 'h2', 'text' => 'Measure and rhythm'],
      ['type' => 'p', 'text' => 'A comfortable line length for long-form reading usually falls between 60 and 80 characters. Combined with generous line height and clear hierarchy between headings and body text, the page invites scrolling instead of fatigue.'],
      ['type' => 'p', 'text' => 'Pair a humanist serif for headlines with a neutral sans for UI chrome, keep font weights disciplined, and reserve emphasis for moments that deserve a pause.'],
      ['type' => 'blockquote', 'text' => 'Design the page so the eye can rest—starting with type.'],
      ['type' => 'ul', 'items' => [
        'Keep body text large enough for mobile without zooming.',
        'Use tabular numbers for dates and metadata.',
        'Let paragraphs breathe; walls of text feel heavier than they are.',
      ]],
      ['type' => 'p', 'text' => 'These choices compound. A blog that respects reading time earns trust before a single sentence lands.'],
    ],
  ],
  'php-without-a-framework' => [
    'slug' => 'php-without-a-framework',
    'title' => 'What I learned wiring PHP without a framework',
    'author' => $defaultAuthor,
    'dateDisplay' => 'Mar 18, 2026',
    'dateIso' => '2026-03-18',
    'tag' => 'Engineering',
    'excerpt' => 'Plain includes, a router, and a path to grow without rewriting everything.',
    'readingMinutes' => 7,
    'content' => [
      ['type' => 'p', 'text' => 'Frameworks save time until they do not—when you fight defaults, upgrade churn, or abstractions you only half understand. A thin PHP layer with explicit routing and views keeps the mental model small: a request hits a route, a controller prepares data, a template renders HTML.'],
      ['type' => 'h2', 'text' => 'What you keep'],
      ['type' => 'p', 'text' => 'You still benefit from Composer when you need packages, from PDO for databases, and from mature hosting. What you skip is the ceremony of bootstrapping a full stack when you only needed a blog-shaped application.'],
      ['type' => 'p', 'text' => 'The trade-off is discipline. Without conventions enforced by a framework, structure has to be intentional: where controllers live, how views receive data, how configuration is loaded. Clear folders beat clever magic.'],
      ['type' => 'blockquote', 'text' => 'Own your architecture early; you can borrow framework pieces later without importing the whole kitchen.'],
      ['type' => 'p', 'text' => 'This project is one possible shape—router, templates, optional MySQL—enough to publish today and refactor tomorrow when requirements clarify.'],
    ],
  ],
];
