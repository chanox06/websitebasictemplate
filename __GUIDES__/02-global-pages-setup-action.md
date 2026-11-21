# Global Internal Pages Setup Action

## Purpose

Use this file after the Internal Pages Planning Instruction.

This action performs shared website-level work that should be completed once before individual internal pages are created.

Apply the standards defined in the planning instruction.

Do not redesign individual page content during this step.

---

# 1. Shared Internal Page Banner

Create or improve the reusable internal-page banner system.

Requirements:

- Use the WordPress Featured Image as the banner image.
- Display the current page title inside the banner.
- Apply an overlay when needed for readability.
- Match the homepage color scheme and visual style.
- Make the banner reusable across standard internal pages.
- Avoid duplicating banner markup on every page.
- Keep Featured Image management inside WordPress.
- Provide a suitable fallback if no Featured Image is assigned.
- Make the banner fully responsive.

Update the appropriate shared theme template or component.

Place internal-page banner styling in `pages.css` where appropriate.

---

# 2. Shared Scroll Animation System

Create or improve the reusable scroll-animation system for internal pages.

Animation direction:

- Left-side content → slide from left
- Right-side content → slide from right
- Centered content → slide upward

Requirements:

- Use subtle movement and opacity.
- Use smooth easing.
- Trigger when content enters the viewport.
- Avoid animating every individual paragraph.
- Reuse the same system across internal pages.
- Respect `prefers-reduced-motion`.
- Keep content visible if JavaScript is unavailable.
- Avoid unnecessary third-party animation libraries.

Prefer a lightweight reusable Intersection Observer implementation unless an existing theme system already handles this.

---

# 3. 404 Page

Create or redesign the website's 404 page.

Requirements:

- Match the homepage and internal-page design system.
- Match typography, colors, spacing, and button styles.
- Clearly show a Page Not Found message.
- Include a short helpful explanation.
- Include a clear Return to Homepage call-to-action.
- Keep the site's existing header and footer.
- Make the page fully responsive.
- Do not leave the default unstyled WordPress 404 appearance.

If the 404 page includes a search form, style the search form so it matches the website design system.

The 404 page should not look like a default WordPress template.

---

# 4. Search Results Page

Create or redesign the WordPress search results page.

The search results page is a shared theme-level page and should visually match the homepage and other internal pages.

Example search URL structure:

`/?s=search-term`

Example:

`http://localhost/_basicWebsites/mltransit/?s=about`

Review the existing search results template and improve it rather than leaving the default WordPress search layout.

## Search Page Requirements

The search page should include:

- A properly designed search-page banner or header area.
- A clear heading showing the current search query.
- A styled search form.
- A clean list or grid of search results.
- Proper spacing between results.
- Clear result titles.
- Readable result excerpts.
- Optional metadata only if it is useful to the website.
- A clear clickable link or button for each result.
- Proper pagination when multiple result pages exist.
- A properly designed no-results state.
- Fully responsive behavior.

## Search Heading

Display the current search query clearly.

Example:

`Search Results for: "About"`

The heading should use the website's typography and hierarchy.

Do not leave it as an unstyled default WordPress heading.

---

## Search Form Styling

Improve the search form so it matches the website.

Style:

- Search input
- Search button
- Input border
- Focus state
- Placeholder text
- Button hover state
- Button focus state
- Spacing between the field and button

The form should visually match the site's existing forms and buttons.

The input and button should align properly on desktop.

On smaller screens, allow the form to stack vertically when necessary.

Do not allow the search button to become disproportionately large or disconnected from the input.

---

## Search Result Styling

Each search result should have a clear visual structure.

Recommended structure:

- Result title
- Short excerpt
- Optional category/date only when appropriate
- Read More link or clickable result title

Use:

- Consistent spacing
- Strong title hierarchy
- Readable body text
- Subtle separators, cards, or borders where appropriate
- Hover states for clickable elements

Do not display results as plain unstyled text blocks.

Avoid excessive card styling if the homepage design is minimal.

Use the homepage design language as the visual reference.

---

## Search Result Excerpts

Keep excerpts readable and controlled.

Requirements:

- Avoid extremely long excerpts.
- Prevent broken or awkward text overflow.
- Maintain consistent spacing between results.
- Preserve meaningful text from the result.
- Do not expose raw shortcodes, HTML, or Gutenberg block markup.

---

## No Search Results State

Create a properly designed no-results state.

When no results are found:

- Clearly inform the visitor that no matching content was found.
- Display the searched term when appropriate.
- Allow the visitor to search again.
- Include a Return to Homepage option if useful.
- Match the site's typography, buttons, spacing, and colors.

Do not leave the default WordPress "Nothing Found" appearance unstyled.

---

## Search Pagination

If search results span multiple pages:

- Style Previous / Next navigation.
- Style page numbers if available.
- Match website button and link styles.
- Ensure accessible focus states.
- Ensure pagination is responsive.

---

## Search Template Implementation

Inspect the existing theme for:

- `search.php`
- `searchform.php`
- `get_search_form()`
- Search-related template parts
- Existing pagination functions
- Existing result card components

Reuse and improve existing components when practical.

Do not duplicate search markup unnecessarily.

Use WordPress-native search functionality.

Do not replace the WordPress search engine unless explicitly instructed.

---

# 5. Shared Form Styling

Improve the shared appearance of website forms.

Improve:

- Input fields
- Textareas
- Select fields
- Labels
- Buttons
- Required field indicators
- Error messages
- Success messages
- Focus states
- Mobile responsiveness

Match the homepage design system.

Do not change form functionality.

Do not change:

- Recipients
- Submission destinations
- Existing field data
- Existing workflows

unless explicitly instructed.

Where possible, make the site's search form visually compatible with these shared form styles while still allowing search-specific layout adjustments.

---

# 6. Form Email Styling

Review how website forms generate notification emails.

Improve the shared email presentation while preserving functionality.

Requirements:

- Match the website's brand colors.
- Use a clean professional email layout.
- Clearly separate labels from submitted values.
- Improve spacing and readability.
- Use email-compatible HTML/CSS.
- Keep emails readable on mobile devices.
- Preserve existing recipients.
- Preserve submitted data.
- Preserve existing submission behavior.

If multiple forms use the same notification system, implement the styling in a reusable way instead of duplicating it.

Do not change email destinations unless explicitly instructed.

---

# 7. CSS Organization

Use:

- `style.css` for global/shared website styles
- `pages.css` for internal-page and shared internal-page styling

Search page and 404 page styles should also be organized within the existing theme CSS structure.

Prefer placing:

- Shared global search/form styles in `style.css`
- Search-results page layout and 404 page layout styles in `pages.css`

Avoid unnecessary new CSS files.

Reuse existing styles and CSS variables where practical.

Do not modify unrelated homepage styling.

---

# 8. Shared Component Review

Before implementing the global changes:

1. Inspect the existing theme structure.
2. Identify the current page template hierarchy.
3. Identify existing form handling.
4. Identify current JavaScript animation logic.
5. Identify any existing `404.php` template.
6. Identify any existing `search.php` template.
7. Identify any existing `searchform.php`.
8. Identify search-result template parts.
9. Identify existing pagination logic.
10. Identify reusable Gutenberg/theme components.

Do not duplicate functionality that already exists and can be improved.

---

# 9. Verification

Verify:

- Shared banner works correctly.
- Page title renders correctly.
- Featured Image behavior works.
- Banner is responsive.
- Scroll animation system is reusable.
- Reduced motion is respected.
- 404 page matches the website.
- 404 search form is styled if present.
- Search results page matches the website.
- Search input and button are properly styled.
- Search results are easy to scan.
- Result excerpts are controlled and readable.
- No-results state is styled.
- Search pagination is styled.
- Search page works on desktop, tablet, and mobile.
- Forms retain existing functionality.
- Notification emails retain existing recipients and data.
- Shared styles are organized properly.
- No unrelated functionality was modified.

Test at least one real search query after implementation.

Example:

`/?s=about`

Also test a search query that returns no results.

---

# Completion Report

After completing this global setup, provide:

- Files modified
- Shared components created or updated
- Banner implementation summary
- Animation implementation summary
- 404 page changes
- Search page changes
- Search form changes
- Search result layout changes
- No-results behavior
- Pagination changes
- Form styling changes
- Form email styling changes
- Any limitations
- Anything requiring manual attention

Do not begin redesigning individual internal page content.

Wait for a Page Action Instruction.