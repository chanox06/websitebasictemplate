# Bulk Internal Pages Recreation Action

## Purpose

Use this instruction after the Internal Pages Planning Instruction and Global Internal Pages Setup have already been established.

The objective is to recreate the current live website's internal pages automatically inside the local WordPress website instead of creating each page manually one at a time.

The AI is responsible for:

1. Discovering the existing internal pages from the reference website.
2. Building an inventory of pages.
3. Creating corresponding WordPress Pages locally.
4. Preserving appropriate page titles, slugs, content, forms, links, and images.
5. Redesigning each page using the new homepage design system.
6. Keeping normal content editable through Gutenberg.
7. Skipping complex content safely when a page requires special handling.
8. Continuing the migration even when one page is problematic.
9. Adding a reusable breadcrumb trail to every standard internal page.
10. Avoiding duplicate page-title headings inside the main content when the page title is already displayed in the Featured Image banner.
11. Updating homepage, header, footer, CTA, card, and internal links after all pages have been created.
12. Ensuring header and footer navigation clearly indicate the current/active page using a persistent hover-like active state.
13. Producing one final migration and special-attention report.

Apply all previously established Planning and Global Setup instructions.

Do not allow one difficult page to stop the migration of the remaining website.

---

# 1. Website Details

**Current Live Website:**

`https://www.earnesteps.com/`

**Local WordPress Website:**

`http://localhost/_basicWebsites/earnesteps/`

---

# 2. Discover Existing Internal Pages

Review the current live website and identify the internal pages that should be recreated locally.

Use available sources such as:

- Main navigation
- Dropdown menus
- Footer navigation
- Homepage links
- Internal page links
- Sitemap, if available
- Existing WordPress page structure, if accessible

Build a page inventory before creating pages.

For every discovered page, determine:

- Page title
- Existing live URL
- Existing slug
- Parent page, if applicable
- Main content
- Forms
- Calls-to-action
- Images
- Important functional elements
- Whether the page appears straightforward or may need special handling

Do not treat these as standard WordPress Pages unless there is a clear reason:

- Blog posts
- Media attachment URLs
- Search results
- Tag archives
- Category archives
- Author archives
- Pagination URLs
- Login/admin URLs
- Query-string URLs

---

# 3. Page Inventory

Before making major changes, create an internal working inventory similar to:

| Page | Live URL | Target Slug | Parent | Status |
|---|---|---|---|---|
| About Us | `/about-us/` | `about-us` | None | To Create |
| Services | `/services/` | `services` | None | To Create |
| Contact Us | `/contact-us/` | `contact-us` | None | To Create |

Use this inventory to avoid:

- Duplicate pages
- Missing pages
- Incorrect slugs
- Recreating the same page twice

You do not need to stop and ask for approval after generating the inventory.

Proceed with the migration unless a serious ambiguity would make further work unsafe.

---

# 4. Page Creation

For every discovered standard internal page, first check whether a corresponding local WordPress Page already exists.

## If the page already exists

Use and update the existing page.

Do not create a duplicate.

## If the page does not exist

Create a new WordPress Page using:

- Correct page title
- Appropriate slug
- Published status
- Gutenberg editor
- Appropriate theme page template
- Correct parent page where applicable

Prefer preserving the existing live-site slug unless there is a strong reason not to.

Use the safest WordPress-native creation method available.

Preferred order:

1. WP-CLI
2. WordPress REST API or existing project tooling
3. WordPress PHP functions

Avoid direct database manipulation unless absolutely necessary.

After creation, verify:

- Page exists
- Title is correct
- Slug is correct
- Publication status is correct
- Parent relationship is correct
- Permalink resolves
- Gutenberg can edit the page

---

# 5. Content Migration

For every recreated page, use the live website as the content reference.

Preserve important:

- Headings
- Paragraphs
- Lists
- Contact information
- Service information
- Calls-to-action
- Forms
- Legal or informational text
- Other meaningful client content

Do not blindly copy obsolete layout markup.

Do not reproduce unnecessary:

- Old wrappers
- Inline styles
- Legacy CSS classes
- Old page-builder markup
- Old frontend scripts

The goal is to migrate the content, not the old frontend implementation.

## Page Title Duplication Rule

The page title is already displayed prominently inside the Featured Image / inner-page banner.

Therefore:

- Do not add the same page title again as the first heading inside the main Gutenberg content.
- Do not begin the main content with a redundant H1 that simply repeats the page title.
- Start the main content with the actual introductory paragraph, a meaningful section heading, or the first real content section.
- Preserve any legitimate secondary heading from the reference page when it adds meaning and is not simply a duplicate of the page title.
- Maintain a correct semantic heading hierarchy after removing redundant title headings.

The intended structure is:

**Featured Image Banner + Page Title**  
↓  
**Breadcrumbs**  
↓  
**Main content begins without repeating the page title**

---

# 6. Design Direction

For every migrated page:

**Live website = content reference**

**New homepage = design reference**

Redesign each page according to the established homepage design system.

Match:

- Colors
- Typography
- Buttons
- Section styling
- Spacing
- Cards
- Border radius
- Shadows
- CTA styling
- Visual hierarchy
- Overall design language

Do not simply clone the old live-site design.

Do not make every internal page look identical.

Choose section structures appropriate to each page's content.

---

# 7. Gutenberg Structure

Build migrated page content using Gutenberg-compatible blocks.

Prefer:

- Group
- Columns
- Column
- Heading
- Paragraph
- List
- Image
- Cover
- Buttons
- Button
- Existing theme patterns/components

Keep normal content editable through Gutenberg.

Do not convert an entire page into one large Custom HTML block.

Do not hardcode normal editable page content into PHP.

---

# 8. Featured Images and Inner Banners

Use the shared internal-page banner system already established.

For each internal page:

- Use the WordPress Featured Image.
- Display the page title inside the shared banner.
- Maintain the established overlay treatment.
- Maintain responsive behavior.

If a suitable banner/featured image exists on the live reference page:

1. Fetch the image.
2. Add it to the local WordPress Media Library or local uploads.
3. Use the local copy.
4. Do not hotlink the live website.

If no suitable featured image exists, use the shared fallback banner behavior.

Do not insert random stock imagery.

---

# 9. Breadcrumbs

Add a breadcrumb trail to every standard internal page.

Place breadcrumbs:

- Directly below the Featured Image / inner-page banner
- Before the main page content
- In a consistent location across all internal pages

Recommended structure:

`Home > Parent Page > Current Page`

Examples:

`Home > About Us`

`Home > Services > Transportation`

Requirements:

- `Home` should link to the local homepage.
- Parent breadcrumb items should link to the correct local parent page when one exists.
- The current page should appear as the final breadcrumb item and should not require a link.
- Reflect the actual WordPress page hierarchy where applicable.
- Use real WordPress-generated URLs instead of hardcoded localhost URLs when working in PHP templates.
- Keep breadcrumbs reusable and centralized rather than manually hardcoding a different breadcrumb implementation into every page.
- Match the website's typography, colors, spacing, hover states, and overall design language.
- Make breadcrumbs responsive and readable on mobile.
- Allow long breadcrumb trails to wrap cleanly without causing horizontal overflow.
- Use appropriate semantic markup and accessibility attributes where practical.
- Do not add a breadcrumb to the homepage itself unless the existing design specifically requires it.

Prefer a lightweight theme-level breadcrumb implementation that can automatically determine page ancestors and the current page.

Do not install a breadcrumb plugin solely for this requirement unless the project already depends on one.

---

# 10. Content Images

For images contained inside the reference page:

## Existing suitable image

Fetch it from the reference site and add it locally.

Requirements:

- Do not hotlink.
- Keep the image editable through Gutenberg.
- Preserve meaningful alt text where available.
- Maintain appropriate aspect ratio.
- Avoid stretching or distortion.
- Use responsive image behavior.

## Existing image is unusable

If the source image is:

- Broken
- Extremely low resolution
- Technically unusable
- Clearly outdated or inappropriate

create an image placeholder instead.

## Additional imagery would improve the new design

If the redesigned layout clearly needs an image in an area where the reference page has none:

- Create an empty Gutenberg Image block or appropriate image placement.
- Size and position it according to the design.
- Leave it empty for manual replacement.

Do not automatically select random stock images.

Core rule:

**Suitable reference image exists → migrate it locally.**

**No suitable reference image exists → create an editable placeholder.**

---

# 11. Forms

When a reference page contains a form:

Preserve the existing form purpose and fields where technically possible.

Use the shared form design system.

Preserve:

- Fields
- Labels
- Required fields
- Recipient behavior
- Submission behavior
- Existing workflow

Use the shared email notification styling.

Do not invent new backend workflows.

If the old form implementation cannot be migrated directly, recreate the equivalent form using the form solution already established in the local project.

If the form requires unclear or complex backend functionality, follow the Special Attention rules below.

---

# 12. Special Handling and Skip Rules

Do not allow one complex or problematic page to stop the migration of the rest of the website.

If a page requires special handling that cannot be completed safely or reliably during bulk migration, follow this process.

## 11.1 Create the Page Shell First

If the page clearly exists on the reference website, still create the corresponding local WordPress Page.

Create:

- Correct page title
- Correct slug
- Correct parent relationship
- Appropriate publication status
- Gutenberg editor support
- Appropriate theme page template
- Shared internal-page banner where applicable

Do not leave the page completely missing from WordPress.

This ensures:

- The URL structure remains complete
- Navigation can still be mapped
- The page can be completed later
- The page can be handled with a separate page-specific instruction

## 11.2 Do Not Force Complex Content Migration

Examples that may require special attention include:

- Complex booking systems
- Custom calculators
- Membership portals
- User dashboards
- Third-party application workflows
- Complex ecommerce functionality
- Advanced filters
- Custom search tools
- Protected content
- External API-driven systems
- Forms with unclear backend logic
- Embedded proprietary systems
- Pages requiring credentials
- Broken or inaccessible reference pages
- Pages where source content cannot be determined reliably

For these pages:

1. Create the WordPress page.
2. Preserve the intended title and slug.
3. Add the shared banner if appropriate.
4. Migrate only clearly safe/static content when possible.
5. Leave unsupported content empty or use a structured placeholder.
6. Do not invent missing functionality.
7. Mark the page as requiring Special Attention.
8. Continue to the next page.

## 11.3 Page Status Labels

Maintain an internal migration status such as:

- `Completed`
- `Completed with Minor Issue`
- `Completed with Special Attention Required`
- `Special Attention Required`
- `Source Unavailable`
- `Skipped Content – Page Created`

A page requiring special handling should not be treated as a failed migration.

## 11.4 Continue Processing

After marking a page for Special Attention:

**Do not stop the bulk migration.**

Continue processing all remaining pages.

Do not ask for approval after every difficult page.

Only stop if the issue affects the entire website or makes further migration unsafe.

## 11.5 Safe Partial Content

If only part of a page is complex, migrate the safe content.

Example:

A page contains:

- Introductory text
- Service details
- Contact information
- Complex booking widget

Migrate:

- Headings
- Paragraphs
- Images
- Static CTAs
- Informational content

Do not recreate the complex booking widget unless implementation is clear and supported.

Leave an appropriate placeholder for the unsupported section when useful.

Mark the page:

`Completed with Special Attention Required`

## 11.6 Do Not Guess

For Special Attention pages, do not invent:

- Business rules
- Form recipients
- API credentials
- Pricing
- Booking logic
- Payment logic
- Authentication logic
- External integration behavior
- Database behavior
- Unsupported user flows

Preserve what can be migrated safely and report the rest.

---

# 13. Internal Links During Migration

While migrating content, convert internal live-domain links to the corresponding local WordPress pages whenever those local pages already exist.

Example:

Live:

`https://www.example.com/about-us/`

Local:

`/about-us/`

Preserve legitimate external URLs.

Preserve appropriate:

- `mailto:`
- `tel:`

links.

If a target page has not yet been created, it is acceptable to defer the final link assignment until the Final Link and Navigation Update phase.

---

# 14. Menu Structure

Where the live website has a meaningful navigation hierarchy, reproduce the corresponding structure locally.

Preserve:

- Parent items
- Child/dropdown items
- Logical page relationships

Use the new website's established navigation design.

Do not copy obsolete menu styling from the old site.

## Active Navigation State

Header and footer navigation must clearly indicate the page currently being viewed.

Requirements:

- The active/current page navigation item should remain visually highlighted.
- The active style should generally match or closely resemble the existing hover state so the interaction language stays consistent.
- Do not rely on hover alone; the active state must remain visible even when the pointer is not over the item.
- When viewing a child page, the relevant parent navigation item should also receive an active/ancestor state where appropriate.
- Apply active states consistently to main navigation, dropdown navigation, and applicable footer navigation.
- Preserve keyboard focus styling separately from current-page styling.
- Do not manually hardcode an active class for only one specific page.

Prefer WordPress-generated navigation state classes and APIs where available, such as current menu item, current page ancestor, and current menu ancestor states.

If the theme uses custom navigation markup instead of WordPress menu output, implement a reusable URL/page-aware active-state solution.

---

# 15. CSS

Use the established CSS architecture.

Use:

`style.css`

for global/shared website styles.

Use:

`pages.css`

for internal-page layouts and page-specific shared styling.

Do not create one stylesheet for every migrated page.

Prefer reusable semantic classes.

If several pages share the same pattern, create reusable styles instead of duplicating CSS.

---

# 16. Scroll Animations

Use the shared animation system already implemented.

Apply:

- Left content → slide from left
- Right content → slide from right
- Center/full-width content → slide upward

Do not create separate animation logic for every page.

---

# 17. Responsive Requirements

Every migrated page must work correctly on:

- 1920px–2000px+ displays
- Standard desktop
- Laptop
- Tablet
- Mobile

Verify:

- No horizontal overflow
- Appropriate content container width
- Readable typography
- Proper column stacking
- Proper image cropping
- Responsive forms
- Responsive banners
- Usable buttons

---

# 18. Do Not Recreate System Templates as Pages

Do not create normal WordPress Pages for:

- 404
- Search results
- Search no-results state
- Archives

These should continue using the shared theme templates established during the Global Setup Action.

---

# 19. Scope and Safety

Do not delete existing local pages simply because they are not found on the live website.

Do not overwrite unrelated custom functionality.

Do not modify WordPress core.

Do not install unnecessary plugins.

Do not introduce another page builder.

Do not duplicate existing pages.

Do not change valid external integrations unless required.

If a page contains functionality significantly more complex than normal page content, follow the Special Attention rules instead of forcing implementation.

---

# 20. Migration Order

Process pages in a logical order.

Recommended:

1. Top-level navigation pages
2. Main service pages
3. Child/service-detail pages
4. About/company pages
5. Contact/appointment pages
6. Secondary informational pages
7. Footer-only pages
8. Legal pages

Complete the entire reasonable page inventory during this action.

Do not stop after every page waiting for another instruction.

---

# 21. Broken or Unavailable Reference Pages

If a specific live page cannot be accessed:

1. Check whether its content can be recovered from existing navigation, local content, or another accessible reference page.
2. Preserve any reliable content that is available.
3. Do not invent missing business information.
4. Create the page shell if its existence and purpose are clear.
5. Mark it `Source Unavailable` or `Special Attention Required`.
6. Continue to the next page.

Do not let one broken reference page prevent migration of the remaining website.

---

# 22. Per-Page Verification

After processing each page, verify:

- Page exists
- No duplicate exists
- Correct title
- Correct slug
- Correct parent relationship
- Correct publication status
- URL resolves
- Gutenberg can edit the page
- Important reference content is present when available
- Images are local rather than hotlinked
- New design matches the homepage
- Shared banner works
- Breadcrumbs appear below the banner and reflect the correct page hierarchy
- The main content does not repeat the page title as a redundant opening H1/heading
- Scroll animation uses the shared system
- Responsive layout works
- No horizontal overflow exists

If the page cannot be fully completed, mark it for Special Attention and continue.

---

# 23. Final Link and Navigation Update

Perform this section **only after all discovered internal pages have been created or processed**.

Do not perform the final website-wide navigation cleanup while pages are still being created.

The purpose of this phase is to connect the completed local website together using the actual pages and slugs that now exist.

---

# 24. Review All Website Links

Review the complete local website for clickable elements.

Include:

- Header navigation
- Dropdown navigation
- Homepage navigation tabs
- Homepage buttons
- Hero buttons
- CTA buttons
- Service cards
- Read More links
- Image links
- Text links
- Footer navigation
- Footer service links
- Footer CTA buttons
- Logo links
- Contact links
- Social media links
- Links inside migrated page content
- Any other clickable website element

---

# 25. Link Assignment Priority

For every clickable element, determine the destination using the following priority.

## A. Matching Local Page Exists

If the corresponding page now exists locally, link to the correct local WordPress page.

Do not continue linking to the old live website when a local equivalent exists.

## B. Newly Created Page Exists

If a page was created during this migration, update relevant:

- Navigation items
- Homepage buttons
- CTA links
- Footer links
- Internal content links

to use the new local page.

Do not leave completed pages disconnected from the website.

## C. Valid External Link Exists

Preserve legitimate external destinations.

Examples:

- Social media
- External booking systems
- Payment portals
- Client portals
- Google Maps
- Third-party services
- External resources

Do not replace valid external URLs with `#`.

## D. Email and Telephone Links

Use or preserve:

`mailto:`

for email addresses.

Use:

`tel:`

for telephone numbers where appropriate.

## E. No Destination Exists

If there is no local page, no valid external destination, and no corresponding functionality:

Use:

`#`

as the temporary placeholder.

Do not invent a URL.

Do not create unnecessary pages just to satisfy a button.

---

# 26. Homepage Link Update

After all internal pages have been created, specifically review the homepage.

Update every relevant:

- Navigation item
- Hero button
- CTA button
- Service card
- Read More link
- About link
- Contact button
- Appointment button
- Image link
- Text link
- Footer link

Connect them to the appropriate newly created local pages.

## Important

Do not redesign the homepage during this phase unless a small technical adjustment is necessary for correct linking.

This is primarily a URL and navigation cleanup task.

---

# 27. Header Navigation

Review the complete header navigation.

Verify:

- Logo links to the homepage.
- Main menu items link correctly.
- Dropdown items link correctly.
- Newly created pages are connected where appropriate.
- Header CTA links correctly.
- Parent/child relationships are correct.
- The current page is visibly highlighted using the established active/hover-like state.
- When viewing a child page, the applicable parent/ancestor menu item is also highlighted where appropriate.
- Active states work without requiring pointer hover.
- No unnecessary old live-domain links remain.
- No empty links remain.

Use the new website's established header design.

Do not copy the old website's navigation styling.

---

# 28. Footer Navigation

Review the entire footer.

Verify:

- Footer logo links correctly.
- Navigation links work.
- Service links work.
- Contact links work.
- Social media links remain correct.
- Legal/information links point to local pages when those pages exist.
- The current page is visibly highlighted in applicable footer navigation.
- Footer active styling matches or closely follows the established footer hover style.
- CTA buttons work.
- `mailto:` links are correct.
- `tel:` links are correct.

If no valid destination exists, use `#`.

---

# 29. Migrated Internal Content Links

Review links inside migrated page content.

Replace old internal live-domain URLs with corresponding local WordPress pages.

Example:

Old:

`https://www.example.com/services/`

Local:

`/services/`

Preserve legitimate external URLs.

---

# 30. Special Attention Page Link Handling

If a Special Attention page was created and has a valid local URL, decide whether navigation should link to it.

## Link the page when:

- The static content is useful
- The page shell is intentionally available
- The incomplete portion does not create a misleading experience

## Use `#` temporarily when:

- The page would be misleading without its missing functionality
- The primary purpose of the page is not functional yet
- The page should not be exposed until special work is completed

Record this decision in the final report.

Do not pretend unfinished functionality works.

---

# 31. Remove Invalid Placeholder Patterns

Check for and resolve:

- Empty `href=""`
- Empty Gutenberg button links
- `javascript:void(0)`
- Temporary development URLs
- Broken localhost URLs
- Links pointing to pages that were not created
- Old internal live-domain links when a local equivalent exists
- Duplicate or malformed URLs

Use `#` only when no valid destination exists.

---

# 32. WordPress Link Handling

When modifying PHP theme templates, prefer WordPress-aware URL generation where appropriate.

Examples:

- `home_url()`
- `get_permalink()`
- WordPress menu APIs
- Existing theme navigation functions

Do not unnecessarily hardcode the localhost domain into reusable PHP templates.

For Gutenberg-managed content, use the appropriate WordPress page link.

The implementation should continue working when the website moves from localhost to staging or production.

---

# 33. Final Website Verification

After page migration and link updates are complete, verify:

## Pages

- All expected pages exist.
- No duplicate pages were created.
- Titles are correct.
- Slugs are correct.
- Parent relationships are correct.
- Publication statuses are correct.
- Gutenberg content remains editable.

## Images

- Reference images are stored locally.
- No unnecessary hotlinked internal images remain.
- Image placeholders exist where manual imagery is required.
- Images maintain appropriate proportions.

## Homepage

- Navigation works.
- Hero buttons work.
- CTA buttons work.
- Service cards work.
- Text/image links work.
- Logo returns to homepage.

## Header

- Main navigation works.
- Dropdown navigation works.
- Header CTA works.
- Current-page navigation is visibly highlighted.
- Parent/ancestor navigation is highlighted appropriately on child pages.
- Active styling remains visible without hover.

## Footer

- Footer navigation works.
- Service links work.
- Contact links work.
- Social links work.
- Legal links work.
- Applicable current-page footer navigation is visibly highlighted.

## Internal Pages

- Breadcrumbs appear on every standard internal page.
- Breadcrumb hierarchy and links are correct.
- Breadcrumbs are positioned below the Featured Image banner and before the main content.
- The page title is not redundantly repeated at the beginning of the main content.
- Content links work.
- CTA links work.
- Cross-page links work.
- Contact links work.

## Shared Templates

- 404 page is styled.
- Search results page is styled.
- Search no-results state is styled.
- Forms retain functionality.
- Form emails retain intended recipients.
- Shared animation system works.

## Responsive

- Large desktop works.
- 1920px–2000px+ layouts remain properly contained.
- Standard desktop works.
- Tablet works.
- Mobile works.
- No horizontal overflow exists.

## Links

- No empty `href` values remain.
- No accidental `javascript:void(0)` links remain.
- No broken local links remain.
- No unnecessary old live-site internal links remain.
- External URLs are preserved.
- `mailto:` links are correct.
- `tel:` links are correct.
- `#` is used only where no valid destination currently exists.

Fix reasonable issues discovered during this final audit before completing the task.

Do not stop after merely identifying minor issues that can safely be corrected.

---

# 34. Final Completion Report

Do not generate the final report until:

1. All discovered pages have been processed.
2. Special Attention pages have been recorded.
3. The final homepage/header/footer/internal link update has been completed.
4. The final website verification has been performed.

Provide one final report with the following sections.

## A. Migration Summary

Report:

- Total reference pages discovered
- Pages created
- Existing pages updated
- Fully completed pages
- Pages completed with minor issues
- Pages requiring special attention
- Pages with unavailable source content
- Pages created with content intentionally skipped

## B. Completed Pages

For each completed page provide:

- Page title
- Slug
- Final local URL
- WordPress Page ID, if available
- Status

## C. Special Attention Required

For every page requiring additional work provide:

- Page title
- Slug
- Local URL
- WordPress Page ID, if available
- Migration status
- What was successfully created
- What content or functionality was skipped
- Why it requires special attention
- What needs to be completed manually or through a separate instruction
- Whether navigation currently links to the page

Example:

| Page | Status | Completed | Needs Attention |
|---|---|---|---|
| Book a Ride | Special Attention Required | Page, slug, banner, intro content | Booking integration requires separate implementation |
| Client Portal | Skipped Content – Page Created | Page shell and navigation | Authentication and external API required |
| Contact Us | Completed | Full content and form | None |

## D. Assets

Report:

- Images migrated
- Image placeholders created
- Broken/unusable images encountered

## E. Navigation and Links

Report:

- Header/menu links updated
- Homepage links and CTAs updated
- Footer links updated
- Internal page links updated
- Breadcrumb system implemented and verified
- Header/footer active navigation states implemented and verified
- External links preserved
- Special Attention pages linked or intentionally left as `#`
- Placeholder `#` links remaining and why

## F. Files Modified

List:

- Theme files modified
- CSS files modified
- JavaScript files modified
- Templates modified

## G. Remaining Manual Work

Clearly list anything that still requires:

- Manual content entry
- Manual image replacement
- API/integration setup
- Credentials
- Complex development
- Form configuration
- Plugin configuration
- Separate page-specific implementation

Do not report the bulk migration as failed simply because some pages require separate handling.

The objective is to complete all straightforward pages while isolating and clearly reporting exceptions.
