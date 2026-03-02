#!/bin/bash
BLADE_DIR="/var/www/mybilling/resources/views"
BACKUP_DIR="/var/www/mybilling/storage/blade_backup_$(date +%Y%m%d_%H%M%S)"

echo "============================================"
echo " ISP Billing - Update Mobile Toggle Button"
echo "============================================"

echo ""
echo "[1/4] Membuat backup ke: $BACKUP_DIR"
mkdir -p "$BACKUP_DIR"
find "$BLADE_DIR" -name "*.blade.php" | while read f; do
    rel=$(realpath --relative-to="$BLADE_DIR" "$f")
    mkdir -p "$BACKUP_DIR/$(dirname "$rel")"
    cp "$f" "$BACKUP_DIR/$rel"
done
echo "      Backup selesai."

echo ""
echo "[2/4] Menghapus tombol arrow lama..."
find "$BLADE_DIR" -name "*.blade.php" | while read f; do
    sed -i '/<a href="#" id="menuToggleBtn"/d' "$f"
done
echo "      Selesai."

echo ""
echo "[3/4] Update CSS mobile toggle..."
find "$BLADE_DIR" -name "*.blade.php" | while read f; do
    if grep -q 'mobile-menu-btn { display: none; }' "$f"; then
        python3 - "$f" << 'PYEOF'
import sys
filepath = sys.argv[1]
with open(filepath, 'r') as fh:
    content = fh.read()
old = '        .mobile-menu-btn { display: none; }'
new = '''        /* ===== MOBILE TOGGLE BUTTON (HAMBURGER MODERN) ===== */
        .mobile-menu-btn {
            display: none;
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 1060;
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--sidebar-bg-start), var(--accent));
            border: none;
            border-radius: 12px;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(233,69,96,0.4);
            transition: all 0.3s ease;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 10px;
        }
        .mobile-menu-btn:hover {
            transform: scale(1.08);
            box-shadow: 0 6px 20px rgba(233,69,96,0.5);
        }
        .mobile-menu-btn .bar {
            display: block;
            width: 20px;
            height: 2px;
            background: white;
            border-radius: 2px;
            transition: all 0.3s ease;
            transform-origin: center;
        }
        .mobile-menu-btn.is-open .bar:nth-child(1) {
            transform: translateY(7px) rotate(45deg);
        }
        .mobile-menu-btn.is-open .bar:nth-child(2) {
            opacity: 0;
            transform: scaleX(0);
        }
        .mobile-menu-btn.is-open .bar:nth-child(3) {
            transform: translateY(-7px) rotate(-45deg);
        }'''
if old in content:
    content = content.replace(old, new, 1)
    with open(filepath, 'w') as fh:
        fh.write(content)
    print(f"  CSS updated: {filepath}")
PYEOF
    fi
done

echo ""
echo "[4/4] Update HTML tombol & JS..."
find "$BLADE_DIR" -name "*.blade.php" | while read f; do
    if ! grep -q 'toggleSidebar' "$f"; then
        continue
    fi
    if ! grep -q 'class="bar"' "$f"; then
        sed -i 's|<body>|<body>\n\n<button id="menuToggleBtn" class="mobile-menu-btn" onclick="toggleSidebar()" aria-label="Toggle menu">\n    <span class="bar"></span>\n    <span class="bar"></span>\n    <span class="bar"></span>\n</button>|' "$f"
    fi
    sed -i 's|\.main-content { margin-left: 0 !important; padding: 15px; }|.main-content { margin-left: 0 !important; padding: 15px; padding-top: 72px; }|g' "$f"
    sed -i 's|background: rgba(0,0,0,0.5); z-index: 1040; }|background: rgba(0,0,0,0.5); z-index: 1040; backdrop-filter: blur(2px); }|g' "$f"
    python3 - "$f" << 'PYEOF'
import sys, re
filepath = sys.argv[1]
with open(filepath, 'r') as fh:
    content = fh.read()
old_fn = r'function toggleSidebar\(\) \{[^}]*\}'
new_fn = '''function toggleSidebar() {
    const sidebar = document.querySelector(".sidebar");
    const overlay = document.getElementById("sidebarOverlay");
    const btn     = document.getElementById("menuToggleBtn");
    sidebar.classList.toggle("show");
    overlay.classList.toggle("show");
    btn.classList.toggle("is-open");
}'''
result = re.sub(old_fn, new_fn, content, flags=re.DOTALL)
old_touch = 'document.getElementById("sidebarOverlay").classList.remove("show");\n    }\n});'
new_touch = 'document.getElementById("sidebarOverlay").classList.remove("show");\n        document.getElementById("menuToggleBtn").classList.remove("is-open");\n    }\n});'
if old_touch in result:
    result = result.replace(old_touch, new_touch, 1)
if result != content:
    with open(filepath, 'w') as fh:
        fh.write(result)
    print(f"  JS updated: {filepath}")
PYEOF
    echo "  Processed: $f"
done

echo ""
echo "============================================"
echo " SELESAI! Semua blade sudah diupdate."
echo " Backup tersimpan di: $BACKUP_DIR"
echo "============================================"
