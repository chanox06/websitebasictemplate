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
- Include a clear Return to Homepage call-to-action.
- Keep the site's existing header and footer.
- Make the page fully responsive.
- Do not leave the default unstyled WordPress 404 appearance.

---

# 4. Shared Form Styling

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

---

# 5. Form Email Styling

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

# 6. CSS Organization

Use:

- `style.css` for global/shared website styles
- `pages.css` for internal-page and shared internal-page styling

Avoid unnecessary new CSS files.

Reuse existing styles and CSS variables where practical.

Do not modify unrelated homepage styling.

---

# 7. Shared Component Review

Before implementing the global changes:

1. Inspect the existing theme structure.
2. Identify the current page template hierarchy.
3. Identify existing form handling.
4. Identify current JavaScript animation logic.
5. Identify any existing 404 template.
6. Identify reusable Gutenberg/theme components.

Do not duplicate functionality that already exists and can be improved.

---

# 8. Verification

Verify:

- Shared banner works correctly.
- Page title renders correctly.
- Featured Image behavior works.
- Banner is responsive.
- Scroll animation system is reusable.
- Reduced motion is respected.
- 404 page matches the website.
- Forms retain existing functionality.
- Notification emails retain existing recipients and data.
- Shared styles are organized properly.
- No unrelated functionality was modified.

---

# Completion Report

After completing this global setup, provide:

- Files modified
- Shared components created or updated
- Banner implementation summary
- Animation implementation summary
- 404 page changes
- Form styling changes
- Form email styling changes
- Any limitations
- Anything requiring manual attention

Do not begin redesigning individual internal page content.

Wait for a Page Action Instruction.
