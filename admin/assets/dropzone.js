// Admin paneli: tüm dosya alanlarını sürükle-bırak bölgesine dönüştürür (tıklayarak seçme de çalışır)
(function () {
  function fmt(b) { return b > 1048576 ? (b / 1048576).toFixed(1) + ' MB' : Math.ceil(b / 1024) + ' KB'; }
  function enhance(input) {
    if (input.dataset.dz) return; input.dataset.dz = '1';
    const zone = document.createElement('div');
    zone.className = 'dropzone';
    zone.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i><div class="dz-text"><strong>Dosyayı buraya sürükleyip bırakın</strong><span>veya tıklayarak seçin' +
      (input.multiple ? ' (birden fazla dosya seçilebilir)' : '') + '</span></div><div class="dz-preview"></div>';
    input.parentNode.insertBefore(zone, input);
    zone.appendChild(input);
    input.classList.add('dz-input');
    const prev = zone.querySelector('.dz-preview');
    function render() {
      prev.innerHTML = '';
      [...input.files].forEach(f => {
        const item = document.createElement('div'); item.className = 'dz-item';
        if (f.type.startsWith('image/')) { const i = document.createElement('img'); i.src = URL.createObjectURL(f); item.appendChild(i); }
        else if (f.type.startsWith('video/')) { const v = document.createElement('video'); v.src = URL.createObjectURL(f); v.muted = true; v.playsInline = true; item.appendChild(v); }
        const c = document.createElement('span'); c.textContent = f.name + ' · ' + fmt(f.size); item.appendChild(c);
        prev.appendChild(item);
      });
      zone.classList.toggle('has-file', input.files.length > 0);
    }
    input.addEventListener('change', render);
    ['dragenter', 'dragover'].forEach(ev => zone.addEventListener(ev, e => { e.preventDefault(); zone.classList.add('over'); }));
    ['dragleave', 'dragend'].forEach(ev => zone.addEventListener(ev, () => zone.classList.remove('over')));
    zone.addEventListener('drop', e => {
      e.preventDefault(); zone.classList.remove('over');
      if (!e.dataTransfer || !e.dataTransfer.files.length) return;
      const dt = new DataTransfer();
      [...e.dataTransfer.files].slice(0, input.multiple ? 100 : 1).forEach(f => dt.items.add(f));
      input.files = dt.files;
      input.dispatchEvent(new Event('change', { bubbles: true }));
    });
  }
  function init() { document.querySelectorAll('input[type=file]').forEach(enhance); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
