from pathlib import Path
from xml.sax.saxutils import escape
import math, zipfile, subprocess, tempfile, shutil
from PIL import Image, ImageDraw, ImageFont
from fontTools.ttLib import TTFont
from fontTools.pens.svgPathPen import SVGPathPen
from fontTools.pens.transformPen import TransformPen
from fontTools.pens.boundsPen import BoundsPen

ROOT = Path('/Users/hammadarshad/Documents/white-sea-star/WSS/brand/wss-logos')
SVG_DIR = ROOT / 'svg'
PNG_DIR = ROOT / 'png'
for d in (SVG_DIR, PNG_DIR):
    d.mkdir(parents=True, exist_ok=True)

NAVY = '#0A1B3D'
NAVY_2 = '#0E2A55'
GOLD = '#B99D6D'
GOLD_BRIGHT = '#C9A227'
CREAM = '#F7F2E7'
WHITE = '#FFFFFF'

FONT_DIDOT = '/System/Library/Fonts/Supplemental/Didot.ttc'
FONT_BASKERVILLE = '/System/Library/Fonts/Supplemental/Baskerville.ttc'
FONT_GILL = '/System/Library/Fonts/Supplemental/GillSans.ttc'

class Font:
    def __init__(self, path, index=0):
        self.font = TTFont(path, fontNumber=index)
        self.upem = self.font['head'].unitsPerEm
        self.glyphset = self.font.getGlyphSet()
        self.cmap = self.font.getBestCmap()

    def path(self, text, size, tracking=0):
        scale = size / self.upem
        cursor = 0.0
        parts = []
        bounds = None
        for ch in text:
            gname = self.cmap.get(ord(ch))
            if gname is None:
                cursor += size * 0.3
                continue
            glyph = self.glyphset[gname]
            pen = SVGPathPen(self.glyphset)
            glyph.draw(TransformPen(pen, (scale, 0, 0, -scale, cursor, 0)))
            d = pen.getCommands()
            if d:
                parts.append(d)
            bpen = BoundsPen(self.glyphset)
            glyph.draw(TransformPen(bpen, (scale, 0, 0, -scale, cursor, 0)))
            if bpen.bounds:
                b = bpen.bounds
                if bounds is None:
                    bounds = list(b)
                else:
                    bounds = [min(bounds[0], b[0]), min(bounds[1], b[1]), max(bounds[2], b[2]), max(bounds[3], b[3])]
            cursor += glyph.width * scale + tracking
        if bounds is None:
            bounds = [0, 0, 0, 0]
        return ' '.join(parts), tuple(bounds), cursor - tracking

didot = Font(FONT_DIDOT, 0)
didot_bold = Font(FONT_DIDOT, 2)
bask = Font(FONT_BASKERVILLE, 0)
bask_bold = Font(FONT_BASKERVILLE, 1)
gill = Font(FONT_GILL, 0)
gill_bold = Font(FONT_GILL, 1)


def centered(text, font, size, cx, cy, color, tracking=0):
    d, (x0, y0, x1, y1), w = font.path(text, size, tracking)
    tx = cx - (x0 + x1) / 2
    ty = cy - (y0 + y1) / 2
    return f'<path d="{d}" fill="{color}" transform="translate({tx:.2f} {ty:.2f})"/>'


def left(text, font, size, x, baseline, color, tracking=0):
    d, (x0, y0, x1, y1), w = font.path(text, size, tracking)
    tx = x - x0
    ty = baseline
    return f'<path d="{d}" fill="{color}" transform="translate({tx:.2f} {ty:.2f})"/>'


def star(cx, cy, r, color):
    # Four-point curved star.
    return (
        f'<path d="M {cx} {cy-r} '
        f'C {cx+0.08*r} {cy-0.18*r}, {cx+0.18*r} {cy-0.08*r}, {cx+r} {cy} '
        f'C {cx+0.18*r} {cy+0.08*r}, {cx+0.08*r} {cy+0.18*r}, {cx} {cy+r} '
        f'C {cx-0.08*r} {cy+0.18*r}, {cx-0.18*r} {cy+0.08*r}, {cx-r} {cy} '
        f'C {cx-0.18*r} {cy-0.08*r}, {cx-0.08*r} {cy-0.18*r}, {cx} {cy-r} Z" '
        f'fill="{color}"/>'
    )


def svg_doc(w, h, body):
    return (
        f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {w} {h}" width="{w}" height="{h}">\n'
        f'{body}\n</svg>\n'
    )


def colorway(dark):
    return {
        'text': CREAM if dark else NAVY,
        'text2': GOLD if dark else GOLD,
        'muted': CREAM if dark else NAVY,
        'gold': GOLD if dark else GOLD,
        'badge_fill': NAVY_2 if dark else NAVY,
        'badge_stroke': GOLD,
    }


def concept_1(dark):
    c = colorway(dark)
    body = []
    body.append(star(600, 250, 62, c['gold']))
    body.append(centered('WSS', didot, 430, 600, 620, c['text'], 8))
    body.append(f'<line x1="360" y1="840" x2="840" y2="840" stroke="{c["gold"]}" stroke-width="2"/>')
    body.append(centered('WHITE SEA STAR', gill_bold, 34, 600, 902, c['gold'], 10))
    body.append(centered('GENERAL TRADING L.L.C', gill, 23, 600, 952, c['muted'], 6))
    return svg_doc(1200, 1200, '\n'.join(body))


def concept_2(dark):
    c = colorway(dark)
    body = []
    body.append(f'<circle cx="600" cy="600" r="470" fill="{c["badge_fill"]}" stroke="{c["badge_stroke"]}" stroke-width="3"/>')
    body.append(f'<circle cx="600" cy="600" r="500" fill="none" stroke="{c["gold"]}" stroke-width="1" opacity="0.7"/>')
    body.append(star(600, 300, 50, c['gold']))
    body.append(centered('WSS', didot, 300, 600, 610, CREAM if not dark else CREAM, 6))
    body.append(centered('WHITE SEA STAR', gill_bold, 26, 600, 800, c['gold'], 8))
    body.append(centered('EST. 2009', gill, 20, 600, 852, CREAM, 7))
    return svg_doc(1200, 1200, '\n'.join(body))


def concept_3(dark):
    c = colorway(dark)
    body = []
    body.append(f'<path d="M 600 110 L 1090 600 L 600 1090 L 110 600 Z" fill="none" stroke="{c["gold"]}" stroke-width="4"/>')
    body.append(star(600, 230, 42, c['gold']))
    body.append(centered('WSS', didot_bold, 300, 600, 600, c['text'], 4))
    body.append(centered('WHITE SEA STAR', gill_bold, 23, 600, 790, c['gold'], 8))
    body.append(centered('EST. 2009', gill, 19, 600, 840, c['muted'], 6))
    return svg_doc(1200, 1200, '\n'.join(body))


def concept_4(dark):
    c = colorway(dark)
    body = []
    body.append(centered('WSS', gill_bold, 430, 600, 560, c['text'], 2))
    body.append(f'<line x1="300" y1="720" x2="900" y2="720" stroke="{c["gold"]}" stroke-width="4"/>')
    body.append(centered('WHITE SEA STAR', gill_bold, 32, 600, 798, c['gold'], 11))
    body.append(centered('GENERAL TRADING L.L.C', gill, 22, 600, 850, c['muted'], 7))
    return svg_doc(1200, 1200, '\n'.join(body))


def concept_5(dark):
    c = colorway(dark)
    body = []
    square = GOLD if dark else NAVY
    letter = NAVY if dark else CREAM
    text = CREAM if dark else NAVY
    body.append(f'<rect x="100" y="230" width="340" height="340" fill="{square}"/>')
    body.append(centered('WSS', didot, 108, 270, 404, letter, 2))
    body.append(left('White Sea Star', didot_bold, 122, 500, 416, text, 0))
    body.append(left('GENERAL TRADING L.L.C', gill_bold, 28, 505, 486, GOLD, 8))
    body.append(left('Sourcing · Supply · Dubai', gill, 22, 505, 540, text, 5))
    return svg_doc(1600, 800, '\n'.join(body))


def concept_6(dark):
    c = colorway(dark)
    body = []
    body.append(star(600, 170, 42, c['gold']))
    body.append(centered('W', didot_bold, 520, 600, 470, c['text'], 0))
    body.append(centered('SS', didot_bold, 330, 600, 820, c['text'], 0))
    body.append(f'<line x1="410" y1="930" x2="790" y2="930" stroke="{c["gold"]}" stroke-width="2"/>')
    body.append(centered('WHITE SEA STAR', gill_bold, 24, 600, 990, c['gold'], 9))
    return svg_doc(1200, 1200, '\n'.join(body))




def concept_pure(dark):
    c = colorway(dark)
    body = [centered('WSS', didot, 460, 600, 600, c['text'], 10)]
    return svg_doc(1200, 1200, '\n'.join(body))


def concept_app_icon(dark):
    # Always a navy tile; dark variant uses a cream WSS, light variant uses gold WSS.
    letter = CREAM if not dark else GOLD
    body = []
    body.append(f'<rect x="0" y="0" width="1200" height="1200" fill="{NAVY}"/>')
    body.append(f'<rect x="70" y="70" width="1060" height="1060" fill="none" stroke="{GOLD}" stroke-width="2" opacity="0.55"/>')
    body.append(centered('WSS', didot, 430, 600, 600, letter, 8))
    return svg_doc(1200, 1200, '\n'.join(body))


CONCEPTS = [
    ('01-serif', 'Serif monogram', concept_1),
    ('02-circle', 'Circle badge', concept_2),
    ('03-diamond', 'Diamond outline', concept_3),
    ('04-modern', 'Modern sans', concept_4),
    ('05-lockup', 'Horizontal lockup', concept_5),
    ('06-stacked', 'Stacked WSS', concept_6),
]

for slug, label, fn in CONCEPTS:
    for mode, dark in (('light', False), ('dark', True)):
        svg = fn(dark)
        svg_path = SVG_DIR / f'wss-{slug}-{mode}.svg'
        svg_path.write_text(svg)
        w, h = (1600, 800) if slug == '05-lockup' else (1200, 1200)
        png_path = PNG_DIR / f'wss-{slug}-{mode}.png'
        profile = tempfile.mkdtemp(prefix='wss-logo-chrome-')
        try:
            subprocess.run([
                '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
                '--headless=new', '--disable-gpu', '--hide-scrollbars',
                '--default-background-color=00000000',
                '--force-device-scale-factor=2',
                f'--user-data-dir={profile}',
                f'--window-size={w},{h}',
                f'--screenshot={png_path}',
                svg_path.as_uri(),
            ], check=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL, timeout=30)
        finally:
            shutil.rmtree(profile, ignore_errors=True)


EXTRA = [
    ('00-pure', 'Pure WSS', concept_pure),
    ('00-app-icon', 'App icon', concept_app_icon),
]
for slug, label, fn in EXTRA:
    for mode, dark in (('light', False), ('dark', True)):
        svg = fn(dark)
        svg_path = SVG_DIR / f'wss-{slug}-{mode}.svg'
        svg_path.write_text(svg)
        png_path = PNG_DIR / f'wss-{slug}-{mode}.png'
        profile = tempfile.mkdtemp(prefix='wss-logo-chrome-')
        try:
            subprocess.run([
                '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
                '--headless=new', '--disable-gpu', '--hide-scrollbars',
                '--default-background-color=00000000',
                '--force-device-scale-factor=2',
                f'--user-data-dir={profile}',
                '--window-size=1200,1200',
                f'--screenshot={png_path}',
                svg_path.as_uri(),
            ], check=True, stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL, timeout=30)
        finally:
            shutil.rmtree(profile, ignore_errors=True)

# Contact sheets.
def make_sheet(dark, filename):
    tile_w, tile_h = 700, 700
    cols, rows = 3, 2
    W, H = tile_w * cols, tile_h * rows + 140
    bg = '#081731' if dark else CREAM
    tile_bg = '#0A1B3D' if dark else WHITE
    label_color = CREAM if dark else NAVY
    sub_color = GOLD
    sheet = Image.new('RGB', (W, H), bg)
    draw = ImageDraw.Draw(sheet)
    title_font = ImageFont.truetype(FONT_DIDOT, 58, index=0)
    sub_font = ImageFont.truetype(FONT_GILL, 22, index=0)
    label_font = ImageFont.truetype(FONT_GILL, 26, index=0)
    draw.text((60, 34), 'WSS logo concepts', font=title_font, fill=label_color)
    draw.text((62, 102), 'White Sea Star General Trading L.L.C', font=sub_font, fill=sub_color)
    for idx, (slug, label, fn) in enumerate(CONCEPTS):
        col = idx % cols
        row = idx // cols
        x = col * tile_w
        y = 140 + row * tile_h
        draw.rectangle((x + 18, y + 18, x + tile_w - 18, y + tile_h - 18), fill=tile_bg)
        img = Image.open(PNG_DIR / f'wss-{slug}-{"dark" if dark else "light"}.png').convert('RGBA')
        box_w, box_h = tile_w - 130, tile_h - 170
        img.thumbnail((box_w, box_h), Image.LANCZOS)
        px = x + (tile_w - img.width) // 2
        py = y + 40 + (box_h - img.height) // 2
        sheet.paste(img, (px, py), img)
        draw.text((x + 48, y + tile_h - 82), f'{idx+1:02d}  {label}', font=label_font, fill=label_color)
    sheet.save(ROOT / filename, optimize=True)

make_sheet(False, 'wss-logo-concepts-light.png')
make_sheet(True, 'wss-logo-concepts-dark.png')

# Zip all deliverables.
zip_path = ROOT / 'WSS-logo-concepts.zip'
with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as z:
    for f in sorted(SVG_DIR.glob('*.svg')) + sorted(PNG_DIR.glob('*.png')):
        z.write(f, f.relative_to(ROOT))
    z.write(ROOT / 'wss-logo-concepts-light.png', 'wss-logo-concepts-light.png')
    z.write(ROOT / 'wss-logo-concepts-dark.png', 'wss-logo-concepts-dark.png')

print('generated', len(list(SVG_DIR.glob('*.svg'))), 'svg and', len(list(PNG_DIR.glob('*.png'))), 'png')
print('zip', zip_path)
