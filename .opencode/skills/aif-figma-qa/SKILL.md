---
name: aif-figma-qa
description: >-
  Compare rendered page against Figma design at multiple breakpoints (375/768/1200/1920px).
  Extracts structured styles from Figma nodes (text content, font-family, font-size,
  font-weight, color, layout direction) and live DOM getComputedStyle(), then diffs
  per-attribute with exact numeric deltas. Use after UI implementation to verify
  pixel-level fidelity against Figma specs, or when user says "check figma", "design qa",
  "compare to design", "verify ui", "visual qa". Requires Figma MCP and Chrome DevTools MCP.
argument-hint: "[url] [figma-node-375] [figma-node-768] [figma-node-1200] [figma-node-1920]"
disable-model-invocation: false
metadata:
  version: "1.0"
  category: quality
---

# Figma QA — Design vs Implementation Comparison

Rigorous structural comparison of rendered UI against Figma design specs.
Extracts **both sides into structured style data** and diffs per-attribute —
no screenshot eyeballing. Every finding includes exact numeric delta:
`fontSize: design=24px → actual=20px → Δ−4px ⚠️`.

---

## Step 0: Parse Arguments

Expected argument shape:

```
/aif-figma-qa <url> <figma-node-375> <figma-node-768> <figma-node-1200> <figma-node-1920>
```

| Position | Required | Description |
|----------|----------|-------------|
| 1 | Yes | Page URL to check (e.g. `http://bronber_store.test/loyalty`) |
| 2 | Yes* | Figma node ID for 375px (mobile) |
| 3 | No | Figma node ID for 768px (tablet) |
| 4 | Yes* | Figma node ID for 1200px (desktop) |
| 5 | No | Figma node ID for 1920px (large desktop) |

\* At least one Figma node is required. If only one node is given, test at all
breakpoints using that node as reference (less precise but functional).

If arguments are missing, ask:

```
AskUserQuestion: What should I check?

I need:
1. Page URL (e.g. http://bronber_store.test/loyalty)
2. Figma node IDs for breakpoints (at least one)

Example: /aif-figma-qa http://bronber_store.test/loyalty 934:9166 934:9637 934:9408 925:681
```

Store parsed values as `URL`, `NODE_375`, `NODE_768`, `NODE_1200`, `NODE_1920`.

---

## Step 1: Build Breakpoint Matrix

Construct the list of breakpoints to test. Only include breakpoints that have
a Figma node ID:

```
breakpoints = []
if NODE_375  → breakpoints.push({ width: 375, label: "Mobile",  node: NODE_375 })
if NODE_768  → breakpoints.push({ width: 768, label: "Tablet",  node: NODE_768 })
if NODE_1200 → breakpoints.push({ width: 1200, label: "Desktop", node: NODE_1200 })
if NODE_1920 → breakpoints.push({ width: 1920, label: "Large",   node: NODE_1920 })
```

---

## Step 2: For Each Breakpoint — Extract + Compare

Process each breakpoint sequentially. For each `{ width, label, node }`:

### 2.1 Resize + Navigate

```
chromeDevtools_resize_page(width=breakpoint.width, height=900)
chromeDevtools_navigate_page(url=URL, type="url")
```

Wait for page to settle:
```
chromeDevtools_wait_for(text=[first visible heading text from Figma data])
```

### 2.2 Extract Figma Design Data

Use Figma MCP to pull the structured design spec:

```
figma-bridge_scan_text_nodes(nodeId=breakpoint.node)
```

This returns all text nodes with: characters, fontFamily, fontWeight,
fontSize, lineHeightPx, textAlignHorizontal, fills (color), and bounding box.

Also get layout structure:
```
figma-bridge_get_node_info(nodeId=breakpoint.node, depth=2)
```

From the node info, extract:
- Background colors (from `fills[].color`)
- Layout mode (frame `layoutMode` if auto-layout, else infer from children positions)
- Border radius (`cornerRadius`)
- Padding/gap (from auto-layout fields: `paddingTop/Bottom/Left/Right`, `itemSpacing`)
- Child element dimensions and positions

Store as `DESIGN_DATA` — a flat list of elements:
```
{ type: "text", text: "Программа лояльности", fontFamily: "Manrope",
  fontWeight: 800, fontSize: 40, color: "#000000",
  x: 51842, y: 158, width: 253, height: 104 }
{ type: "frame", name: "Benefits card", bgColor: "#ffffff",
  cornerRadius: 5, x: ..., y: ..., width: ..., height: ... }
```

### 2.3 Extract DOM Computed Styles

Run a script to extract computed styles for all visible text elements and
key structural elements:

```javascript
// evaluate_script — extract all text-bearing elements with computed styles
() => {
    const walk = (el) => {
        const style = getComputedStyle(el);
        const rect = el.getBoundingClientRect();
        const ownText = Array.from(el.childNodes)
            .filter(n => n.nodeType === 3)
            .map(n => n.textContent.trim())
            .filter(t => t.length > 0)
            .join(' ');

        // Only include elements that have their own direct text
        if (ownText.length > 0 && rect.width > 0 && rect.height > 0) {
            results.push({
                tag: el.tagName.toLowerCase(),
                text: ownText.substring(0, 200),
                fontFamily: style.fontFamily.replace(/['"]/g, '').split(',')[0].trim(),
                fontSize: parseFloat(style.fontSize),
                fontWeight: parseInt(style.fontWeight),
                lineHeight: parseFloat(style.lineHeight),
                color: style.color,
                textAlign: style.textAlign,
                x: Math.round(rect.left),
                y: Math.round(rect.top),
                width: Math.round(rect.width),
                height: Math.round(rect.height),
            });
        }

        // Also capture key structural elements (sections, cards)
        if (el.tagName === 'SECTION' || el.classList.length > 0 &&
            (el.className.includes('card') || el.className.includes('banner')
             || el.className.includes('hero') || el.className.includes('benefit')
             || el.className.includes('step'))) {
            const bg = style.backgroundColor;
            if (bg !== 'rgba(0, 0, 0, 0)' && bg !== 'transparent') {
                structuralResults.push({
                    className: el.className.baseVal || el.className,
                    bg: bg,
                    borderRadius: style.borderRadius,
                    x: Math.round(rect.left),
                    y: Math.round(rect.top),
                    width: Math.round(rect.width),
                    height: Math.round(rect.height),
                });
            }
        }

        for (const child of el.children) walk(child);
    };

    const results = [];
    const structuralResults = [];
    walk(document.body);
    return { text: results, structural: structuralResults };
}
```

Store the return value as `DOM_DATA`.

### 2.4 Compare: Figma vs DOM

Match elements by **normalized text content** (lowercase, trimmed, first 50 chars).
For each matched pair, compare these properties:

#### Comparison Properties

| Property | Figma field | DOM field | Tolerance | Severity if outside |
|----------|-------------|-----------|-----------|---------------------|
| **Font size** | `fontSize` | `fontSize` | ±2px | MAJOR if >4px diff |
| **Font weight** | `fontWeight` | `fontWeight` | exact | MAJOR |
| **Font family** | `fontFamily` | `fontFamily` | contains | MINOR |
| **Text color** | hex from fills.rgb | rgb from `color` | exact | MAJOR |
| **Text align** | `textAlignHorizontal` | `textAlign` | exact | MINOR |
| **Text content** | `characters` | `text` | normalized match | BLOCKER if missing |

For structural elements (cards, banners):
| Property | Figma field | DOM field | Tolerance |
|----------|-------------|-----------|-----------|
| **Background** | hex from fills.color | `backgroundColor` | exact |
| **Border radius** | `cornerRadius` | `borderRadius` (px) | ±2px |

#### Color Normalization

Figma gives RGB as 0-1 floats. Convert to hex:
```
r = Math.round(fills[0].color.r * 255)
hex = "#" + [r,g,b].map(v => v.toString(16).padStart(2,'0')).join('')
```

DOM gives `rgb(r, g, b)`. Convert to hex the same way.

#### Text Matching Strategy

```
1. Normalize both sides: lowercase, trim, collapse whitespace, strip punctuation
2. For each Figma text node, find the best DOM match:
   a. Exact normalized match → full comparison
   b. Partial match (DOM text contains Figma text or vice versa, >60% overlap) → compare
   c. No match → report as MISSING IN DOM (BLOCKER)
3. For each DOM text element with no Figma match → report as EXTRA IN DOM (MINOR)
```

### 2.5 Record Findings

For each comparison, record:

```
{
    breakpoint: "Mobile (375px)",
    severity: "MAJOR" | "MINOR" | "PASS" | "BLOCKER",
    element: "Title «Программа лояльности»",
    property: "fontSize",
    design: 40,
    actual: 36,
    delta: -4,
    note: "4px smaller than design"
}
```

---

## Step 3: Generate Report

### 3.1 Per-Breakpoint Summary

For each breakpoint, output:

```
## 📱 Mobile (375px)

### Typography
| Element | Property | Design | Actual | Δ | Status |
|---------|----------|--------|--------|---|--------|
| «Программа лояльности» | fontSize | 40px | 40px | 0 | ✅ |
| «Программа лояльности» | fontWeight | 800 | 800 | 0 | ✅ |
| Subtitle text | fontSize | 18px | 16px | -2 | ⚠️ MINOR |
| Button text | text content | "Зарегистрироваться" | "ЗАРЕГИСТРИРОВАТЬСЯ" | case | ⚠️ MINOR |

### Colors
| Element | Property | Design | Actual | Status |
|---------|----------|--------|--------|--------|
| Title | color | #000000 | #000000 | ✅ |
| Bonus value | color | #7212BC | #7212bc | ✅ |

### Layout
| Element | Property | Design | Actual | Status |
|---------|----------|--------|--------|--------|
| Benefits card | background | #FFFFFF | #ffffff | ✅ |
| Benefits card | borderRadius | 5px | 5px | ✅ |

### Missing / Extra
| Issue | Details | Severity |
|-------|---------|----------|
| ❌ Missing in DOM | Figma text «Эксклюзивные акции» not found | BLOCKER |
| ➕ Extra in DOM | DOM element «Debugbar» not in design | MINOR |
```

### 3.2 Overall Verdict

```
## Verdict

| Breakpoint | BLOCKER | MAJOR | MINOR | PASS | Score |
|------------|---------|-------|-------|------|-------|
| Mobile 375 | 0 | 1 | 3 | 22 | 85% |
| Desktop 1200 | 0 | 0 | 1 | 25 | 96% |

Overall: ⚠️ MINOR ISSUES — 1 MAJOR, 4 MINOR across breakpoints
```

Scoring: `PASS_count / total_findings * 100`

### 3.3 Severity Definitions

| Severity | Meaning | Action |
|----------|---------|--------|
| **BLOCKER** | Text content missing, element not rendered | Must fix |
| **MAJOR** | Font size >4px off, wrong font weight, wrong color | Should fix |
| **MINOR** | Font family variant, text-align, case difference | Nice to fix |
| **PASS** | Property matches within tolerance | No action |

---

## Step 4: Suggest Fixes (if findings exist)

If any MAJOR or BLOCKER findings:

```
AskUserQuestion: Found N issues. Fix now?

Options:
1. Fix MAJOR/BLOCKER only — address critical discrepancies
2. Show full report — I'll review and decide
3. Accept as-is — skip fixes
```

If "Fix MAJOR/BLOCKER" → for each finding:
1. Identify the CSS file and selector responsible
2. Calculate the correct value from Figma data
3. Apply the fix via `edit` tool
4. Re-run the comparison for that breakpoint to verify

---

## Important Notes

### What This Skill Does NOT Do
- ❌ Pixel-diff screenshots (use pixelmatch/applitools for that)
- ❌ Test interactivity (clicks, form submission)
- ❌ Validate responsive behavior between breakpoints
- ❌ Check accessibility (use `/aif-security-checklist` instead)

### Limitations
- Text matching is fuzzy — short text strings may match incorrectly
- `getComputedStyle` returns resolved values, not authored CSS
- Figma auto-layout padding/gap maps imperfectly to flexbox
- Elements hidden by `display:none` are excluded (correct behavior)

### Figma MCP Tools Used
- `figma-bridge_scan_text_nodes` — all text nodes with styles
- `figma-bridge_get_node_info` — layout structure, fills, radius
- `figma-bridge_get_nodes_info` — batch node info

### Chrome DevTools MCP Tools Used
- `chromeDevtools_resize_page` — set viewport width
- `chromeDevtools_navigate_page` — go to URL
- `chromeDevtools_evaluate_script` — extract computed styles
- `chromeDevtools_wait_for` — wait for render
- `chromeDevtools_take_snapshot` — DOM structure reference
