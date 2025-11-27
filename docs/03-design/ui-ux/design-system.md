# Design System - Petty Cash App

## Overview

Dokumen ini merupakan spesifikasi design system untuk Petty Cash App yang bertujuan untuk mendefinisikan standar visual dan komponen UI yang digunakan, yaitu: memastikan konsistensi desain di seluruh aplikasi dengan pendekatan iOS-style yang modern dan user-friendly.

## Design Principles

### iOS-Style Guidelines

Aplikasi ini mengadopsi prinsip-prinsip desain Apple iOS, antara lain:

1. **Spring Physics** - Animasi natural dengan efek bouncy yang terasa native
2. **Press Feedback** - Scale-down effect (0.97) saat element di-tap
3. **Glass Effect** - Frosted glass dengan backdrop blur
4. **Haptic Feedback** - Vibration API untuk tactile response
5. **Staggered Animations** - Sequential entrance animations untuk lists
6. **Gesture Recognition** - Swipe-to-delete, drag-to-dismiss

## Color Palette

### Primary Colors

| Name | Hex | Usage |
|------|-----|-------|
| Blue 600 | #2563EB | Primary actions, links |
| Blue 700 | #1D4ED8 | Primary hover |
| Indigo 700 | #4338CA | Gradients, accents |
| Indigo 800 | #3730A3 | Dark gradient end |

### Category Colors

| Category | Hex | Background (20%) |
|----------|-----|------------------|
| Office Supplies | #3B82F6 | rgba(59, 130, 246, 0.2) |
| Food & Beverages | #F97316 | rgba(249, 115, 22, 0.2) |
| Transportation | #8B5CF6 | rgba(139, 92, 246, 0.2) |
| Miscellaneous | #6B7280 | rgba(107, 114, 128, 0.2) |
| Other | #9CA3AF | rgba(156, 163, 175, 0.2) |

### Semantic Colors

| Name | Light Mode | Dark Mode | Usage |
|------|------------|-----------|-------|
| Success | #10B981 | #34D399 | Positive actions |
| Warning | #F59E0B | #FBBF24 | Warnings, alerts |
| Error | #EF4444 | #F87171 | Errors, destructive |
| Info | #3B82F6 | #60A5FA | Informational |

### Gradient Presets

```css
/* Hero Card - Balance */
.gradient-primary {
    background: linear-gradient(to bottom right, #2563EB, #1D4ED8, #3730A3);
}

/* Success - Fund Added */
.gradient-success {
    background: linear-gradient(to right, #10B981, #059669);
}

/* Warning - Setup Required */
.gradient-warning {
    background: linear-gradient(to right, #F59E0B, #EA580C);
}

/* Edit Mode */
.gradient-amber {
    background: linear-gradient(to right, #F59E0B, #EA580C);
}
```

## Typography

### Font Family

```css
font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 
             "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
```

### Font Sizes

| Name | Size | Line Height | Usage |
|------|------|-------------|-------|
| xs | 0.75rem (12px) | 1rem | Labels, hints |
| sm | 0.875rem (14px) | 1.25rem | Secondary text |
| base | 1rem (16px) | 1.5rem | Body text |
| lg | 1.125rem (18px) | 1.75rem | Subheadings |
| xl | 1.25rem (20px) | 1.75rem | Card titles |
| 2xl | 1.5rem (24px) | 2rem | Section headers |
| 3xl | 1.875rem (30px) | 2.25rem | Page titles |
| 4xl | 2.25rem (36px) | 2.5rem | Hero numbers |
| 5xl | 3rem (48px) | 1 | Large balance display |

### Font Weights

| Weight | Value | Usage |
|--------|-------|-------|
| Normal | 400 | Body text |
| Medium | 500 | Labels, buttons |
| Semibold | 600 | Card titles |
| Bold | 700 | Headlines, amounts |

## Spacing System

### Base Unit: 4px

| Name | Size | Usage |
|------|------|-------|
| 1 | 4px | Minimal gap |
| 2 | 8px | Icon gaps |
| 3 | 12px | Compact padding |
| 4 | 16px | Default padding |
| 5 | 20px | Section gap |
| 6 | 24px | Card padding |
| 8 | 32px | Section spacing |
| 10 | 40px | Large gaps |
| 12 | 48px | Hero spacing |

## Border Radius

| Name | Size | Usage |
|------|------|-------|
| sm | 0.125rem | Small badges |
| DEFAULT | 0.25rem | Inputs |
| md | 0.375rem | Buttons |
| lg | 0.5rem | Cards |
| xl | 0.75rem | Larger cards |
| 2xl | 1rem | Category icons |
| 3xl | 1.5rem | Hero elements |
| full | 9999px | Pills, circles |

## Components

### Cards

```vue
<!-- Standard Card -->
<Card class="border-0 shadow-lg">
    <CardHeader>
        <CardTitle>Title</CardTitle>
        <CardDescription>Description</CardDescription>
    </CardHeader>
    <CardContent>
        <!-- Content -->
    </CardContent>
</Card>

<!-- Hero Card (Gradient) -->
<Card class="border-0 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white shadow-xl">
    <!-- Content -->
</Card>
```

### Buttons

```vue
<!-- Primary Button -->
<Button class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700">
    Primary Action
</Button>

<!-- Secondary Button -->
<Button variant="outline">
    Secondary
</Button>

<!-- Destructive Button -->
<Button variant="destructive">
    Delete
</Button>

<!-- Ghost Button -->
<Button variant="ghost">
    Cancel
</Button>
```

### Input Fields

```vue
<Input 
    class="h-12" 
    placeholder="Enter value"
/>

<!-- Large Input (Amount) -->
<Input 
    class="h-14 pl-12 text-2xl font-bold" 
    type="text"
    inputmode="decimal"
/>
```

### Category Selector

```vue
<button
    class="flex flex-col items-center gap-2 rounded-2xl border-2 p-4 transition-all duration-200 active:scale-95"
    :class="{
        'border-blue-500 bg-blue-50': selected,
        'border-gray-200 hover:border-gray-300': !selected,
    }"
>
    <div 
        class="flex h-12 w-12 items-center justify-center rounded-xl"
        :style="{ backgroundColor: color + '20' }"
    >
        <div 
            class="h-4 w-4 rounded-full"
            :style="{ backgroundColor: color }"
        />
    </div>
    <span class="text-xs font-medium">Category Name</span>
</button>
```

## Animations

### Entrance Animations

```css
/* Fade in from bottom */
.animate-in.fade-in.slide-in-from-bottom-4 {
    animation: fadeInUp 0.5s ease-out;
}

/* Staggered delay */
style="animation-delay: 100ms"
style="animation-delay: 150ms"
style="animation-delay: 200ms"
```

### Interactive Feedback

```css
/* Press feedback */
.active\:scale-\[0\.97\]:active {
    transform: scale(0.97);
}

/* Hover scale */
.hover\:scale-110:hover {
    transform: scale(1.1);
}
```

### Transitions

```css
/* Standard transition */
transition-all duration-200

/* Longer animation */
transition-all duration-300

/* Spring-like duration */
transition-all duration-500
```

## Icons

### Icon Library: Lucide Vue Next

```vue
import { 
    Plus, 
    Wallet, 
    ArrowUpRight, 
    ArrowDownRight,
    Pencil,
    Trash2,
    Calendar,
    Filter,
    // ... more icons
} from 'lucide-vue-next';
```

### Icon Sizes

| Size | Class | Usage |
|------|-------|-------|
| Small | h-4 w-4 | Buttons, labels |
| Medium | h-5 w-5 | Card headers |
| Large | h-6 w-6 | Action buttons |
| XL | h-10 w-10 | Hero elements |
| 2XL | h-12 w-12 | Empty states |

## Dark Mode

Aplikasi mendukung dark mode menggunakan Tailwind's dark variant:

```vue
<!-- Background -->
<div class="bg-white dark:bg-gray-950">

<!-- Text -->
<p class="text-gray-900 dark:text-white">

<!-- Borders -->
<div class="border-gray-200 dark:border-gray-800">

<!-- Cards -->
<Card class="dark:bg-gray-900">
```

## Responsive Breakpoints

| Breakpoint | Width | Usage |
|------------|-------|-------|
| sm | 640px | Mobile landscape |
| md | 768px | Tablets |
| lg | 1024px | Laptops |
| xl | 1280px | Desktops |
| 2xl | 1536px | Large screens |

## Component Library

Menggunakan **Shadcn/Vue** dengan komponen:

- Alert
- Avatar
- Badge
- Breadcrumb
- Button
- Card
- Checkbox
- Collapsible
- Dialog
- Dropdown Menu
- Input
- Label
- Navigation Menu
- Pin Input
- Separator
- Sheet
- Sidebar
- Skeleton
- Spinner
- Tooltip

---

*Design system ini akan diperbarui seiring dengan evolusi visual aplikasi.*

