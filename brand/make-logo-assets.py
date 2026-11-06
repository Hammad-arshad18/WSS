#!/usr/bin/env python3
"""Build the White Sea Star brand assets from the supplied logo artwork.

Run from the project root:
    python3 brand/make-logo-assets.py

Input : /Users/hammadarshad/Downloads/Logo Design Improvement.png
Output: assets/img/{logo,logo-light,mark,mark-light}.png
        assets/img/{favicon.png,logo-mark.png,og.jpg}
        favicon.svg, apple-touch-icon.png
"""

from pathlib import Path
import math

import numpy as np
from PIL import Image, ImageDraw, ImageFilter, ImageFont

ROOT = Path(__file__).resolve().parent.parent
SOURCE = Path("/Users/hammadarshad/Downloads/Logo Design Improvement.png")
IMG = ROOT / "assets" / "img"

NAVY = (10, 27, 61)
NAVY_MID = (14, 42, 85)
GOLD = (201, 162, 39)
GOLD_SOFT = (218, 178, 74)
CREAM = (244, 239, 227)

FONT_BASKERVILLE = "/System/Library/Fonts/Supplemental/Baskerville.ttc"
FONT_GILL = "/System/Library/Fonts/Supplemental/GillSans.ttc"


def unmatte(image: Image.Image) -> Image.Image:
    """Turn the white logo background into alpha, keeping the ink colours."""
    rgb = np.asarray(image.convert("RGB")).astype(np.float32) / 255.0
    low = rgb.min(axis=2)
    alpha = np.clip((1.0 - low - 0.02) / 0.98, 0.0, 1.0)
    alpha[alpha < 0.04] = 0.0

    safe = np.maximum(alpha, 1e-4)[..., None]
    colour = np.clip((rgb - (1.0 - safe)) / safe, 0.0, 1.0)
    colour[alpha == 0] = 0.0

    out = np.dstack([colour, alpha])
    return Image.fromarray((out * 255.0 + 0.5).astype(np.uint8), "RGBA")


def trim(image: Image.Image, pad: int = 0) -> Image.Image:
    box = image.split()[3].point(lambda v: 255 if v > 8 else 0).getbbox()
    if box is None:
        return image
    l, t, r, b = box
    l, t = max(0, l - pad), max(0, t - pad)
    r, b = min(image.width, r + pad), min(image.height, b + pad)
    return image.crop((l, t, r, b))


def recolour_light(image: Image.Image) -> Image.Image:
    """Navy ink -> cream, gold ink -> brighter gold, for dark backgrounds."""
    arr = np.asarray(image).astype(np.float32)
    rgb = arr[..., :3]
    a = arr[..., 3:4]
    r, g, b = rgb[..., 0], rgb[..., 1], rgb[..., 2]

    navy_mask = (b >= r + 8) & (a[..., 0] > 0)
    gold_mask = (r > b + 24) & (a[..., 0] > 0)

    out = rgb.copy()
    out[navy_mask] = CREAM
    out[gold_mask] = GOLD_SOFT

    return Image.fromarray(np.dstack([out, a]).astype(np.uint8), "RGBA")


def star_points(cx, cy, radius, inner_ratio=0.42, rotation=-math.pi / 2):
    pts = []
    for i in range(10):
        angle = rotation + i * math.pi / 5
        r = radius if i % 2 == 0 else radius * inner_ratio
        pts.append((cx + math.cos(angle) * r, cy + math.sin(angle) * r))
    return pts


def star_polygon(cx, cy, radius, steps=720):
    """A five-arm starfish silhouette with pointed tips and concave webbing."""
    pts = []
    segment = math.pi / 5  # half of one arm
    for i in range(steps):
        angle = -math.pi / 2 + i * 2 * math.pi / steps
        delta = (angle + math.pi / 2) % (2 * math.pi / 5)
        if delta > segment:
            delta = 2 * math.pi / 5 - delta
        r = radius * (1.0 - 0.88 * (delta / segment) ** 0.85)
        r = max(r, radius * 0.12)
        pts.append((cx + math.cos(angle) * r, cy + math.sin(angle) * r))
    return pts


def star_image(radius, fill, supersample=4):
    """Render the starfish on a transparent tile with clean, anti-aliased edges."""
    size = int(radius * 2.6)
    big = Image.new("RGBA", (size * supersample, size * supersample), (0, 0, 0, 0))
    bd = ImageDraw.Draw(big)
    bd.polygon(star_polygon(size * supersample / 2, size * supersample / 2, radius * supersample),
               fill=fill + (255,))
    return big.resize((size, size), Image.LANCZOS)



def draw_tracked(draw, xy, text, font, fill, tracking=0.0, anchor_left=True):
    x, y = xy
    if not anchor_left:
        widths = [draw.textlength(ch, font=font) for ch in text]
        x -= sum(widths) + tracking * (len(text) - 1)
    for ch in text:
        draw.text((x, y), ch, font=font, fill=fill)
        x += draw.textlength(ch, font=font) + tracking


def star_svg(cx, cy, radius, fill):
    points = star_polygon(cx, cy, radius, steps=180)

    def f(v):
        return f"{v:.1f}"

    path = f"M {f(points[0][0])} {f(points[0][1])}"
    for x, y in points[1:]:
        path += f" L {f(x)} {f(y)}"
    path += " Z"
    return f'<path d="{path}" fill="{fill}"/>'


def build_og(logo_light: Image.Image, path: Path):
    W, H = 1200, 630
    bg = Image.new("RGB", (W, H), (5, 14, 32))
    px = bg.load()
    for y in range(H):
        for x in range(W):
            # Diagonal navy gradient.
            t = (x / W) * 0.45 + (y / H) * 0.55
            r = int(5 + t * 9)
            g = int(14 + t * 20)
            b = int(32 + t * 42)
            px[x, y] = (r, g, b)

    draw = ImageDraw.Draw(bg, "RGBA")

    # Soft gold glow on the right.
    glow = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    gd = ImageDraw.Draw(glow)
    gd.ellipse((720, -140, 1260, 400), fill=(201, 162, 39, 42))
    glow = glow.filter(ImageFilter.GaussianBlur(90))
    bg = Image.alpha_composite(bg.convert("RGBA"), glow).convert("RGB")
    draw = ImageDraw.Draw(bg, "RGBA")

    # Faint concentric rings, echoing the site's hero.
    ring = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    rd = ImageDraw.Draw(ring)
    for radius in (150, 230, 310):
        rd.ellipse((940 - radius, 320 - radius, 940 + radius, 320 + radius),
                   outline=(244, 239, 227, 16), width=1)
    bg = Image.alpha_composite(bg.convert("RGBA"), ring).convert("RGB")
    draw = ImageDraw.Draw(bg, "RGBA")

    # Logo lockup, top left.
    logo_w = 320
    logo_h = round(logo_w * logo_light.height / logo_light.width)
    logo = logo_light.resize((logo_w, logo_h), Image.LANCZOS)
    bg.paste(logo, (72, 64), logo)

    # Headline.
    headline = ImageFont.truetype(FONT_BASKERVILLE, 70, index=0)
    draw.text((72, 268), "Global sourcing.", font=headline, fill=CREAM)
    draw.text((72, 352), "Delivered with certainty.", font=headline, fill=GOLD_SOFT)

    small = ImageFont.truetype(FONT_GILL, 21, index=0)
    draw_tracked(draw, (74, 486), "DUBAI  ·  ESTABLISHED 2009", small, (218, 178, 74), tracking=3.1)
    draw_tracked(draw, (1128, 486), "whiteseastar.com", small, (244, 239, 227), tracking=0.6, anchor_left=False)

    bg.save(path, quality=88, optimize=True, progressive=True)


def main():
    IMG.mkdir(parents=True, exist_ok=True)

    source = Image.open(SOURCE).convert("RGB")
    full = unmatte(source.crop((403, 689, 5211, 2075)))
    full = trim(full, pad=8)

    # Mark = starfish + WSS script (the script ends at x 2400 in the source).
    src_mark = unmatte(source.crop((680, 689, 2426, 1935)))
    src_mark = trim(src_mark, pad=8)

    logo_w = 1400
    logo = full.resize((logo_w, round(logo_w * full.height / full.width)), Image.LANCZOS)
    logo.save(IMG / "logo.png", optimize=True)

    logo_light = recolour_light(logo)
    logo_light.save(IMG / "logo-light.png", optimize=True)

    mark_w = 760
    mark = src_mark.resize((mark_w, round(mark_w * src_mark.height / src_mark.width)), Image.LANCZOS)
    mark.save(IMG / "mark.png", optimize=True)
    recolour_light(mark).save(IMG / "mark-light.png", optimize=True)

    # Square brand tile, used for the icon and structured data.
    size = 512
    tile = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    td = ImageDraw.Draw(tile)
    td.rounded_rectangle((0, 0, size - 1, size - 1), radius=104, fill=NAVY)
    td.rounded_rectangle((18, 18, size - 19, size - 19), radius=88,
                         outline=GOLD + (90,), width=3)
    star_layer = star_image(size * 0.30, GOLD_SOFT)
    tile.alpha_composite(star_layer, (int(size / 2 - star_layer.width / 2),
                                      int(size / 2 + 8 - star_layer.height / 2)))
    tile.save(IMG / "logo-mark.png", optimize=True)

    icon = tile.resize((512, 512), Image.LANCZOS)
    icon.save(IMG / "favicon.png", optimize=True)
    tile.resize((180, 180), Image.LANCZOS).convert("RGB").save(ROOT / "apple-touch-icon.png", optimize=True)

    star = star_svg(256, 264, 150, "#D9B44A")
    svg = (
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="512" height="512" role="img" '
        'aria-label="White Sea Star">\n'
        '<rect width="512" height="512" rx="104" fill="#0A1B3D"/>\n'
        '<rect x="18" y="18" width="476" height="476" rx="88" fill="none" '
        'stroke="#C9A227" stroke-opacity=".35" stroke-width="3"/>\n'
        f'{star}\n'
        '</svg>\n'
    )
    (ROOT / "favicon.svg").write_text(svg)

    build_og(logo_light, IMG / "og.jpg")

    for name in ("logo.png", "logo-light.png", "mark.png", "mark-light.png",
                 "logo-mark.png", "favicon.png"):
        p = IMG / name
        print(f"{name:16} {Image.open(p).size}  {p.stat().st_size // 1024} KB")
    print("og.jpg           ", Image.open(IMG / "og.jpg").size)
    print("apple-touch-icon ", Image.open(ROOT / "apple-touch-icon.png").size)


if __name__ == "__main__":
    main()
