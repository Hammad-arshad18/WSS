#!/usr/bin/env python3
"""Build the White Sea Star brand assets from the supplied artwork.

Run from the project root:
    python3 brand/make-logo-assets.py

Inputs
    ~/Downloads/WSS-monogram-final.png  geometric WSS logo (header, footer, portal)
    ~/Downloads/WSS-logo-final.png gold starfish used as the brand seal

Outputs
    assets/img/{wss,wss-light}.png          WSS monogram (navy / cream)
    assets/img/{mark,mark-light}.png        Gold starfish seal
    assets/img/{logo,logo-light}.png        Monogram at social-image size
    assets/img/{favicon.png,logo-mark.png,og.jpg}
    assets/img/icon-192.png
    favicon.svg, apple-touch-icon.png
"""

from pathlib import Path
import base64
import io

import numpy as np
from PIL import Image, ImageDraw, ImageFilter, ImageFont

ROOT = Path(__file__).resolve().parent.parent
LOGO_SOURCE = Path("/Users/hammadarshad/Downloads/WSS-monogram-final.png")
STAR_SOURCE = Path("/Users/hammadarshad/Downloads/WSS-logo-final.png")
IMG = ROOT / "assets" / "img"

NAVY = (10, 27, 61)
GOLD = (201, 162, 39)
GOLD_SOFT = (218, 178, 74)
CREAM = (244, 239, 227)

FONT_BASKERVILLE = "/System/Library/Fonts/Supplemental/Baskerville.ttc"
FONT_GILL = "/System/Library/Fonts/Supplemental/GillSans.ttc"

# Tight bounds of the gold starfish inside the earlier lockup artwork.
STAR_BOX = (1455, 693, 1928, 1125)


def unmatte(image: Image.Image, bg: tuple[float, float, float]) -> Image.Image:
    """Turn the flat background into alpha and un-mix the anti-aliased edges."""
    rgb = np.asarray(image.convert("RGB")).astype(np.float32)
    field = np.array(bg, dtype=np.float32)

    distance = np.abs(rgb - field).max(axis=2)
    alpha = np.clip((distance - 6.0) / 45.0, 0.0, 1.0)
    alpha[alpha < 0.08] = 0.0

    safe = np.maximum(alpha, 1e-4)[..., None]
    colour = np.clip((rgb - (1.0 - safe) * field) / safe, 0.0, 255.0)
    colour[alpha == 0] = 0.0

    out = np.dstack([colour, alpha * 255.0])
    return Image.fromarray((out + 0.5).astype(np.uint8), "RGBA")


def recolour_light(image: Image.Image) -> Image.Image:
    """Navy ink -> cream, gold ink -> brighter gold, for dark backgrounds."""
    arr = np.asarray(image).astype(np.float32)
    rgb = arr[..., :3]
    alpha = arr[..., 3:4]
    r, b = rgb[..., 0], rgb[..., 2]

    gold = ((r - b) > 24) & (alpha[..., 0] > 0)
    out = rgb.copy()
    out[~gold] = CREAM
    out[gold] = np.clip(rgb[gold] * 1.10 + 8.0, 0.0, 255.0)

    return Image.fromarray(np.dstack([out, alpha]).astype(np.uint8), "RGBA")


def trim(image: Image.Image, pad: int = 0) -> Image.Image:
    box = image.split()[3].point(lambda v: 255 if v > 8 else 0).getbbox()
    if box is None:
        return image
    l, t, r, b = box
    l, t = max(0, l - pad), max(0, t - pad)
    r, b = min(image.width, r + pad), min(image.height, b + pad)
    return image.crop((l, t, r, b))


def resize_to_width(image: Image.Image, width: int) -> Image.Image:
    return image.resize((width, round(width * image.height / image.width)), Image.LANCZOS)


def draw_tracked(draw, xy, text, font, fill, tracking=0.0, anchor_left=True):
    x, y = xy
    if not anchor_left:
        widths = [draw.textlength(ch, font=font) for ch in text]
        x -= sum(widths) + tracking * (len(text) - 1)
    for ch in text:
        draw.text((x, y), ch, font=font, fill=fill)
        x += draw.textlength(ch, font=font) + tracking


def icon_tile(monogram_light: Image.Image, size: int = 512) -> Image.Image:
    """Navy rounded tile with the cream WSS monogram."""
    tile = Image.new("RGBA", (size, size), (0, 0, 0, 0))
    td = ImageDraw.Draw(tile)
    td.rounded_rectangle((0, 0, size - 1, size - 1), radius=104, fill=NAVY)

    mark_w = int(size * 0.64)
    mark = resize_to_width(monogram_light, mark_w)
    tile.alpha_composite(mark, ((size - mark.width) // 2, (size - mark.height) // 2))
    return tile


def build_og(logo_light: Image.Image, path: Path):
    W, H = 1200, 630
    bg = Image.new("RGB", (W, H), (5, 14, 32))
    px = bg.load()
    for y in range(H):
        for x in range(W):
            t = (x / W) * 0.45 + (y / H) * 0.55
            px[x, y] = (int(5 + t * 9), int(14 + t * 20), int(32 + t * 42))

    glow = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    gd = ImageDraw.Draw(glow)
    gd.ellipse((720, -140, 1260, 400), fill=(201, 162, 39, 42))
    glow = glow.filter(ImageFilter.GaussianBlur(90))
    bg = Image.alpha_composite(bg.convert("RGBA"), glow).convert("RGB")

    ring = Image.new("RGBA", (W, H), (0, 0, 0, 0))
    rd = ImageDraw.Draw(ring)
    for radius in (170, 260, 350):
        rd.ellipse((960 - radius, 330 - radius, 960 + radius, 330 + radius),
                   outline=(244, 239, 227, 14), width=1)
    bg = Image.alpha_composite(bg.convert("RGBA"), ring).convert("RGB")
    draw = ImageDraw.Draw(bg, "RGBA")

    # Monogram, top left.
    logo_w = 400
    logo_h = round(logo_w * logo_light.height / logo_light.width)
    logo = logo_light.resize((logo_w, logo_h), Image.LANCZOS)
    bg.paste(logo, (84, 82), logo)

    # Gold hairline under the monogram.
    rule_y = 82 + logo_h + 34
    draw.line((84, rule_y, 84 + logo_w, rule_y), fill=GOLD + (160,), width=2)

    headline = ImageFont.truetype(FONT_BASKERVILLE, 62, index=0)
    draw.text((84, 330), "Global sourcing.", font=headline, fill=CREAM)
    draw.text((84, 404), "Delivered with certainty.", font=headline, fill=GOLD_SOFT)

    small = ImageFont.truetype(FONT_GILL, 21, index=0)
    draw_tracked(draw, (86, 512), "DUBAI  ·  ESTABLISHED 2009", small, GOLD_SOFT, tracking=3.1)
    draw_tracked(draw, (1116, 512), "whiteseastar.com", small, CREAM, tracking=0.6, anchor_left=False)

    bg.save(path, quality=88, optimize=True, progressive=True)


def embedded_svg(tile: Image.Image, path: Path):
    """A single-file SVG favicon that embeds the rendered tile."""
    small = tile.resize((256, 256), Image.LANCZOS)
    buffer = io.BytesIO()
    small.save(buffer, format="PNG", optimize=True)
    payload = base64.b64encode(buffer.getvalue()).decode("ascii")
    svg = (
        '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="512" height="512" '
        'role="img" aria-label="White Sea Star">\n'
        f'<image width="512" height="512" href="data:image/png;base64,{payload}"/>\n'
        '</svg>\n'
    )
    path.write_text(svg)


def main():
    IMG.mkdir(parents=True, exist_ok=True)

    logo_source = Image.open(LOGO_SOURCE).convert("RGB")
    star_source = Image.open(STAR_SOURCE).convert("RGB")

    # The monogram artwork is centred on a flat field; trim to its own ink.
    monogram = trim(unmatte(logo_source, (254, 254, 254)), pad=4)
    star = trim(unmatte(star_source.crop(STAR_BOX), (255, 255, 255)), pad=6)

    wss = resize_to_width(monogram, 600)
    wss.save(IMG / "wss.png", optimize=True)
    wss_light = recolour_light(wss)
    wss_light.save(IMG / "wss-light.png", optimize=True)

    logo = resize_to_width(monogram, 800)
    logo.save(IMG / "logo.png", optimize=True)
    logo_light = recolour_light(logo)
    logo_light.save(IMG / "logo-light.png", optimize=True)

    mark = resize_to_width(star, 420)
    mark.save(IMG / "mark.png", optimize=True)
    mark_light = recolour_light(mark)
    mark_light.save(IMG / "mark-light.png", optimize=True)

    tile = icon_tile(wss_light, 512)
    tile.save(IMG / "logo-mark.png", optimize=True)
    tile.save(IMG / "favicon.png", optimize=True)
    tile.resize((192, 192), Image.LANCZOS).save(IMG / "icon-192.png", optimize=True)
    tile.resize((180, 180), Image.LANCZOS).convert("RGB").save(ROOT / "apple-touch-icon.png",
                                                              optimize=True)

    embedded_svg(tile, ROOT / "favicon.svg")
    build_og(logo_light, IMG / "og.jpg")

    for name in ("wss.png", "wss-light.png", "logo.png", "logo-light.png",
                 "mark.png", "mark-light.png", "logo-mark.png", "favicon.png",
                 "icon-192.png", "og.jpg"):
        p = IMG / name
        with Image.open(p) as opened:
            print(f"{name:16} {opened.size}  {p.stat().st_size // 1024} KB")
    with Image.open(ROOT / "apple-touch-icon.png") as opened:
        print("apple-touch-icon ", opened.size)
    print("favicon.svg      ", (ROOT / "favicon.svg").stat().st_size // 1024, "KB")


if __name__ == "__main__":
    main()
