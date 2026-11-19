# Internal Pages Planning Instruction

## Purpose

This file defines the permanent design, development, and implementation direction for all internal WordPress Gutenberg pages.

Use this file first before any page-specific work begins.

These instructions apply to all internal pages unless explicitly overridden by a later instruction.

---

# 1. Design Direction

Use the newly developed homepage as the primary visual reference for all internal pages.

Match the homepage's:

- Color scheme
- Typography
- Button styling
- Spacing
- Section backgrounds
- Border radius
- Shadows
- Hover effects
- Visual hierarchy
- Modern and professional appearance

All internal pages should clearly belong to the same website.

Do not make every page use the exact same layout.

Each page should use a content structure appropriate to its actual information.

Avoid:

- Generic AI-looking layouts
- Repetitive section structures
- Overcrowded content
- Excessive decorative elements
- Excessive animation
- Unnecessary complexity

Keep the design clean, spacious, professional, modern, and easy to understand.

---

# 2. Content Reference

For each page, a local development URL and current live website URL will be provided.

Use the current live website primarily as the reference for:

- Page title
- Existing text content
- Headings
- Paragraphs
- Lists
- Forms
- Contact information
- Calls-to-action
- Other important client information

Do not use the live website as the primary visual design reference.

## Core Rule

**Live website = content reference**

**New homepage = design reference**

Do not remove important client content unless specifically instructed.

You may improve:

- Content hierarchy
- Section arrangement
- Readability
- Visual presentation
- Calls-to-action
- Spacing

Do not invent business information, claims, services, contact information, testimonials, statistics, or other content that is not supported by the reference website.

---

# 3. Internal Page Banner

All standard internal pages should use a consistent reusable inner-page banner.

Requirements:

- Use the WordPress Featured Image as the banner image.
- Display the current page title inside the banner.
- Use an overlay when necessary for readability.
- Match the homepage design system.
- Keep banner styling consistent across internal pages.
- Ensure responsive behavior.
- Maintain good readability on large screens and mobile devices.

If no Featured Image is assigned, use an appropriate theme fallback background.

Do not automatically add random stock images.

The final images will be updated manually.

Prefer implementing the banner through a shared theme template or reusable component rather than duplicating markup on every page.

---

# 4. Page Content Design

Improve the presentation of the existing content.

Use layouts appropriate to the page, including when useful:

- Two-column sections
- Alternating left/right layouts
- Full-width sections
- Cards
- Highlight boxes
- CTA sections
- Lists
- Content panels
- Icon-supported content
- Structured form sections

Do not force every page into the same section pattern.

Prioritize:

1. Readability
2. Content hierarchy
3. Whitespace
4. Visual balance
5. Professional presentation
6. Conversion-focused calls-to-action
7. Responsive behavior

---

# 5. Gutenberg Requirements

The website uses WordPress Gutenberg.

Keep page content editable through Gutenberg whenever practical.

Prefer:

- Core Gutenberg blocks
- Existing theme blocks
- Existing reusable components
- Existing block patterns
- Existing theme utilities

Avoid:

- Introducing another page builder
- Hardcoding editable page content into PHP unnecessarily
- Installing new plugins unless required
- Rebuilding functionality already available in the theme

Preserve the existing WordPress and Gutenberg structure whenever possible.

---

# 6. CSS Structure

Focus primarily on:

- `style.css`
- `pages.css`

## `style.css`

Use for:

- Global website styles
- Typography
- Global buttons
- Shared UI elements
- Header
- Footer
- Global design tokens
- General reusable styles

## `pages.css`

Use for:

- Internal page styling
- Inner-page banner
- Internal content sections
- Page layouts
- Internal page responsive rules
- Internal page animations
- Page-specific styling

Do not create separate CSS files for every page unless there is a clear technical need.

Avoid:

- Excessive inline CSS
- Duplicate styles
- Unnecessary `!important`
- Unnecessary new stylesheets

Reuse existing classes and CSS variables whenever practical.

Do not modify unrelated homepage styling.

---

# 7. Scroll Animation Standards

Use subtle scroll entrance animations based on content position.

## Left-side content

Animate:

**Left → Right**

## Right-side content

Animate:

**Right → Left**

## Centered or full-width content

Animate:

**Bottom → Up**

Recommended behavior:

- Small movement distance, approximately 20–40px
- Subtle fade-in
- Smooth easing
- Approximately 500–700ms duration
- Trigger when the element enters the viewport

Do not animate every paragraph or small element individually.

Animate meaningful content groups or sections.

Requirements:

- Work on desktop, tablet, and mobile
- Respect `prefers-reduced-motion`
- Keep content accessible even if JavaScript fails
- Avoid excessive delays
- Avoid animation libraries unless already used by the theme

Prefer reusing the existing animation system or a lightweight Intersection Observer implementation.

---

# 8. Forms

When a page contains a form, improve the visual design while preserving functionality.

Improve:

- Field styling
- Labels
- Spacing
- Buttons
- Validation states
- Error messages
- Success messages
- Required field indicators
- Focus states
- Mobile layout

Do not change without explicit instruction:

- Form recipients
- Submission destination
- Submitted field data
- Existing workflow

---

# 9. Form Email Design

Website form notification emails should visually align with the website branding.

Requirements:

- Use the website's primary and secondary colors
- Use a clean professional layout
- Clearly separate field labels and submitted values
- Improve spacing and readability
- Use email-compatible HTML/CSS
- Remain readable on mobile
- Preserve existing recipients
- Preserve submitted data
- Preserve existing submission behavior

Do not make email templates unnecessarily complex.

Shared form email styling should be implemented in a reusable way when possible.

---

# 10. 404 Page

The website's 404 page should match the website design system.

Requirements:

- Match homepage colors and typography
- Use the same button styles
- Maintain the site header and footer
- Clearly explain that the page was not found
- Include a Return to Homepage call-to-action
- Be responsive
- Avoid the default unstyled WordPress 404 appearance

---

# 11. Responsive Design

All internal pages must work correctly on:

- Large desktop displays
- 1920px and wider screens
- Standard desktop
- Laptop
- Tablet
- Mobile

Requirements:

- Prevent excessive content stretching
- Maintain readable typography
- Maintain balanced whitespace
- Stack columns properly
- Resize images correctly
- Keep buttons usable
- Prevent horizontal overflow
- Maintain readable banners
- Preserve content hierarchy on small screens

---

# 12. Accessibility

Use:

- Semantic heading structure
- Sufficient color contrast
- Visible keyboard focus states
- Appropriate labels for form controls
- Appropriate image alt text
- Reduced motion support
- Accessible interactive elements

Do not sacrifice usability for visual effects.

---

# 13. Development Rules

Before modifying any files:

1. Inspect the existing theme structure.
2. Review the homepage implementation.
3. Identify reusable components.
4. Identify existing styles and utilities.
5. Review the live page content.
6. Determine the minimum files that need modification.

During implementation:

- Reuse existing code where practical.
- Avoid unnecessary dependencies.
- Avoid rebuilding working components.
- Keep changes scoped.
- Keep code maintainable.
- Do not modify unrelated pages.
- Do not overwrite unrelated content.

---

# 14. Completion Check

Before considering a page complete, verify:

- Important live-page content has been preserved.
- The design matches the new homepage.
- The layout does not simply copy the old live site.
- The Featured Image banner works correctly.
- The page title appears inside the banner.
- Gutenberg editability is preserved.
- Page styling is organized in `pages.css`.
- Global styles are reused where appropriate.
- Scroll animation direction is correct.
- Desktop layout works correctly.
- Tablet layout works correctly.
- Mobile layout works correctly.
- Forms still function correctly.
- No horizontal overflow exists.
- Unrelated functionality was not modified.

---

# 15. Completion Report

After each implementation, provide a concise summary containing:

- Page or feature updated
- Files modified
- Design improvements
- Functional changes
- Any limitations
- Any manual work required

Do not claim something was tested unless it was actually tested.

---

# Final Rule

These instructions remain active for all subsequent internal-page work.

Do not start individual page redesigns until a separate Page Action Instruction is provided.
