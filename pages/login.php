<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Admin Login</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
<link href="/php/assets/css/style.css" rel="stylesheet">
<style>
body{display:flex;align-items:center;justify-content:center}
.ctn{max-width:400px;width:100%}
.card{background:var(--card);border:1px solid var(--b);border-radius:20px;padding:32px;backdrop-filter:blur(16px);animation:fu .5s ease}
.logo{text-align:center;margin-bottom:20px}
.li{width:60px;height:60px;border-radius:16px;background:linear-gradient(135deg,rgba(0,242,254,.25),rgba(157,78,221,.25));border:1px solid var(--b);display:flex;align-items:center;justify-content:center;margin:0 auto 10px;font-size:26px;color:var(--p);box-shadow:0 0 20px rgba(0,242,254,.2)}
h2{font-size:20px;font-weight:800;text-align:center}
.fg{margin-top:16px}.fg label{display:block;font-size:12px;color:var(--d);margin-bottom:6px;font-weight:600}
.iw{position:relative}.iw i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--d);font-size:14px}
.iw input{width:100%;padding:12px 14px 12px 42px;background:rgba(0,0,0,.4);border:1px solid rgba(255,255,255,.1);border-radius:12px;color:#fff;font-size:14px;outline:none;transition:all .3s}
.iw input:focus{border-color:var(--p);box-shadow:0 0 12px rgba(0,242,254,.15)}
.btn{width:100%;padding:14px;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;transition:all .3s;margin-top:18px;display:flex;align-items:center;justify-content:center;gap:8px;background:linear-gradient(135deg,#00f2fe,#4facfe);color:#070913}
.btn:hover{box-shadow:0 5px 20px var(--pg);transform:translateY(-2px)}
.em{margin-top:12px;padding:10px;border-radius:10px;background:rgba(255,23,68,.1);border:1px solid rgba(255,23,68,.3);color:#ff1744;font-size:13px;text-align:center;display:none}
</style>
</head>
<body>
<div class="ctn"><div class="card">
<div class="logo"><div class="li"><i class="fa-solid fa-user-shield"></i></div><h2>Admin Login</h2></div>
<div class="fg"><label>Mật khẩu</label><div class="iw"><i class="fa-solid fa-lock"></i><input type="password" id="lp" placeholder="Nhập mật khẩu..." onkeydown="if(event.key==='Enter')dl()"></div></div>
<button class="btn" onclick="dl()"><i class="fa-solid fa-right-to-bracket"></i> Đăng Nhập</button>
<div id="le" class="em"></div>
</div></div>
<script>
function dl(){const p=document.getElementById("lp").value;if(!p)return;const e=document.getElementById("le");e.style.display="none";fetch("/login",{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({password:p})}).then(r=>r.json()).then(d=>{if(d.status==="success")window.location.href="/admin";else{e.textContent=d.message;e.style.display="block"}}).catch(()=>{e.textContent="Lỗi kết nối";e.style.display="block"})}
</script>
</body></html>
