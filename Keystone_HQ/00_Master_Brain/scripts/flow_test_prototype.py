import asyncio
import os
import sys
import re
from pathlib import Path
from playwright.async_api import async_playwright

sys.stdout.reconfigure(encoding='utf-8')

# Constants
KIRA_CHARACTER_ID = "187992d1-2294-4d4c-b9d3-bb625830104d"
OUTPUT_DIR = Path(r"C:\Users\Curtis\Desktop\SHORT_TEST_KIRA")
os.makedirs(OUTPUT_DIR, exist_ok=True)
os.makedirs(OUTPUT_DIR / "Videos", exist_ok=True)
os.makedirs(OUTPUT_DIR / "Images", exist_ok=True)

# Prompts
IDENTITY_CORE = (
    "A hyper-realistic, stunningly attractive 25-year-old female fashion model named Kira Frost. "
    "She is wearing an ultra-sexy, form-fitting black latex corset top with high-cut designer leather pants and stilettos. "
    "She has long, voluminous dark hair, flawless glowing skin, and striking cat-eye makeup. "
    "Cinematic neon-noir lighting, deep shadows, 8k resolution, high-end electronic music video aesthetic."
)
BEHAVIORAL_VIBE = (
    "She moves with a slow, predatory confidence. Her walk is deliberate and rhythmic. "
    "She does not smile; her expression is intense, commanding, and slightly mysterious, making piercing eye contact."
)

VIDEO_DIRECTIVES = [
    "Wide establishing shot. She is standing perfectly still at the end of a long, dark, smoky underground tunnel. Neon magenta lights flicker off her latex corset. The camera slowly pushes in towards her. No subtitles.",
    "Extreme macro close-up of her eyes. Deep shadows. A bright neon light bar slowly reflects across her iris. She slowly blinks once, maintaining intense eye contact with the lens. No subtitles.",
    "Medium tracking shot from the waist up. She is walking smoothly forward through the smoky tunnel. She abruptly whips her head to look sharply to the right side of the frame, her dark hair flying in slow motion. No subtitles.",
    "Fast pan moving right to left across a concrete wall covered in water droplets. The camera settles on a Wide Shot of her standing on an empty, rain-slicked city rooftop at night, city lights glowing in the deep background. No subtitles."
]

BROLL_DIRECTIVES = [
    "Cinematic low-angle wide shot of a wet, rain-slicked concrete rooftop in a futuristic city at night. Dark moody atmosphere, flashing magenta and cyan neon billboards reflecting in the puddles, deep shadows, 8k resolution. No subtitles.",
    "Macro shot of a dark concrete wall with water droplets slowly trickling down. Harsh blue neon light casting long shadows across the textured surface, cinematic look, 8k. No subtitles.",
    "Abstract wide shot of a minimalist concrete underground tunnel with a single flickering warm spotlight from directly above, thick swirling white smoke filling the space, dark moody aesthetic. No subtitles.",
    "Cinematic close-up of a high-end black luxury sports car parked in a dark wet alleyway at night. Flickering neon lights reflecting off the glossy metallic paint, realistic rain, deep shadows. No subtitles."
]

async def get_finished_count(page):
    items = await page.locator("[aria-roledescription='draggable']:visible").all()
    finished = 0
    for it in items:
        try:
            text = await it.inner_text()
            is_completed_video = "play_circle" in text and "Failed" not in text and not re.search(r'\d+%', text)
            img_el = it.locator('img[alt="Generated image"]').first
            is_completed_image = (await img_el.count() > 0) and ("Failed" not in text) and not re.search(r'\d+%', text)
            if is_completed_video or is_completed_image:
                finished += 1
        except Exception:
            pass
    return finished

async def main():
    print("[+] Reading Chrome Debugging Port...")
    devtools_port_path = Path(r"C:\Users\Curtis\AppData\Local\Google\Chrome\User Data\DevToolsActivePort")
    if not devtools_port_path.exists():
         print("[-] DevToolsActivePort file not found! Ensure Chrome is running with remote debugging enabled.")
         sys.exit(1)
         
    with open(devtools_port_path, "r") as f:
        lines = f.read().splitlines()
    port = lines[0]
    path = lines[1]
    ws_url = f"ws://127.0.0.1:{port}{path}"
    
    async with async_playwright() as p:
        print("[+] Connecting to Chrome CDP...")
        browser = await p.chromium.connect_over_cdp(ws_url)
        context = browser.contexts[0]
        
        page = None
        for p_ in context.pages:
            if "labs.google/fx/tools/flow/project" in p_.url:
                page = p_
                break
                
        if not page:
            print("[-] Google Flow project page not found. Please open Google Flow in Chrome first.")
            return
            
        print(f"[+] Connected to page: {page.url}")
        await page.bring_to_front()
        await page.keyboard.press("Escape")
        await page.wait_for_timeout(500)
        
        # Get baseline finished count
        initial_finished = await get_finished_count(page)
        print(f"[+] Baseline completed/finished items count: {initial_finished}")
        
        # ── STEP 1: Apply Video Settings (4s, 16:9, Omni Flash) ──
        print("[+] Opening settings panel for Video...")
        settings_btn = page.locator('button').filter(has_text=re.compile(r'crop_(16_9|9_16|landscape|portrait|square)')).first
        
        video_tab = page.locator("button[role='tab']").filter(has_text="Video").first
        image_tab = page.locator("button[role='tab']").filter(has_text="Image").first
        
        popover_visible = False
        if await video_tab.is_visible() or await image_tab.is_visible():
            popover_visible = True
        else:
            for attempt in range(5):
                await settings_btn.click(force=True)
                await page.wait_for_timeout(1000)
                if await video_tab.is_visible() or await image_tab.is_visible():
                    popover_visible = True
                    break
                
        if not popover_visible:
            print("[-] ERROR: Settings popover failed to open!")
            return
            
        print("[+] Selecting Video Mode Tab...")
        await video_tab.click(force=True)
        await page.wait_for_timeout(500)
        
        # Model dropdown selector
        dropdown_btn = page.locator("button").filter(has_text=re.compile("arrow_drop_down")).last
        if await dropdown_btn.is_visible():
            txt = await dropdown_btn.inner_text()
            if "Omni Flash" not in txt and "Veo 3.1" not in txt:
                print("  [+] Changing video model to Omni Flash...")
                await dropdown_btn.click(force=True)
                await page.wait_for_timeout(500)
                omni_opt = page.locator("[role='option'], [role='menuitem'], button").filter(has_text=re.compile("Omni Flash|Veo 3.1 Fast", re.I)).first
                await omni_opt.click(force=True)
                await page.wait_for_timeout(500)
                
        print("[+] Setting aspect ratio to 16:9...")
        aspect_btn = page.locator("button").filter(has_text="16:9").first
        await aspect_btn.click(force=True)
        await page.wait_for_timeout(500)
        
        print("[+] Setting duration to 4s...")
        dur_btn = page.locator("button").filter(has_text="4s").first
        if await dur_btn.is_visible():
            await dur_btn.click(force=True)
        else:
            fallback_dur = page.locator("button").filter(has_text=re.compile(r'^5s$|^10s$')).first
            await fallback_dur.click(force=True)
            
        print("[+] Setting generation quantity to 1x...")
        qty_btn = page.locator("button").filter(has_text="1x").first
        await qty_btn.click(force=True)
        
        await page.keyboard.press("Escape")
        await page.wait_for_timeout(500)
        print("[+] Video settings applied successfully!")

        # ── STEP 2: Submit 4 Talking Video Prompts ──
        print("[+] Submitting 4 talking video prompts...")
        for i, directive in enumerate(VIDEO_DIRECTIVES, 1):
            editor = page.locator("[data-slate-editor]").first
            await editor.focus()
            await page.keyboard.press("Control+A")
            await page.keyboard.press("Backspace")
            await page.wait_for_timeout(300)
            
            full_prompt = f"{IDENTITY_CORE} {BEHAVIORAL_VIBE} {directive}"
            single_line_prompt = full_prompt.replace("\n", " ").replace("\r", " ")
            await page.keyboard.type(single_line_prompt, delay=2)
            await page.wait_for_timeout(300)
            
            # Character Likeness reference setup in UI editor store
            await page.evaluate("""(charId) => {
                const editor = document.querySelector('[data-slate-editor]');
                const fiberKey = Object.keys(editor).find(k => k.startsWith('__reactFiber$'));
                let current = editor[fiberKey];
                let store = null;
                while (current) {
                    if (current.memoizedProps && current.memoizedProps.promptBoxStore) {
                        store = current.memoizedProps.promptBoxStore;
                        break;
                    }
                    current = current.return;
                }
                if (!store) return;
                const actions = store.getState().actions;
                actions.clearIngredients();
                actions.clearCharacterServerIds();
                actions.clearLikenessIngredients();
                actions.addCharacterIngredient({
                    characterServerId: charId,
                    source: 'PLUS_BUTTON'
                });
            }""", KIRA_CHARACTER_ID)
            await page.wait_for_timeout(500)
            
            # Submit
            await editor.focus()
            await page.keyboard.press("Control+Enter")
            print(f"  [+] Submitted Video Prompt {i}!")
            await page.wait_for_timeout(1000)

        # ── STEP 3: Submit 4 B-roll Image Prompts ──
        print("[+] Opening settings panel for Image...")
        popover_visible = False
        if await video_tab.is_visible() or await image_tab.is_visible():
            popover_visible = True
        else:
            for attempt in range(5):
                await settings_btn.click(force=True)
                await page.wait_for_timeout(1000)
                if await video_tab.is_visible() or await image_tab.is_visible():
                    popover_visible = True
                    break
                    
        if not popover_visible:
            print("[-] ERROR: Settings popover failed to open for image settings!")
            return
            
        print("[+] Selecting Image Mode Tab...")
        await image_tab.click(force=True)
        await page.wait_for_timeout(500)
        
        # Model dropdown check for images
        if await dropdown_btn.is_visible():
            txt = await dropdown_btn.inner_text()
            if "Banana Pro" not in txt and "Nano Banana" not in txt:
                print("  [+] Changing image model to Nano Banana Pro...")
                await dropdown_btn.click(force=True)
                await page.wait_for_timeout(500)
                banana_opt = page.locator("[role='option'], [role='menuitem'], button").filter(has_text=re.compile("Banana Pro|Nano Banana 2|Banana", re.I)).first
                await banana_opt.click(force=True)
                await page.wait_for_timeout(500)
                
        print("[+] Setting aspect ratio to 16:9...")
        aspect_btn = page.locator("button").filter(has_text="16:9").first
        await aspect_btn.click(force=True)
        await page.wait_for_timeout(500)
        
        print("[+] Setting quantity to 1x...")
        qty_btn = page.locator("button").filter(has_text="1x").first
        await qty_btn.click(force=True)
        
        await page.keyboard.press("Escape")
        await page.wait_for_timeout(500)
        
        print("[+] Submitting 4 B-roll image prompts...")
        for i, directive in enumerate(BROLL_DIRECTIVES, 1):
            editor = page.locator("[data-slate-editor]").first
            await editor.focus()
            await page.keyboard.press("Control+A")
            await page.keyboard.press("Backspace")
            await page.wait_for_timeout(300)
            
            single_line = directive.replace("\n", " ").replace("\r", " ")
            await page.keyboard.type(single_line, delay=2)
            await page.wait_for_timeout(300)
            
            # Clear likeness ingredients (reference photo skip) for B-rolls
            await page.evaluate("""() => {
                const editor = document.querySelector('[data-slate-editor]');
                const fiberKey = Object.keys(editor).find(k => k.startsWith('__reactFiber$'));
                let current = editor[fiberKey];
                let store = null;
                while (current) {
                    if (current.memoizedProps && current.memoizedProps.promptBoxStore) {
                        store = current.memoizedProps.promptBoxStore;
                        break;
                    }
                    current = current.return;
                }
                if (!store) return;
                const actions = store.getState().actions;
                actions.clearIngredients();
                actions.clearCharacterServerIds();
                actions.clearLikenessIngredients();
            }""")
            await page.wait_for_timeout(500)
            
            # Submit
            await editor.focus()
            await page.keyboard.press("Control+Enter")
            print(f"  [+] Submitted B-roll Image {i}!")
            await page.wait_for_timeout(1000)
            
        # ── STEP 4: Wait for Renders to Complete ──
        total_queued = len(VIDEO_DIRECTIVES) + len(BROLL_DIRECTIVES)
        target_finished = initial_finished + total_queued
        print(f"[+] All prompts queued. Waiting for renders (target count: {target_finished})...")
        
        success = False
        for poll in range(120):  # Wait up to 20 minutes
            await asyncio.sleep(10)
            current_finished = await get_finished_count(page)
            completed_total = current_finished - initial_finished
            print(f"  -> Progress: {completed_total}/{total_queued} completed...")
            if current_finished >= target_finished:
                print("[+] All rendering completed successfully!")
                success = True
                break
                
        if not success:
            print("[-] Warning: Rendering timed out. We will proceed to rename whatever has completed.")
            
        await page.wait_for_timeout(2000)
        
        # ── STEP 5: UI Renaming in Flow (Videos) ──
        print("[+] Clicking 'Videos' tab on sidebar to filter grid...")
        video_sidebar_tab = page.get_by_text("Videos", exact=True).first
        await video_sidebar_tab.click(force=True)
        await page.wait_for_timeout(2000)
        
        all_cards = await page.locator('[aria-roledescription="draggable"]:visible').all()
        completed_videos = []
        for card in all_cards:
            txt = await card.inner_text()
            if "play_circle" in txt and "Failed" not in txt:
                completed_videos.append(card)
                
        video_rename_count = min(len(completed_videos), 4)
        print(f"[+] Renaming the oldest {video_rename_count} completed videos bottom-up...")
        for i in range(video_rename_count):
            all_c = await page.locator('[aria-roledescription="draggable"]:visible').all()
            comp_vids = []
            for c in all_c:
                txt = await c.inner_text()
                if "play_circle" in txt and "Failed" not in txt:
                    comp_vids.append(c)
                    
            if not comp_vids:
                break
                
            card_idx = len(comp_vids) - 1 - i
            if card_idx < 0 or card_idx >= len(comp_vids):
                break
            card = comp_vids[card_idx]
            new_name = str(i + 1)
            
            print(f"  [{i+1}/{video_rename_count}] Renaming video card index {card_idx} to '{new_name}'...")
            await card.evaluate("el => el.scrollIntoView({block: 'center'})")
            await page.wait_for_timeout(500)
            
            # Hover top-left edge to avoid avatar overlay
            await card.hover(position={"x": 10, "y": 10})
            await page.wait_for_timeout(500)
            # Right-click directly to open menu (no left-click to avoid opening the video player)
            await card.click(button="right", force=True, position={"x": 10, "y": 10})
            await page.wait_for_timeout(1000)
            
            rename_btn = page.locator('[role="menuitem"]:visible, button:visible').filter(has_text=re.compile("Rename")).first
            await rename_btn.click(timeout=3000)
            await page.wait_for_timeout(1000)
            
            input_box = page.locator("input:focus").first
            if await input_box.count() > 0 and await input_box.is_visible():
                await input_box.fill(new_name)
                await page.wait_for_timeout(300)
                await input_box.press("Enter")
                await page.wait_for_timeout(1000)
                print(f"    [OK] Renamed to '{new_name}'")
            else:
                print(f"    [-] ERROR: Focused input box not found for card index {card_idx}!")

        # ── STEP 6: UI Renaming in Flow (Images) ──
        print("[+] Clicking 'Images' tab on sidebar to filter grid...")
        image_sidebar_tab = page.get_by_text("Images", exact=True).first
        await image_sidebar_tab.click(force=True)
        await page.wait_for_timeout(2000)
        
        all_cards = await page.locator('[aria-roledescription="draggable"]:visible').all()
        completed_images = []
        for card in all_cards:
            txt = await card.inner_text()
            if "Failed" not in txt and "Kira Frost" not in txt:
                img_target = card.locator('img[alt="Generated image"]').first
                if await img_target.count() > 0:
                    completed_images.append(card)
                
        image_rename_count = min(len(completed_images), 4)
        print(f"[+] Renaming the oldest {image_rename_count} completed images bottom-up...")
        for i in range(image_rename_count):
            all_c = await page.locator('[aria-roledescription="draggable"]:visible').all()
            comp_imgs = []
            for c in all_c:
                txt = await c.inner_text()
                if "Failed" not in txt and "Kira Frost" not in txt:
                    img_target = c.locator('img[alt="Generated image"]').first
                    if await img_target.count() > 0:
                        comp_imgs.append(c)
                    
            if not comp_imgs:
                break
                
            card_idx = len(comp_imgs) - 1 - i
            if card_idx < 0 or card_idx >= len(comp_imgs):
                break
            card = comp_imgs[card_idx]
            new_name = f"A{i + 1}"
            
            print(f"  [{i+1}/{image_rename_count}] Renaming image card index {card_idx} to '{new_name}'...")
            await card.evaluate("el => el.scrollIntoView({block: 'center'})")
            await page.wait_for_timeout(500)
            
            # Hover top-left edge to avoid avatar overlay
            await card.hover(position={"x": 10, "y": 10})
            await page.wait_for_timeout(500)
            # Left-click to select
            await card.click(button="left", force=True, position={"x": 10, "y": 10})
            await page.wait_for_timeout(300)
            # Right-click to open menu
            await card.click(button="right", force=True, position={"x": 10, "y": 10})
            await page.wait_for_timeout(1000)
            
            rename_btn = page.locator('[role="menuitem"]:visible, button:visible').filter(has_text=re.compile("Rename")).first
            await rename_btn.click(timeout=3000)
            await page.wait_for_timeout(1000)
            
            input_box = page.locator("input:focus").first
            if await input_box.count() > 0 and await input_box.is_visible():
                await input_box.fill(new_name)
                await page.wait_for_timeout(300)
                await input_box.press("Enter")
                await page.wait_for_timeout(1000)
                print(f"    [OK] Renamed to '{new_name}'")
            else:
                print(f"    [-] ERROR: Focused input box not found for card index {card_idx}!")

        print("[+] Switching back to 'Videos' tab for downloading...")
        await page.get_by_text("Videos", exact=True).first.click(force=True)
        await page.wait_for_timeout(2000)
        
        print("[+] Injecting CSS to hide notifications during download...")
        await page.add_style_tag(content="div[class*='notification'], section[aria-label*='Notification']{display:none!important}")
        await page.wait_for_timeout(500)
        
        print("[+] Downloading videos bottom-up (no rotation)...")
        for i in range(video_rename_count):
            all_c = await page.locator('[aria-roledescription="draggable"]:visible').all()
            comp_vids = []
            for c in all_c:
                txt = await c.inner_text()
                if "play_circle" in txt and "Failed" not in txt:
                    comp_vids.append(c)
                    
            if not comp_vids:
                print("  [ERROR] No video cards found for downloading")
                break
                
            new_name = str(i + 1)
            # Find card by name first, fall back to straight sequential index (i) from the top
            target = None
            for c in comp_vids:
                txt = await c.inner_text()
                if re.search(r'\b' + re.escape(new_name) + r'\b', txt) or txt.strip().endswith(new_name):
                    target = c
                    break
            if not target:
                card_idx = i if i < len(comp_vids) else 0
                target = comp_vids[card_idx]
                
            await target.evaluate("el => el.scrollIntoView({block: 'center'})")
            await page.wait_for_timeout(500)
            
            # Hover top-left edge to avoid avatar overlay
            await target.hover(position={"x": 10, "y": 10})
            await page.wait_for_timeout(500)
            # Right-click directly to open menu (no left-click to avoid opening the video player)
            await target.click(button="right", force=True, position={"x": 10, "y": 10})
            await page.wait_for_timeout(1000)
            
            download_item = page.locator('[role="menuitem"]:visible, button:visible').filter(has_text="Download").first
            resolution = page.locator('[role="menuitem"]:visible, button:visible').filter(has_text=re.compile("1080p|720p", re.I)).first
            
            async with page.expect_download(timeout=10000) as download_info:
                await download_item.click(timeout=3000)
                try:
                    await resolution.wait_for(state="visible", timeout=1500)
                    await resolution.click(timeout=3000)
                except Exception:
                    pass
            download = await download_info.value
            
            final_path = OUTPUT_DIR / "Videos" / f"{i+1}.mp4"
            await download.save_as(final_path)
            print(f"    [OK] Downloaded and saved video to: {final_path}")
            await page.wait_for_timeout(1000)

        print("[+] Switching back to 'Images' tab for downloading...")
        await page.get_by_text("Images", exact=True).first.click(force=True)
        await page.wait_for_timeout(2000)
        
        print("[+] Downloading B-roll images sequentially (no rotation)...")
        for i in range(image_rename_count):
            all_c = await page.locator('[aria-roledescription="draggable"]:visible').all()
            comp_imgs = []
            for c in all_c:
                txt = await c.inner_text()
                if "Failed" not in txt and "Kira Frost" not in txt:
                    img_target = c.locator('img[alt="Generated image"]').first
                    if await img_target.count() > 0:
                        comp_imgs.append(c)
                    
            if not comp_imgs:
                print("  [ERROR] No image cards found for downloading")
                break
                
            new_name = f"A{i + 1}"
            # Find card by name first, fall back to straight sequential index (i) from the top
            target = None
            for c in comp_imgs:
                txt = await c.inner_text()
                if re.search(r'\b' + re.escape(new_name) + r'\b', txt) or txt.strip().endswith(new_name):
                    target = c
                    break
            if not target:
                card_idx = i if i < len(comp_imgs) else 0
                target = comp_imgs[card_idx]
                
            await target.evaluate("el => el.scrollIntoView({block: 'center'})")
            await page.wait_for_timeout(500)
            
            # Hover top-left edge to avoid avatar overlay
            await target.hover(position={"x": 10, "y": 10})
            await page.wait_for_timeout(500)
            # Left-click to select
            await target.click(button="left", force=True, position={"x": 10, "y": 10})
            await page.wait_for_timeout(300)
            # Right-click to open menu
            await target.click(button="right", force=True, position={"x": 10, "y": 10})
            await page.wait_for_timeout(1000)
            
            download_item = page.locator('[role="menuitem"]:visible, button:visible').filter(has_text="Download").first
            resolution = page.locator('[role="menuitem"]:visible, button:visible').filter(has_text=re.compile("1K Original|Original size", re.I)).first
            
            async with page.expect_download(timeout=10000) as download_info:
                await download_item.click(timeout=3000)
                try:
                    await resolution.wait_for(state="visible", timeout=1500)
                    await resolution.click(timeout=3000)
                except Exception:
                    pass
            download = await download_info.value
            
            final_path = OUTPUT_DIR / "Images" / f"A{i+1}.jpeg"
            await download.save_as(final_path)
            print(f"    [OK] Downloaded and saved image to: {final_path}")
            await page.wait_for_timeout(1000)

        print("[+] PROTOTYPE RUN COMPLETED SUCCESSFULLY!")

if __name__ == "__main__":
    asyncio.run(main())
