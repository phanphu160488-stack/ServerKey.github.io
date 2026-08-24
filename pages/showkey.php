<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Show Key</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Space+Grotesk:wght@400;600;700&display=swap" rel="stylesheet">
<link href="/php/assets/css/style.css" rel="stylesheet">
<style>
body{display:flex;align-items:center;justify-content:center;padding:20px}
.ctn{max-width:480px;width:100%}
.card{background:var(--card);border:1px solid var(--b);border-radius:20px;padding:24px;backdrop-filter:blur(16px);animation:fu .5s ease}
.rb{padding:14px;border-radius:12px;font-size:13px;line-height:1.7}
.rb.ok{background:rgba(0,230,118,.1);border:1px solid rgba(0,230,118,.3);color:var(--ok)}
.rb.er{background:rgba(255,23,68,.1);border:1px solid rgba(255,23,68,.3);color:var(--err)}
</style>
</head>
<body>
<div class="ctn"><div class="card">
<?php
$content = $err ? '<div class="rb er" style="display:block">❌ '.e($err).'</div>' : (
    '<div class="rb ok" style="display:block"><div style="text-align:center;margin-bottom:12px"><i class="fa-solid fa-circle-check" style="font-size:38px;color:var(--ok)"></i></div>'.
    '<h3 style="text-align:center;margin-bottom:10px">🎉 Key Của Bạn</h3>'.
    '<div style="background:rgba(0,0,0,.4);border-radius:10px;padding:14px;text-align:center;margin-bottom:10px"><div id="kv" style="font-family:monospace;font-size:18px;font-weight:700;color:var(--p);letter-spacing:2px">'.e($key).'</div></div>'.
    '<p style="font-size:12px;color:var(--d)">⏰ '.e($dur).' · Hết hạn: '.e($exp==='forever'?'Vĩnh viễn':$exp).'</p>'.
    '<div style="text-align:center;margin-top:12px"><button onclick="navigator.clipboard.writeText(document.getElementById(\'kv\').textContent).then(()=>alert(\'Đã copy!\'))" style="padding:10px 24px;border:none;border-radius:10px;background:linear-gradient(135deg,#00f2fe,#4facfe);color:#070913;font-weight:700;cursor:pointer;font-size:13px"><i class="fa-solid fa-copy"></i> Copy Key</button></div></div>'
);
echo $content;
?>
<div style="text-align:center;margin-top:14px"><a href="/" style="color:var(--p);font-size:12px"><i class="fa-solid fa-arrow-left"></i> Quay về</a></div>
</div></div>
</body></html>
