{{--
  Email Finder — fully client-side Alpine.js tool.
  Generates search engine query URLs to find professional email addresses by name and company.
--}}
<div x-data="emailFinder()" class="space-y-6">

    {{-- Mode toggle --}}
    <div class="flex gap-1 p-1 bg-gray-100 rounded-xl w-full sm:w-auto">
        <button
            @click="mode = 'mine'"
            :class="mode === 'mine' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'"
            class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-sm font-medium transition-all"
        >
            Find my email
        </button>
        <button
            @click="mode = 'other'"
            :class="mode === 'other' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'"
            class="flex-1 sm:flex-none px-4 py-2 rounded-lg text-sm font-medium transition-all"
        >
            Find someone else's
        </button>
    </div>

    {{-- Input form --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="ef-name" class="block text-sm font-medium text-gray-700 mb-1">Full name</label>
            <input
                id="ef-name"
                type="text"
                x-model="name"
                @input="buildQueries()"
                :placeholder="mode === 'mine' ? 'e.g. Jane or Jane Doe' : 'e.g. John Smith'"
                class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent"
            />
        </div>
        <div>
            <label for="ef-company" class="block text-sm font-medium text-gray-700 mb-1">
                Company
                <span x-show="mode === 'mine'" class="text-gray-400 font-normal">(optional)</span>
            </label>
            <input
                id="ef-company"
                type="text"
                x-model="company"
                @input="buildQueries()"
                :placeholder="mode === 'mine' ? 'e.g. Spotify' : 'e.g. Acme Inc'"
                class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent"
            />
        </div>
    </div>

    {{-- Domain — with clear helper for casual users --}}
    <div>
        <label for="ef-domain" class="block text-sm font-medium text-gray-700 mb-1">
            Company website domain
            <span x-show="mode === 'other'" class="text-gray-400 font-normal">(optional — improves results)</span>
        </label>
        <input
            id="ef-domain"
            type="text"
            x-model="domain"
            @input="buildQueries()"
            :placeholder="mode === 'mine' ? 'e.g. spotify.com — needed for email format suggestions' : 'e.g. acme.com'"
            class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent"
        />
        <p class="mt-1.5 text-xs text-gray-500">
            The part after <span class="font-mono">www.</span> in the company's website. From <span class="font-mono">https://www.spotify.com/about</span> → use <span class="font-mono">spotify.com</span>
        </p>
    </div>

    {{-- Search engine selector --}}
    <div>
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Open search in</p>
        <div class="flex flex-wrap gap-2">
            <template x-for="engine in engines" :key="engine.id">
                <button
                    @click="selectedEngine = engine.id"
                    :class="selectedEngine === engine.id ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors"
                    x-text="engine.label"
                ></button>
            </template>
        </div>
    </div>

    {{-- Email format suggestions (when domain provided) — guess your own email --}}
    <template x-if="emailFormats.length > 0">
        <div class="space-y-4">
            <h3 class="text-sm font-semibold text-gray-700">Likely email formats</h3>
            <p class="text-sm text-gray-500" x-show="mode === 'mine'">Based on your name and domain. Search each to see if it appears on the web — if it does, that's your format.</p>
            <p class="text-sm text-gray-500" x-show="mode === 'other'">Common formats for this name at this domain. Search each to verify which one is used.</p>
            <div class="space-y-2">
                <template x-for="(fmt, i) in emailFormats" :key="i">
                    <div class="flex flex-col gap-2 p-3 bg-green-50 rounded-xl border border-green-200">
                        <p class="text-sm font-mono text-gray-900 font-medium" x-text="fmt.email"></p>
                        <div class="flex gap-2">
                            <a
                                :href="fmt.searchUrls[selectedEngine]"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                Search for this email
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            <button
                                @click="copyEmail(fmt.email)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                <span x-text="copiedFormat === fmt.email ? 'Copied!' : 'Copy'"></span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- Generated queries --}}
    <template x-if="queries.length > 0">
        <div class="space-y-4">
            <h3 class="text-sm font-semibold text-gray-700">Search queries</h3>
            <p class="text-sm text-gray-500" x-show="mode === 'mine'">Pages that might list your email — conference bios, PDFs, team pages. Try these if format suggestions don't appear.</p>
            <p class="text-sm text-gray-500" x-show="mode === 'other'">Find pages that might list their email — conference bios, PDFs, team pages, etc.</p>

            <div class="space-y-3">
                <template x-for="(q, i) in queries" :key="i">
                    <div class="flex flex-col gap-2 p-3 bg-gray-50 rounded-xl border border-gray-200">
                        <div>
                            <span class="text-xs font-medium text-gray-400" x-text="q.strategy"></span>
                            <p class="text-sm text-gray-800 font-mono break-words mt-0.5" x-text="q.query"></p>
                        </div>
                        <div class="flex gap-2">
                            <a
                                :href="q.urls[selectedEngine]"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                <span x-text="engineLabel(selectedEngine)"></span>
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                            </a>
                            <button
                                @click="copyUrl(q.urls[selectedEngine], i)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                <span x-text="copiedId === i ? 'Copied!' : 'Copy'"></span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    {{-- Empty state --}}
    <template x-if="queries.length === 0 && emailFormats.length === 0 && (name.trim() || company.trim() || domain.trim())">
        <div class="text-center py-6 text-amber-600 text-sm bg-amber-50 border border-amber-200 rounded-xl">
            <span x-show="mode === 'mine'">Add your name and domain (e.g. spotify.com) to get likely email formats.</span>
            <span x-show="mode === 'other'">Enter name and either company or domain to generate queries.</span>
        </div>
    </template>

    <template x-if="!name.trim() && !company.trim() && !domain.trim()">
        <div class="text-center py-8 text-gray-400 text-sm">
            <span x-show="mode === 'mine'">Enter your name and your company's domain to guess your work email format.</span>
            <span x-show="mode === 'other'">Enter a name and company or domain to find someone's email.</span>
        </div>
    </template>

    {{-- Tip --}}
    <div class="rounded-xl p-4 text-sm bg-blue-50 border border-blue-200 text-blue-800">
        <span class="font-semibold">Tip:</span>
        <span x-show="mode === 'mine'">Add your domain to get format suggestions (e.g. jane.doe@spotify.com). Search each to see if it appears anywhere. Try Bing or Yandex if Google shows nothing.</span>
        <span x-show="mode === 'other'">Add the domain for better results — you'll get likely email formats plus search queries. Try Bing or Yandex if Google limits results.</span>
        <span class="block mt-2 text-blue-900/90">This tool only opens web search — it can’t find addresses that aren’t on public pages (many work emails never appear in Google). Dedicated email APIs (e.g. enrichment tools) use separate databases, not the same as these queries.</span>
    </div>
</div>

@push('scripts')
<script>
function emailFinder() {
    const engines = {
        google: 'Google',
        bing: 'Bing',
        duckduckgo: 'DuckDuckGo',
        yandex: 'Yandex',
    };

    const baseUrls = {
        google: 'https://www.google.com/search?q=',
        bing: 'https://www.bing.com/search?q=',
        duckduckgo: 'https://duckduckgo.com/?q=',
        yandex: 'https://yandex.com/search/?text=',
    };

    return {
        mode: 'mine',
        name: '',
        company: '',
        domain: '',
        selectedEngine: 'google',
        engines: Object.entries(engines).map(([id, label]) => ({ id, label })),
        queries: [],
        emailFormats: [],
        copiedId: null,
        copiedFormat: null,
        copyTimeout: null,

        buildQueries() {
            const n = this.name.trim();
            const c = this.company.trim();
            const d = this.domain.trim().replace(/^https?:\/\//, '').replace(/\/.*$/, '');

            if (!n || (!c && !d)) {
                this.queries = [];
                this.emailFormats = [];
                return;
            }

            const enc = (q) => encodeURIComponent(q);
            const url = (q) => ({
                google: baseUrls.google + enc(q),
                bing: baseUrls.bing + enc(q),
                duckduckgo: baseUrls.duckduckgo + enc(q),
                yandex: baseUrls.yandex + enc(q),
            });

            const qs = [];

            if (d) {
                // DOMAIN PROVIDED — web search can only surface emails already on public pages (bios, PDFs, code, etc.)
                // #1: Exact name + @domain — strongest signal when quoted in pages
                qs.push({ strategy: 'Name + @domain (best)', query: `"${n}" "@${d}"`, urls: url(`"${n}" "@${d}"`) });

                // Company site: common URL paths where emails appear in HTML (mailto:) or text
                qs.push({ strategy: 'Company site + email', query: `site:${d} "${n}" email`, urls: url(`site:${d} "${n}" email`) });
                qs.push({
                    strategy: 'Team / people / contact pages',
                    query: `site:${d} "${n}" (inurl:team OR inurl:people OR inurl:contact OR inurl:about OR inurl:staff)`,
                    urls: url(`site:${d} "${n}" (inurl:team OR inurl:people OR inurl:contact OR inurl:about OR inurl:staff)`),
                });
                qs.push({
                    strategy: 'mailto: on company site',
                    query: `site:${d} ("${n}" mailto OR "${n}" "@${d}")`,
                    urls: url(`site:${d} ("${n}" mailto OR "${n}" "@${d}")`),
                });

                // PDFs: conference proceedings, whitepapers, speaker lists
                qs.push({ strategy: 'PDF documents', query: `filetype:pdf "${n}" "@${d}"`, urls: url(`filetype:pdf "${n}" "@${d}"`) });
                // Word/attachments from intranet leaks, event handouts
                qs.push({
                    strategy: 'Word / document attachments',
                    query: `(filetype:doc OR filetype:docx) "${n}" "${d}"`,
                    urls: url(`(filetype:doc OR filetype:docx) "${n}" "${d}"`),
                });

                // GitHub / GitLab: commit metadata & profiles sometimes list work email
                qs.push({ strategy: 'GitHub (developers)', query: `site:github.com "${n}" "@${d}"`, urls: url(`site:github.com "${n}" "@${d}"`) });
                qs.push({ strategy: 'GitLab (developers)', query: `site:gitlab.com "${n}" "@${d}"`, urls: url(`site:gitlab.com "${n}" "@${d}"`) });

                // Stack Overflow / Dev: company email in profile or conference talks
                qs.push({
                    strategy: 'Stack Overflow (developers)',
                    query: `site:stackoverflow.com "${n}" "${d}"`,
                    urls: url(`site:stackoverflow.com "${n}" "${d}"`),
                });

                // Conference / speaker bios (company name helps disambiguate common names)
                qs.push({
                    strategy: 'Conference / speaker',
                    query: `"${n}" ${c || d} (speaker OR keynote OR bio) (email OR contact OR mailto)`,
                    urls: url(`"${n}" ${c || d} (speaker OR keynote OR bio) (email OR contact OR mailto)`),
                });

                // PDF employee lists / rosters (narrower than "@domain" alone — less noise than old "pattern discovery")
                qs.push({
                    strategy: 'PDFs mentioning @domain',
                    query: `filetype:pdf "${n}" ("@${d}" OR "${d}")`,
                    urls: url(`filetype:pdf "${n}" ("@${d}" OR "${d}")`),
                });

                // SlideShare / SpeakerDeck: presenter contact on slides
                qs.push({
                    strategy: 'Presentations',
                    query: `(site:slideshare.net OR site:speakerdeck.com) "${n}" "@${d}"`,
                    urls: url(`(site:slideshare.net OR site:speakerdeck.com) "${n}" "@${d}"`),
                });

                // Press wires sometimes include media contacts with emails
                if (c) {
                    qs.push({
                        strategy: 'Press releases',
                        query: `"${n}" "${c}" (site:prnewswire.com OR site:businesswire.com OR site:globenewswire.com)`,
                        urls: url(`"${n}" "${c}" (site:prnewswire.com OR site:businesswire.com OR site:globenewswire.com)`),
                    });
                }

                // LinkedIn rarely shows raw email in snippets; still useful to confirm identity + role before guessing format
                qs.push({ strategy: 'LinkedIn (identity check)', query: `"${n}" "${d}" site:linkedin.com/in`, urls: url(`"${n}" "${d}" site:linkedin.com/in`) });

                // Exclude social for “email OR contact” broad search — FB/IG almost never have professional emails in snippets
                qs.push({
                    strategy: 'Broad search (no social)',
                    query: `"${n}" "${d}" (email OR contact OR mailto) -site:facebook.com -site:instagram.com -site:tiktok.com`,
                    urls: url(`"${n}" "${d}" (email OR contact OR mailto) -site:facebook.com -site:instagram.com -site:tiktok.com`),
                });
            } else {
                // NO DOMAIN — fallback queries (weaker but still useful)
                qs.push({ strategy: 'Name + company + email', query: `"${n}" "${c}" email`, urls: url(`"${n}" "${c}" email`) });
                qs.push({ strategy: 'Name + contact', query: `"${n}" ${c} (email OR contact)`, urls: url(`"${n}" ${c} (email OR contact)`) });
                qs.push({ strategy: 'LinkedIn profile', query: `"${n}" site:linkedin.com ${c}`, urls: url(`"${n}" site:linkedin.com ${c}`) });
                qs.push({ strategy: 'Conference/speaker', query: `"${n}" ${c} speaker email OR contact`, urls: url(`"${n}" ${c} speaker email OR contact`) });
                qs.push({ strategy: 'Testimonials/reviews', query: `"${n}" "${c}" (testimonial OR review) email`, urls: url(`"${n}" "${c}" (testimonial OR review) email`) });
                qs.push({ strategy: 'Excluding social', query: `"${n}" "${c}" (email OR contact) -site:linkedin.com -site:facebook.com`, urls: url(`"${n}" "${c}" (email OR contact) -site:linkedin.com -site:facebook.com`) });
            }

            this.queries = qs;

            // Email format suggestions — guess likely formats and search for exact match
            const formats = [];
            if (d) {
                const parts = n.toLowerCase().split(/\s+/).filter(Boolean);
                const first = parts[0] || '';
                const firstInitial = first.charAt(0) || '';
                const last = parts.slice(1).join('') || '';
                const lastLower = parts.slice(1).join(' ').toLowerCase().replace(/\s+/g, '') || '';

                const addFormat = (local) => {
                    const email = local + '@' + d;
                    if (!formats.some(f => f.email === email)) {
                        formats.push({
                            email,
                            searchUrls: url('"' + email + '"'),
                        });
                    }
                };

                addFormat(first);
                if (firstInitial) addFormat(firstInitial);
                if (last) {
                    addFormat(first + '.' + lastLower);
                    addFormat(firstInitial + '.' + lastLower);
                    addFormat(first + lastLower);
                    addFormat(firstInitial + lastLower);
                    addFormat(first + '_' + lastLower);
                    addFormat(first + '-' + lastLower);
                }
            }
            this.emailFormats = formats;
        },

        engineLabel(id) {
            return engines[id] || id;
        },

        copyUrl(url, index) {
            if (this.copyTimeout) clearTimeout(this.copyTimeout);
            navigator.clipboard.writeText(url).then(() => {
                this.copiedId = index;
                this.copiedFormat = null;
                this.copyTimeout = setTimeout(() => { this.copiedId = null; }, 2000);
            });
        },

        copyEmail(email) {
            if (this.copyTimeout) clearTimeout(this.copyTimeout);
            navigator.clipboard.writeText(email).then(() => {
                this.copiedFormat = email;
                this.copiedId = null;
                this.copyTimeout = setTimeout(() => { this.copiedFormat = null; }, 2000);
            });
        },
    };
}
</script>
@endpush
