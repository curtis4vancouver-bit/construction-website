import asyncio
import re
import time
import sys
from pathlib import Path
from playwright.async_api import async_playwright

sys.stdout.reconfigure(encoding='utf-8')

async def connect_to_chrome(pw):
    port_file = r"C:\Users\Curtis\AppData\Local\Google\Chrome\User Data\DevToolsActivePort"
    try:
        with open(port_file, "r") as f:
            lines = f.readlines()
            cdp_url = f"ws://127.0.0.1:{lines[0].strip()}{lines[1].strip()}"
    except Exception:
        cdp_url = "http://127.0.0.1:9222"
        
    import urllib.parse
    parsed = urllib.parse.urlparse(cdp_url)
    port = parsed.port if parsed.port else 9222
    print(f"Connecting to Chrome at {cdp_url} with Host header 127.0.0.1:{port}...")
    browser = await pw.chromium.connect_over_cdp(cdp_url, headers={"Host": f"127.0.0.1:{port}"})
    
    flow_page = None
    for context in browser.contexts:
        for page in context.pages:
            if "labs.google" in page.url and "flow" in page.url:
                flow_page = page
                break
        if flow_page:
            break
            
    if not flow_page:
        raise Exception("No Google Flow tab found! Please ensure it's open.")
    
    return browser, flow_page

async def main():
    async with async_playwright() as pw:
        browser, page = await connect_to_chrome(pw)
        try:
            # Re-enter project if dashboard is active
            if "project" not in page.url:
                print("[!] Dashboard detected. Re-entering project...")
                project_card = page.locator('a[href*="/project/"]').first
                await project_card.click(force=True)
                await page.wait_for_timeout(3000)

            print("[+] Clicking 'Images' tab on sidebar...")
            try:
                images_tab = page.locator('button:has(i:has-text("image")), button:has-text("Images"), i:has-text("image")').first
                await images_tab.click(force=True, timeout=5000)
                await page.wait_for_timeout(2000)
            except Exception as e:
                print(f"[!] Warning: Could not click Images tab ({e}). Continuing...")

            # Scroll to the absolute bottom first to force-mount all grid elements
            print("[+] Pre-scrolling to the bottom to mount all virtualized cards...")
            await page.evaluate("""() => {
                window.scrollTo(0, 100000);
                const containers = Array.from(document.querySelectorAll('*')).filter(el => el.scrollHeight > el.clientHeight && window.getComputedStyle(el).overflowY !== 'visible');
                containers.forEach(c => c.scrollTop = 100000);
            }""")
            await page.wait_for_timeout(1500)
            
            # Scroll back to the top
            print("[+] Scrolling back to the top...")
            await page.evaluate("""() => {
                window.scrollTo(0, 0);
                const containers = Array.from(document.querySelectorAll('*')).filter(el => el.scrollHeight > el.clientHeight && window.getComputedStyle(el).overflowY !== 'visible');
                containers.forEach(c => c.scrollTop = 0);
            }""")
            await page.wait_for_timeout(1500)

            # Match B-roll image cards (both unrenamed 'Generated image' and already renamed 'A1'-'A51')
            # Excluding reference photos (like B1, B2)
            cards_locator = page.get_by_role("button", name=re.compile(r"^(Generated image|A\d+)$"))
            cards = await cards_locator.all()
            N = len(cards)
            print(f"[+] Found {N} B-roll cards to rename.")
            
            if N == 0:
                print("[-] No B-roll cards found. Exiting.")
                return

            print(f"[+] Initiating robust, sequential renaming for {N} images...")
            for i in range(N):
                new_name = f"A{N - i}"
                print(f"[{i+1}/{N}] Renaming card index {i} to '{new_name}'...")
                
                # Retrieve element from the static cards list
                card = cards[i]
                
                # Ensure it is visible and scrolled into view
                await card.scroll_into_view_if_needed()
                await page.wait_for_timeout(100)
                
                # Right click
                await card.click(button="right", force=True)
                await page.wait_for_timeout(350)
                
                # Click Rename
                rename_option = page.get_by_role("menuitem", name="whiteboard Rename")
                await rename_option.click()
                await page.wait_for_timeout(350)
                
                # Fill input box (Playwright automatically handles clearing and typing)
                input_box = page.get_by_role("textbox", name="Editable text")
                await input_box.fill(new_name)
                await page.wait_for_timeout(100)
                
                # Press Enter key to submit
                await page.keyboard.press("Enter")
                print(f"     [OK] Renamed to {new_name}")
                await page.wait_for_timeout(450)
                
            print("[+] Renaming loop completed successfully!")
        finally:
            await browser.close()

if __name__ == "__main__":
    asyncio.run(main())
