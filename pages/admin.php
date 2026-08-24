<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Admin | NEXUS PHP</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
<link href="/php/assets/css/style.css" rel="stylesheet">
<style>
.lay{display:flex;min-height:100vh}
.sb{width:250px;background:rgba(10,15,30,.95);border-right:1px solid var(--b);padding:20px 14px;position:fixed;top:0;left:0;bottom:0;z-index:100;display:flex;flex-direction:column;backdrop-filter:blur(12px);transition:transform .3s}
.sbl{display:flex;align-items:center;gap:10px;padding:6px 10px 20px;border-bottom:1px solid rgba(255,255,255,.06);margin-bottom:16px}
.sbl .ico{width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,rgba(0,242,254,.25),rgba(157,78,221,.25));border:1px solid var(--b);display:flex;align-items:center;justify-content:center;color:var(--p);font-size:18px}
.sbl h1{font-size:14px;font-weight:800;line-height:1.2}.sbl span{font-size:10px;color:var(--d)}
.nm{flex:1;display:flex;flex-direction:column;gap:3px}
.ni{display:flex;align-items:center;gap:10px;padding:11px 12px;border-radius:10px;color:var(--d);font-size:13px;font-weight:600;cursor:pointer;border:1px solid transparent;transition:all .3s}
.ni:hover{background:rgba(0,242,254,.07);color:var(--t);transform:translateX(3px)}
.ni.act{background:linear-gradient(135deg,rgba(0,242,254,.15),rgba(157,78,221,.12));border-color:var(--b);color:var(--p)}
.mn{flex:1;margin-left:250px;padding:24px 28px 50px}
.tp{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;flex-wrap:wrap;gap:10px}
.tp h2{font-size:20px;font-weight:800;display:flex;align-items:center;gap:8px}.tp h2 i{color:var(--p)}
.sec{display:none;animation:fu .4s ease}.sec.act{display:block}
.mt{display:none;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);color:var(--t);padding:8px 12px;border-radius:8px;cursor:pointer;font-size:14px}
@media(max-width:900px){.sb{transform:translateX(-100%)}.sb.open{transform:translateX(0)}.mn{margin-left:0;padding:16px}.mt{display:inline-flex}}
</style>
</head>
<body>
<div class="lay">
<aside class="sb" id="sb">
<div class="sbl"><div class="ico"><i class="fa-solid fa-user-shield"></i></div><div><h1>NEXUS ADMIN <span style="color:var(--a);font-size:9px">PHP</span></h1><span>Key Server v2</span></div></div>
<nav class="nm">
<div class="ni act" onclick="st('dash')"><i class="fa-solid fa-gauge-high"></i> Tổng Quan</div>
<div class="ni" onclick="st('keys')"><i class="fa-solid fa-key"></i> Quản Lý Key</div>
<div class="ni" onclick="st('notify')"><i class="fa-solid fa-bell"></i> Thông Báo</div>
<div class="ni" onclick="st('logs')"><i class="fa-solid fa-scroll"></i> Nhật Ký</div>
<div class="ni" onclick="st('money')"><i class="fa-solid fa-sack-dollar"></i> Doanh Thu</div>
<div class="ni" onclick="st('security')"><i class="fa-solid fa-shield-halved"></i> Bảo Mật</div>
<div class="ni" onclick="st('backup')"><i class="fa-solid fa-cloud-arrow-up"></i> Sao Lưu</div>
<div class="ni" onclick="st('sys')"><i class="fa-solid fa-server"></i> Hệ Thống</div>
<div class="ni" onclick="st('set')"><i class="fa-solid fa-gear"></i> Cài Đặt</div>
</nav>
<div style="border-top:1px solid rgba(255,255,255,.06);padding-top:12px;display:flex;flex-direction:column;gap:6px">
<a href="/" class="btn" style="justify-content:center"><i class="fa-solid fa-globe"></i> Trang Chủ</a>
<button onclick="fetch('/logout').then(()=>location.href='/login')" class="btn bd" style="justify-content:center"><i class="fa-solid fa-right-from-bracket"></i> Đăng Xuất</button>
</div></aside>
<main class="mn">
<div class="tp"><h2><i class="fa-solid fa-bars mt" onclick="document.getElementById('sb').classList.toggle('open')"></i> NEXUS ADMIN <span style="color:var(--a);font-size:12px">(PHP)</span></h2><div><button class="btn" onclick="refreshAll()"><i class="fa-solid fa-rotate"></i> Làm Mới</button></div></div>

<!-- DASHBOARD -->
<div class="sec act" id="t-dash">
<div class="sg">
<div class="sc"><div class="si"><i class="fa-solid fa-key"></i></div><div><div class="sv" id="sT">0</div><div class="sl">Tổng Key</div></div></div>
<div class="sc"><div class="si" style="color:var(--ok);background:rgba(0,230,118,.12)"><i class="fa-solid fa-circle-check"></i></div><div><div class="sv" id="sA">0</div><div class="sl">Hoạt Động</div></div></div>
<div class="sc"><div class="si" style="color:var(--w);background:rgba(255,171,0,.12)"><i class="fa-solid fa-clock"></i></div><div><div class="sv" id="sE">0</div><div class="sl">Hết Hạn</div></div></div>
<div class="sc"><div class="si" style="color:#d500f9;background:rgba(157,78,221,.12)"><i class="fa-solid fa-hand-fist"></i></div><div><div class="sv" id="sB">0</div><div class="sl">Bị Ban</div></div></div>
<div class="sc"><div class="si" style="color:var(--ok);background:rgba(0,230,118,.12)"><i class="fa-solid fa-mouse-pointer"></i></div><div><div class="sv" id="sC">0</div><div class="sl">Click</div></div></div>
<div class="sc"><div class="si" style="color:var(--w);background:rgba(255,171,0,.12)"><i class="fa-solid fa-sack-dollar"></i></div><div><div class="sv" id="sM">$0</div><div class="sl">Doanh Thu</div></div></div>
<div class="sc"><div class="si" style="color:#d500f9;background:rgba(157,78,221,.12)"><i class="fa-solid fa-ban"></i></div><div><div class="sv" id="sBI">0</div><div class="sl">IP Bị Ban</div></div></div>
<div class="sc"><div class="si" style="color:var(--s);background:rgba(79,172,254,.12)"><i class="fa-solid fa-bolt"></i></div><div><div class="sv" id="sTD">0</div><div class="sl">Hôm Nay</div></div></div>
</div>
<div class="gc"><div class="ch"><h3><i class="fa-solid fa-bell"></i> Thông Báo Gần Đây</h3></div><div id="dNoti">Đang tải...</div></div>
<div class="gc"><div class="ch"><h3><i class="fa-solid fa-scroll"></i> Hoạt Động</h3></div><div id="dLogs">Đang tải...</div></div>
</div>

<!-- KEYS -->
<div class="sec" id="t-keys">
<div class="gc"><div class="ch"><h3><i class="fa-solid fa-plus-circle"></i> Tạo Key Đơn</h3></div>
<div class="fr"><input type="text" id="ckI" class="sf" style="flex:2;min-width:160px" placeholder="Key tùy chỉnh (bỏ trống = tự tạo)">
<select id="ckD" class="sf" style="flex:1;min-width:100px"><option value="1day">1 Ngày</option><option value="3day">3 Ngày</option><option value="7day">7 Ngày</option><option value="30day">30 Ngày</option><option value="365day">365 Ngày</option><option value="forever">Vĩnh viễn</option></select>
<button onclick="ckCreate()" class="btn bp" style="flex:1;justify-content:center"><i class="fa-solid fa-bolt"></i> TẠO KEY</button></div></div>
<div class="gc"><div class="ch"><h3><i class="fa-solid fa-layer-group"></i> Bulk Create</h3></div>
<div class="fr"><input type="number" id="bkN" class="sf" style="width:80px" min="1" max="100" value="5"><input type="text" id="bkP" class="sf" style="width:100px" value="NEXUS" placeholder="Prefix">
<select id="bkD" class="sf"><option value="1day">1 Ngày</option><option value="3day">3 Ngày</option><option value="7day">7 Ngày</option><option value="forever">Vĩnh viễn</option></select>
<button onclick="bulkCreate()" class="btn bp"><i class="fa-solid fa-bolt"></i> TẠO HÀNG LOẠT</button></div><div id="bkR" style="margin-top:8px"></div></div>
<div class="gc"><div class="ch"><h3><i class="fa-solid fa-list-check"></i> Danh Sách Key</h3>
<div style="display:flex;gap:8px;flex-wrap:wrap"><input type="text" id="skI" onkeyup="fk()" class="sf" placeholder="🔍 Tìm...">
<select id="skS" class="sf" onchange="fk()"><option value="">Tất cả</option><option value="active">Hoạt động</option><option value="banned">Bị Ban</option><option value="expired">Hết hạn</option></select>
<button onclick="resetAll()" class="btn bd"><i class="fa-solid fa-trash-can"></i> RESET ALL</button></div></div>
<div class="fr" style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(255,255,255,.06)">
<select id="bdS" class="sf" style="width:auto"><option value="">-- Xóa hàng loạt --</option><option value="expired">Xóa Hết Hạn</option><option value="banned">Xóa Bị Ban</option></select>
<button onclick="bulkDel()" class="btn bd" style="padding:7px 12px"><i class="fa-solid fa-trash"></i> Xóa</button></div>
<div class="tw"><table><thead><tr><th>Key</th><th>Thời Hạn</th><th>Hết Hạn</th><th>Trạng Thái</th><th>HWID</th><th>Hành Động</th></tr></thead><tbody id="kTB"></tbody></table></div></div>
</div>

<!-- NOTIFICATIONS -->
<div class="sec" id="t-notify">
<div class="gc"><div class="ch"><h3><i class="fa-solid fa-paper-plane"></i> Gửi Thông Báo</h3></div>
<div class="fr" style="flex-direction:column;align-items:stretch">
<input type="text" id="nbT" class="sf" placeholder="Tiêu đề">
<textarea id="nbM" class="sf" style="min-height:80px;resize:vertical;font-family:inherit" placeholder="Nội dung..."></textarea>
<div class="fr"><select id="nbTp" class="sf"><option value="info">ℹ️ Info</option><option value="success">✅ Success</option><option value="warning">⚠️ Warning</option><option value="danger">🚨 Danger</option></select>
<button onclick="sendNoti()" class="btn bp"><i class="fa-solid fa-paper-plane"></i> Gửi</button></div></div></div>
<div class="gc"><div class="ch"><h3><i class="fa-solid fa-bell"></i> Lịch Sử</h3><button onclick="loadNoti()" class="btn"><i class="fa-solid fa-rotate"></i></button></div>
<div id="notiList">Đang tải...</div></div>
</div>

<!-- LOGS -->
<div class="sec" id="t-logs">
<div class="gc"><div class="ch"><h3><i class="fa-solid fa-scroll"></i> Nhật Ký</h3>
<div style="display:flex;gap:8px;flex-wrap:wrap"><select id="lgF" class="sf" onchange="loadLogs()"><option value="">Tất cả</option><option value="ADMIN_LOGIN">Login</option><option value="ADMIN_CREATE_KEY">Tạo key</option><option value="ADMIN_DELETE_KEY">Xóa key</option><option value="KEY_ISSUED">Phát hành</option></select>
<button onclick="loadLogs()" class="btn"><i class="fa-solid fa-rotate"></i></button>
<button onclick="clearLogs()" class="btn bd"><i class="fa-solid fa-trash"></i></button></div></div>
<div id="lgList">Đang tải...</div></div>
</div>

<!-- MONEY -->
<div class="sec" id="t-money">
<div class="gc"><div class="ch"><h3><i class="fa-solid fa-sack-dollar"></i> Doanh Thu</h3></div>
<div class="eh"><div class="ea" id="eA">$0.00</div><div class="el">Tổng Doanh Thu</div></div>
<div class="sg"><div class="sc"><div class="si"><i class="fa-solid fa-mouse-pointer"></i></div><div><div class="sv" id="eC">0</div><div class="sl">Tổng Click</div></div></div>
<div class="sc"><div class="si" style="color:var(--ok);background:rgba(0,230,118,.12)"><i class="fa-solid fa-bolt"></i></div><div><div class="sv" id="eT">0</div><div class="sl">Hôm Nay</div></div></div></div>
<div class="bc" id="eBC"></div></div>
</div>

<!-- SECURITY -->
<div class="sec" id="t-security">
<div class="gc"><div class="ch"><h3><i class="fa-solid fa-ban"></i> Ban IP</h3></div>
<div class="fr"><input type="text" id="biI" class="sf" style="flex:2" placeholder="IP"><input type="text" id="biR" class="sf" style="flex:1" placeholder="Lý do">
<button onclick="banIp()" class="btn bd" style="flex:1;justify-content:center"><i class="fa-solid fa-ban"></i> BAN</button></div></div>
<div class="gc"><div class="ch"><h3><i class="fa-solid fa-ban"></i> Danh Sách IP Ban</h3><button onclick="loadBanIps()" class="btn"><i class="fa-solid fa-rotate"></i></button></div>
<div class="tw"><table><thead><tr><th>IP</th><th>Thời Gian</th><th>Lý Do</th><th>Hành Động</th></tr></thead><tbody id="biTB"></tbody></table></div></div>
</div>

<!-- BACKUP -->
<div class="sec" id="t-backup">
<div class="gc"><div class="ch"><h3><i class="fa-solid fa-cloud-arrow-up"></i> Tạo Backup</h3></div>
<button onclick="mkBackup()" class="btn bp"><i class="fa-solid fa-download"></i> Tạo Backup</button><div id="bkInfo" style="margin-top:10px"></div></div>
<div class="gc"><div class="ch"><h3><i class="fa-solid fa-box-archive"></i> Danh Sách</h3><button onclick="loadBackups()" class="btn"><i class="fa-solid fa-rotate"></i></button></div>
<div class="tw"><table><thead><tr><th>Tên</th><th>Size</th><th>Ngày</th><th>Hành Động</th></tr></thead><tbody id="bkTB"></tbody></table></div></div>
</div>

<!-- SYSTEM -->
<div class="sec" id="t-sys">
<div class="gc"><div class="ch"><h3><i class="fa-solid fa-server"></i> Hệ Thống</h3><button onclick="loadSys()" class="btn"><i class="fa-solid fa-rotate"></i></button></div>
<div id="sysInfo">Đang tải...</div></div>
<div class="gc"><div class="ch"><h3><i class="fa-solid fa-file-lines"></i> Files</h3></div>
<div class="tw"><table><thead><tr><th>File</th><th>Size</th><th>Modified</th></tr></thead><tbody id="sysTB"></tbody></table></div></div>
</div>

<!-- SETTINGS -->
<div class="sec" id="t-set">
<div class="gc"><div class="ch"><h3><i class="fa-solid fa-gear"></i> Cài Đặt</h3><button onclick="saveSet()" class="btn bp"><i class="fa-solid fa-floppy-disk"></i> Lưu</button></div>
<div class="sr"><div class="sri"><h4>Mã hoá HTML</h4><p class="srp">Chống xem source</p></div><label class="tg"><input type="checkbox" id="setHE"><span class="sl"></span></label></div>
<div class="sr"><div class="sri"><h4>Chống F12</h4></div><label class="tg"><input type="checkbox" id="setAF"><span class="sl"></span></label></div>
<div class="sr"><div class="sri"><h4>Giá Link4m / 1K view</h4></div><input type="number" step="0.01" id="setR" class="sf" style="width:120px"></div>
<div class="sr"><div class="sri"><h4>API Key 1</h4></div><input type="text" id="setK1" class="sf" style="width:220px"></div>
<div class="sr"><div class="sri"><h4>API Key 2</h4></div><input type="text" id="setK2" class="sf" style="width:220px"></div>
<div class="sr"><div class="sri"><h4>Đổi Mật Khẩu</h4></div><input type="password" id="setPw" class="sf" style="width:180px" placeholder="Để trống nếu không đổi"></div>
<div class="sr"><div class="sri"><h4>Bật Webhook</h4><p class="srp">Gửi thông báo Discord/Slack</p></div><label class="tg"><input type="checkbox" id="setNE"><span class="sl"></span></label></div>
<div class="sr"><div class="sri"><h4>Webhook URL</h4></div><input type="text" id="setNW" class="sf" style="width:350px" placeholder="https://discord.com/api/webhooks/..."></div>
</div>
</div>
</main>
</div>
<div class="toast" id="toast"></div>
<script src="/php/assets/js/admin.js"></script>
</body></html>
