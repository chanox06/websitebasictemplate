# Internal Page Action Instruction

## Purpose

Use this file for each individual internal page after the Planning Instruction and Global Setup Action have been established.

Change only the page-specific values below for each new page.

---

# Page Details

**Page Name:** Set an Appointment

**Local Development URL:**  
http://localhost/_basicWebsites/samsonstyles/index.php/media-company-set-an-appointment/

**Current Live Website URL:**  
https://www.samsonstyles.com/media-company-set-an-appointment/

---

# Action

Create or redesign this internal page using the previously established Internal Pages Planning Instruction and the shared internal-page system.

Use:

**Live website = content reference**

**New homepage = design reference**

Work on this page only.

---

# 1. Review the Existing Page

Review the current live page and identify:

- Page title
- Headings
- Paragraphs
- Lists
- Forms
- Calls-to-action
- Contact information
- Important client information
- Existing functional elements

Preserve important existing client content.

Do not invent unsupported business information.

---

# 2. Redesign the Local Page

Redesign the local page using the new homepage design system.

Match:

- Colors
- Typography
- Buttons
- Spacing
- Background treatments
- Visual hierarchy
- Cards or content panels where appropriate
- Overall design language

Do not simply copy the live site's old layout.

Improve the content organization and visual presentation.

Use a layout appropriate to the content of this specific page.

Do not force the same section structure used on other pages.

---

# 3. Featured Image Banner

Use the shared internal-page banner system.

Requirements:

- Use the WordPress Featured Image.
- Display the page title inside the banner.
- Use the shared overlay treatment.
- Maintain responsive behavior.
- Leave the final image selection for manual update.

Do not add random stock images.

---

# 4. Page Images

Leave content images blank or ready for manual replacement unless explicitly instructed otherwise.

Do not automatically download or insert random stock imagery.

Preserve placeholders or Gutenberg image areas where appropriate.

---

# 5. Scroll Animations

Use the shared animation system.

Apply animation direction based on layout:

- Left-side content → slide from left
- Right-side content → slide from right
- Centered content → slide upward

Do not create a new animation system for this page unless the shared system requires a bug fix.

---

# 6. CSS

Use existing global styles from `style.css`.

Add or update page-specific internal-page styles in:

`pages.css`

Avoid:

- New per-page CSS files
- Excessive inline styles
- Duplicate styles
- Unnecessary `!important`

Reuse existing components and CSS variables where practical.

---

# 7. Gutenberg

Keep the page editable through Gutenberg.

Prefer:

- Core Gutenberg blocks
- Existing block patterns
- Existing theme blocks
- Existing reusable components

Do not hardcode editable content into PHP unless technically necessary.

Do not introduce another page builder.

---

# 8. Forms

If this page contains a form:

- Use the shared form styling.
- Improve layout and spacing if necessary.
- Preserve all existing fields.
- Preserve submission behavior.
- Preserve recipients.
- Preserve submitted data.
- Use the shared form email styling.

Do not redesign or rebuild the form backend unless explicitly instructed.

---

# 9. Responsive Design

Verify the page on:

- Large desktop
- 1920px and wider
- Standard desktop
- Laptop
- Tablet
- Mobile

Ensure:

- No horizontal overflow
- Proper column stacking
- Readable text
- Appropriate spacing
- Responsive banner
- Usable buttons
- Proper form layout

---

# 10. Scope Control

Do not modify unrelated pages.

Do not rebuild shared systems unless a genuine fix is required for this page.

Do not introduce new libraries, frameworks, or plugins unless necessary.

Keep changes focused on this page.

---

# 11. Completion Check

Before finishing, verify:

- Important live-page content is present.
- Page design matches the homepage.
- Old live-site design was not copied.
- Featured Image banner works.
- Page title is inside the banner.
- Gutenberg editability is preserved.
- Page-specific styles are in `pages.css`.
- Shared styles are reused.
- Scroll animations use the shared system.
- Forms retain functionality.
- Desktop layout works.
- Tablet layout works.
- Mobile layout works.
- No horizontal overflow exists.
- No unrelated functionality was modified.

---

# Completion Report

After implementation, provide a concise report containing:

- Page updated
- Files modified
- Main design improvements
- Functional changes, if any
- Shared components reused
- Any issues or limitations
- Anything requiring manual attention
