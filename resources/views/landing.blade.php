<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Thaka Khawa — Smart Mess Management System for shared living">
<title>Thaka Khawa — Smart Mess Management Platform</title>
<link rel="shortcut icon" type="image/x-icon" href="{{ URL::asset('/build/img/favicon.png') }}">
<link rel="stylesheet" href="{{ URL::asset('build/css/bootstrap.min.css') }}">
<!-- Tabler Icons — CDN primary, local fallback -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.34.0/dist/tabler-icons.min.css">
<style>
/* local font-face fallback in case CDN is blocked */
@font-face {
    font-family: "tabler-icons";
    font-style: normal;
    font-weight: 400;
    src: url("{{ URL::asset('build/plugins/tabler-icons/fonts/tabler-icons.woff2') }}") format("woff2"),
         url("{{ URL::asset('build/plugins/tabler-icons/fonts/tabler-icons.woff') }}") format("woff"),
         url("{{ URL::asset('build/plugins/tabler-icons/fonts/tabler-icons.ttf') }}") format("truetype");
}
</style>
<style>
/* ── Variables ─────────────────────────────────────────────── */
:root {
  --orange:      #FE9F43;
  --orange-dark: #e8892e;
  --orange-glow: rgba(254,159,67,.25);
  --navy:        #141432;
  --navy2:       #1b1b4b;
  --navy3:       #092C4C;
  --purple:      #353570;
  --green:       #3EB780;
  --text:        #212B36;
  --muted:       #6b7280;
  --border:      #E6EAED;
  --white:       #ffffff;
  --light:       #f9fafb;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
html { scroll-behavior: smooth; }
body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: var(--text); background: var(--white); overflow-x: hidden; }
a { text-decoration: none; }
img { max-width: 100%; }

/* ── Typography ─────────────────────────────────────────────── */
.section-label { font-size: 12px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; color: var(--orange); }
.section-title { font-size: clamp(1.8rem, 3.5vw, 2.6rem); font-weight: 800; color: var(--navy); line-height: 1.2; }
.section-sub   { font-size: 1rem; color: var(--muted); line-height: 1.7; max-width: 560px; margin: 0 auto; }

/* ── Buttons ─────────────────────────────────────────────────── */
.btn-orange {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 13px 28px; border-radius: 10px;
  background: var(--orange); color: var(--white);
  font-weight: 700; font-size: 15px; border: none;
  box-shadow: 0 6px 20px var(--orange-glow);
  transition: all .25s; cursor: pointer;
}
.btn-orange:hover { background: var(--orange-dark); color: var(--white); transform: translateY(-2px); box-shadow: 0 10px 28px rgba(254,159,67,.4); }
.btn-ghost {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 12px 26px; border-radius: 10px;
  background: transparent; color: var(--white);
  font-weight: 600; font-size: 15px;
  border: 2px solid rgba(255,255,255,.3);
  transition: all .25s;
}
.btn-ghost:hover { border-color: var(--orange); color: var(--orange); background: rgba(254,159,67,.08); }
.btn-outline-orange {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 12px 26px; border-radius: 10px;
  background: transparent; color: var(--orange);
  font-weight: 700; font-size: 15px;
  border: 2px solid var(--orange);
  transition: all .25s;
}
.btn-outline-orange:hover { background: var(--orange); color: var(--white); box-shadow: 0 6px 20px var(--orange-glow); }

/* ══════════════════════════════════════════════════════════════
   NAVBAR
══════════════════════════════════════════════════════════════ */
.lnav {
  position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
  background: rgba(20,20,50,.92);
  backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
  border-bottom: 1px solid rgba(255,255,255,.06);
  transition: all .3s;
}
.lnav.scrolled { background: rgba(20,20,50,.98); box-shadow: 0 4px 24px rgba(0,0,0,.35); }
.lnav-inner { display: flex; align-items: center; height: 68px; gap: 0; }

/* Logo */
.lnav-logo { display: flex; align-items: center; gap: 10px; color: var(--white); font-size: 1.2rem; font-weight: 800; letter-spacing: -.3px; flex-shrink: 0; }
.lnav-logo .logo-box {
  width: 38px; height: 38px; border-radius: 10px;
  background: var(--orange);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.1rem; color: var(--white);
  box-shadow: 0 4px 14px var(--orange-glow); flex-shrink: 0;
}
.lnav-logo em { color: var(--orange); font-style: normal; }

/* Nav links */
.lnav-links { display: flex; align-items: center; gap: 2px; margin: 0 auto; list-style: none; }
.lnav-links a { padding: 7px 15px; border-radius: 8px; font-size: .875rem; font-weight: 500; color: rgba(255,255,255,.75); transition: all .2s; }
.lnav-links a:hover { color: var(--orange); background: rgba(254,159,67,.1); }

/* Right auth */
.lnav-auth { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.btn-nav-in {
  padding: 8px 18px; border-radius: 8px; font-size: .875rem; font-weight: 600;
  color: rgba(255,255,255,.85); border: 1.5px solid rgba(255,255,255,.2);
  background: transparent; transition: all .2s;
}
.btn-nav-in:hover { color: var(--orange); border-color: var(--orange); background: rgba(254,159,67,.08); }
.btn-nav-up {
  padding: 8px 20px; border-radius: 8px; font-size: .875rem; font-weight: 700;
  background: var(--orange); color: var(--white); border: none;
  box-shadow: 0 3px 12px var(--orange-glow); transition: all .25s;
}
.btn-nav-up:hover { background: var(--orange-dark); color: var(--white); transform: translateY(-1px); box-shadow: 0 6px 18px rgba(254,159,67,.45); }

/* Avatar pill */
.nav-user-pill {
  display: flex; align-items: center; gap: 9px;
  padding: 5px 14px 5px 5px; border-radius: 50px;
  border: 1.5px solid rgba(255,255,255,.15);
  background: rgba(255,255,255,.06); color: var(--white);
  font-size: .85rem; font-weight: 600; transition: all .2s;
}
.nav-user-pill:hover { border-color: var(--orange); color: var(--white); }
.nav-avatar {
  width: 30px; height: 30px; border-radius: 50%;
  background: var(--orange); color: var(--white);
  font-weight: 700; font-size: 12px;
  display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.btn-nav-dash {
  padding: 8px 18px; border-radius: 8px; font-size: .875rem; font-weight: 700;
  background: var(--orange); color: var(--white); border: none;
  display: inline-flex; align-items: center; gap: 6px;
  box-shadow: 0 3px 12px var(--orange-glow); transition: all .25s;
}
.btn-nav-dash:hover { background: var(--orange-dark); color: var(--white); transform: translateY(-1px); }

/* Mobile */
.lnav-toggle { display: none; border: none; background: none; width: 38px; height: 38px; border-radius: 8px; align-items: center; justify-content: center; cursor: pointer; color: rgba(255,255,255,.8); font-size: 1.3rem; transition: background .2s; }
.lnav-toggle:hover { background: rgba(255,255,255,.08); }
.lnav-mobile { display: none !important; background: rgba(20,20,50,.98); border-top: 1px solid rgba(255,255,255,.06); padding: 10px 0; }
.lnav-mobile.open { display: block !important; }
.lnav-mobile a { display: flex; align-items: center; gap: 10px; padding: 11px 20px; font-size: .9rem; color: rgba(255,255,255,.75); font-weight: 500; transition: all .2s; }
.lnav-mobile a:hover { color: var(--orange); background: rgba(254,159,67,.08); }
.lnav-mobile .mob-divider { height: 1px; background: rgba(255,255,255,.07); margin: 6px 0; }
@media(max-width:991px) {
  .lnav-links, .lnav-auth-desktop { display: none !important; }
  .lnav-toggle { display: flex !important; }
}

/* ══════════════════════════════════════════════════════════════
   HERO
══════════════════════════════════════════════════════════════ */
.hero {
  min-height: 100vh;
  background: var(--navy);
  display: flex; align-items: center;
  padding: 110px 0 80px;
  position: relative; overflow: hidden;
}
/* Animated blob background */
.hero::before {
  content: '';
  position: absolute; top: -200px; right: -200px;
  width: 700px; height: 700px; border-radius: 50%;
  background: radial-gradient(circle, rgba(254,159,67,.18) 0%, transparent 70%);
  animation: blob1 8s ease-in-out infinite alternate;
}
.hero::after {
  content: '';
  position: absolute; bottom: -150px; left: -150px;
  width: 600px; height: 600px; border-radius: 50%;
  background: radial-gradient(circle, rgba(53,53,112,.5) 0%, transparent 70%);
  animation: blob2 10s ease-in-out infinite alternate;
}
@keyframes blob1 { 0%{transform:translate(0,0) scale(1)} 100%{transform:translate(-80px,60px) scale(1.1)} }
@keyframes blob2 { 0%{transform:translate(0,0) scale(1)} 100%{transform:translate(60px,-40px) scale(1.15)} }

.hero-inner { position: relative; z-index: 2; }
.hero-tag {
  display: inline-flex; align-items: center; gap: 8px;
  background: rgba(254,159,67,.12); border: 1px solid rgba(254,159,67,.3);
  color: var(--orange); padding: 7px 16px; border-radius: 50px;
  font-size: 13px; font-weight: 600; margin-bottom: 24px;
}
.hero-tag span { width: 6px; height: 6px; background: var(--orange); border-radius: 50%; display: inline-block; animation: pulse-dot 1.5s ease-in-out infinite; }
@keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.7)} }

.hero h1 { font-size: clamp(2.2rem, 5vw, 3.8rem); font-weight: 800; color: var(--white); line-height: 1.12; }
.hero h1 .orange { color: var(--orange); position: relative; display: inline-block; }
.hero h1 .orange::after {
  content: '';
  position: absolute; bottom: -4px; left: 0; right: 0; height: 3px;
  background: var(--orange); border-radius: 2px; opacity: .5;
}
.hero-sub { font-size: 1.1rem; color: rgba(255,255,255,.65); line-height: 1.75; max-width: 520px; margin-top: 18px; }
.hero-btns { display: flex; gap: 14px; flex-wrap: wrap; margin-top: 32px; }
.hero-proof { display: flex; gap: 24px; flex-wrap: wrap; margin-top: 28px; }
.hero-proof-item { display: flex; align-items: center; gap: 7px; font-size: 13px; color: rgba(255,255,255,.55); }
.hero-proof-item i { color: var(--green); font-size: 15px; }

/* Hero mockup card */
.hero-card {
  background: rgba(255,255,255,.04);
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 20px; overflow: hidden;
  backdrop-filter: blur(12px);
  box-shadow: 0 30px 80px rgba(0,0,0,.4);
  animation: float 6s ease-in-out infinite;
}
@keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
.hero-card-top { background: rgba(254,159,67,.15); border-bottom: 1px solid rgba(255,255,255,.08); padding: 12px 16px; display: flex; align-items: center; gap: 7px; }
.hct-dot { width: 10px; height: 10px; border-radius: 50%; }
.hero-card-body { padding: 20px; }
.mock-stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px; }
.mock-stat {
  background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.08);
  border-radius: 12px; padding: 14px 16px;
}
.mock-stat .label { font-size: 11px; color: rgba(255,255,255,.4); margin-bottom: 4px; }
.mock-stat .val { font-size: 1.3rem; font-weight: 800; }
.mock-stat .val.orange { color: var(--orange); }
.mock-stat .val.green  { color: var(--green); }
.mock-stat .val.blue   { color: #60a5fa; }
.mock-stat .val.white  { color: var(--white); }

.mock-meal-row { display: flex; align-items: center; justify-content: space-between; padding: 9px 0; border-bottom: 1px solid rgba(255,255,255,.06); font-size: 13px; color: rgba(255,255,255,.7); }
.mock-meal-row:last-child { border: none; }
.mock-badge { padding: 3px 10px; border-radius: 50px; font-size: 11px; font-weight: 700; }
.mock-badge.open   { background: rgba(62,183,128,.15); color: var(--green); }
.mock-badge.closed { background: rgba(255,255,255,.08); color: rgba(255,255,255,.4); }
.mock-chart-bar { height: 4px; border-radius: 2px; background: rgba(255,255,255,.1); margin-top: 6px; overflow: hidden; }
.mock-chart-fill { height: 100%; border-radius: 2px; background: var(--orange); }

/* Floating stat badges */
.hero-float-badge {
  position: absolute; z-index: 3;
  background: var(--white); border-radius: 12px;
  padding: 10px 14px; box-shadow: 0 8px 24px rgba(0,0,0,.25);
  display: flex; align-items: center; gap: 10px;
  font-size: 13px; font-weight: 600; color: var(--navy);
  white-space: nowrap;
  animation: floatBadge 4s ease-in-out infinite;
}
.hero-float-badge .badge-icon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
@keyframes floatBadge { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }

/* ══════════════════════════════════════════════════════════════
   STATS STRIP
══════════════════════════════════════════════════════════════ */
.stats-strip { background: var(--orange); padding: 32px 0; }
.stat-item { text-align: center; }
.stat-num { font-size: 2rem; font-weight: 800; color: var(--white); line-height: 1; }
.stat-lbl { font-size: 13px; color: rgba(255,255,255,.8); margin-top: 4px; font-weight: 500; }
.stat-divider { width: 1px; background: rgba(255,255,255,.25); margin: 0 auto; height: 50px; }

/* ══════════════════════════════════════════════════════════════
   FEATURES
══════════════════════════════════════════════════════════════ */
.features { padding: 96px 0; background: var(--white); }
.feat-card {
  background: var(--light); border: 1px solid var(--border);
  border-radius: 16px; padding: 28px;
  transition: all .3s; height: 100%;
  position: relative; overflow: hidden;
}
.feat-card::after {
  content: ''; position: absolute; bottom: 0; left: 0; right: 0; height: 3px;
  background: var(--orange); transform: scaleX(0); transform-origin: left;
  transition: transform .3s;
}
.feat-card:hover { border-color: rgba(254,159,67,.4); box-shadow: 0 12px 36px rgba(254,159,67,.1); transform: translateY(-4px); background: var(--white); }
.feat-card:hover::after { transform: scaleX(1); }
.feat-icon {
  width: 52px; height: 52px; border-radius: 14px;
  background: rgba(254,159,67,.12); display: flex; align-items: center; justify-content: center;
  font-size: 1.5rem; color: var(--orange); margin-bottom: 18px;
  transition: all .3s;
}
.feat-card:hover .feat-icon { background: var(--orange); color: var(--white); }
.feat-card h5 { font-size: .95rem; font-weight: 700; color: var(--navy); margin-bottom: 8px; }
.feat-card p  { font-size: .85rem; color: var(--muted); line-height: 1.65; margin: 0; }

/* ══════════════════════════════════════════════════════════════
   HOW IT WORKS
══════════════════════════════════════════════════════════════ */
.how { padding: 96px 0; background: var(--navy); position: relative; overflow: hidden; }
.how::before {
  content: ''; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%);
  width: 800px; height: 800px; border-radius: 50%;
  background: radial-gradient(circle, rgba(53,53,112,.6) 0%, transparent 70%);
  pointer-events: none;
}
.step-connector { border-top: 2px dashed rgba(254,159,67,.25); margin-top: 32px; display: none; }
@media(min-width:768px){ .step-connector { display: block; } }
.step-wrap { position: relative; z-index: 2; text-align: center; }
.step-num {
  width: 56px; height: 56px; border-radius: 50%;
  background: var(--orange); color: var(--white);
  font-size: 1.2rem; font-weight: 800;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 16px;
  box-shadow: 0 6px 20px var(--orange-glow);
  position: relative; z-index: 1;
}
.step-icon-wrap {
  width: 60px; height: 60px; border-radius: 16px;
  background: rgba(254,159,67,.1); border: 1px solid rgba(254,159,67,.2);
  display: flex; align-items: center; justify-content: center;
  font-size: 1.5rem; color: var(--orange);
  margin: 0 auto 14px;
}
.step-wrap h6 { font-size: .9rem; font-weight: 700; color: var(--white); margin-bottom: 8px; }
.step-wrap p  { font-size: .82rem; color: rgba(255,255,255,.5); line-height: 1.65; }

/* ══════════════════════════════════════════════════════════════
   TESTIMONIAL / HIGHLIGHT STRIP
══════════════════════════════════════════════════════════════ */
.highlight-strip { padding: 64px 0; background: var(--light); }
.highlight-card {
  background: var(--white); border-radius: 16px;
  padding: 28px; border: 1px solid var(--border);
  height: 100%; transition: all .3s;
}
.highlight-card:hover { box-shadow: 0 12px 32px rgba(0,0,0,.08); transform: translateY(-3px); }
.highlight-card .hl-number { font-size: 2.2rem; font-weight: 800; color: var(--orange); line-height: 1; }
.highlight-card .hl-label  { font-size: .85rem; color: var(--muted); margin-top: 4px; font-weight: 500; }
.highlight-card .hl-icon   { font-size: 2rem; color: var(--orange); opacity: .2; }

/* ══════════════════════════════════════════════════════════════
   PRICING
══════════════════════════════════════════════════════════════ */
.pricing { padding: 96px 0; background: var(--white); }
.plan-card {
  border: 2px solid var(--border); border-radius: 20px;
  padding: 36px 32px; height: 100%;
  transition: all .3s; position: relative; overflow: hidden;
  background: var(--white);
}
.plan-card::before {
  content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px;
  background: transparent; transition: background .3s;
}
.plan-card:hover { border-color: rgba(254,159,67,.4); box-shadow: 0 16px 48px rgba(254,159,67,.1); transform: translateY(-4px); }
.plan-card:hover::before { background: var(--orange); }
.plan-card.featured {
  border-color: var(--orange); background: var(--navy);
  box-shadow: 0 20px 60px rgba(254,159,67,.2);
  transform: scale(1.04);
  z-index: 1;
}
.plan-card.featured::before { background: var(--orange); }
.plan-card.featured:hover { transform: scale(1.04) translateY(-4px); }
.popular-badge {
  position: absolute; top: 20px; right: 20px;
  background: var(--orange); color: var(--white);
  padding: 4px 12px; border-radius: 50px;
  font-size: 11px; font-weight: 700; letter-spacing: .5px;
}
.plan-name { font-size: .95rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
.plan-name.light { color: rgba(255,255,255,.6); }
.plan-name.dark  { color: var(--muted); }
.plan-price { font-size: 2.8rem; font-weight: 800; line-height: 1; margin: 16px 0 4px; }
.plan-price.light { color: var(--white); }
.plan-price.dark  { color: var(--navy); }
.plan-price sub { font-size: 1rem; font-weight: 500; vertical-align: baseline; }
.plan-period { font-size: 13px; margin-bottom: 24px; }
.plan-period.light { color: rgba(255,255,255,.45); }
.plan-period.dark  { color: var(--muted); }
.plan-divider { border-color: rgba(255,255,255,.1); }
.plan-features { list-style: none; margin: 20px 0 28px; }
.plan-features li { display: flex; align-items: flex-start; gap: 10px; padding: 7px 0; font-size: .88rem; }
.plan-features li i { font-size: 15px; flex-shrink: 0; margin-top: 1px; }
.plan-features li.light { color: rgba(255,255,255,.75); }
.plan-features li.dark  { color: #374151; }
.plan-members { font-size: 13px; padding: 8px 14px; border-radius: 8px; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 20px; font-weight: 600; }
.plan-members.light { background: rgba(254,159,67,.15); color: var(--orange); }
.plan-members.dark  { background: rgba(254,159,67,.1); color: var(--orange); }

/* ══════════════════════════════════════════════════════════════
   CTA
══════════════════════════════════════════════════════════════ */
.cta-section {
  padding: 96px 0;
  background: linear-gradient(135deg, var(--navy) 0%, var(--navy2) 50%, #0f0f35 100%);
  position: relative; overflow: hidden;
}
.cta-section::before {
  content: '';
  position: absolute; top: -100px; left: 50%; transform: translateX(-50%);
  width: 900px; height: 400px; border-radius: 50%;
  background: radial-gradient(ellipse, rgba(254,159,67,.12) 0%, transparent 70%);
}
.cta-section h2 { font-size: clamp(1.8rem, 3.5vw, 2.8rem); font-weight: 800; color: var(--white); }
.cta-section p { color: rgba(255,255,255,.6); font-size: 1.05rem; }

/* ══════════════════════════════════════════════════════════════
   FOOTER
══════════════════════════════════════════════════════════════ */
.lfoot { background: #0c0c28; padding: 60px 0 28px; color: rgba(255,255,255,.5); }
.lfoot-logo { font-size: 1.3rem; font-weight: 800; color: var(--white); display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
.lfoot-logo .logo-box { width: 34px; height: 34px; border-radius: 8px; background: var(--orange); display: flex; align-items: center; justify-content: center; color: var(--white); font-size: 1rem; }
.lfoot-heading { font-size: .85rem; font-weight: 700; color: var(--white); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 16px; }
.lfoot a { color: rgba(255,255,255,.45); font-size: .875rem; display: block; margin-bottom: 10px; transition: color .2s; }
.lfoot a:hover { color: var(--orange); }
.lfoot-divider { border-color: rgba(255,255,255,.06); margin: 32px 0 20px; }
.lfoot-bottom { font-size: .8rem; color: rgba(255,255,255,.25); }

/* ══════════════════════════════════════════════════════════════
   SCROLL REVEAL ANIMATION
══════════════════════════════════════════════════════════════ */
.reveal { opacity: 0; transform: translateY(30px); transition: opacity .6s ease, transform .6s ease; }
.reveal.visible { opacity: 1; transform: translateY(0); }
</style>
</head>
<body>

{{-- ═══════════════════ NAVBAR ═══════════════════ --}}
<nav class="lnav" id="lnav">
  <div class="container">
    <div class="lnav-inner">

      <a href="{{ url('/') }}" class="lnav-logo me-5">
        <img src="{{URL::asset('build/img/logo.svg')}}" alt="Thaka Khawa" style="height: 44px; max-width: 200px;">
      </a>

      <ul class="lnav-links d-none d-lg-flex">
        <li><a href="#features">{{ __('Features') }}</a></li>
        <li><a href="#how">{{ __('How it Works') }}</a></li>
        <li><a href="#pricing">{{ __('Pricing') }}</a></li>
        <li><a href="{{ route('blog.index') }}">{{ __('Blog') }}</a></li>
        <li><a href="{{ route('public.about') }}">{{ __('About') }}</a></li>
        <li><a href="{{ route('public.contact') }}">{{ __('Contact') }}</a></li>
      </ul>

      <div class="lnav-auth lnav-auth-desktop ms-auto d-none d-lg-flex align-items-center gap-2">
        {{-- Language switcher --}}
        @php $llocale = app()->getLocale(); @endphp
        <div class="dropdown">
          <button class="btn btn-sm dropdown-toggle d-flex align-items-center gap-2 px-3 py-1"
                  type="button" data-bs-toggle="dropdown"
                  style="border-radius:20px;font-size:13px;font-weight:600;color:rgba(255,255,255,.85);background:rgba(255,255,255,.1);border:1.5px solid rgba(255,255,255,.2)">
            <img src="{{ URL::asset('build/img/flags/'.($llocale==='bn'?'bd':'us').'.png') }}" alt="" height="14" style="border-radius:2px">
            {{ $llocale==='bn' ? 'বাংলা' : 'EN' }}
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow py-1" style="border-radius:10px;min-width:130px">
            <li><a class="dropdown-item d-flex align-items-center gap-2 py-2 {{ $llocale==='en'?'active':'' }}" href="{{ route('lang.switch','en') }}">
              <img src="{{ URL::asset('build/img/flags/us.png') }}" alt="" height="13" style="border-radius:2px"> English
              @if($llocale==='en') <i class="ti ti-check ms-auto text-success"></i> @endif
            </a></li>
            <li><a class="dropdown-item d-flex align-items-center gap-2 py-2 {{ $llocale==='bn'?'active':'' }}" href="{{ route('lang.switch','bn') }}">
              <img src="{{ URL::asset('build/img/flags/bd.png') }}" alt="" height="13" style="border-radius:2px"> বাংলা
              @if($llocale==='bn') <i class="ti ti-check ms-auto text-success"></i> @endif
            </a></li>
          </ul>
        </div>
        @auth
          <a href="{{ url('/') }}" class="nav-user-pill">
            <div class="nav-avatar">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
            <span style="max-width:110px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ Auth::user()->name }}</span>
          </a>
          <a href="{{ route('mess.index') }}" class="btn-nav-dash">
            <i class="ti ti-layout-dashboard"></i> {{ __('Dashboard') }}
          </a>
        @else
          <a href="{{ route('signin') }}" class="btn-nav-in">{{ __('Sign In') }}</a>
          <a href="{{ route('register') }}" class="btn-nav-up">
            <i class="ti ti-user-plus me-1"></i>{{ __('Sign Up') }}
          </a>
        @endauth
      </div>

      <button class="lnav-toggle ms-auto" id="navToggle"><i class="ti ti-menu-2" id="navToggleIcon"></i></button>
    </div>
  </div>

  <div class="lnav-mobile" id="navMobile">
    <div class="container">
      <a href="#features"><i class="ti ti-layout-grid me-2"></i>{{ __('Features') }}</a>
      <a href="#how"><i class="ti ti-route me-2"></i>{{ __('How it Works') }}</a>
      <a href="#pricing"><i class="ti ti-tag me-2"></i>{{ __('Pricing') }}</a>
      <a href="{{ route('blog.index') }}"><i class="ti ti-news me-2"></i>{{ __('Blog') }}</a>
      <a href="{{ route('public.about') }}"><i class="ti ti-info-circle me-2"></i>{{ __('About') }}</a>
      <a href="{{ route('public.contact') }}"><i class="ti ti-mail me-2"></i>{{ __('Contact') }}</a>
      <a href="{{ route('public.faq') }}"><i class="ti ti-help me-2"></i>{{ __('FAQ') }}</a>
      <div class="mob-divider"></div>
      <a href="{{ route('lang.switch','en') }}" style="{{ app()->getLocale()==='en'?'color:var(--orange);font-weight:700':'' }}"><i class="ti ti-language me-2"></i>English</a>
      <a href="{{ route('lang.switch','bn') }}" style="{{ app()->getLocale()==='bn'?'color:var(--orange);font-weight:700':'' }}"><i class="ti ti-language me-2"></i>বাংলা</a>
      <div class="mob-divider"></div>
      @auth
        <a href="{{ route('mess.index') }}" style="color:var(--orange);font-weight:700"><i class="ti ti-layout-dashboard me-2"></i>{{ __('Dashboard') }}</a>
        <a href="{{ route('signout') }}"><i class="ti ti-logout me-2"></i>{{ __('Logout') }}</a>
      @else
        <a href="{{ route('signin') }}"><i class="ti ti-login me-2"></i>{{ __('Sign In') }}</a>
        <a href="{{ route('register') }}" style="color:var(--orange);font-weight:700"><i class="ti ti-user-plus me-2"></i>{{ __('Sign Up') }}</a>
      @endauth
    </div>
  </div>
</nav>

{{-- ═══════════════════ HERO ═══════════════════ --}}
<section class="hero">
  <div class="container hero-inner">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="hero-tag">
          <span></span> {{ __('Smart Mess Management Platform') }}
        </div>
        <h1>{{ __('Manage Your Mess Smarter & Easier') }}</h1>
        <p class="hero-sub">
          {{ __('Thaka Khawa is the complete platform for shared living — track meals, split expenses, coordinate market duties, generate monthly reports, and more. All automated.') }}
        </p>
        <div class="hero-btns">
          <a href="{{ route('register') }}" class="btn-orange">
            <i class="ti ti-rocket"></i> {{ __('Start Free Today') }}
          </a>
          <a href="#features" class="btn-ghost">
            <i class="ti ti-eye"></i> {{ __('See Features') }}
          </a>
        </div>
        <div class="hero-proof">
          <div class="hero-proof-item"><i class="ti ti-check"></i> {{ __('No credit card needed') }}</div>
          <div class="hero-proof-item"><i class="ti ti-check"></i> {{ __('Free plan available') }}</div>
          <div class="hero-proof-item"><i class="ti ti-check"></i> {{ __('Setup in 2 minutes') }}</div>
        </div>
      </div>

      <div class="col-lg-6 position-relative">
        {{-- Main mockup card --}}
        <div class="hero-card">
          <div class="hero-card-top">
            <div class="hct-dot" style="background:#ff5f57"></div>
            <div class="hct-dot" style="background:#ffbd2e"></div>
            <div class="hct-dot" style="background:#28c840"></div>
            <span style="color:rgba(255,255,255,.5);font-size:12px;margin-left:8px">Thaka Khawa — Dashboard</span>
          </div>
          <div class="hero-card-body">
            <div class="mock-stat-grid">
              <div class="mock-stat">
                <div class="label">Total Members</div>
                <div class="val orange">18 <span style="font-size:.8rem;font-weight:500;color:rgba(255,255,255,.3)">/ 20</span></div>
                <div class="mock-chart-bar"><div class="mock-chart-fill" style="width:90%"></div></div>
              </div>
              <div class="mock-stat">
                <div class="label">This Month Deposit</div>
                <div class="val green">৳54,000</div>
                <div class="mock-chart-bar"><div class="mock-chart-fill" style="width:78%;background:#3EB780"></div></div>
              </div>
              <div class="mock-stat">
                <div class="label">Total Expenses</div>
                <div class="val" style="color:#FFCA18">৳48,320</div>
                <div class="mock-chart-bar"><div class="mock-chart-fill" style="width:65%;background:#FFCA18"></div></div>
              </div>
              <div class="mock-stat">
                <div class="label">Cash in Hand</div>
                <div class="val blue">৳5,680</div>
                <div class="mock-chart-bar"><div class="mock-chart-fill" style="width:20%;background:#60a5fa"></div></div>
              </div>
            </div>
            <div style="background:rgba(255,255,255,.04);border-radius:12px;padding:14px;border:1px solid rgba(255,255,255,.07)">
              <div style="font-size:12px;font-weight:700;color:rgba(255,255,255,.5);margin-bottom:10px;text-transform:uppercase;letter-spacing:.8px">Today's Meals</div>
              <div class="mock-meal-row">
                <span>🌅 Breakfast</span>
                <span style="color:rgba(255,255,255,.35);font-size:12px">16 attending</span>
                <span class="mock-badge open">Open</span>
              </div>
              <div class="mock-meal-row">
                <span>☀️ Lunch</span>
                <span style="color:rgba(255,255,255,.35);font-size:12px">18 attending</span>
                <span class="mock-badge open">Open</span>
              </div>
              <div class="mock-meal-row">
                <span>🌙 Dinner</span>
                <span style="color:rgba(255,255,255,.35);font-size:12px">15 attending</span>
                <span class="mock-badge closed">8:00 PM</span>
              </div>
            </div>
          </div>
        </div>

        {{-- Floating badge 1 --}}
        <div class="hero-float-badge" style="top:-20px;left:-30px;animation-delay:.5s">
          <div class="badge-icon" style="background:#fff5e6"><i class="ti ti-coins" style="color:var(--orange)"></i></div>
          <div>
            <div style="font-size:11px;color:#6b7280;font-weight:500">Monthly Saved</div>
            <div style="font-size:15px;font-weight:800;color:var(--navy)">৳12,400</div>
          </div>
        </div>

        {{-- Floating badge 2 --}}
        <div class="hero-float-badge" style="bottom:-10px;right:-20px;animation-delay:1.2s">
          <div class="badge-icon" style="background:#e8f8f0"><i class="ti ti-check" style="color:#3EB780"></i></div>
          <div>
            <div style="font-size:11px;color:#6b7280;font-weight:500">Report Ready</div>
            <div style="font-size:12px;font-weight:700;color:var(--navy)">April 2025</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════════ STATS STRIP ═══════════════════ --}}
<div class="stats-strip">
  <div class="container">
    <div class="row text-center g-4">
      <div class="col-6 col-md-3">
        <div class="stat-num" data-target="500">0+</div>
        <div class="stat-lbl">{{ __('Active Messes') }}</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-num" data-target="10000">0+</div>
        <div class="stat-lbl">{{ __('Members Managed') }}</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-num">৳2M+</div>
        <div class="stat-lbl">{{ __('Expenses Tracked') }}</div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-num">99%</div>
        <div class="stat-lbl">{{ __('Satisfaction Rate') }}</div>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════════ FEATURES ═══════════════════ --}}
<section class="features" id="features">
  <div class="container">
    <div class="text-center mb-5 reveal">
      <div class="section-label mb-2">{{ __('What We Offer') }}</div>
      <h2 class="section-title">{{ __('Everything Your Mess Needs') }}</h2>
      <p class="section-sub mt-3">{{ __('A complete platform covering every aspect of shared mess life — automated, accurate, and always accessible.') }}</p>
    </div>
    <div class="row g-4">
      @php
      $features = [
        ['icon'=>'ti-tools-kitchen-2','title'=>__('Meal Management'),   'desc'=>__('Track daily meal attendance for breakfast, lunch & dinner. Auto-mark members ON, set cut-off times, and manage meal-off requests easily.')],
        ['icon'=>'ti-shopping-cart',  'title'=>__('Market Routine'),    'desc'=>__('Assign market duty using a visual calendar. Build shopping lists, track spending, and let members exchange duties.')],
        ['icon'=>'ti-coins',          'title'=>__('Expense Tracking'),  'desc'=>__('Log all mess expenses with categories. Get a full breakdown per member every month — electricity, gas, cook bill, and more.')],
        ['icon'=>'ti-cash',           'title'=>__('Deposit Management'),'desc'=>__('Record monthly deposits from each member. Track who paid, who is due, and keep a clear financial ledger.')],
        ['icon'=>'ti-report-analytics','title'=>__('Monthly Reports'),  'desc'=>__('Auto-generate monthly reports showing meal cost, expense share, total payable, due amount, and carry-forward.')],
        ['icon'=>'ti-crown',          'title'=>__('Manager Rotation'),  'desc'=>__('Assign a new manager each month. Members rate the manager with stars. Best manager gets recognized and rewarded!')],
        ['icon'=>'ti-trophy',         'title'=>__('Rewards & Points'),  'desc'=>__('Recognize outstanding members monthly. Award points and gifts to the best member, market person, and top manager.')],
        ['icon'=>'ti-layout-kanban',  'title'=>__('Meal Kanban Board'), 'desc'=>__('Plan daily meals visually. Add items to cook, move them to Cooking then Done — keep every member informed in real-time.')],
        ['icon'=>'ti-flag',           'title'=>__('Member Reporting'),  'desc'=>__('Members report rule violations to the manager. Reporters earn points when resolved, promoting accountability.')],
        ['icon'=>'ti-users',          'title'=>__('Member Management'), 'desc'=>__('Add, remove, and manage members with roles — owner, manager, author, or member. Share an 8-digit invite code.')],
        ['icon'=>'ti-arrows-exchange','title'=>__('Duty Exchange'),     'desc'=>__("Can't do market duty? Request an exchange with another member. Accept or reject — fully managed in-app.")],
        ['icon'=>'ti-chart-bar',      'title'=>__('Financial Summary'), 'desc'=>__('See cash in hand, total deposits vs expenses, per-meal cost, and individual dues — all calculated automatically.')],
      ];
      @endphp
      @foreach($features as $i => $f)
      <div class="col-md-6 col-xl-4 reveal" style="transition-delay:{{ ($i % 3) * 0.1 }}s">
        <div class="feat-card">
          <div class="feat-icon"><i class="ti {{ $f['icon'] }}"></i></div>
          <h5>{{ $f['title'] }}</h5>
          <p>{{ $f['desc'] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════════ HOW IT WORKS ═══════════════════ --}}
<section class="how" id="how">
  <div class="container position-relative" style="z-index:2">
    <div class="text-center mb-5 reveal">
      <div class="section-label mb-2" style="color:var(--orange)">{{ __('Simple Process') }}</div>
      <h2 class="section-title" style="color:var(--white)">{{ __('Up & Running in Minutes') }}</h2>
      <p class="section-sub mt-3" style="color:rgba(255,255,255,.5)">{{ __('No complex setup. No technical knowledge needed. Just sign up and start managing your mess today.') }}</p>
    </div>
    <div class="row g-4 g-md-0 align-items-center justify-content-center">
      @foreach([
        ['n'=>'1','icon'=>'ti-user-plus',          'title'=>__('Create Account'),   'desc'=>__('Register free with your name, email, and password. No credit card required.')],
        ['n'=>'2','icon'=>'ti-building-community', 'title'=>__('Set Up Your Mess'), 'desc'=>__('Add your mess name and address. You instantly get an 8-digit invite code.')],
        ['n'=>'3','icon'=>'ti-users',              'title'=>__('Invite Members'),   'desc'=>__('Share the invite code with your housemates. They join in seconds.')],
        ['n'=>'4','icon'=>'ti-rocket',             'title'=>__('Start Managing'),   'desc'=>__('Mark meals, assign duties, track expenses, and generate monthly reports!')],
      ] as $i => $step)
      @if($i > 0)
      <div class="col-md-1 d-none d-md-flex align-items-center justify-content-center">
        <i class="ti ti-arrow-right" style="color:rgba(254,159,67,.35);font-size:1.5rem"></i>
      </div>
      @endif
      <div class="col-md-2 reveal" style="transition-delay:{{ $i * 0.15 }}s">
        <div class="step-wrap">
          <div class="step-num">{{ $step['n'] }}</div>
          <div class="step-icon-wrap"><i class="ti {{ $step['icon'] }}"></i></div>
          <h6>{{ $step['title'] }}</h6>
          <p>{{ $step['desc'] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════════ HIGHLIGHT STRIP ═══════════════════ --}}
<div class="highlight-strip">
  <div class="container">
    <div class="row g-4">
      @foreach([
        ['num'=>'500+', 'label'=>__('Messes actively using Thaka Khawa'),   'icon'=>'ti-building-community'],
        ['num'=>'10K+', 'label'=>__('Members whose dues are auto-calculated'),'icon'=>'ti-users'],
        ['num'=>'৳2M+', 'label'=>__('Total expenses tracked on the platform'),'icon'=>'ti-coin'],
        ['num'=>'100%', 'label'=>__('Reports generated automatically every month'),'icon'=>'ti-report-analytics'],
      ] as $h)
      <div class="col-md-6 col-xl-3 reveal">
        <div class="highlight-card d-flex align-items-start gap-3">
          <div style="flex-shrink:0">
            <i class="ti {{ $h['icon'] }} hl-icon" style="font-size:2.2rem;color:var(--orange);opacity:.15"></i>
          </div>
          <div>
            <div class="hl-number">{{ $h['num'] }}</div>
            <div class="hl-label">{{ $h['label'] }}</div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>

{{-- ═══════════════════ PRICING ═══════════════════ --}}
<section class="pricing" id="pricing">
  <div class="container">
    <div class="text-center mb-5 reveal">
      <div class="section-label mb-2">{{ __('Pricing') }}</div>
      <h2 class="section-title">{{ __('Simple, Transparent Pricing') }}</h2>
      <p class="section-sub mt-3">{{ __('Start free and upgrade as your mess grows. No hidden fees, no surprise charges.') }}</p>
    </div>

    @if($plans->isEmpty())
    <div class="text-center py-5">
      <div style="font-size:3.5rem;opacity:.1;color:var(--navy)" class="mb-3"><i class="ti ti-package-off"></i></div>
      <p class="text-muted">{{ __('Pricing plans coming soon. Check back shortly.') }}</p>
      <a href="{{ route('register') }}" class="btn-orange d-inline-flex mt-3">
        <i class="ti ti-rocket"></i> {{ __('Register Free Now') }}
      </a>
    </div>
    @else
    @php
      $count = $plans->count();
      $colClass = match(true) {
        $count === 1 => 'col-md-6 col-lg-4',
        $count === 2 => 'col-md-5',
        default      => 'col-md-6 col-lg-4',
      };
    @endphp
    <div class="row g-4 justify-content-center align-items-center">
      @foreach($plans as $plan)
      @php
        $featured  = $plan->is_featured;
        $isFree    = $plan->price == 0;
        $features  = $plan->feature_lines;
        $textClass = $featured ? 'light' : 'dark';
      @endphp
      <div class="{{ $colClass }} reveal">
        <div class="plan-card {{ $featured ? 'featured' : '' }}">
          @if($featured)
          <div class="popular-badge">⭐ {{ __('Popular') }}</div>
          @endif

          <div class="plan-name {{ $textClass }}">{{ $plan->name }}</div>

          <div class="plan-price {{ $textClass }}">
            @if($isFree)
              <sub style="font-size:1.4rem">৳</sub>0
            @else
              <sub style="font-size:1.4rem">৳</sub>{{ number_format($plan->price, 0) }}
            @endif
          </div>
          <div class="plan-period {{ $textClass }}">
            {{ $isFree ? __('Free forever') : 'per '.($plan->duration_months == 1 ? __('month(s)') : $plan->duration_months.' '.__('month(s)')) }}
          </div>

          <div class="plan-members {{ $textClass }}">
            <i class="ti ti-users"></i> {{ __('Up to :count members / mess', ['count' => $plan->max_members]) }}
          </div>

          <hr class="{{ $featured ? 'plan-divider' : '' }}" style="{{ $featured ? '' : 'border-color:var(--border)' }}">

          <ul class="plan-features">
            @forelse($features as $feat)
            <li class="{{ $textClass }}">
              <i class="ti ti-check" style="color:var(--orange)"></i>
              <span>{{ $feat }}</span>
            </li>
            @empty
            <li class="{{ $textClass }}">
              <i class="ti ti-check" style="color:var(--orange)"></i>
              <span>{{ __('All features included') }}</span>
            </li>
            @endforelse
          </ul>

          @if($featured)
          <a href="{{ route('register') }}" class="btn-orange d-flex justify-content-center">
            <i class="ti ti-rocket"></i> Get {{ $plan->name }}
          </a>
          @elseif($isFree)
          <a href="{{ route('register') }}" class="btn-outline-orange d-flex justify-content-center">
            <i class="ti ti-user-plus"></i> Start Free
          </a>
          @else
          <a href="{{ route('register') }}" class="btn-outline-orange d-flex justify-content-center">
            <i class="ti ti-arrow-right"></i> Get Started
          </a>
          @endif
        </div>
      </div>
      @endforeach
    </div>

    <p class="text-center mt-4" style="color:var(--muted);font-size:.85rem">
      <i class="ti ti-shield-check me-1" style="color:var(--green)"></i>
      {{ __('All plans include full feature access. No credit card required to get started.') }}
    </p>
    @endif
  </div>
</section>

{{-- ═══════════════════ CTA ═══════════════════ --}}
<section class="cta-section">
  <div class="container text-center position-relative" style="z-index:2">
    <div class="reveal">
      <div class="section-label mb-3" style="color:var(--orange)">Get Started Today</div>
      <h2>{{ __('Ready to Simplify Your Mess Life?') }}</h2>
      <p class="mt-3 mb-5">{{ __('Join hundreds of messes already using Thaka Khawa to stay organized and conflict-free.') }}</p>
      <div class="d-flex gap-3 justify-content-center flex-wrap">
        <a href="{{ route('register') }}" class="btn-orange" style="font-size:16px;padding:15px 36px">
          <i class="ti ti-rocket"></i> {{ __('Create Free Account') }}
        </a>
        <a href="{{ route('signin') }}" class="btn-ghost" style="font-size:16px;padding:14px 32px">
          <i class="ti ti-login"></i> {{ __('Sign In') }}
        </a>
      </div>
      <div class="mt-4 d-flex justify-content-center gap-4 flex-wrap" style="color:rgba(255,255,255,.35);font-size:13px">
        <span><i class="ti ti-check me-1" style="color:var(--green)"></i>Free forever plan</span>
        <span><i class="ti ti-check me-1" style="color:var(--green)"></i>No credit card needed</span>
        <span><i class="ti ti-check me-1" style="color:var(--green)"></i>Cancel anytime</span>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════════ FOOTER ═══════════════════ --}}
<footer class="lfoot">
  <div class="container">
    <div class="row g-4 g-lg-5">
      <div class="col-12 col-lg-4">
        <div class="lfoot-logo">
          <img src="{{URL::asset('build/img/logo.svg')}}" alt="Thaka Khawa" style="height: 40px; max-width: 190px;">
        </div>
        <p style="font-size:.875rem;line-height:1.7;max-width:300px">
          {{ __('Smart mess management for shared living. Track meals, expenses, and more — all in one beautiful platform.') }}
        </p>
        <div class="d-flex gap-3 mt-3">
          <a href="#" style="width:36px;height:36px;border-radius:8px;background:rgba(255,255,255,.06);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.4);font-size:1rem;transition:all .2s" onmouseover="this.style.background='rgba(254,159,67,.2)';this.style.color='#FE9F43'" onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.color='rgba(255,255,255,.4)'">
            <i class="ti ti-brand-facebook"></i></a>
          <a href="#" style="width:36px;height:36px;border-radius:8px;background:rgba(255,255,255,.06);display:flex;align-items:center;justify-content:center;color:rgba(255,255,255,.4);font-size:1rem;transition:all .2s" onmouseover="this.style.background='rgba(254,159,67,.2)';this.style.color='#FE9F43'" onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.color='rgba(255,255,255,.4)'">
            <i class="ti ti-brand-twitter"></i></a>
        </div>
      </div>
      <div class="col-6 col-md-3 col-lg-2">
        <div class="lfoot-heading">{{ __('Product') }}</div>
        <a href="{{ route('public.features') }}">{{ __('Features') }}</a>
        <a href="{{ route('blog.index') }}">{{ __('Blog') }}</a>
        <a href="#pricing">{{ __('Pricing') }}</a>
      </div>
      <div class="col-6 col-md-3 col-lg-2">
        <div class="lfoot-heading">{{ __('Company') }}</div>
        <a href="{{ route('public.about') }}">{{ __('About Us') }}</a>
        <a href="{{ route('public.contact') }}">{{ __('Contact') }}</a>
        <a href="{{ route('public.faq') }}">{{ __('FAQ') }}</a>
        <a href="{{ route('public.privacy') }}">{{ __('Privacy Policy') }}</a>
      </div>
      <div class="col-12 col-md-6 col-lg-4">
        <div class="lfoot-heading">{{ __('Account') }}</div>
        <a href="{{ route('signin') }}">{{ __('Sign In') }}</a>
        <a href="{{ route('register') }}">{{ __('Register Free') }}</a>
        @auth
          <a href="{{ route('mess.index') }}">{{ __('Dashboard') }}</a>
        @endauth
        <div class="mt-3" style="background:rgba(254,159,67,.1);border:1px solid rgba(254,159,67,.2);border-radius:10px;padding:12px 16px">
          <div style="font-size:12px;color:rgba(255,255,255,.4);margin-bottom:4px">{{ __('Start managing your mess today') }}</div>
          <a href="{{ route('register') }}" style="color:var(--orange);font-size:.875rem;font-weight:700">
            {{ __('Register Free') }} <i class="ti ti-arrow-right ms-1"></i>
          </a>
        </div>
      </div>
    </div>
    <hr class="lfoot-divider">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 lfoot-bottom">
      <span>© {{ date('Y') }} Thaka Khawa. {{ __('All rights reserved.') }}</span>
      <span>{{ __('Built with ❤️ for mess communities') }}</span>
    </div>
  </div>
</footer>

<script src="{{ URL::asset('build/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ URL::asset('build/js/bootstrap.bundle.min.js') }}"></script>
<script>
// ── Navbar scroll ──────────────────────────────────────────
const lnav = document.getElementById('lnav');
window.addEventListener('scroll', () => {
  lnav.classList.toggle('scrolled', window.scrollY > 40);
});

// ── Mobile menu ────────────────────────────────────────────
const navToggle     = document.getElementById('navToggle');
const navMobile     = document.getElementById('navMobile');
const navToggleIcon = document.getElementById('navToggleIcon');
navToggle.addEventListener('click', () => {
  const open = navMobile.classList.toggle('open');
  navToggleIcon.className = open ? 'ti ti-x' : 'ti ti-menu-2';
});

// ── Smooth scroll ──────────────────────────────────────────
document.querySelectorAll('a[href^="#"]').forEach(a => {
  a.addEventListener('click', function(e) {
    const href = this.getAttribute('href');
    if (href === '#') return;
    const target = document.querySelector(href);
    if (target) {
      e.preventDefault();
      window.scrollTo({ top: target.getBoundingClientRect().top + window.scrollY - 74, behavior: 'smooth' });
      navMobile.classList.remove('show');
      navToggleIcon.className = 'ti ti-menu-2';
    }
  });
});

// ── Scroll reveal ──────────────────────────────────────────
const revealEls = document.querySelectorAll('.reveal');
const revealObs = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      revealObs.unobserve(entry.target);
    }
  });
}, { threshold: 0.12 });
revealEls.forEach(el => revealObs.observe(el));
</script>
</body>
</html>
