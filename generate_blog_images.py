#!/usr/bin/env python3

import json
import os
import ssl
import sys
import time
import urllib.request
from pathlib import Path
from io import BytesIO

try:
    import certifi
    SSL_CONTEXT = ssl.create_default_context(cafile=certifi.where())
except ImportError:
    SSL_CONTEXT = ssl.create_default_context()

try:
    import replicate
except ImportError:
    print("ERROR: replicate library not found. Run: pip install replicate")
    sys.exit(1)

try:
    from PIL import Image
except ImportError:
    print("ERROR: Pillow library not found. Run: pip install Pillow")
    sys.exit(1)


# --- Config ---
SCRIPT_DIR   = Path(__file__).parent
JSON_FILE    = SCRIPT_DIR / "blog_image_prompts.json"
OUTPUT_DIR   = SCRIPT_DIR / "public/images/blog"
THUMBS_DIR   = OUTPUT_DIR / "thumbs"
ENV_FILE     = SCRIPT_DIR / ".env"

FULL_WIDTH   = 1200
FULL_HEIGHT  = 800
THUMB_WIDTH  = 400
THUMB_HEIGHT = 267
OUTPUT_FORMAT = "webp"
OUTPUT_QUALITY = 85

MODEL = "black-forest-labs/flux-1.1-pro"


def load_env(env_path):
    token = os.environ.get("REPLICATE_API_TOKEN")
    if token:
        return token

    if not env_path.exists():
        return None

    with open(env_path) as f:
        for line in f:
            line = line.strip()
            if line.startswith("REPLICATE_API_TOKEN="):
                value = line.split("=", 1)[1].strip().strip('"').strip("'")
                if value:
                    return value
    return None


def download_image(url):
    with urllib.request.urlopen(url, context=SSL_CONTEXT) as response:
        return BytesIO(response.read())


def save_images(image_bytes, slug):
    full_path  = OUTPUT_DIR / f"{slug}.webp"
    thumb_path = THUMBS_DIR / f"{slug}.webp"

    img = Image.open(image_bytes).convert("RGB")

    # Full size
    img_full = img.resize((FULL_WIDTH, FULL_HEIGHT), Image.LANCZOS)
    img_full.save(full_path, "WEBP", quality=OUTPUT_QUALITY)

    # Thumbnail
    img_thumb = img.resize((THUMB_WIDTH, THUMB_HEIGHT), Image.LANCZOS)
    img_thumb.save(thumb_path, "WEBP", quality=OUTPUT_QUALITY)

    return full_path, thumb_path


def main():
    import argparse
    parser = argparse.ArgumentParser()
    parser.add_argument("--limit", type=int, default=0, help="Generate only first N images (0 = all)")
    args = parser.parse_args()

    token = load_env(ENV_FILE)
    if not token:
        print("ERROR: REPLICATE_API_TOKEN not found in .env or environment.")
        sys.exit(1)

    os.environ["REPLICATE_API_TOKEN"] = token

    with open(JSON_FILE, encoding="utf-8") as f:
        articles = json.load(f)

    if args.limit > 0:
        articles = articles[:args.limit]

    OUTPUT_DIR.mkdir(parents=True, exist_ok=True)
    THUMBS_DIR.mkdir(parents=True, exist_ok=True)

    total   = len(articles)
    skipped = 0
    done    = 0
    errors  = []

    print(f"\nGenerating images for {total} articles...\n")

    for article in articles:
        slug   = article["slug"]
        prompt = article["image_prompt"]
        title  = article["title_en"]

        full_path = OUTPUT_DIR / f"{slug}.webp"

        if full_path.exists():
            print(f"  [SKIP] {slug}")
            skipped += 1
            continue

        print(f"  [{done + 1 + skipped}/{total}] {title}")

        max_retries = 5
        for attempt in range(max_retries):
            try:
                output = replicate.run(
                    MODEL,
                    input={
                        "prompt":           prompt,
                        "width":            FULL_WIDTH,
                        "height":           FULL_HEIGHT,
                        "output_format":    OUTPUT_FORMAT,
                        "output_quality":   OUTPUT_QUALITY,
                        "safety_tolerance": 2,
                    },
                )

                url = str(output) if not isinstance(output, str) else output
                image_bytes = download_image(url)
                full_path, thumb_path = save_images(image_bytes, slug)

                print(f"         -> saved: {full_path.name}  |  thumb: {thumb_path.name}")
                done += 1
                time.sleep(11)
                break

            except Exception as e:
                err_str = str(e)
                if "429" in err_str:
                    # parse "resets in ~Xs" from message
                    wait = 15
                    import re
                    m = re.search(r'resets in ~(\d+)s', err_str)
                    if m:
                        wait = int(m.group(1)) + 5
                    print(f"         [429] Rate limited. Waiting {wait}s... (attempt {attempt+1}/{max_retries})")
                    time.sleep(wait)
                else:
                    print(f"         ERROR: {e}")
                    errors.append(slug)
                    break
        else:
            print(f"         FAILED after {max_retries} retries.")
            errors.append(slug)

    print(f"\nDone. Generated: {done}  |  Skipped: {skipped}  |  Errors: {len(errors)}")
    if errors:
        print(f"Failed slugs: {', '.join(errors)}")


if __name__ == "__main__":
    main()
