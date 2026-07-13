import asyncio
import re
import time
from pathlib import Path
from playwright.async_api import async_playwright

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
            print("[+] Clicking 'Videos' tab on sidebar...")
            try:
                # Try multiple locators for the Videos tab
                videos_tab = page.locator('i:has-text("videocam"), button:has-text("Videos"), button:has-text("videocam")').first
                await videos_tab.click(force=True, timeout=5000)
                await page.wait_for_timeout(2000)
            except Exception as e:
                print(f"[!] Warning: Could not click Videos tab ({e}). Continuing anyway...")
            
            # Find all video cards (buttons containing a video tag)
            cards_locator = page.locator('button:has(video)')
            N = await cards_locator.count()
            print(f"[+] Found {N} video cards on the page.")
            
            if N == 0:
                print("[-] No video cards found. Exiting.")
                return
                
            # Loop from 0 to N-1 (top-down)
            # The newest is at index 0, oldest at index N-1
            # We rename index i to (N - i)
            for i in range(N):
                new_name = str(N - i)
                print(f"[{i+1}/{N}] Renaming card {i} (newest to oldest index) to '{new_name}'...")
                
                # Retrieve the card locator again to prevent stale element issues
                card = page.locator('button:has(video)').nth(i)
                
                # Right click
                await card.click(button="right", force=True)
                await page.wait_for_timeout(600)
                
                # Click Rename in context menu
                rename_option = page.get_by_role("menuitem", name="whiteboard Rename")
                await rename_option.click()
                await page.wait_for_timeout(600)
                
                # Fill input box (Playwright fill automatically deletes existing text and types)
                input_box = page.get_by_role("textbox", name="Editable text")
                await input_box.fill(new_name)
                await page.wait_for_timeout(200)
                
                # Press Enter key to submit
                await page.keyboard.press("Enter")
                print(f"     [✓] Successfully renamed and submitted.")
                await page.wait_for_timeout(800)
                
            print("[+] Renaming loop completed successfully!")
        finally:
            await browser.close()

if __name__ == "__main__":
    asyncio.run(main())
