<?php

declare(strict_types=1);

namespace URLCV\EmailFinder\Laravel;

use App\Tools\Contracts\ToolInterface;

/**
 * Laravel tool adapter for the Email Finder package.
 *
 * Generates search engine query URLs to help find professional email addresses
 * by name and company. Frontend-only — no server round-trip.
 */
class EmailFinderTool implements ToolInterface
{
    public function slug(): string
    {
        return 'email-finder';
    }

    public function name(): string
    {
        return 'Email Finder';
    }

    public function summary(): string
    {
        return 'Generate search engine queries to find someone\'s email address by name and company.';
    }

    public function descriptionMd(): ?string
    {
        return <<<'MD'
## Email Finder

Enter a person's name and company, and this tool generates multiple search engine queries designed to surface their professional email address.

### How it works

- **Input:** Full name and company name
- **Output:** Pre-built search URLs for Google, Bing, DuckDuckGo, and Yandex
- Each query uses a different strategy — direct `@domain` search, `site:` + `inurl:` on the company domain, PDFs, GitHub/GitLab, press wires, etc.
- Click any link to open the search in a new tab and scan results for the email

### Query strategies (examples)

- Direct: `"Jane Doe" "@acme.com"`
- Company site paths: `site:acme.com "Jane Doe" (inurl:team OR inurl:contact …)`
- Documents: `filetype:pdf`, Word files, SlideShare
- Developers: GitHub / GitLab / Stack Overflow with the company domain
- Likely address formats (when domain is known) to verify in search

### Use cases for recruiters

- Finding candidate contact details when you only have name and employer
- Sourcing outreach when LinkedIn InMail credits are exhausted
- Verifying email addresses before cold outreach
MD;
    }

    public function categories(): array
    {
        return ['sourcing'];
    }

    public function tags(): array
    {
        return ['email', 'sourcing', 'contact', 'recruitment', 'search'];
    }

    public function inputSchema(): array
    {
        return [
            'name' => [
                'type'        => 'text',
                'label'       => 'Full name',
                'placeholder' => 'e.g. John Smith',
                'required'    => true,
                'max_length'  => 200,
                'help'        => 'The person\'s full name.',
            ],
            'company' => [
                'type'        => 'text',
                'label'       => 'Company',
                'placeholder' => 'e.g. Acme Inc',
                'required'    => true,
                'max_length'  => 200,
                'help'        => 'Company or organisation name.',
            ],
        ];
    }

    public function run(array $input): array
    {
        return [];
    }

    public function mode(): string
    {
        return 'frontend';
    }

    public function isAsync(): bool
    {
        return false;
    }

    public function isPublic(): bool
    {
        return true;
    }

    public function frontendView(): ?string
    {
        return 'email-finder::email-finder';
    }

    public function rateLimitPerMinute(): int
    {
        return 30;
    }

    public function cacheTtlSeconds(): int
    {
        return 0;
    }

    public function sortWeight(): int
    {
        return 90;
    }
}
