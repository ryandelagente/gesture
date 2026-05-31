/*!
 * Gesture Bug Widget — visual feedback widget (BugHerd-style)
 * Embed:
 *   <script src="https://your-host/widget.js" data-key="bh_xxx" async></script>
 *
 * Optional data-attrs on the script tag:
 *   data-endpoint   – override feedback endpoint (default: same origin as widget.js)
 *   data-color      – primary color (default: #2563eb)
 *   data-position   – bottom-right | bottom-left (default: bottom-right)
 */
(function () {
  'use strict';
  if (window.__GestureBugWidgetLoaded) return;
  window.__GestureBugWidgetLoaded = true;

  var scriptEl = document.currentScript || (function () {
    var s = document.getElementsByTagName('script');
    return s[s.length - 1];
  })();

  // Team-only gating: on the agency's own sites the widget is hidden from
  // logged-out / public visitors. Team members visit the page once with ?gf=1
  // to unlock it (persisted via localStorage). Use ?gf=0 to disable again.
  //
  // Triggered by either:
  //   • data-team-only="true"  on the embed script tag, OR
  //   • the page hostname matches a known agency domain (wehelptradies.com.au
  //     and its subdomains). Client sites that embed the widget for public
  //     feedback are unaffected.
  try {
    var qs = new URLSearchParams(location.search);
    if (qs.get('gf') === '1') localStorage.setItem('gesture.feedback.enabled', '1');
    if (qs.get('gf') === '0') localStorage.removeItem('gesture.feedback.enabled');

    var attrTeamOnly = scriptEl && scriptEl.getAttribute('data-team-only') === 'true';
    var hostTeamOnly = /(^|\.)wehelptradies\.com\.au$/i.test(location.hostname || '');
    var teamOnly = attrTeamOnly || hostTeamOnly;

    if (teamOnly && localStorage.getItem('gesture.feedback.enabled') !== '1') {
      return; // public visitor on an agency-owned site — don't render
    }
  } catch (e) { /* localStorage may be unavailable; fail open and render */ }

  var WIDGET_KEY = (scriptEl && scriptEl.getAttribute('data-key')) || '';
  if (!WIDGET_KEY) {
    console.warn('[Gesture-widget] missing data-key attribute');
    return;
  }

  var scriptSrc = (scriptEl && scriptEl.src) || '';
  var defaultEndpoint = scriptSrc
    ? scriptSrc.replace(/\/widget\.js.*$/, '') + '/api/widget/feedback'
    : '/api/widget/feedback';

  var ENDPOINT = (scriptEl && scriptEl.getAttribute('data-endpoint')) || defaultEndpoint;
  var COLOR    = (scriptEl && scriptEl.getAttribute('data-color')) || '#2563eb';
  var POSITION = (scriptEl && scriptEl.getAttribute('data-position')) || 'bottom-right';

  // --- i18n ---------------------------------------------------------------
  var I18N = {
    en: {
      send_feedback: 'Send feedback', sending: 'Sending…', sent: 'Thanks — feedback sent!',
      description: 'Description', description_ph: 'What\'s wrong or what would you change?', required: 'required',
      your_name: 'Your name', your_email: 'Your email', optional: 'Optional',
      priority: 'Priority', severity: 'Severity', cancel: 'Cancel',
      pin_hint: 'Click on the part of the page you want to comment on — press Esc to cancel',
      capturing: 'Capturing screenshot…', uploading_video: 'Uploading video…', desc_required: 'Description is required.',
      welcome_title: 'Spotted something?', welcome_body: 'Use this button to send the team feedback.',
      welcome_steps: ['Click <strong>{label}</strong> below.', 'Click the part of the page that\'s wrong.', 'Describe it &amp; hit send. We\'ll get a screenshot automatically.'],
      try_now: 'Try it now', got_it: 'Got it',
      history_title: 'Feedback on this page', history_loading: 'Loading…', history_empty: 'No feedback yet on this page. Be the first!',
      record: 'Record screen', recording: 'Recording', stop: 'Stop',
      skip: 'Skip', rect: 'Rect', arrow: 'Arrow', blur: 'Blur', undo: 'Undo', use_screenshot: 'Use this screenshot',
      help_title: 'What gets sent?',
      help_items: ['Your description (required) and optional name/email', 'A screenshot of what you\'re looking at right now', 'The URL of this page and the element you pinned', 'Your browser &amp; OS (so we can reproduce the issue)'],
      help_reassure: 'Your feedback goes directly to the development team — no account or login needed.'
    },
    es: {
      send_feedback: 'Enviar comentarios', sending: 'Enviando…', sent: '¡Gracias — comentarios enviados!',
      description: 'Descripción', description_ph: '¿Qué está mal o qué cambiarías?', required: 'obligatorio',
      your_name: 'Tu nombre', your_email: 'Tu email', optional: 'Opcional',
      priority: 'Prioridad', severity: 'Gravedad', cancel: 'Cancelar',
      pin_hint: 'Haz clic en la parte de la página que quieras comentar — pulsa Esc para cancelar',
      capturing: 'Capturando pantalla…', uploading_video: 'Subiendo video…', desc_required: 'La descripción es obligatoria.',
      welcome_title: '¿Encontraste algo?', welcome_body: 'Usa este botón para enviar comentarios al equipo.',
      welcome_steps: ['Pulsa <strong>{label}</strong> abajo.', 'Haz clic en la parte de la página que está mal.', 'Descíbelo y envíalo. Captura automática.'],
      try_now: 'Probar ahora', got_it: 'Entendido',
      history_title: 'Comentarios en esta página', history_loading: 'Cargando…', history_empty: 'Aún sin comentarios. ¡Sé el primero!',
      record: 'Grabar pantalla', recording: 'Grabando', stop: 'Detener',
      skip: 'Omitir', rect: 'Rectángulo', arrow: 'Flecha', blur: 'Desenfocar', undo: 'Deshacer', use_screenshot: 'Usar esta captura',
      help_title: '¿Qué se envía?',
      help_items: ['Tu descripción (obligatoria) y nombre/email opcional', 'Una captura de pantalla de lo que estás viendo', 'La URL de esta página y el elemento marcado', 'Tu navegador y SO (para reproducir el problema)'],
      help_reassure: 'Tus comentarios van directos al equipo de desarrollo — sin cuenta ni inicio de sesión.'
    },
    fr: {
      send_feedback: 'Envoyer un avis', sending: 'Envoi…', sent: 'Merci — message envoyé !',
      description: 'Description', description_ph: 'Qu\'est-ce qui ne va pas ou que changeriez-vous ?', required: 'requis',
      your_name: 'Votre nom', your_email: 'Votre email', optional: 'Optionnel',
      priority: 'Priorité', severity: 'Gravité', cancel: 'Annuler',
      pin_hint: 'Cliquez sur la partie de la page à commenter — Échap pour annuler',
      capturing: 'Capture d\'écran…', uploading_video: 'Envoi de la vidéo…', desc_required: 'La description est requise.',
      welcome_title: 'Un problème ?', welcome_body: 'Utilisez ce bouton pour envoyer vos retours à l\'équipe.',
      welcome_steps: ['Cliquez sur <strong>{label}</strong> ci-dessous.', 'Cliquez sur l\'élément problématique.', 'Décrivez-le et envoyez. Capture automatique.'],
      try_now: 'Essayer', got_it: 'Compris',
      history_title: 'Retours sur cette page', history_loading: 'Chargement…', history_empty: 'Aucun retour pour le moment. Soyez le premier !',
      record: 'Enregistrer l\'écran', recording: 'Enregistrement', stop: 'Arrêter',
      skip: 'Passer', rect: 'Rectangle', arrow: 'Flèche', blur: 'Flouter', undo: 'Annuler', use_screenshot: 'Utiliser cette capture',
      help_title: 'Que sera envoyé ?',
      help_items: ['Votre description (requise) et nom/email optionnels', 'Une capture de l\'écran actuel', 'L\'URL et l\'élément ciblé', 'Navigateur et OS (pour reproduire le bug)'],
      help_reassure: 'Vos retours vont directement à l\'équipe — aucun compte nécessaire.'
    },
    de: {
      send_feedback: 'Feedback senden', sending: 'Senden…', sent: 'Danke — Feedback gesendet!',
      description: 'Beschreibung', description_ph: 'Was ist falsch oder was würden Sie ändern?', required: 'erforderlich',
      your_name: 'Ihr Name', your_email: 'Ihre E-Mail', optional: 'Optional',
      priority: 'Priorität', severity: 'Schweregrad', cancel: 'Abbrechen',
      pin_hint: 'Klicken Sie auf den Teil der Seite, den Sie kommentieren möchten — Esc zum Abbrechen',
      capturing: 'Screenshot wird erstellt…', uploading_video: 'Video wird hochgeladen…', desc_required: 'Beschreibung ist erforderlich.',
      welcome_title: 'Etwas entdeckt?', welcome_body: 'Mit dieser Schaltfläche können Sie dem Team Feedback senden.',
      welcome_steps: ['Klicken Sie unten auf <strong>{label}</strong>.', 'Klicken Sie auf das fehlerhafte Element.', 'Beschreiben und absenden. Screenshot automatisch.'],
      try_now: 'Jetzt testen', got_it: 'Verstanden',
      history_title: 'Feedback zu dieser Seite', history_loading: 'Lädt…', history_empty: 'Noch kein Feedback. Seien Sie der Erste!',
      record: 'Bildschirm aufnehmen', recording: 'Aufnahme', stop: 'Stopp',
      skip: 'Überspringen', rect: 'Rechteck', arrow: 'Pfeil', blur: 'Unscharf', undo: 'Rückgängig', use_screenshot: 'Diesen Screenshot verwenden',
      help_title: 'Was wird gesendet?',
      help_items: ['Ihre Beschreibung (erforderlich) und optional Name/E-Mail', 'Ein Screenshot des aktuellen Bildschirms', 'Die URL dieser Seite und das markierte Element', 'Ihr Browser und Betriebssystem (zur Reproduktion)'],
      help_reassure: 'Ihr Feedback geht direkt an das Entwicklerteam — ohne Anmeldung.'
    },
    pt: {
      send_feedback: 'Enviar feedback', sending: 'Enviando…', sent: 'Obrigado — feedback enviado!',
      description: 'Descrição', description_ph: 'O que está errado ou o que mudaria?', required: 'obrigatório',
      your_name: 'Seu nome', your_email: 'Seu email', optional: 'Opcional',
      priority: 'Prioridade', severity: 'Gravidade', cancel: 'Cancelar',
      pin_hint: 'Clique na parte da página que deseja comentar — pressione Esc para cancelar',
      capturing: 'Capturando tela…', uploading_video: 'Enviando vídeo…', desc_required: 'A descrição é obrigatória.',
      welcome_title: 'Encontrou algo?', welcome_body: 'Use este botão para enviar feedback à equipe.',
      welcome_steps: ['Clique em <strong>{label}</strong> abaixo.', 'Clique na parte com problema.', 'Descreva e envie. Captura automática.'],
      try_now: 'Testar agora', got_it: 'Entendi',
      history_title: 'Feedback nesta página', history_loading: 'Carregando…', history_empty: 'Ainda sem feedback. Seja o primeiro!',
      record: 'Gravar tela', recording: 'Gravando', stop: 'Parar',
      skip: 'Pular', rect: 'Retângulo', arrow: 'Seta', blur: 'Desfocar', undo: 'Desfazer', use_screenshot: 'Usar esta captura',
      help_title: 'O que será enviado?',
      help_items: ['Sua descrição (obrigatória) e nome/email opcionais', 'Uma captura da tela atual', 'A URL desta página e o elemento marcado', 'Seu navegador e SO (para reproduzir o bug)'],
      help_reassure: 'Seu feedback vai direto para o time de desenvolvimento — sem login.'
    },
    ja: {
      send_feedback: 'フィードバックを送信', sending: '送信中…', sent: '送信しました。ありがとうございます。',
      description: '説明', description_ph: '何が問題ですか？', required: '必須',
      your_name: 'お名前', your_email: 'メールアドレス', optional: '任意',
      priority: '優先度', severity: '重要度', cancel: 'キャンセル',
      pin_hint: 'コメントしたい部分をクリックしてください — Esc でキャンセル',
      capturing: 'スクリーンショットを撮影中…', uploading_video: '動画をアップロード中…', desc_required: '説明は必須です。',
      welcome_title: '何か見つけましたか？', welcome_body: 'このボタンでチームにフィードバックを送れます。',
      welcome_steps: ['下の <strong>{label}</strong> をクリック', '問題の部分をクリック', '説明して送信。スクリーンショットは自動です。'],
      try_now: '試してみる', got_it: 'OK',
      history_title: 'このページへのフィードバック', history_loading: '読み込み中…', history_empty: 'まだフィードバックがありません。最初になりましょう！',
      record: '画面を録画', recording: '録画中', stop: '停止',
      skip: 'スキップ', rect: '長方形', arrow: '矢印', blur: 'ぼかし', undo: '元に戻す', use_screenshot: 'この画像を使用',
      help_title: '送信される内容',
      help_items: ['説明（必須）と、任意で名前/メール', '現在の画面のスクリーンショット', 'このページの URL とピンした要素', 'ブラウザと OS（再現のため）'],
      help_reassure: 'フィードバックは開発チームに直接送られます — アカウント不要。'
    }
  };
  var langOverride = (scriptEl && scriptEl.getAttribute('data-lang')) || '';
  var detectedLang = (langOverride || (navigator.language || 'en')).slice(0, 2).toLowerCase();
  var L = I18N[detectedLang] || I18N.en;
  function t(k, vars) {
    var v = L[k] || I18N.en[k] || k;
    if (vars && typeof v === 'string') Object.keys(vars).forEach(function (n) { v = v.split('{' + n + '}').join(vars[n]); });
    return v;
  }
  var BRAND_LOGO = '';
  var BUTTON_LABEL = '';
  var WELCOME_TEXT = '';

  // Pull server-side branding (color/logo/text) before rendering
  var CONFIG_URL = ENDPOINT.replace(/\/feedback$/, '/config');
  fetch(CONFIG_URL + '?widget_key=' + encodeURIComponent(WIDGET_KEY), { credentials: 'omit' })
    .then(function (r) { return r.ok ? r.json() : {}; })
    .catch(function () { return {}; })
    .then(function (cfg) {
      if (cfg.brand_color && /^#[0-9a-f]{3,8}$/i.test(cfg.brand_color)) COLOR = cfg.brand_color;
      if (cfg.brand_logo_url) BRAND_LOGO = String(cfg.brand_logo_url);
      if (cfg.button_label) BUTTON_LABEL = String(cfg.button_label);
      if (cfg.welcome_text) WELCOME_TEXT = String(cfg.welcome_text);
      bootstrap();
    });
  function bootstrap() {

  // --- styles -------------------------------------------------------------
  var css = [
    '.tbw-root,.tbw-root *{box-sizing:border-box;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif}',
    '.tbw-fab{position:fixed;z-index:2147483646;width:52px;height:52px;border-radius:9999px;background:' + COLOR + ';color:#fff;border:none;box-shadow:0 6px 20px rgba(0,0,0,.25);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:22px;transition:transform .15s;-webkit-tap-highlight-color:transparent;touch-action:manipulation}',
    '@media (pointer:coarse){.tbw-fab{width:60px;height:60px;font-size:24px}}',
    '.tbw-fab:hover{transform:scale(1.05)}',
    '.tbw-pos-br{bottom:20px;right:20px}',
    '.tbw-pos-bl{bottom:20px;left:20px}',
    '.tbw-overlay{position:fixed;inset:0;z-index:2147483645;background:rgba(0,0,0,.05);cursor:crosshair}',
    '.tbw-hint{position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:2147483647;background:#111827;color:#fff;padding:8px 14px;border-radius:6px;font-size:13px;box-shadow:0 4px 12px rgba(0,0,0,.3)}',
    '.tbw-modal-bg{position:fixed;inset:0;z-index:2147483646;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;padding:16px}',
    '.tbw-modal{background:#fff;border-radius:10px;width:100%;max-width:440px;padding:20px;box-shadow:0 20px 50px rgba(0,0,0,.3);max-height:90vh;overflow:auto}',
    '.tbw-title{font-size:17px;font-weight:600;margin:0 0 12px;color:#111827;display:flex;justify-content:space-between;align-items:center}',
    '.tbw-close{background:none;border:none;cursor:pointer;font-size:22px;color:#6b7280;line-height:1;padding:0}',
    '.tbw-field{margin-bottom:10px}',
    '.tbw-field label{display:block;font-size:12px;font-weight:600;margin-bottom:4px;color:#374151}',
    '.tbw-field input,.tbw-field textarea,.tbw-field select{width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:14px;font-family:inherit;outline:none}',
    '.tbw-field input:focus,.tbw-field textarea:focus,.tbw-field select:focus{border-color:' + COLOR + ';box-shadow:0 0 0 3px ' + COLOR + '33}',
    '.tbw-field textarea{resize:vertical;min-height:80px}',
    '.tbw-row{display:grid;grid-template-columns:1fr 1fr;gap:10px}',
    '.tbw-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:14px}',
    '.tbw-btn{padding:8px 14px;border-radius:6px;border:none;font-size:14px;cursor:pointer;font-weight:500}',
    '.tbw-btn-primary{background:' + COLOR + ';color:#fff}',
    '.tbw-btn-secondary{background:#f3f4f6;color:#374151}',
    '.tbw-thumb{margin-top:8px;border:1px solid #e5e7eb;border-radius:6px;overflow:hidden}',
    '.tbw-thumb img{display:block;width:100%}',
    '.tbw-status{font-size:12px;color:#6b7280;margin-top:8px}',
    '.tbw-status-ok{color:#059669}',
    '.tbw-status-err{color:#dc2626}',
    '.tbw-pin{position:fixed;z-index:2147483647;width:24px;height:24px;border-radius:9999px;background:' + COLOR + ';color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;margin-left:-12px;margin-top:-12px;box-shadow:0 0 0 3px rgba(255,255,255,.9),0 4px 10px rgba(0,0,0,.3);pointer-events:none}',
    /* live pin badges */
    '.tbw-livepin{position:absolute;z-index:2147483640;width:26px;height:26px;margin-left:-13px;margin-top:-13px;border-radius:9999px;background:' + COLOR + ';color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;cursor:pointer;border:2px solid #fff;box-shadow:0 3px 8px rgba(0,0,0,.3);transition:transform .15s}',
    '.tbw-livepin:hover{transform:scale(1.15)}',
    '.tbw-livepin-popup{position:absolute;z-index:2147483641;background:#fff;border:1px solid #e5e7eb;border-radius:8px;box-shadow:0 10px 30px rgba(0,0,0,.2);padding:12px 14px;width:260px;font-size:12.5px;color:#111827;line-height:1.5;animation:tbw-fadein .15s ease-out}',
    '.tbw-livepin-popup strong{display:block;font-size:13px;margin-bottom:4px}',
    '.tbw-livepin-popup .meta{color:#6b7280;font-size:11px;margin-bottom:6px}',
    '.tbw-livepin-popup .status{display:inline-block;padding:2px 8px;border-radius:9999px;font-size:10px;background:#dbeafe;color:#1e3a8a;margin-left:4px}',
    /* annotation editor */
    '.tbw-anno-bg{position:fixed;inset:0;z-index:2147483646;background:rgba(0,0,0,.85);display:flex;flex-direction:column;align-items:center;justify-content:center;padding:14px}',
    '.tbw-anno-bar{display:flex;gap:6px;background:#1f2937;padding:6px 10px;border-radius:8px;margin-bottom:10px;box-shadow:0 6px 18px rgba(0,0,0,.4)}',
    '.tbw-anno-bar button{background:transparent;color:#cbd5e1;border:1px solid #374151;padding:6px 12px;border-radius:5px;cursor:pointer;font-size:12.5px;display:inline-flex;align-items:center;gap:6px}',
    '.tbw-anno-bar button.active{background:' + COLOR + ';color:#fff;border-color:' + COLOR + '}',
    '.tbw-anno-bar .sp{width:1px;background:#374151;margin:0 4px}',
    '.tbw-anno-canvas{cursor:crosshair;border-radius:4px;box-shadow:0 10px 30px rgba(0,0,0,.5);max-width:90vw;max-height:74vh}',
    '.tbw-anno-actions{margin-top:10px;display:flex;gap:8px}',
    '.tbw-anno-actions .ghost{background:#374151;color:#cbd5e1;border:none;padding:8px 16px;border-radius:6px;cursor:pointer;font-size:13px}',
    '.tbw-anno-actions .primary{background:' + COLOR + ';color:#fff;border:none;padding:8px 16px;border-radius:6px;cursor:pointer;font-size:13px;font-weight:500}',
    /* welcome tooltip + help popover */
    '.tbw-welcome{position:fixed;z-index:2147483645;width:280px;background:#111827;color:#fff;padding:14px 16px;border-radius:8px;box-shadow:0 10px 30px rgba(0,0,0,.3);font-size:13px;line-height:1.5;animation:tbw-fadein .3s ease-out}',
    '.tbw-welcome strong{display:block;font-size:14px;margin-bottom:4px}',
    '.tbw-welcome ol{margin:8px 0 10px;padding-left:18px}',
    '.tbw-welcome li{margin:2px 0}',
    '.tbw-welcome-actions{display:flex;justify-content:flex-end;gap:8px}',
    '.tbw-welcome-actions button{background:' + COLOR + ';color:#fff;border:none;padding:5px 12px;border-radius:5px;cursor:pointer;font-size:12px;font-weight:500}',
    '.tbw-welcome-actions button.ghost{background:transparent;color:#9ca3af}',
    '.tbw-welcome::after{content:"";position:absolute;border:8px solid transparent}',
    '.tbw-welcome-br::after{bottom:-16px;right:14px;border-top-color:#111827}',
    '.tbw-welcome-bl::after{bottom:-16px;left:14px;border-top-color:#111827}',
    '.tbw-help{margin-left:6px;width:22px;height:22px;border-radius:9999px;background:#e5e7eb;border:none;cursor:pointer;font-size:12px;color:#374151;display:inline-flex;align-items:center;justify-content:center}',
    '.tbw-help:hover{background:#d1d5db}',
    '.tbw-help-panel{background:#f0f9ff;border:1px solid #bae6fd;border-radius:6px;padding:10px 12px;margin-top:8px;font-size:12.5px;color:#075985;line-height:1.5}',
    '.tbw-help-panel ul{margin:4px 0 0;padding-left:18px}',
    '@keyframes tbw-fadein{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}',
  ].join('\n');

  var style = document.createElement('style');
  style.textContent = css;
  document.head.appendChild(style);

  // --- helpers ------------------------------------------------------------
  function el(tag, cls, html) {
    var e = document.createElement(tag);
    if (cls) e.className = cls;
    if (html != null) e.innerHTML = html;
    return e;
  }

  function getCssSelector(node) {
    if (!node || node === document.body || node === document.documentElement) return '';
    var path = [];
    while (node && node.nodeType === 1 && path.length < 6) {
      var sel = node.nodeName.toLowerCase();
      if (node.id) {
        sel = '#' + CSS.escape(node.id);
        path.unshift(sel);
        break;
      } else {
        var sib = node, i = 1;
        while ((sib = sib.previousElementSibling)) {
          if (sib.nodeName === node.nodeName) i++;
        }
        if (node.className && typeof node.className === 'string') {
          var firstCls = node.className.trim().split(/\s+/)[0];
          if (firstCls) sel += '.' + CSS.escape(firstCls);
        }
        sel += ':nth-of-type(' + i + ')';
      }
      path.unshift(sel);
      node = node.parentElement;
    }
    return path.join(' > ');
  }

  function detectBrowser(ua) {
    if (/Edg\//.test(ua)) return 'Edge';
    if (/OPR\//.test(ua)) return 'Opera';
    if (/Chrome\//.test(ua) && !/Edg|OPR/.test(ua)) return 'Chrome';
    if (/Firefox\//.test(ua)) return 'Firefox';
    if (/Safari\//.test(ua) && !/Chrome/.test(ua)) return 'Safari';
    return 'Unknown';
  }
  function detectOS(ua) {
    if (/Windows NT/.test(ua)) return 'Windows';
    if (/Mac OS X/.test(ua)) return 'macOS';
    if (/Android/.test(ua)) return 'Android';
    if (/iPhone|iPad|iPod/.test(ua)) return 'iOS';
    if (/Linux/.test(ua)) return 'Linux';
    return 'Unknown';
  }

  function loadHtml2Canvas() {
    return new Promise(function (resolve) {
      if (window.html2canvas) return resolve(window.html2canvas);
      var s = document.createElement('script');
      s.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
      s.onload = function () { resolve(window.html2canvas); };
      s.onerror = function () { resolve(null); };
      document.head.appendChild(s);
    });
  }

  // --- state --------------------------------------------------------------
  var state = {
    pinX: null, pinY: null,
    selector: null,
    screenshot: null,
  };

  // --- console + error capture (ring buffers) -----------------------------
  var consoleBuf = [];   // last 50 console entries
  var errorBuf   = [];   // last 20 uncaught errors / promise rejections
  var CONSOLE_CAP = 50, ERROR_CAP = 20;

  function pushRing(buf, entry, cap) {
    buf.push(entry);
    if (buf.length > cap) buf.shift();
  }

  function stringifyArg(a) {
    if (a == null) return String(a);
    if (typeof a === 'string') return a;
    if (a instanceof Error) return a.message + (a.stack ? '\n' + a.stack : '');
    try { return JSON.stringify(a); } catch (e) { return String(a); }
  }

  ['log', 'info', 'warn', 'error', 'debug'].forEach(function (lvl) {
    if (typeof console === 'undefined' || typeof console[lvl] !== 'function') return;
    var orig = console[lvl].bind(console);
    console[lvl] = function () {
      try {
        var args = Array.prototype.slice.call(arguments);
        pushRing(consoleBuf, {
          level: lvl,
          message: args.map(stringifyArg).join(' ').slice(0, 2000),
          at: Date.now()
        }, CONSOLE_CAP);
      } catch (e) {}
      return orig.apply(console, arguments);
    };
  });

  window.addEventListener('error', function (e) {
    pushRing(errorBuf, {
      message: (e.message || 'Unknown error').slice(0, 2000),
      source: (e.filename || '').slice(0, 500),
      line: e.lineno || 0,
      col: e.colno || 0,
      stack: e.error && e.error.stack ? String(e.error.stack).slice(0, 5000) : '',
      at: Date.now()
    }, ERROR_CAP);
  }, true);

  // --- Web Vitals capture -------------------------------------------------
  var perfMetrics = {};
  try {
    if (typeof PerformanceObserver !== 'undefined') {
      // LCP
      new PerformanceObserver(function (list) {
        var entries = list.getEntries();
        var last = entries[entries.length - 1];
        if (last) perfMetrics.lcp = Math.round(last.renderTime || last.loadTime || last.startTime);
      }).observe({ type: 'largest-contentful-paint', buffered: true });
      // CLS
      var clsValue = 0;
      new PerformanceObserver(function (list) {
        list.getEntries().forEach(function (e) { if (!e.hadRecentInput) clsValue += e.value; });
        perfMetrics.cls = Math.round(clsValue * 1000) / 1000;
      }).observe({ type: 'layout-shift', buffered: true });
      // FCP
      new PerformanceObserver(function (list) {
        list.getEntries().forEach(function (e) {
          if (e.name === 'first-contentful-paint') perfMetrics.fcp = Math.round(e.startTime);
        });
      }).observe({ type: 'paint', buffered: true });
      // FID (first-input)
      new PerformanceObserver(function (list) {
        list.getEntries().forEach(function (e) { perfMetrics.fid = Math.round(e.processingStart - e.startTime); });
      }).observe({ type: 'first-input', buffered: true });
    }
    window.addEventListener('load', function () {
      var nav = (performance.getEntriesByType && performance.getEntriesByType('navigation')[0]) || null;
      if (nav) {
        perfMetrics.ttfb = Math.round(nav.responseStart - nav.requestStart);
        perfMetrics.dom_load_ms = Math.round(nav.domContentLoadedEventEnd - nav.startTime);
        perfMetrics.full_load_ms = Math.round(nav.loadEventEnd - nav.startTime);
      }
    });
  } catch (e) {}

  window.addEventListener('unhandledrejection', function (e) {
    var reason = e.reason;
    pushRing(errorBuf, {
      message: ('Unhandled promise rejection: ' + (reason && reason.message ? reason.message : stringifyArg(reason))).slice(0, 2000),
      source: '', line: 0, col: 0,
      stack: reason && reason.stack ? String(reason.stack).slice(0, 5000) : '',
      at: Date.now()
    }, ERROR_CAP);
  });

  // --- feed/list endpoint -------------------------------------------------
  function fetchExistingFeedback() {
    var url = ENDPOINT + '?widget_key=' + encodeURIComponent(WIDGET_KEY)
            + '&page_url=' + encodeURIComponent(location.href.slice(0, 2048));
    return fetch(url, { method: 'GET', headers: { 'Accept': 'application/json' }, credentials: 'omit' })
      .then(function (r) { return r.ok ? r.json() : { bugs: [] }; })
      .catch(function () { return { bugs: [] }; });
  }

  function openHistoryPanel() {
    var bg = el('div', 'tbw-root tbw-modal-bg');
    var modal = el('div', 'tbw-modal');
    modal.style.maxWidth = '520px';
    modal.innerHTML = ''
      + '<div class="tbw-title">Feedback on this page<button class="tbw-close" aria-label="Close">&times;</button></div>'
      + '<div class="tbw-status">Loading…</div>'
      + '<div data-list style="margin-top:8px"></div>';
    bg.appendChild(modal);
    document.body.appendChild(bg);

    function close() { bg.remove(); fab.style.display = ''; }
    modal.querySelector('.tbw-close').onclick = close;
    bg.addEventListener('click', function (e) { if (e.target === bg) close(); });

    fetchExistingFeedback().then(function (data) {
      var status = modal.querySelector('.tbw-status');
      var list   = modal.querySelector('[data-list]');
      status.textContent = '';
      var bugs = data.bugs || [];
      if (bugs.length === 0) {
        list.innerHTML = '<div style="padding:20px;text-align:center;color:#6b7280;font-size:13px">No feedback yet on this page. Be the first!</div>';
        return;
      }
      bugs.forEach(function (b) {
        var item = el('div');
        item.style.cssText = 'border:1px solid #e5e7eb;border-radius:6px;padding:10px 12px;margin-bottom:8px';
        var who = b.guest_name ? escapeHtml(b.guest_name) : 'Someone';
        var when = b.created_at ? new Date(b.created_at).toLocaleString() : '';
        var statusBadge = b.status ? '<span style="display:inline-block;padding:2px 8px;border-radius:9999px;font-size:11px;background:#dbeafe;color:#1e3a8a;margin-left:6px">' + escapeHtml(b.status) + '</span>' : '';
        var commentsHtml = '';
        if (b.comments && b.comments.length) {
          commentsHtml = '<div style="margin-top:6px;padding-top:6px;border-top:1px solid #f3f4f6;font-size:12.5px;color:#374151">'
            + b.comments.map(function (c) {
                return '<div style="margin:4px 0"><strong>' + escapeHtml(c.author) + ':</strong> ' + escapeHtml(c.body).slice(0, 200) + '</div>';
              }).join('')
            + '</div>';
        }
        item.innerHTML =
          '<div style="font-weight:600;font-size:13.5px">' + escapeHtml(b.title || '') + statusBadge + '</div>' +
          '<div style="font-size:12px;color:#6b7280;margin-top:2px">' + who + ' · ' + when + '</div>' +
          '<div style="margin-top:6px;font-size:13px;color:#374151;white-space:pre-wrap">' + escapeHtml((b.description || '').slice(0, 280)) + '</div>' +
          commentsHtml;
        list.appendChild(item);
      });
    });
  }

  // --- video recording (Phase 2) ------------------------------------------
  var videoBlob = null;
  var videoDurationS = 0;

  function startVideoRecording() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getDisplayMedia) {
      alert('Screen recording is not supported in this browser.');
      return;
    }
    navigator.mediaDevices.getDisplayMedia({
      video: { displaySurface: 'browser' },
      audio: false
    }).then(function (stream) {
      var chunks = [];
      var mime = 'video/webm;codecs=vp9';
      if (!MediaRecorder.isTypeSupported(mime)) mime = 'video/webm;codecs=vp8';
      if (!MediaRecorder.isTypeSupported(mime)) mime = 'video/webm';
      var rec = new MediaRecorder(stream, { mimeType: mime, videoBitsPerSecond: 1_500_000 });
      var startedAt = Date.now();
      var bar = showRecordingBar(function stop() { try { rec.stop(); } catch (e) {} });
      rec.ondataavailable = function (e) { if (e.data && e.data.size) chunks.push(e.data); };
      rec.onstop = function () {
        stream.getTracks().forEach(function (t) { try { t.stop(); } catch (e) {} });
        bar.remove();
        videoBlob = new Blob(chunks, { type: mime });
        videoDurationS = Math.min(30, Math.round((Date.now() - startedAt) / 1000));
        // After recording, proceed straight to pin mode (the video is attached after bug create)
        startPinMode();
      };
      rec.start();
      // hard limit 30s
      setTimeout(function () { if (rec.state === 'recording') rec.stop(); }, 30_000);
      // also stop if user ends sharing
      stream.getVideoTracks()[0].addEventListener('ended', function () {
        if (rec.state === 'recording') rec.stop();
      });
    }).catch(function (err) {
      console.warn('[Gesture-widget] recording cancelled:', err && err.message);
    });
  }

  function showRecordingBar(onStop) {
    var bar = document.createElement('div');
    bar.className = 'tbw-root';
    bar.style.cssText = 'position:fixed;top:18px;left:50%;transform:translateX(-50%);z-index:2147483647;background:#dc2626;color:#fff;padding:8px 16px;border-radius:9999px;font-size:13px;font-weight:500;box-shadow:0 6px 20px rgba(0,0,0,.3);display:flex;align-items:center;gap:10px';
    bar.innerHTML = '<span style="width:10px;height:10px;background:#fff;border-radius:9999px;animation:tbw-fadein 1s infinite alternate"></span>Recording — <span data-time>0:00</span> / 0:30';
    var stop = document.createElement('button');
    stop.style.cssText = 'background:rgba(255,255,255,.25);color:#fff;border:none;border-radius:5px;padding:4px 12px;cursor:pointer;font-size:12px;margin-left:6px';
    stop.textContent = 'Stop';
    stop.onclick = onStop;
    bar.appendChild(stop);
    document.body.appendChild(bar);
    var t0 = Date.now();
    var iv = setInterval(function () {
      var s = Math.floor((Date.now() - t0) / 1000);
      var mm = Math.floor(s / 60), ss = s % 60;
      var span = bar.querySelector('[data-time]');
      if (span) span.textContent = mm + ':' + (ss < 10 ? '0' : '') + ss;
      if (!bar.parentNode) clearInterval(iv);
    }, 250);
    return { remove: function () { bar.remove(); clearInterval(iv); } };
  }

  function uploadVideo(bugId) {
    var fd = new FormData();
    fd.append('widget_key', WIDGET_KEY);
    fd.append('bug_id', String(bugId));
    fd.append('duration_s', String(videoDurationS || 0));
    fd.append('video', videoBlob, 'recording.webm');
    var videoUrl = ENDPOINT.replace(/\/feedback$/, '/video');
    return fetch(videoUrl, { method: 'POST', body: fd, credentials: 'omit' })
      .then(function (r) { return r.json().catch(function () { return {}; }); })
      .catch(function () { return {}; });
  }

  function escapeHtml(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
  }

  // --- floating button ----------------------------------------------------
  var fab = el('button', 'tbw-root tbw-fab tbw-pos-' + (POSITION === 'bottom-left' ? 'bl' : 'br'));
  fab.setAttribute('aria-label', BUTTON_LABEL || 'Send feedback');
  fab.title = BUTTON_LABEL || 'Send feedback';
  if (BRAND_LOGO) {
    fab.innerHTML = '<img src="' + escapeHtml(BRAND_LOGO) + '" alt="" style="width:24px;height:24px;border-radius:9999px;object-fit:cover">';
  } else {
    fab.innerHTML = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';
  }
  fab.onclick = startPinMode;
  document.body.appendChild(fab);

  // video record button (only if supported)
  if (navigator.mediaDevices && navigator.mediaDevices.getDisplayMedia && typeof MediaRecorder !== 'undefined') {
    var recBtn = el('button', 'tbw-root tbw-fab tbw-pos-' + (POSITION === 'bottom-left' ? 'bl' : 'br'));
    recBtn.style.width = '38px';
    recBtn.style.height = '38px';
    recBtn.style.fontSize = '14px';
    recBtn.style[POSITION === 'bottom-left' ? 'left' : 'right'] = '126px';
    recBtn.style.bottom = '25px';
    recBtn.style.background = '#dc2626';
    recBtn.setAttribute('aria-label', 'Record a short video');
    recBtn.title = 'Record a short screen video (max 30s)';
    recBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="6"/></svg>';
    recBtn.onclick = startVideoRecording;
    document.body.appendChild(recBtn);
  }

  // small history button next to the FAB
  var hist = el('button', 'tbw-root tbw-fab tbw-pos-' + (POSITION === 'bottom-left' ? 'bl' : 'br'));
  hist.style.width = '38px';
  hist.style.height = '38px';
  hist.style.fontSize = '16px';
  hist.style[POSITION === 'bottom-left' ? 'left' : 'right'] = '78px';
  hist.style.bottom = '25px';
  hist.style.background = '#374151';
  hist.setAttribute('aria-label', 'See feedback on this page');
  hist.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>';
  hist.onclick = openHistoryPanel;
  document.body.appendChild(hist);

  // First-visit welcome tooltip — shown once per browser
  showWelcomeIfFirstVisit();

  // Render existing pins on this page (if any)
  renderLivePinsForThisPage();

  function renderLivePinsForThisPage() {
    fetchExistingFeedback().then(function (data) {
      var bugs = (data.bugs || []).filter(function (b) {
        return b.pin_x != null && b.pin_y != null && b.viewport_w && b.viewport_h
            && !['Resolved', 'Closed', 'Done'].includes(b.status);
      });
      if (!bugs.length) return;
      var container = el('div', 'tbw-root');
      container.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:2147483640';
      document.body.appendChild(container);

      bugs.slice(0, 30).forEach(function (b, i) {
        var x = (b.pin_x / b.viewport_w) * document.documentElement.scrollWidth;
        var y = (b.pin_y / b.viewport_h) * document.documentElement.scrollHeight;
        if (b.pin_x <= window.innerWidth && b.pin_y <= window.innerHeight) {
          x = b.pin_x; y = b.pin_y;
        }
        var dot = el('div', 'tbw-root tbw-livepin');
        dot.style.left = x + 'px';
        dot.style.top  = y + 'px';
        dot.style.pointerEvents = 'auto';
        dot.textContent = String(i + 1);
        dot.title = b.title || 'Open feedback';
        dot.addEventListener('click', function (e) {
          e.stopPropagation();
          document.querySelectorAll('.tbw-livepin-popup').forEach(function (n) { n.remove(); });
          var pop = el('div', 'tbw-root tbw-livepin-popup');
          var status = b.status ? '<span class="status">' + escapeHtml(b.status) + '</span>' : '';
          var who = b.guest_name ? escapeHtml(b.guest_name) : 'Someone';
          var when = b.created_at ? new Date(b.created_at).toLocaleString() : '';
          pop.innerHTML = '<strong>' + escapeHtml(b.title || '') + status + '</strong>' +
                          '<div class="meta">' + who + ' · ' + when + '</div>' +
                          '<div>' + escapeHtml((b.description || '').slice(0, 200)) + '</div>';
          pop.style.left = (x + 18) + 'px';
          pop.style.top  = (y - 8) + 'px';
          container.appendChild(pop);
          var dismiss = function (ev) {
            if (!pop.contains(ev.target)) { pop.remove(); document.removeEventListener('click', dismiss, true); }
          };
          setTimeout(function () { document.addEventListener('click', dismiss, true); }, 0);
        });
        container.appendChild(dot);
      });
    });
  }

  function showWelcomeIfFirstVisit() {
    try { if (localStorage.getItem('tbw-welcomed')) return; } catch (e) {}
    var isLeft = POSITION === 'bottom-left';
    var tip = el('div', 'tbw-root tbw-welcome ' + (isLeft ? 'tbw-welcome-bl' : 'tbw-welcome-br'));
    var label = BUTTON_LABEL || t('send_feedback');
    var steps = (L.welcome_steps || I18N.en.welcome_steps).map(function (s) {
      return '<li>' + s.replace('{label}', escapeHtml(label)) + '</li>';
    }).join('');
    tip.innerHTML =
      '<strong>' + t('welcome_title') + ' 🐞</strong>' +
      (WELCOME_TEXT ? escapeHtml(WELCOME_TEXT) : t('welcome_body')) +
      '<ol>' + steps + '</ol>' +
      '<div class="tbw-welcome-actions">' +
        '<button class="ghost" type="button" data-act="dismiss">' + t('got_it') + '</button>' +
        '<button type="button" data-act="try">' + t('try_now') + '</button>' +
      '</div>';
    document.body.appendChild(tip);
    // position above the FAB
    var fabRect = fab.getBoundingClientRect();
    tip.style.bottom = (window.innerHeight - fabRect.top + 12) + 'px';
    if (isLeft) {
      tip.style.left = '20px';
    } else {
      tip.style.right = '20px';
    }
    function dismiss() {
      try { localStorage.setItem('tbw-welcomed', '1'); } catch (e) {}
      tip.remove();
    }
    tip.querySelector('[data-act=dismiss]').onclick = dismiss;
    tip.querySelector('[data-act=try]').onclick = function () { dismiss(); startPinMode(); };
    // auto-dismiss after 20s
    setTimeout(function () { if (tip.parentNode) dismiss(); }, 20000);
  }

  // --- pin mode -----------------------------------------------------------
  function startPinMode() {
    fab.style.display = 'none';
    var hint = el('div', 'tbw-root tbw-hint', '🎯 &nbsp;' + t('pin_hint'));
    var overlay = el('div', 'tbw-root tbw-overlay');
    document.body.appendChild(hint);
    document.body.appendChild(overlay);

    function cancel() {
      document.removeEventListener('keydown', onEsc);
      overlay.remove();
      hint.remove();
      fab.style.display = '';
    }
    function onEsc(e) { if (e.key === 'Escape') cancel(); }
    document.addEventListener('keydown', onEsc);

    function pinAt(x, y) {
      overlay.style.pointerEvents = 'none';
      var below = document.elementFromPoint(x, y);
      overlay.style.pointerEvents = '';
      state.pinX = Math.round(x + window.scrollX);
      state.pinY = Math.round(y + window.scrollY);
      state.selector = getCssSelector(below);
      overlay.remove();
      hint.remove();
      document.removeEventListener('keydown', onEsc);
      captureScreenshotAndOpenForm();
    }

    overlay.addEventListener('click', function (e) {
      pinAt(e.clientX, e.clientY);
    });
    // Touch support
    overlay.addEventListener('touchend', function (e) {
      if (e.changedTouches && e.changedTouches[0]) {
        e.preventDefault();
        var t = e.changedTouches[0];
        pinAt(t.clientX, t.clientY);
      }
    }, { passive: false });
  }

  function captureScreenshotAndOpenForm() {
    var loading = el('div', 'tbw-root tbw-hint', '📸 ' + t('capturing'));
    document.body.appendChild(loading);

    loadHtml2Canvas().then(function (h2c) {
      if (!h2c) { loading.remove(); openForm(); return; }
      h2c(document.body, {
        useCORS: true, allowTaint: true, logging: false,
        width: window.innerWidth, height: window.innerHeight,
        x: window.scrollX, y: window.scrollY,
        scale: Math.min(window.devicePixelRatio || 1, 1.5)
      }).then(function (canvas) {
        try {
          var ctx = canvas.getContext('2d');
          var px = (state.pinX - window.scrollX) * (canvas.width / window.innerWidth);
          var py = (state.pinY - window.scrollY) * (canvas.height / window.innerHeight);
          ctx.fillStyle = COLOR;
          ctx.strokeStyle = '#ffffff';
          ctx.lineWidth = 3;
          ctx.beginPath();
          ctx.arc(px, py, 14, 0, Math.PI * 2);
          ctx.fill();
          ctx.stroke();
        } catch (e) {}
        loading.remove();
        openAnnotationEditor(canvas);
      }).catch(function () {
        loading.remove();
        openForm();
      });
    });
  }

  function openAnnotationEditor(srcCanvas) {
    var bg = el('div', 'tbw-root tbw-anno-bg');
    var bar = el('div', 'tbw-anno-bar');
    bar.innerHTML = ''
      + '<button data-tool="none" class="active" title="Skip annotation">✋ Skip</button>'
      + '<div class="sp"></div>'
      + '<button data-tool="rect" title="Rectangle">▭ Rect</button>'
      + '<button data-tool="arrow" title="Arrow">↗ Arrow</button>'
      + '<button data-tool="blur" title="Blur sensitive">🌫 Blur</button>'
      + '<div class="sp"></div>'
      + '<button data-act="undo" title="Undo last">↶ Undo</button>';

    var work = document.createElement('canvas');
    work.className = 'tbw-anno-canvas';
    work.width = srcCanvas.width;
    work.height = srcCanvas.height;
    var ctx = work.getContext('2d');
    ctx.drawImage(srcCanvas, 0, 0);
    var base = document.createElement('canvas');
    base.width = work.width; base.height = work.height;
    base.getContext('2d').drawImage(srcCanvas, 0, 0);

    var actions = el('div', 'tbw-anno-actions');
    actions.innerHTML = '<button class="ghost" data-act="cancel">Cancel</button><button class="primary" data-act="confirm">Use this screenshot</button>';

    bg.appendChild(bar);
    bg.appendChild(work);
    bg.appendChild(actions);
    document.body.appendChild(bg);

    // scale display to fit viewport
    var maxW = window.innerWidth * 0.9, maxH = window.innerHeight * 0.74;
    var scale = Math.min(maxW / work.width, maxH / work.height, 1);
    work.style.width = (work.width * scale) + 'px';
    work.style.height = (work.height * scale) + 'px';

    var tool = 'none';
    var history = []; // snapshots for undo

    bar.querySelectorAll('button[data-tool]').forEach(function (b) {
      b.addEventListener('click', function () {
        tool = b.getAttribute('data-tool');
        bar.querySelectorAll('button[data-tool]').forEach(function (x) { x.classList.remove('active'); });
        b.classList.add('active');
        work.style.cursor = tool === 'none' ? 'default' : 'crosshair';
      });
    });
    bar.querySelector('[data-act="undo"]').addEventListener('click', function () {
      if (history.length) {
        var snap = history.pop();
        ctx.putImageData(snap, 0, 0);
      }
    });

    var drag = null;
    function pos(e) {
      var rect = work.getBoundingClientRect();
      return { x: (e.clientX - rect.left) / scale, y: (e.clientY - rect.top) / scale };
    }
    function startDrag(p) {
      if (tool === 'none') return;
      history.push(ctx.getImageData(0, 0, work.width, work.height));
      drag = { start: p, snap: ctx.getImageData(0, 0, work.width, work.height) };
    }
    function moveDrag(p) {
      if (!drag) return;
      ctx.putImageData(drag.snap, 0, 0);
      drawShape(tool, drag.start.x, drag.start.y, p.x, p.y);
    }
    work.addEventListener('mousedown', function (e) { startDrag(pos(e)); });
    work.addEventListener('mousemove', function (e) { moveDrag(pos(e)); });
    work.addEventListener('mouseup',   function ()  { drag = null; });
    work.addEventListener('touchstart', function (e) {
      if (e.touches[0]) { e.preventDefault(); startDrag(pos(e.touches[0])); }
    }, { passive: false });
    work.addEventListener('touchmove', function (e) {
      if (e.touches[0]) { e.preventDefault(); moveDrag(pos(e.touches[0])); }
    }, { passive: false });
    work.addEventListener('touchend', function () { drag = null; });

    function drawShape(t, x1, y1, x2, y2) {
      ctx.lineWidth = 4;
      ctx.strokeStyle = '#ef4444';
      if (t === 'rect') {
        ctx.strokeRect(Math.min(x1, x2), Math.min(y1, y2), Math.abs(x2 - x1), Math.abs(y2 - y1));
      } else if (t === 'arrow') {
        ctx.beginPath();
        ctx.moveTo(x1, y1); ctx.lineTo(x2, y2);
        // arrowhead
        var ang = Math.atan2(y2 - y1, x2 - x1);
        var hl = 16;
        ctx.lineTo(x2 - hl * Math.cos(ang - Math.PI / 7), y2 - hl * Math.sin(ang - Math.PI / 7));
        ctx.moveTo(x2, y2);
        ctx.lineTo(x2 - hl * Math.cos(ang + Math.PI / 7), y2 - hl * Math.sin(ang + Math.PI / 7));
        ctx.stroke();
      } else if (t === 'blur') {
        var rx = Math.min(x1, x2), ry = Math.min(y1, y2);
        var rw = Math.abs(x2 - x1), rh = Math.abs(y2 - y1);
        if (rw > 4 && rh > 4) {
          var img = ctx.getImageData(rx, ry, rw, rh);
          // 5px block pixelation
          var b = 12;
          var tmp = document.createElement('canvas');
          tmp.width = rw; tmp.height = rh;
          tmp.getContext('2d').putImageData(img, 0, 0);
          ctx.imageSmoothingEnabled = false;
          ctx.drawImage(tmp, 0, 0, rw, rh, rx, ry, Math.max(1, rw / b), Math.max(1, rh / b));
          ctx.drawImage(work, rx, ry, Math.max(1, rw / b), Math.max(1, rh / b), rx, ry, rw, rh);
          ctx.imageSmoothingEnabled = true;
        }
      }
    }

    actions.querySelector('[data-act="cancel"]').onclick = function () {
      bg.remove();
      fab.style.display = '';
    };
    actions.querySelector('[data-act="confirm"]').onclick = function () {
      state.screenshot = work.toDataURL('image/jpeg', 0.75);
      bg.remove();
      var modal = openForm();
      var thumb = modal.querySelector('.tbw-thumb');
      thumb.innerHTML = '';
      var img = document.createElement('img');
      img.src = state.screenshot;
      thumb.appendChild(img);
    };
  }

  // --- form modal ---------------------------------------------------------
  function openForm() {
    var bg = el('div', 'tbw-root tbw-modal-bg');
    var modal = el('div', 'tbw-modal');
    modal.innerHTML = ''
      + '<div class="tbw-title">'
      +   '<span>' + t('send_feedback')
      +     '<button class="tbw-help" type="button" data-act="help" title="?" aria-label="Help">?</button>'
      +   '</span>'
      +   '<button class="tbw-close" aria-label="Close">&times;</button>'
      + '</div>'
      + '<div class="tbw-help-panel" data-help style="display:none">'
      +   '<strong>' + t('help_title') + '</strong>'
      +   '<ul>' + (L.help_items || I18N.en.help_items).map(function (i) { return '<li>' + i + '</li>'; }).join('') + '</ul>'
      +   '<div style="margin-top:6px">' + t('help_reassure') + '</div>'
      + '</div>'
      + '<div class="tbw-field"><label>' + t('description') + ' *</label><textarea name="description" required placeholder="' + escapeHtml(t('description_ph')) + '"></textarea></div>'
      // Honeypot fields — invisible to humans, attractive to bots
      + '<div style="position:absolute;left:-9999px;top:-9999px;opacity:0" aria-hidden="true" tabindex="-1">'
      +   '<label>Company<input type="text" name="hp_company" tabindex="-1" autocomplete="off"></label>'
      +   '<label>Website<input type="url" name="hp_url" tabindex="-1" autocomplete="off"></label>'
      + '</div>'
      + '<div class="tbw-row">'
      +   '<div class="tbw-field"><label>' + t('your_name') + '</label><input type="text" name="guest_name" placeholder="' + escapeHtml(t('optional')) + '"></div>'
      +   '<div class="tbw-field"><label>' + t('your_email') + '</label><input type="email" name="guest_email" placeholder="' + escapeHtml(t('optional')) + '"></div>'
      + '</div>'
      + '<div class="tbw-row">'
      +   '<div class="tbw-field"><label>' + t('priority') + '</label><select name="priority"><option value="low">Low</option><option value="medium" selected>Medium</option><option value="high">High</option><option value="critical">Critical</option></select></div>'
      +   '<div class="tbw-field"><label>' + t('severity') + '</label><select name="severity"><option value="minor">Minor</option><option value="major" selected>Major</option><option value="critical">Critical</option><option value="blocker">Blocker</option></select></div>'
      + '</div>'
      + '<div class="tbw-thumb"></div>'
      + '<div class="tbw-status"></div>'
      + '<div class="tbw-actions">'
      +   '<button type="button" class="tbw-btn tbw-btn-secondary" data-act="cancel">' + t('cancel') + '</button>'
      +   '<button type="button" class="tbw-btn tbw-btn-primary" data-act="send">' + t('send_feedback') + '</button>'
      + '</div>';
    bg.appendChild(modal);
    document.body.appendChild(bg);

    function close() {
      bg.remove();
      fab.style.display = '';
      state.pinX = state.pinY = state.selector = state.screenshot = null;
    }
    modal.querySelector('.tbw-close').onclick = close;
    modal.querySelector('[data-act="cancel"]').onclick = close;
    modal.querySelector('[data-act="send"]').onclick = function () {
      submitForm(modal, close);
    };
    modal.querySelector('[data-act="help"]').onclick = function () {
      var p = modal.querySelector('[data-help]');
      p.style.display = p.style.display === 'none' ? 'block' : 'none';
    };
    bg.addEventListener('click', function (e) { if (e.target === bg) close(); });
    return modal;
  }

  function submitForm(modal, onDone) {
    var descEl = modal.querySelector('[name=description]');
    var statusEl = modal.querySelector('.tbw-status');
    statusEl.classList.remove('tbw-status-ok', 'tbw-status-err');
    if (!descEl.value.trim()) {
      descEl.focus();
      statusEl.textContent = t('desc_required');
      statusEl.classList.add('tbw-status-err');
      return;
    }

    var sendBtn = modal.querySelector('[data-act=send]');
    sendBtn.disabled = true;
    sendBtn.textContent = t('sending');
    statusEl.textContent = '';

    var ua = navigator.userAgent;
    var payload = {
      widget_key:       WIDGET_KEY,
      description:      descEl.value.trim(),
      title:            descEl.value.trim().slice(0, 80),
      guest_name:       modal.querySelector('[name=guest_name]').value.trim() || null,
      guest_email:      modal.querySelector('[name=guest_email]').value.trim() || null,
      priority:         modal.querySelector('[name=priority]').value,
      severity:         modal.querySelector('[name=severity]').value,
      page_url:         location.href.slice(0, 2048),
      element_selector: state.selector,
      pin_x:            state.pinX,
      pin_y:            state.pinY,
      viewport_w:       window.innerWidth,
      viewport_h:       window.innerHeight,
      user_agent:       ua.slice(0, 1024),
      browser:          detectBrowser(ua),
      os:               detectOS(ua),
      screenshot:       state.screenshot,
      console_log:      consoleBuf.slice(),
      js_errors:        errorBuf.slice(),
      perf_metrics:     perfMetrics,
      hp_company:       modal.querySelector('[name=hp_company]') ? modal.querySelector('[name=hp_company]').value : '',
      hp_url:           modal.querySelector('[name=hp_url]') ? modal.querySelector('[name=hp_url]').value : '',
    };

    fetch(ENDPOINT, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Widget-Key': WIDGET_KEY },
      body: JSON.stringify(payload),
      credentials: 'omit',
    }).then(function (r) {
      return r.json().then(function (j) { return { ok: r.ok, body: j }; });
    }).then(function (res) {
      if (res.ok) {
        statusEl.textContent = t('sent');
        statusEl.classList.add('tbw-status-ok');
        if (videoBlob && res.body && res.body.bug_id) {
          statusEl.textContent = t('uploading_video');
          uploadVideo(res.body.bug_id).finally(function () {
            videoBlob = null; videoDurationS = 0;
            setTimeout(onDone, 600);
          });
        } else {
          setTimeout(onDone, 1200);
        }
      } else {
        statusEl.textContent = 'Could not send: ' + (res.body && res.body.error ? res.body.error : 'unknown error');
        statusEl.classList.add('tbw-status-err');
        sendBtn.disabled = false;
        sendBtn.textContent = 'Send feedback';
      }
    }).catch(function (err) {
      statusEl.textContent = 'Network error: ' + err.message;
      statusEl.classList.add('tbw-status-err');
      sendBtn.disabled = false;
      sendBtn.textContent = 'Send feedback';
    });
  }
  } // end bootstrap()
})();
