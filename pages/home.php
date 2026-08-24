<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>NEXUS KEY SERVER</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
<link href="/php/assets/css/style.css" rel="stylesheet">
<style>
body{display:flex;align-items:center;justify-content:center}
.ctn{max-width:520px;width:100%;padding:20px}
.hero{text-align:center;margin-bottom:28px}
.logo{width:80px;height:80px;border-radius:20px;background:linear-gradient(135deg,rgba(0,242,254,.25),rgba(157,78,221,.25));border:1px solid var(--b);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:36px;color:var(--p);box-shadow:0 0 30px rgba(0,242,254,.2);animation:float 3s ease-in-out infinite}
h1{font-size:26px;font-weight:800;background:linear-gradient(135deg,var(--p),var(--a));-webkit-background-clip:text;-webkit-text-fill-color:transparent;margin-bottom:6px}
.sub{color:var(--d);font-size:13px}
.card{background:var(--card);border:1px solid var(--b);border-radius:20px;padding:24px;margin-bottom:14px;backdrop-filter:blur(16px);animation:fu .5s ease}
.card h3{font-size:16px;margin-bottom:14px;display:flex;align-items:center;gap:8px}.card h3 i{color:var(--p)}
.ig{position:relative}.ig i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--d);font-size:14px}
.ig input{width:100%;padding:12px 14px 12px 42px;background:rgba(0,0,0,.4);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:14px;outline:none;transition:all .3s}
.ig input:focus{border-color:var(--p);box-shadow:0 0 12px rgba(0,242,254,.15)}
.btn{padding:14px;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;transition:all .3s;display:flex;align-items:center;justify-content:center;gap:8px;width:100%}
.bp{background:linear-gradient(135deg,#00f2fe,#4facfe);color:#070913}.bp:hover{box-shadow:0 5px 20px var(--pg);transform:translateY(-2px)}
.ds{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px}
.db{padding:10px 16px;border-radius:10px;border:1px solid rgba(255,255,255,.1);background:rgba(0,0,0,.3);color:var(--t);cursor:pointer;font-size:13px;font-weight:600;transition:all .3s}
.db.act,.db:hover{border-color:var(--p);color:var(--p);background:rgba(0,242,254,.1)}
.rb{margin-top:14px;padding:14px;border-radius:12px;font-size:13px;line-height:1.6;display:none}
.rb.ok{display:block;background:rgba(0,230,118,.1);border:1px solid rgba(0,230,118,.3);color:var(--ok)}
.rb.er{display:block;background:rgba(255,23,68,.1);border:1px solid rgba(255,23,68,.3);color:var(--err)}
.foot{text-align:center;color:var(--d);font-size:11px;margin-top:16px}
.nb{background:linear-gradient(135deg,rgba(0,242,254,.12),rgba(157,78,221,.12));border:1px solid var(--b);border-radius:14px;padding:14px 18px;margin-bottom:14px;font-size:13px;line-height:1.6;animation:fu .6s ease}
.nb .nt{font-weight:700;color:var(--p);display:flex;align-items:center;gap:6px;margin-bottom:4px}.nb .nm{color:var(--d)}
.nb.warn{border-color:rgba(255,171,0,.3);background:rgba(255,171,0,.08)}.nb.warn .nt{color:#ffab00}
.nb.danger{border-color:rgba(255,23,68,.3);background:rgba(255,23,68,.08)}.nb.danger .nt{color:#ff1744}
.nb.success{border-color:rgba(0,230,118,.3);background:rgba(0,230,118,.08)}.nb.success .nt{color:#00e676}
</style>
</head>
<body>
<div class="ctn">
    <div class="hero">
        <div class="logo"><i class="fa-solid fa-key"></i></div>
        <h1>NEXUS KEY SERVER</h1>
        <p class="sub">Hệ thống quản lý key bản quyền (PHP)</p>
    </div>
    <div id="notifyArea"></div>
    <div class="card">
        <h3><i class="fa-solid fa-magnifying-glass"></i> Kiểm Tra Key</h3>
        <div style="display:flex;flex-direction:column;gap:12px">
            <div class="ig"><i class="fa-solid fa-key"></i><input type="text" id="ck" placeholder="Nhập key cần kiểm tra..."></div>
            <button class="btn bp" onclick="checkKey()"><i class="fa-solid fa-check"></i> Kiểm Tra</button>
            <div id="ckR" class="rb"></div>
        </div>
    </div>
    <div class="card">
        <h3><i class="fa-solid fa-gift" style="color:var(--a)"></i> Nhận Key Miễn Phí</h3>
        <p style="font-size:13px;color:var(--d);margin-bottom:12px">Vượt link Link4m để nhận key</p>
        <div class="ds">
            <div class="db act" data-d="1day" onclick="selDur(this)">1 Ngày</div>
            <div class="db" data-d="3day" onclick="selDur(this)">3 Ngày</div>
            <div class="db" data-d="7day" onclick="selDur(this)">7 Ngày</div>
        </div>
        <button class="btn bp" onclick="getKey()"><i class="fa-solid fa-bolt"></i> Lấy Key Ngay</button>
        <div id="gkR" class="rb"></div>
    </div>
    <div class="foot">Powered by <a href="/admin">NEXUS KEY SERVER</a> (PHP) · Multi-file Architecture</div>
</div>
<script src="/php/assets/js/app.js"></script>
<script>loadNotify();</script>
</body></html>
