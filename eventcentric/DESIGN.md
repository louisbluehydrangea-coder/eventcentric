---
name: Event Centric
colors:
  surface: '#fbf8ff'
  surface-dim: '#d6d8f1'
  surface-bright: '#fbf8ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f3f2ff'
  surface-container: '#ececff'
  surface-container-high: '#e5e7ff'
  surface-container-highest: '#dfe1f9'
  on-surface: '#171b2c'
  on-surface-variant: '#5a4139'
  inverse-surface: '#2c2f42'
  inverse-on-surface: '#f0efff'
  outline: '#8e7068'
  outline-variant: '#e3bfb4'
  surface-tint: '#af3100'
  primary: '#a92f00'
  on-primary: '#ffffff'
  primary-container: '#d1410c'
  on-primary-container: '#fffaf9'
  inverse-primary: '#ffb59f'
  secondary: '#a93800'
  on-secondary: '#ffffff'
  secondary-container: '#ff6f35'
  on-secondary-container: '#601c00'
  tertiary: '#5c5973'
  on-tertiary: '#ffffff'
  tertiary-container: '#75718d'
  on-tertiary-container: '#fffbff'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#ffdbd1'
  primary-fixed-dim: '#ffb59f'
  on-primary-fixed: '#3a0a00'
  on-primary-fixed-variant: '#852300'
  secondary-fixed: '#ffdbcf'
  secondary-fixed-dim: '#ffb59b'
  on-secondary-fixed: '#380d00'
  on-secondary-fixed-variant: '#812800'
  tertiary-fixed: '#e4dfff'
  tertiary-fixed-dim: '#c8c3e2'
  on-tertiary-fixed: '#1b1930'
  on-tertiary-fixed-variant: '#47445d'
  background: '#fbf8ff'
  on-background: '#171b2c'
  surface-variant: '#dfe1f9'
typography:
  headline-xl:
    fontFamily: Inter
    fontSize: 48px
    fontWeight: '800'
    lineHeight: 56px
    letterSpacing: -0.02em
  headline-lg:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-bold:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
  label-caps:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '700'
    lineHeight: 16px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  xs: 4px
  sm: 12px
  md: 24px
  lg: 48px
  xl: 64px
  container-max: 1080px
  gutter: 24px
---

## Brand & Style

The design system is built to evoke a sense of community, energy, and seamless organization. It targets both event creators who need professional, reliable tools and attendees looking for vibrant, trustworthy experiences. 

The visual style follows a **Corporate Modern** aesthetic with a heavy emphasis on clarity and high-contrast focal points. It prioritizes functional minimalism, using generous white space to allow content (event imagery and titles) to drive the visual narrative. The interface feels established and robust, utilizing a "system-first" approach where hierarchy is clearly defined through weight and color rather than decorative elements.

## Colors

The palette is anchored by a high-energy "Action Orange" (#D1410C), which is used exclusively for primary calls to action, links, and interactive states to ensure maximum conversion. 

- **Primary (#D1410C):** Used for main buttons and critical interactive elements.
- **Secondary (#F6682F):** A brighter orange used for hover states and accents to provide a sense of illumination.
- **Deep Navy (#39364F):** The primary color for typography and iconography, offering better readability and a more premium feel than pure black.
- **Cool Grey (#6F7287):** Reserved for secondary text, metadata, and borders to create clear visual hierarchy.
- **White (#FFFFFF):** The essential canvas for the spacious, clean layout.

## Typography

This design system utilizes **Inter** to replicate the clean, architectural feel of Neue Plak. The typography is the primary driver of hierarchy. 

Headlines use heavy weights (Bold/Extra Bold) with slightly tighter letter spacing to create a grounded, impactful presence. Body text is optimized for legibility with ample line height. Labels and metadata often use a semi-bold weight to distinguish them from standard body copy without needing to change color, maintaining a clean aesthetic.

## Layout & Spacing

The design system employs a **Fixed Grid** model for desktop experiences, centering content within a 1080px container to ensure focus and readability. 

A strict 8px spacing scale governs the rhythm. Padding within components (like cards and buttons) is generous to evoke a premium, "un-cluttered" feel. Layouts should utilize "White Space as a Separator," meaning large gaps (48px+) between major sections are preferred over horizontal dividers.

## Elevation & Depth

The design system utilizes a flat foundation with depth used specifically for interactivity and containment. 

- **Tonal Layers:** Subtle grey backgrounds (#F8F8FA) are used to differentiate the header or specific sections from the main white body.
- **Ambient Shadows:** Cards use a very soft, diffused shadow (0px 4px 15px rgba(0,0,0,0.05)) that intensifies slightly on hover to indicate clickability.
- **Low-Contrast Outlines:** Input fields and secondary containers use 1px solid borders in a light grey (#DBDAE3) to maintain structure without adding visual noise.

## Shapes

The shape language is defined by a "Soft Rounded" approach. A standard radius of 8px (0.5rem) is applied to buttons, input fields, and small UI components. 

For larger elements like Event Cards, a more pronounced radius (up to 16px) is used to soften the overall interface and make the imagery feel more approachable. Buttons are never pill-shaped, but maintain a consistent, structured corner radius that suggests stability.

## Components

### Buttons
- **Primary:** Solid #D1410C background with #FFFFFF text. No border. 8px radius. High-emphasis.
- **Secondary:** Transparent background with #39364F border and text. Used for less critical actions.
- **Text Link:** #D1410C bold text with no underline unless hovered.

### Input Fields
- **Default:** 1px border (#DBDAE3), 8px radius, white background. Labels sit above the field in Navy (#39364F) Semi-bold.
- **Focus:** Border changes to 2px #39364F or #D1410C depending on the context.

### Event Cards
- **Structure:** Vertical stack. Image at the top with a 16px top-corner radius (or full 16px if the card is floating).
- **Content:** The text area below the image has 16px-24px padding. 
- **Typography in Cards:** Title is Headline-md. Subtext (Date/Location) is Body-sm in #6F7287.
- **Interactive State:** The entire card lifts slightly on hover using a deeper shadow.

### Chips/Tags
- **Style:** Light grey background (#F8F8FA) with Navy (#39364F) text. Small font size (12px) and 4px radius. Used for categories or filters.