# Homepage Finalization and Visual Polish Action

## Purpose

Use this instruction **after the homepage has already been manually recreated in WordPress Gutenberg** and **before starting any internal-page planning, global page setup, or bulk internal-page recreation**.

The homepage already exists and has already been plotted section by section inside Gutenberg, typically including:

- Header
- Main navigation
- Hero / banner
- Main content sections
- CTA sections
- Footer

The objective of this action is **not to rebuild the homepage from scratch**.

The objective is to review, refine, polish, and finalize the existing homepage so it becomes the complete visual and interaction reference for all internal pages that will be created afterward.

After this action is complete:

**Homepage = final visual design system for the entire website**

Internal pages should later inherit the homepage's:

- Colors
- Typography
- Button behavior
- Hover states
- Animation language
- Spacing
- Cards
- Border radius
- Shadows
- Section backgrounds
- Container widths
- Responsive behavior
- Header
- Footer
- Visual hierarchy

Do not begin internal-page recreation until the homepage has been finalized.

---

# 1. Inspect the Existing Homepage First

Before changing anything:

1. Inspect the complete homepage from header through footer.
2. Inspect the existing Gutenberg structure.
3. Inspect the active custom theme.
4. Inspect existing CSS files.
5. Inspect existing JavaScript.
6. Inspect existing animation logic.
7. Inspect current button styles.
8. Inspect current hover/focus states.
9. Inspect current responsive behavior.
10. Identify design inconsistencies.
11. Identify visual areas that still look unfinished, flat, generic, or unpolished.

Do not immediately rewrite working sections.

Improve the existing implementation instead of unnecessarily rebuilding it.

---

# 2. Preserve the Approved Homepage Structure

The homepage layout has already been manually recreated and should be treated as the approved structural direction.

Do not:

- Rearrange major sections without a clear reason
- Remove important sections
- Replace the existing layout with a generic AI layout
- Rebuild the homepage using a different page builder
- Replace Gutenberg with hardcoded page content
- Introduce unnecessary plugins
- Change unrelated website functionality

You may improve:

- Spacing
- Alignment
- Typography
- Interactive states
- Animation
- Visual consistency
- Section transitions
- Responsive behavior
- Card presentation
- CTA emphasis
- Navigation polish
- Accessibility
- Overall visual quality

---

# 3. Establish the Final Homepage Design System

Use the existing homepage to establish a consistent reusable design system.

Review and normalize:

## Colors

Identify and consistently use:

- Primary color
- Secondary color
- Accent color
- Dark text color
- Light text color
- Background colors
- Border colors
- Hover colors
- Active colors

Avoid introducing slightly different color values for similar components unless intentionally required by the design.

Prefer existing CSS variables or create reusable design tokens when appropriate.

## Typography

Standardize:

- H1
- H2
- H3
- H4
- Body text
- Small labels
- Eyebrow text
- Buttons
- Navigation
- Footer text

Maintain consistent:

- Font family
- Font weight
- Line height
- Letter spacing
- Responsive scaling

Use `clamp()` where appropriate for responsive typography.

Avoid inconsistent heading sizes between visually similar sections.

## Spacing

Review:

- Section top/bottom padding
- Container spacing
- Column gaps
- Card gaps
- Text-to-button spacing
- Heading-to-paragraph spacing
- Mobile spacing

The homepage should feel intentional and balanced.

Avoid:

- Sections that feel compressed
- Sections with excessive empty space
- Inconsistent padding between similar sections
- Uneven card spacing

---

# 4. Button System — Mandatory

Buttons must have complete interactive states.

Every primary, secondary, outline, CTA, and navigation-style button should have:

- Default state
- Hover state
- Keyboard focus state
- Active/pressed state where appropriate
- Disabled state if applicable

## Mandatory Hover Color Rule

Every important button must visibly switch from one established website brand color to another clearly different website brand color on hover.

Example brand palette:

- Red
- Blue
- Black

Valid examples:

```text
Default: Red
Hover: Blue
```

```text
Default: Blue
Hover: Black
```

```text
Default: Black
Hover: Red
```

The hover color must be a genuinely different brand color.

### Not Allowed

Do not use only:

- Faded version of the same color
- Lower opacity
- Higher opacity
- Darker shade of the same color
- Lighter shade of the same color
- Brightness filter
- Saturation filter
- Shadow-only change
- Glow-only change
- Border-only change
- Transform-only effect

as the button's main hover feedback.

Example that is NOT acceptable:

```text
Default: #D9272E
Hover: #B82127
```

if both are simply red variations.

Instead:

```text
Default: Red
Hover: Blue
```

or another clearly different brand color.

The hover state must remain consistent with the website's established palette.

---

# 5. Button Icon Rule

If a button contains an icon:

- Do not animate the icon on hover.
- Do not rotate the icon.
- Do not slide the icon.
- Do not scale the icon.
- Do not bounce the icon.
- Do not add a separate icon hover animation.

Changing the button background color is enough.

If the icon inherits `currentColor`, changing its color automatically for contrast is acceptable.

The icon itself should remain visually stable.

Example:

```css
.site-button {
    background: var(--color-red);
    color: #fff;
}

.site-button:hover {
    background: var(--color-blue);
    color: #fff;
}
```

---

# 6. Button Hover Transition

Use a smooth color transition.

Recommended:

```css
transition:
    background-color 0.25s ease,
    color 0.25s ease,
    border-color 0.25s ease;
```

A small transform may be used only if it suits the design, but it should never replace the required color change.

Do not make buttons jump, bounce, or shift unnecessarily.

The primary hover feedback is:

**COLOR CHANGE**

---

# 7. Button Contrast and Audit

Verify:

- Hover text remains readable.
- Background and text contrast remain accessible.
- Border color works with the hover state.
- Buttons remain clear on light and dark sections.
- Every major button changes to a distinctly different brand background color.
- No faded same-color hover remains.
- No unnecessary icon animation remains.
- Keyboard focus state remains visible.

Do not mark the homepage complete if any major button still uses a faded or same-color hover treatment.

---

# 8. Navigation Hover and Active States

Review header and footer navigation.

Every navigation item should have intentional:

- Hover state
- Keyboard focus state
- Active/current-page state

The active/current page state should remain visibly highlighted even when the pointer is not hovering.

Where appropriate, use the same or closely related visual treatment as hover.

For dropdown menus:

- Parent hover must be clear.
- Dropdown links must have visible hover states.
- Current child page should remain highlighted.
- Parent/ancestor item should remain active where appropriate.

Do not rely on browser-default link styling.

---

# 9. Homepage Animation — Mandatory

The finalized homepage must not feel completely static or flat.

Add meaningful animation effects throughout the homepage.

Animation is required, but it must be intentional and appropriate to:

- The nature of the business
- The content
- The section layout
- The visual design
- The target audience

Do not apply one repeated animation to most of the page.

The homepage must not look like:

```text
Section 1 → slide
Section 2 → slide
Section 3 → slide
Section 4 → slide
Section 5 → slide
```

This is considered incomplete.

Animation must vary according to:

- Section layout
- Content type
- Visual importance
- Business style
- Position of elements

---

# 10. Create an Animation Plan Before Coding

Before modifying animation code, review the entire homepage and create an internal animation map.

Example only:

| Section | Main Effect | Supporting Effect |
|---|---|---|
| Hero | Fade + rise | CTA fade |
| Feature icons | Pop-in stagger | None |
| Services | Card stagger upward | Hover lift |
| About split | Left/right reveal | None |
| Statistics | Count-up + fade | Stagger |
| Testimonials | Soft scale/fade | None |
| CTA | Background reveal | Content rise |
| Footer | Simple fade | None |

Do not blindly use this exact map.

Determine suitable effects from the actual homepage.

Mandatory rule:

**Adjacent sections should not all use the same animation pattern.**

If most sections use the same animation class, revise the animation plan.

---

# 11. Recommended Animation Vocabulary

Use a controlled mix of effects.

## Fade + Rise

Suitable for:

- Introductory copy
- Centered headings
- CTA text

Example:

```text
opacity 0 → 1
translateY(24px) → 0
```

## Left Reveal

Suitable for:

- Left-side content
- Alternating sections

Example:

```text
opacity 0 → 1
translateX(-30px) → 0
```

## Right Reveal

Suitable for:

- Right-side content
- Alternate split sections

Example:

```text
opacity 0 → 1
translateX(30px) → 0
```

## Scale / Pop-In

Suitable for:

- Icons
- Small feature cards
- Badges
- Short highlighted content

Example:

```text
opacity 0 → 1
scale(0.92) → scale(1)
```

Keep this subtle.

## Staggered Cards

Suitable for:

- Services
- Benefits
- Features
- Team cards

Cards should enter sequentially instead of appearing at the same time.

## Statistics

If actual numerical statistics exist:

- Use count-up when technically appropriate.
- Combine with subtle opacity/rise.

Do not use fake counting effects for non-numeric content.

## Decorative Motion

Decorative design elements may use:

- Slow rotation
- Floating
- Gentle circular movement
- Subtle pulse

Only use this on actual decorative elements.

Do not rotate important text, buttons, or functional icons.

---

# 12. Animation by Content Type

Use motion based on what the section communicates.

## Hero Section

Possible effects:

- Headline fade/rise
- Supporting text delayed slightly
- CTA delayed slightly
- Visual area reveal
- Decorative accent movement

Keep hero animation polished and not overly theatrical.

## Services / Cards

Possible effects:

- Staggered card entrance
- Slight scale/pop
- Small upward movement
- Icon pop
- Hover lift

## About / Mission Split Sections

If text is on the left:

- Text may slide from left.

If visual content is on the right:

- Visual area may slide from right.

Reverse the direction when the layout is reversed.

## Statistics

Possible effects:

- Count-up numbers
- Fade/rise
- Subtle stagger

## Testimonials

Possible effects:

- Fade
- Soft slide
- Gentle scale-in

Avoid aggressive motion that distracts from reading.

## CTA Sections

Possible effects:

- Background reveal
- CTA content rise
- Button appearance
- Subtle decorative movement

## Decorative Shapes / Icons

Depending on the design and business:

- Floating
- Rotating
- Circling
- Pulsing

may be used carefully.

---

# 13. Animation Must Match the Nature of the Business

Do not use one universal motion style for every website.

## Healthcare / Senior Care / Professional Services

Prefer:

- Smooth fades
- Gentle slides
- Soft upward reveals
- Slow card lifts
- Subtle icon movement

Avoid:

- Aggressive bounce
- Fast spinning
- Flashing
- Chaotic movement

## Children's / Education Websites

More playful motion may be appropriate:

- Pop-in
- Gentle bounce
- Floating elements
- Small rotations
- Playful staggered cards

Keep it professional and usable.

## Transportation / Automotive

Possible motion:

- Horizontal slide
- Directional movement
- Progressive reveals
- Subtle speed-inspired transitions

Avoid gimmicky motion.

## Corporate / Financial / Legal

Prefer:

- Clean fades
- Small slides
- Controlled reveals
- Minimal scaling

Motion should feel confident and professional.

## Creative / Entertainment

More expressive effects may be used where the design supports them.

---

# 14. Animation Timing and Delay — Mandatory

Animations must be slow enough for users to appreciate the transition, but not so slow that the interface feels unresponsive.

Do not make major entrance animations happen so quickly that elements appear to simply pop into place.

## Default Entrance Duration

For normal section/content entrance animations use approximately:

```text
700ms–1100ms
```

Recommended default:

```text
850ms–950ms
```

Do not normally use:

```text
200ms
300ms
400ms
```

for major homepage entrance animations.

Those timings are too fast for the intended visual experience.

Small supporting interactions may use shorter durations.

---

# 15. Initial Animation Delay

Important section content should use a small intentional delay after entering the viewport.

Recommended:

```text
100ms–250ms
```

Example:

```css
transition:
    opacity 900ms cubic-bezier(0.22, 1, 0.36, 1) 150ms,
    transform 900ms cubic-bezier(0.22, 1, 0.36, 1) 150ms;
```

This gives the user enough time to perceive the transition.

---

# 16. Stagger Related Elements

For grouped content such as:

- Service cards
- Feature cards
- Statistics
- Testimonials
- Icons
- Benefits

do not animate every item simultaneously.

Use staggered delays.

Recommended example:

```text
Item 1 → 100ms
Item 2 → 220ms
Item 3 → 340ms
Item 4 → 460ms
```

Typical stagger interval:

```text
100ms–180ms
```

This should create a visible sequence without making the user wait excessively.

---

# 17. Section-Level Choreography

Do not animate every element in a section at exactly the same moment.

For important sections, use a short sequence.

Example:

```text
Section heading      → 100ms
Supporting text      → 250ms
Cards/content        → 400ms
CTA/button           → 550ms
```

This creates deliberate visual flow.

Do not exaggerate the delays beyond what feels natural.

---

# 18. Different Sections Need Different Timing

Animation variety includes timing, not just direction.

Do not apply the exact same duration and delay to every section.

Example variation:

| Section | Effect | Duration | Delay |
|---|---|---:|---:|
| Hero headline | Fade + rise | 950ms | 150ms |
| Hero supporting copy | Fade | 900ms | 300ms |
| Hero CTA | Fade + rise | 800ms | 450ms |
| Feature icons | Pop stagger | 750ms | 120ms intervals |
| Services cards | Rise stagger | 900ms | 140ms intervals |
| About content | Side reveal | 1000ms | 180ms |
| Statistics | Fade/count | 900ms | 120ms intervals |
| Testimonials | Soft scale | 1000ms | 200ms |
| CTA | Fade + rise | 950ms | 180ms |

This table is only an example.

Choose timing according to the actual design and content.

---

# 19. Hero Animation Timing

The hero is the first impression and should feel deliberate.

Suggested sequence:

```text
Eyebrow / small label
        ↓
100–150ms

Main heading
        ↓
150–250ms later

Supporting paragraph
        ↓
150–200ms later

CTA
        ↓
150–200ms later

Visual/image area
        ↓
may enter separately
```

The entire hero sequence should generally complete within approximately:

```text
1.5–2.5 seconds
```

after the page is ready.

Do not delay the hero excessively.

---

# 20. Scroll Entrance Trigger

Do not wait until the entire section is centered on screen before triggering the animation.

Trigger slightly before the content reaches the primary viewing area.

Recommended Intersection Observer behavior:

```js
rootMargin: "0px 0px -10% 0px"
```

or another sensible threshold.

This gives the motion room to complete naturally as the user scrolls toward the section.

---

# 21. Smoothness Is Mandatory

Animations must feel smooth.

Avoid:

- Jerky movement
- Large travel distances
- Sudden snapping
- Excessive bouncing
- Long blocking delays
- Multiple transforms fighting each other
- Animation restart loops
- Layout shifts

Recommended entrance movement:

```text
20px–40px
```

Recommended durations:

```text
700ms–1100ms
```

Recommended easing:

```css
cubic-bezier(0.22, 1, 0.36, 1)
```

or another smooth easing appropriate to the design.

---

# 22. Avoid Laggy Animation

Longer animation does not mean heavier animation.

Prefer GPU-friendly properties:

```text
transform
opacity
```

Avoid frequently animating:

```text
width
height
margin
padding
top
left
```

when transform-based alternatives exist.

A smooth `900ms` transform is better than a laggy `500ms` layout animation.

---

# 23. Do Not Over-Delay

Animation should be noticeable but should never block content.

Avoid normal content delays such as:

```text
1000ms
1500ms
2000ms
```

before the element even begins appearing.

For ordinary section entrances, keep the initial delay generally below:

```text
400ms
```

Use cumulative stagger carefully.

The target is:

**Visible → smooth → deliberate → polished**

not:

**Fast → barely noticeable**

and not:

**Slow → frustrating**

---

# 24. Do Not Replay Constantly

Normal entrance animations should generally play once when the visitor first reaches the section.

Do not repeatedly replay animations whenever the user slightly scrolls the element in and out of the viewport.

Continuous movement should be reserved only for subtle decorative elements where appropriate.

---

# 25. Reusable Animation Timing Tokens

Create reusable timing variables where appropriate.

Example:

```css
:root {
    --motion-fast: 500ms;
    --motion-normal: 900ms;
    --motion-slow: 1050ms;

    --motion-delay-sm: 100ms;
    --motion-delay-md: 200ms;
    --motion-delay-lg: 350ms;

    --motion-ease: cubic-bezier(0.22, 1, 0.36, 1);
}
```

Use these as a consistent foundation while still varying animation per section.

---

# 26. Do Not Animate Everything

Animation variety does not mean every element needs movement.

Do not independently animate:

- Every paragraph
- Every word
- Every small icon
- Every button
- Every decorative line

Animate meaningful visual groups.

The goal is:

**dynamic and polished**

not:

**constantly moving**

---

# 27. Reusable Animation Classes

Use a small reusable animation vocabulary such as:

```text
.reveal-up
.reveal-left
.reveal-right
.reveal-scale
.reveal-fade
.stagger-group
.float-soft
.rotate-soft
```

Do not create dozens of nearly identical classes.

Reuse a controlled set while applying them differently across sections.

---

# 28. Intersection Observer

Prefer one reusable Intersection Observer.

Expected behavior:

```text
Element has animation class
        ↓
Element enters viewport
        ↓
Add active class
        ↓
CSS performs animation
```

Do not create a separate observer for every section unless technically necessary.

---

# 29. Reduced Motion and Accessibility

Animation must respect:

```css
@media (prefers-reduced-motion: reduce)
```

When reduced motion is requested:

- Disable non-essential transforms.
- Remove unnecessary transitions.
- Keep content immediately visible.
- Preserve functionality.

The homepage must remain usable if JavaScript is unavailable.

Do not hide important content permanently until JavaScript executes.

---

# 30. Hover Effects Beyond Buttons

Review interactive homepage components.

Add appropriate hover states to:

- Service cards
- Feature cards
- Linked images
- Icons
- Social links
- Read More links
- CTA links
- Navigation
- Footer links
- Clickable content panels

Possible effects:

- Background color change
- Border color change
- Shadow change
- Small translate
- Icon color change
- Text color change
- Underline animation

Do not make every component use the same hover behavior.

Use effects appropriate to the component.

Button icons remain subject to the separate Button Icon Rule and should not receive movement animations.

---

# 31. Card Polish

Review all cards for consistency.

Verify:

- Equal visual height where appropriate
- Consistent padding
- Consistent radius
- Consistent shadows
- Correct image areas
- Proper heading hierarchy
- Button/link alignment
- Hover behavior
- Responsive stacking

Cards should feel intentionally designed rather than default Gutenberg boxes.

---

# 32. Section Backgrounds and Transitions

Review transitions between homepage sections.

The page should not feel like a sequence of disconnected rectangular blocks.

Where appropriate, improve:

- Background alternation
- Subtle gradients
- Decorative shapes
- Section dividers
- Overlapping elements
- Border treatments
- Controlled whitespace
- Visual continuity

Do not overdecorate.

The page should remain professional and easy to scan.

---

# 33. Header Finalization

Review the header for:

- Logo size
- Navigation spacing
- Header height
- CTA placement
- Dropdown styling
- Sticky behavior if already part of the design
- Hover states
- Active states
- Mobile navigation
- Tablet behavior

If the header becomes sticky:

- Ensure it does not cover content.
- Ensure mobile behavior remains usable.
- Avoid unnecessary layout shift.

Do not add sticky behavior if it does not fit the approved design.

---

# 34. Hero / Banner Finalization

Review:

- Heading readability
- CTA prominence
- Text width
- Vertical alignment
- Image/visual placement
- Overlay
- Background
- Minimum height
- Large-screen behavior
- Mobile stacking
- Animation sequencing

The first viewport should clearly communicate:

- Who the organization is
- What it offers
- What the primary CTA is

Do not change approved business content unless necessary for presentation.

---

# 35. Footer Finalization

Review:

- Footer layout
- Column widths
- Link hierarchy
- Contact information
- Social icons
- CTA
- Copyright
- Legal links
- Hover states
- Active states
- Mobile stacking

The footer should feel like part of the same design system as the homepage.

---

# 36. Forms on the Homepage

If the homepage contains a form:

Improve:

- Field appearance
- Labels
- Spacing
- Validation
- Required indicators
- Focus states
- Button hover behavior
- Mobile layout

Do not change:

- Recipient
- Submission destination
- Field data
- Existing backend workflow

unless explicitly instructed.

---

# 37. Responsive Finalization

Test and polish the homepage at approximately:

## Large Desktop

- 1920px
- 2000px+

## Standard Desktop

- 1366px–1440px

## Laptop

- 1024px–1280px

## Tablet

- 768px–1023px

## Mobile

- 320px–767px

Verify:

- No horizontal overflow
- Proper section stacking
- Proper typography scaling
- Balanced spacing
- Proper card widths
- Correct navigation behavior
- Usable buttons
- Proper image sizing
- Animation remains smooth
- Animation delay remains appropriate
- Hover-dependent interactions are not required on touch devices

Do not only optimize for the desktop layout.

---

# 38. Gutenberg Integrity

The homepage should remain editable through Gutenberg.

Do not convert the page into one giant hardcoded template merely to achieve visual polish.

Preserve editable:

- Text
- Headings
- Buttons
- Lists
- Images
- Main content sections

Use semantic CSS classes when additional styling is required.

Reuse existing theme components and block structures.

---

# 39. CSS Organization

Use the project's existing CSS organization.

Prefer:

`style.css`

for:

- Global colors
- Typography
- Buttons
- Header
- Footer
- Global hover states
- Shared interactions
- Shared animation tokens

Use existing homepage/theme stylesheets where already established for homepage-specific sections.

Do not create unnecessary new CSS files.

Avoid:

- Duplicate CSS
- Excessive inline styles
- Excessive `!important`
- Fragile selectors
- Generated Gutenberg IDs
- Deep DOM selectors

---

# 40. JavaScript Organization

Use existing JavaScript where practical.

If animation/intersection logic is required:

- Keep it reusable.
- Keep it lightweight.
- Avoid separate scripts for every section.
- Avoid multiple observers performing the same job.
- Avoid unnecessary libraries.
- Trigger normal entrance animations once.
- Avoid heavy scroll listeners.

Use data attributes or semantic classes when helpful.

---

# 41. Performance Check

Before considering the homepage complete, verify that polish has not introduced unnecessary performance problems.

Avoid:

- Large animation dependencies
- Continuous expensive animations
- Excessive DOM manipulation
- Heavy scroll listeners
- Repeated layout calculations
- Duplicate scripts
- Animation of expensive layout properties

Prefer:

- Intersection Observer
- `transform`
- `opacity`

---

# 42. Section Animation Diversity Check

Before considering the homepage complete, create an internal checklist for every major homepage section.

For each section record:

```text
Section Name
Entrance Effect
Supporting Effect
Duration
Delay / Stagger
```

Then verify:

- Not every section uses the same movement.
- Adjacent sections do not repeatedly use identical animation.
- Animation direction makes sense with the layout.
- Important sections receive stronger emphasis.
- Supporting sections use calmer motion.
- Decorative movement is restrained.
- Major animation duration is generally 700ms–1100ms.
- Visitors can clearly perceive the transition.
- Animation does not feel sluggish.

If most sections use the same animation class, effect, or timing, revise the animation plan.

---

# 43. Final Homepage Design Audit

Review the complete homepage as one experience.

## Visual

Verify:

- Design feels polished.
- Colors are consistent.
- Typography is consistent.
- Spacing is balanced.
- Cards are consistent.
- CTA hierarchy is clear.
- Header and footer match.
- Homepage does not look like default Gutenberg output.
- Homepage does not look generic or unfinished.

## Buttons

Verify:

- Every major button changes to a distinctly different brand background color.
- Hover is not merely a faded version of the same color.
- Hover is not merely a darker/lighter version of the same color.
- Button icons do not move or animate.
- Hover colors match the brand.
- Focus states are visible.
- Text contrast remains readable.

## Navigation

Verify:

- Header hover states work.
- Footer hover states work.
- Active/current states work.
- Dropdown states work.

## Animation

Verify:

- Homepage contains intentional visual motion.
- Different sections use different suitable effects.
- Adjacent sections are not monotonous.
- Major animations are not too fast.
- Visitors can appreciate the transition.
- Related elements use stagger where appropriate.
- Timing varies according to content and importance.
- Animations are smooth.
- Animations do not lag.
- Animations do not cause layout shifts.
- Animations match the nature of the business.
- Reduced-motion behavior works.
- Content remains accessible without animation.

## Responsive

Verify:

- Large desktop works.
- Standard desktop works.
- Laptop works.
- Tablet works.
- Mobile works.
- No horizontal overflow exists.
- Animations remain smooth on mobile.

## Gutenberg

Verify:

- Editable content remains Gutenberg-manageable.
- No unnecessary hardcoding was introduced.

Fix safe issues found during this audit before completing the action.

---

# 44. Homepage Becomes the Final Design Reference

After this finalization action is complete:

**Do not continue redesigning the homepage while building internal pages unless a genuine bug or shared design-system issue is discovered.**

The finalized homepage becomes the primary design reference for all internal-page work.

Internal pages should inherit:

- Brand colors
- Typography
- Button styles
- Button hover color behavior
- Navigation interaction
- Animation language
- Animation timing
- Cards
- Spacing
- Section backgrounds
- Borders
- Radius
- Shadows
- Responsive behavior

Do not let internal-page work gradually create a second unrelated design system.

---

# Completion Report

After finalizing the homepage, provide:

## Homepage Areas Reviewed

List the major sections reviewed.

## Visual Improvements

Report:

- Typography improvements
- Spacing improvements
- Card improvements
- Section improvements
- Header/footer improvements

## Interaction Improvements

Report:

- Button default → hover color mappings
- Navigation hover/active changes
- Card hover effects
- Other interaction improvements

## Animation Improvements

Report:

- Animation system used
- Animation map by section
- Types of animation applied
- Typical duration ranges
- Delay/stagger behavior
- Reduced-motion handling

## Responsive Improvements

Report:

- Desktop fixes
- Tablet fixes
- Mobile fixes
- Large-screen fixes

## Files Modified

List:

- CSS files
- JavaScript files
- PHP/theme files
- Gutenberg content changes

## Remaining Manual Work

Clearly identify anything still requiring manual attention.

---

# Final Rule

This action must be completed before starting the internal-page prompts.

The homepage must first become the polished, final visual and interaction standard for the website.

Do not treat the homepage as complete simply because all sections exist.

A complete homepage must also include:

- Consistent styling
- Proper button color-changing hover states
- No faded same-color button hover behavior
- Stable button icons with no unnecessary hover animation
- Intentional hover interactions
- Varied section animation effects
- Smooth animation timing
- Deliberate animation delays
- Staggered related content where appropriate
- Responsive polish
- Accessibility
- Visual hierarchy
- Design consistency
- Gutenberg editability

Only after the homepage passes this finalization should the Internal Pages Planning Instruction and subsequent page actions begin.
