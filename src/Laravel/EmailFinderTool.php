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
- **Output:** Pre-built search URLs for Google, Bing, and DuckDuckGo
- Each query uses a different strategy — direct email search, site-specific search, LinkedIn, contact pages, etc.
- Click any link to open the search in a new tab and scan results for the email

### Query strategies

- Direct email search: `"John Smith" email "Acme Inc"`
- Company domain search: `John Smith site:acme.com`
- LinkedIn + company: `John Smith site:linkedin.com Acme`
- Contact page search: `"John Smith" contact Acme`
- Email format hints: `John Smith Acme email format`

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
