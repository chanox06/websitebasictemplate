# Internal Page Action Instruction

## Purpose

Use this file for each individual internal page after the Planning Instruction and Global Setup Action have been established.

# Action

Create the WordPress page if it does not already exist, then design and populate it using the previously established Internal Pages Planning Instruction and shared internal-page system.

Use:

**Live website = content reference**

**New homepage = design reference**

Work on this page only.

---

# 1. Create or Locate the WordPress Page

Before designing the page, check whether a WordPress page with the specified slug already exists.

Use the following values:

**Page Name:** 
Corporate Account

**Page Slug:**  
`luxury-transportation-corporate-account`

**Local Development Base URL:**  
http://localhost/_basicWebsites/earnesteps/luxury-transportation-corporate-account


**Current Live Website URL: (I just add this to local since live site is broken)**  
https://www.earnesteps.com/senior-placement-consultant-in-sacramento-california


## If the page does not exist

Create a new WordPress Page using:

- Title: `Sample About Us`
- Slug: `sample-url-about-us`
- Status: Published
- Content editor: Gutenberg
- Appropriate default page template used by this theme

Do not create the page as a post or custom post type.

Do not create duplicate pages.

## If the page already exists

Use the existing page.

Do not create another page with the same or similar slug.

Update its content and design according to this instruction.

## Page Creation Method

Use the most reliable method available in the local WordPress environment.

Preferred order:

1. WP-CLI, if available
2. Existing WordPress development tooling or API
3. Direct WordPress/PHP execution using WordPress functions if necessary

Avoid manually inserting records directly into the WordPress database unless there is no safer WordPress-native option.

After creating the page, verify:

- The page exists
- The title is correct
- The slug is exactly correct
- The page is published
- The permalink resolves correctly
- Gutenberg can edit the page

---

# 2. Review the Existing Live Page

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

Do not automatically copy the old page title if the Page Details above specify a different title.

The Page Details in this instruction take priority for:

- New page title
- New slug
- New local URL

The live page is primarily the content reference.

---

# 3. Build the Gutenberg Page Content

Populate the newly created or existing WordPress page with Gutenberg-compatible content.

Prefer:

- Core Gutenberg blocks
- Existing block patterns
- Existing theme blocks
- Existing reusable components

The page content should remain editable from the WordPress Gutenberg editor.

Do not create the entire page as one large Custom HTML block unless absolutely necessary.

Do not hardcode editable page content into PHP templates.

Use meaningful block structure for:

- Headings
- Paragraphs
- Columns
- Groups
- Buttons
- Lists
- Forms
- Other page sections

---

# 4. Redesign the Page

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

# 5. Featured Image Banner

Use the shared internal-page banner system.

Requirements:

- Use the WordPress Featured Image.
- Display the page title inside the banner.
- Use the shared overlay treatment.
- Maintain responsive behavior.
- Image already exists on live site → fetch it and add it locally.

If no Featured Image is assigned, use the shared fallback banner behavior.

Do not add random stock images.

---

# 6. Page Images

Image already exists on live site → fetch it and add it locally.

Image exists but is unusable → create a placeholder.

New design needs an additional image → create an empty image placement for me to fill manually.

Do not automatically download or insert random stock imagery.

Preserve placeholders or Gutenberg image areas where appropriate.

---

# 7. Scroll Animations

Use the shared animation system.

Apply animation direction based on layout:

- Left-side content → slide from left
- Right-side content → slide from right
- Centered content → slide upward

Do not create a new animation system for this page unless the shared system requires a bug fix.

---

# 8. CSS

Use existing global styles from:

`style.css`

Add or update page-specific internal-page styles in:

`pages.css`

Avoid:

- New per-page CSS files
- Excessive inline styles
- Duplicate styles
- Unnecessary `!important`

Reuse existing components and CSS variables where practical.

---

# 9. Forms

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

# 10. Responsive Design

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

# 11. Scope Control

Do not modify unrelated pages.

Do not rebuild shared systems unless a genuine fix is required for this page.

Do not introduce new libraries, frameworks, or plugins unless necessary.

Keep changes focused on this page.

---

# 12. Completion Check

Before finishing, verify:

- The WordPress page exists.
- No duplicate page was created.
- Page title is correct.
- Page slug is exactly correct.
- Page is published.
- Final local URL resolves successfully.
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

- Page created or updated
- Page title
- Page slug
- Final local URL
- WordPress page ID, if available
- Publication status
- Files modified
- Main design improvements
- Functional changes, if any
- Shared components reused
- Any issues or limitations
- Anything requiring manual attention