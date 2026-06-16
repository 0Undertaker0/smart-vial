document.addEventListener('DOMContentLoaded', function(){
  const form = document.getElementById('incidentFilters');
  if (!form) return;
  const applyBtn = document.getElementById('applyFilters');
  const clearBtn = document.getElementById('clearFilters');
  const tableBody = document.querySelector('#incidentsTable tbody');
  const storageKey = 'incidents_filters';

  function readFiltersFromForm() {
    const fd = new FormData(form);
    const obj = {};
    for (const [k,v] of fd.entries()) {
      if (v !== null && v !== undefined && String(v).trim() !== '') obj[k] = v;
    }
    return obj;
  }

  function setFormFromFilters(filters) {
    for (const key in filters) {
      const el = form.elements[key];
      if (el) el.value = filters[key];
    }
  }

  function save(filters) {
    try { sessionStorage.setItem(storageKey, JSON.stringify(filters)); } catch(e) { /* ignore */ }
  }

  function load() {
    try { const raw = sessionStorage.getItem(storageKey); return raw ? JSON.parse(raw) : null; } catch(e) { return null; }
  }

  function buildQuery(filters) {
    const params = new URLSearchParams();
    params.append('ajax','1');
    for (const k in filters) params.append(k, filters[k]);
    return params.toString();
  }

  function escapeHtml(s) { if (s===null||s===undefined) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }

  function updateTable(data) {
    if (!tableBody) return;
    tableBody.innerHTML = '';
    if (!data || data.length===0) {
      tableBody.innerHTML = '<tr><td colspan="5">No hay registros</td></tr>';
      return;
    }
    for (const it of data) {
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${escapeHtml(it.id)}</td><td>${escapeHtml(it.titulo)}</td><td>${escapeHtml(it.fecha)}</td><td>${escapeHtml(it.gravedad)}</td><td>${escapeHtml(it.reportante)}</td>`;
      tableBody.appendChild(tr);
    }
  }

  async function fetchAndUpdate(filters) {
    const qs = buildQuery(filters);
    const url = `${window.location.pathname}?${qs}`;
    try {
      const res = await fetch(url, {headers: {'X-Requested-With': 'XMLHttpRequest'}});
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const data = await res.json();
      updateTable(data);
    } catch(e) {
      console.error('Error fetching incidents', e);
    }
  }

  applyBtn.addEventListener('click', function(){
    const filters = readFiltersFromForm();
    save(filters);
    fetchAndUpdate(filters);
  });

  clearBtn.addEventListener('click', function(){
    sessionStorage.removeItem(storageKey);
    form.reset();
    fetchAndUpdate({});
  });

  // Restore on load
  const saved = load();
  if (saved) {
    setFormFromFilters(saved);
    fetchAndUpdate(saved);
  }

});
