(function () {
  'use strict';
  const cfg = window.AngebotDealsAdmin || {};
  let currentId = null;

  const lookupBtn = document.getElementById('angebot-admin-lookup-btn');
  const redeemBtn = document.getElementById('angebot-admin-redeem-btn');
  const result = document.getElementById('angebot-admin-lookup-result');
  const codeInput = document.getElementById('angebot-admin-code');

  lookupBtn?.addEventListener('click', async function () {
    const body = new URLSearchParams({
      action: 'angebot_lookup_voucher',
      nonce: cfg.nonce,
      code: codeInput?.value || '',
    });
    const res = await fetch(cfg.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' });
    const json = await res.json();
    if (!json.success) {
      result.innerHTML = '<p style="color:#b32d2e">' + (json.data?.message || 'Fehler') + '</p>';
      redeemBtn.disabled = true;
      currentId = null;
      return;
    }
    const d = json.data;
    currentId = d.id;
    redeemBtn.disabled = !d.can_redeem;
    result.innerHTML =
      '<p><strong>' + d.deal_title + '</strong><br>Status: ' + d.status_label +
      '<br>Kunde: ' + d.customer +
      (d.expires_at ? '<br>Ablauf: ' + d.expires_at : '') + '</p>';
  });

  redeemBtn?.addEventListener('click', async function () {
    if (!currentId) return;
    const body = new URLSearchParams({
      action: 'angebot_redeem_voucher',
      nonce: cfg.nonce,
      voucher_id: String(currentId),
    });
    const res = await fetch(cfg.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' });
    const json = await res.json();
    result.innerHTML = '<p>' + (json.data?.message || '') + '</p>';
    redeemBtn.disabled = true;
  });
})();
