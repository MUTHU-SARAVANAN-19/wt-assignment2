import http.server
import socketserver
import threading
import subprocess
import os
import time

PORT = 8989
DIRECTORY = os.path.dirname(os.path.abspath(__file__))

class Handler(http.server.SimpleHTTPRequestHandler):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, directory=DIRECTORY, **kwargs)
    def log_message(self, format, *args):
        pass # suppress console noise

# Start local test web server
httpd = socketserver.TCPServer(("127.0.0.1", PORT), Handler)
server_thread = threading.Thread(target=httpd.serve_forever)
server_thread.daemon = True
server_thread.start()
print(f"HTTP Server started at http://127.0.0.1:{PORT}")

chrome = r"C:\Program Files\Google\Chrome\Application\chrome.exe"
screenshot_dir = os.path.join(DIRECTORY, "screenshots")
os.makedirs(screenshot_dir, exist_ok=True)

pages = [
    ("q1_registration/index.html", "01_q1_registration_form.png", (1100, 1050)),
    ("screenshots/previews/q1_validation_state.html", "02_q1_client_validation.png", (1100, 1150)),
    ("screenshots/previews/q2_admin_dashboard.html", "03_q2_admin_dashboard.png", (1280, 850)),
    ("screenshots/previews/q2_add_student.html", "04_q2_add_student.png", (1100, 900)),
    ("screenshots/previews/q2_view_student.html", "05_q2_view_student.png", (1100, 850)),
    ("screenshots/previews/q3_login_page.html", "06_q3_servlet_login.png", (1000, 750)),
    ("screenshots/previews/q3_student_portal.html", "07_q3_student_portal.png", (1280, 950)),
]

for url_path, filename, (w, h) in pages:
    full_url = f"http://127.0.0.1:{PORT}/{url_path}"
    out_file = os.path.join(screenshot_dir, filename)
    cmd = [
        chrome,
        "--headless",
        "--no-sandbox",
        "--disable-gpu",
        f"--window-size={w},{h}",
        f"--screenshot={out_file}",
        full_url
    ]
    p = subprocess.run(cmd, capture_output=True, text=True)
    size = os.path.getsize(out_file) if os.path.exists(out_file) else 0
    print(f"Captured {filename}: {size} bytes")

httpd.shutdown()
print("Screenshot capture completed successfully!")
