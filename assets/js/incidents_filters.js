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
      tableBody.innerHTML = '<tr><td colspan="6">No hay registros</td></tr>';
      return;
    }
    for (const it of data) {
      const tr = document.createElement('tr');
      const perms = window.__PERMS || {};
      let actions = '';
      if (perms.incident_edit) {
        actions += `<a class="btn btn-sm btn-primary me-1 edit-btn" href="?c=incident&a=edit&id=${encodeURIComponent(it.id)}" data-id="${escapeHtml(it.id)}">Editar</a>`;
      }
      if (perms.incident_delete) {
        actions += `<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="${escapeHtml(it.id)}">Eliminar</button>`;
      }
      tr.innerHTML = `<td>${escapeHtml(it.id)}</td><td>${escapeHtml(it.titulo)}</td><td>${escapeHtml(it.fecha)}</td><td>${escapeHtml(it.gravedad)}</td><td>${escapeHtml(it.reportante)}</td><td>${actions}</td>`;
      tableBody.appendChild(tr);
    }
  }

  // Delegate click handler for dynamic delete buttons (AJAX delete)
  tableBody.addEventListener('click', function(e){
    const target = e.target;
    if (!target.classList.contains('delete-btn')) return;
    if (!confirm('¿Eliminar este incidente? Esta acción puede revertirse desde administración.')) return;
    const id = target.getAttribute('data-id');
    const fd = new FormData();
    fd.append('id', id);
    fd.append('csrf_token', window.__CSRF || '');
    fetch(`${window.location.pathname}?c=incident&a=delete`, { method: 'POST', credentials: 'same-origin', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'} })
      .then(async res => {
        if (!res.ok) {
          const err = await res.json().catch(()=>({message:'Error'}));
          throw new Error(err.message || 'Error al eliminar');
        }
        // remove row
        const tr = target.closest('tr');
        if (tr) tr.remove();
      })
      .catch(err => alert(err.message || 'Error al eliminar'));
  });

  // Handle Edit button (delegated) to open modal and load data via AJAX
  tableBody.addEventListener('click', function(e){
    const target = e.target.closest('.edit-btn');
    if (!target) return;
    e.preventDefault();
    const id = target.getAttribute('data-id');
    if (!id) return;
    // Fetch incident JSON
    const curr = new URL(window.location.href);
    const currentController = curr.searchParams.get('c') || 'incident';
    const url = `${window.location.pathname}?c=${encodeURIComponent(currentController)}&a=view&id=${encodeURIComponent(id)}&ajax=1`;
    fetch(url, { credentials: 'same-origin', headers: {'X-Requested-With': 'XMLHttpRequest'} })
      .then(async res => {
        if (!res.ok) {
          const err = await res.json().catch(()=>({message:'Error al obtener registro'}));
          throw new Error(err.message || 'Error');
        }
        return res.json();
      })
      .then(data => {
        // Fill modal
        document.getElementById('edit_id').value = data.id || '';
        document.getElementById('edit_titulo').value = data.titulo || '';
        document.getElementById('edit_descripcion').value = data.descripcion || '';
        document.getElementById('edit_gravedad').value = data.gravedad || 'media';
        document.getElementById('edit_lat').value = data.lat || '';
        document.getElementById('edit_lng').value = data.lng || '';
        document.getElementById('edit_direccion').value = data.direccion || '';
        // Show modal (Bootstrap 5)
        var myModal = new bootstrap.Modal(document.getElementById('editIncidentModal'));
        myModal.show();
      })
      .catch(err => alert(err.message || 'Error al cargar incidente'));
  });

  // Save edit from modal
  const saveBtn = document.getElementById('saveEditBtn');
  if (saveBtn) {
    saveBtn.addEventListener('click', function(){
      const form = document.getElementById('editIncidentForm');
      const fd = new FormData(form);
      const id = fd.get('id');
      fd.append('csrf_token', window.__CSRF || '');
      const url = `${window.location.pathname}?c=incident&a=edit&id=${encodeURIComponent(id)}`;
      fetch(url, { method: 'POST', credentials: 'same-origin', body: fd, headers: {'X-Requested-With': 'XMLHttpRequest'} })
        .then(async res => {
          if (!res.ok) {
            const err = await res.json().catch(()=>({message:'Error al guardar'}));
            throw new Error(err.message || 'Error');
          }
          return res.json().catch(()=>({ok:true}));
        })
        .then(() => {
          // Close modal
          var modalEl = document.getElementById('editIncidentModal');
          var modal = bootstrap.Modal.getInstance(modalEl);
          if (modal) modal.hide();
          // Refresh table
          const filters = readFiltersFromForm() || {};
          fetchAndUpdate(filters);
        })
        .catch(err => alert(err.message || 'Error al guardar cambios'));
    });
  }

  async function fetchAndUpdate(filters) {
    const qs = buildQuery(filters);
    // Ensure we include the current controller param (c) so request lands on the same route
    const curr = new URL(window.location.href);
    const currentController = curr.searchParams.get('c') || 'incident';
    const url = `${window.location.pathname}?c=${encodeURIComponent(currentController)}&${qs}`;
    try {
      const res = await fetch(url, {credentials: 'same-origin', headers: {'X-Requested-With': 'XMLHttpRequest'}});
      if (!res.ok) {
        // Try to read JSON error message, otherwise fallback
        let msg = 'Error al cargar datos (HTTP ' + res.status + ')';
        try { const err = await res.json(); if (err && err.message) msg = err.message; } catch(e) { /* ignore */ }
        alert(msg);
        updateTable([]);
        return;
      }
      const data = await res.json();
      updateTable(data);
    } catch(e) {
      console.error('Error fetching incidents', e);
      alert('Error de red al obtener incidentes');
    }
  }

  applyBtn.addEventListener('click', function(){
    const filters = readFiltersFromForm();
    save(filters);
    fetchAndUpdate(filters);
  });

  clearBtn.addEventListener('click', async function(){
    sessionStorage.removeItem(storageKey);
    form.reset();
    // Llamar al servidor para que borre filtros guardados en sesión y actualizar la tabla
    const curr = new URL(window.location.href);
    const currentController = curr.searchParams.get('c') || 'incident';
    const url = `${window.location.pathname}?c=${encodeURIComponent(currentController)}&ajax=1&clear_filters=1`;
    try {
      const res = await fetch(url, { credentials: 'same-origin', headers: {'X-Requested-With': 'XMLHttpRequest'} });
      if (!res.ok) {
        let err = await res.json().catch(()=>({message:'Error'}));
        alert(err.message || 'Error al limpiar filtros');
        return;
      }
      const data = await res.json();
      updateTable(data);
      // Also update URL to remove filter params for clarity
      try { history.replaceState({}, document.title, window.location.pathname + '?c=' + encodeURIComponent(currentController)); } catch(e) { /* ignore */ }
    } catch(e) {
      console.error(e);
      alert('Error de red al limpiar filtros');
    }
  });

  // Restore on load
  const saved = load();
  if (saved) {
    setFormFromFilters(saved);
    fetchAndUpdate(saved);
  }

});
