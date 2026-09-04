let solicitudes = [];

let currentFilter = 'todos';
let editingId = null;
let chartMode = 'anual';
const GOOGLE_CLIENT_ID = '342172921145-rmrooml1gbo344drmrjpmrc4iknea7vo.apps.googleusercontent.com';

function avatarUrl(name) {
  const colors = ['16a34a','3b82f6','f59e0b','ef4444','8b5cf6','06b6d4','f97316'];
  const hash = name.split('').reduce((a,c)=>a+c.charCodeAt(0),0);
  const bg = colors[hash % colors.length];
  return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=${bg}&color=fff&size=64`;
}

function fallbackUserAvatar(name) {
  const initials = String(name || 'Admin User').trim().split(/\s+/).slice(0, 2).map(part => part[0]).join('').toUpperCase();
  const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"><rect width="64" height="64" fill="#16a34a"/><text x="32" y="39" text-anchor="middle" font-family="Arial" font-size="23" font-weight="bold" fill="white">${initials}</text></svg>`;
  return `data:image/svg+xml;charset=UTF-8,${encodeURIComponent(svg)}`;
}

function normalize(str) {
  return str.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
}

function updateStats() {
  const total = solicitudes.length;
  const aprobadas = solicitudes.filter(s => s.estado === 'aprobada').length;
  const rechazadas = solicitudes.filter(s => s.estado === 'rechazada').length;
  const resueltas = aprobadas + rechazadas;

  document.getElementById('stat-total').textContent = total;
  document.getElementById('stat-resueltas').textContent = resueltas;
  renderResolutionChart();
}

function renderResolutionChart() {
  const chart = document.getElementById('resolutionChart');
  if (!chart) return;
  const now = new Date();
  const months = Array.from({length: 6}, (_, index) => {
    const date = new Date(now.getFullYear(), now.getMonth() - (5 - index), 1);
    return {date, label: date.toLocaleDateString('es-ES', {month: 'short'}).replace('.', '')};
  });
  const bars = months.map(month => {
    const records = solicitudes.filter(s => {
      const date = new Date(`${s.fecha}T00:00:00`);
      return date.getFullYear() === month.date.getFullYear() && date.getMonth() === month.date.getMonth();
    });
    const resolved = records.filter(s => s.estado === 'aprobada' || s.estado === 'rechazada').length;
    const percentage = records.length ? Math.round((resolved / records.length) * 100) : 0;
    return {label: month.label, percentage};
  });
  chart.innerHTML = bars.map(bar => `
    <div class="bar-wrap">
      <div class="bar ${bar.percentage >= 70 ? 'active' : ''}" style="--bar-height:4%" data-height="${Math.max(bar.percentage, 4)}">
        <div class="bar-badge">${bar.percentage}%</div>
      </div>
      <span>${bar.label}</span>
    </div>
  `).join('');
  requestAnimationFrame(() => chart.querySelectorAll('.bar').forEach(bar => {
    bar.style.setProperty('--bar-height', `${bar.dataset.height}%`);
  }));
}

function renderTable() {
  const tbody = document.getElementById('tableBody');
  const search = normalize(document.getElementById('searchInput').value);

  const filtered = solicitudes.filter(s => {
    const matchesFilter = currentFilter === 'todos' || s.estado === currentFilter;
    const text = normalize(s.nombre + ' ' + s.matricula + ' ' + s.tipo + ' ' + s.carrera);
    return matchesFilter && text.includes(search);
  });

  document.getElementById('emptyState').style.display = filtered.length ? 'none' : 'flex';

  tbody.innerHTML = filtered.map(s => `
    <tr>
      <td>
        <div class="student-cell">
          <img src="${avatarUrl(s.nombre)}" alt="" class="student-avatar">
          <div class="student-info">
            <div class="name">${s.nombre}</div>
            <div class="meta">${s.carrera}</div>
          </div>
        </div>
      </td>
      <td style="font-family:monospace;font-size:12px;color:var(--text-secondary)">${s.matricula}</td>
      <td style="font-size:13px">${s.tipo}</td>
      <td style="font-size:12px;color:var(--text-secondary)">${s.fecha}</td>
      <td><span class="status-dot status-${s.estado}">${s.estado.charAt(0).toUpperCase()+s.estado.slice(1)}</span></td>
      <td>
        <div class="row-actions">
          <button class="action-btn" onclick="editSolicitud(${s.id})" title="Editar">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
          </button>
          <button class="action-btn" onclick="deleteSolicitud(${s.id})" title="Eliminar">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
          </button>
        </div>
      </td>
    </tr>
  `).join('');
}

function renderRecent() {
  const recent = [...solicitudes].sort((a,b) => b.id - a.id).slice(0, 5);
  document.getElementById('recentList').innerHTML = recent.map(s => `
    <div class="recent-item">
      <img src="${avatarUrl(s.nombre)}" alt="" class="recent-avatar">
      <div class="recent-info">
        <div class="name">${s.nombre}</div>
        <div class="type">${s.tipo}</div>
      </div>
      <span class="recent-status ${s.estado}">${s.estado.charAt(0).toUpperCase()+s.estado.slice(1)}</span>
    </div>
  `).join('');
}

function setFilter(el) {
  document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('active'));
  el.classList.add('active');
  currentFilter = el.dataset.filter;
  renderTable();
}

function openModal() {
  editingId = null;
  document.getElementById('modalTitle').textContent = 'Nueva solicitud';
  document.getElementById('solicitudForm').reset();
  document.getElementById('editId').value = '';
  document.getElementById('modalOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  document.getElementById('modalOverlay').classList.remove('open');
  document.body.style.overflow = '';
}

async function saveSolicitud(e) {
  e.preventDefault();

  const data = {
    nombre: document.getElementById('nombre').value.trim(),
    matricula: document.getElementById('matricula').value.trim(),
    carrera: document.getElementById('carrera').value.trim(),
    email: document.getElementById('email').value.trim(),
    tipo: document.getElementById('tipo').value,
    estado: document.getElementById('estado').value,
    descripcion: document.getElementById('descripcion').value.trim()
  };

  if (editingId) data.id = editingId;

  try {
    const response = await fetch(editingId ? 'api/update.php' : 'api/create.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(data)
    });
    const result = await response.json();
    if (!response.ok || !result.success) throw new Error(result.message || 'No se pudo guardar la solicitud.');

    closeModal();
    await cargarSolicitudes();
  } catch (error) {
    alert(error.message || 'Error de conexión con el servidor.');
  }
}

function editSolicitud(id) {
  const s = solicitudes.find(x => x.id === id);
  if (!s) return;

  editingId = id;
  document.getElementById('modalTitle').textContent = 'Editar solicitud';
  document.getElementById('nombre').value = s.nombre;
  document.getElementById('matricula').value = s.matricula;
  document.getElementById('carrera').value = s.carrera;
  document.getElementById('email').value = s.email;
  document.getElementById('tipo').value = s.tipo;
  document.getElementById('estado').value = s.estado;
  document.getElementById('descripcion').value = s.descripcion || '';
  document.getElementById('modalOverlay').classList.add('open');
  document.body.style.overflow = 'hidden';
}

async function deleteSolicitud(id) {
  if (!confirm('¿Eliminar esta solicitud permanentemente?')) return;

  try {
    const response = await fetch('api/delete.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({id})
    });
    const result = await response.json();
    if (!response.ok || !result.success) throw new Error(result.message || 'No se pudo eliminar la solicitud.');

    await cargarSolicitudes();
  } catch (error) {
    alert(error.message || 'Error de conexión con el servidor.');
  }
}

async function cargarSolicitudes() {
  try {
    const response = await fetch('api/list.php');
    const result = await response.json();
    if (!response.ok || !result.success) throw new Error(result.message || 'No se pudieron cargar las solicitudes.');
    solicitudes = Array.isArray(result.data) ? result.data : result;
    updateStats();
    renderTable();
    renderRecent();
  } catch (error) {
    alert(error.message || 'Error de conexión con el servidor.');
  }
}

function focusSearch() {
  const searchInput = document.getElementById('searchInput');
  searchInput.focus();
  searchInput.scrollIntoView({behavior: 'smooth', block: 'center'});
}

function showSection(section, event) {
  event.preventDefault();
  document.querySelectorAll('.top-link, .side-link').forEach(link => link.classList.remove('active'));
  event.currentTarget.classList.add('active');
  if (section === 'solicitudes' || section === 'estudiantes') focusSearch();
  else window.scrollTo({top: 0, behavior: 'smooth'});
}

function showNotifications(event) {
  if (event) event.preventDefault();
  const pending = solicitudes.filter(s => s.estado === 'pendiente').length;
  document.getElementById('quickPanelTitle').textContent = 'Notificaciones';
  document.getElementById('quickPanelContent').innerHTML = pending
    ? `<div class="notification-item"><strong>${pending} solicitud${pending === 1 ? '' : 'es'} pendiente${pending === 1 ? '' : 's'}</strong>Requieren revisión del coordinador.</div><div class="notification-item"><strong>Panel actualizado</strong>Los datos se sincronizaron correctamente.</div>`
    : '<div class="notification-item"><strong>Todo al día</strong>No tienes notificaciones pendientes.</div>';
  document.getElementById('quickPanel').classList.add('open');
}

function closeQuickPanel() { document.getElementById('quickPanel').classList.remove('open'); }

function toggleUserMenu(event) {
  event.preventDefault();
  document.getElementById('userMenu').classList.toggle('open');
}

function openSettings(event) {
  if (event) event.preventDefault();
  document.getElementById('userMenu').classList.remove('open');
  document.getElementById('settingsPanel').classList.add('open');
}

function closeSettings() { document.getElementById('settingsPanel').classList.remove('open'); }

function setAccent(color) {
  document.documentElement.style.setProperty('--accent', color);
  document.documentElement.style.setProperty('--accent-hover', color);
  localStorage.setItem('edurequest-accent', color);
  document.querySelectorAll('.color-swatch').forEach(swatch => swatch.classList.toggle('selected', swatch.dataset.color === color));
}

function toggleDarkMode(enabled) {
  document.documentElement.dataset.theme = enabled ? 'dark' : '';
  localStorage.setItem('edurequest-dark', enabled ? '1' : '0');
}

function setChartMode(button, mode) {
  document.querySelectorAll('.chart-tab').forEach(tab => tab.classList.remove('active'));
  button.classList.add('active');
  chartMode = mode;
  renderResolutionChart();
}

function exportPDF() {
  if (!solicitudes.length) { showMessage('No hay solicitudes para generar el reporte.'); return; }
  if (!window.jspdf) { showMessage('No se pudo cargar el generador de PDF. Revisa tu conexión a Internet.'); return; }

  const {jsPDF} = window.jspdf;
  const pdf = new jsPDF({orientation: 'landscape', unit: 'mm', format: 'a4'});
  const pageWidth = pdf.internal.pageSize.getWidth();
  const aprobadas = solicitudes.filter(s => s.estado === 'aprobada').length;
  const pendientes = solicitudes.filter(s => s.estado === 'pendiente').length;
  const rechazadas = solicitudes.filter(s => s.estado === 'rechazada').length;
  const generatedAt = new Date().toLocaleString('es-MX');

  pdf.setFillColor(22, 163, 74);
  pdf.rect(0, 0, pageWidth, 24, 'F');
  pdf.setTextColor(255, 255, 255);
  pdf.setFontSize(20);
  pdf.setFont(undefined, 'bold');
  pdf.text('INCREEDU', 14, 15);
  pdf.setFontSize(10);
  pdf.setFont(undefined, 'normal');
  pdf.text('Reporte de solicitudes estudiantiles', pageWidth - 14, 15, {align: 'right'});

  pdf.setTextColor(15, 23, 42);
  pdf.setFontSize(11);
  pdf.setFont(undefined, 'normal');
  pdf.text(`Generado: ${generatedAt}`, 14, 34);
  pdf.text(`Total: ${solicitudes.length}    Aprobadas: ${aprobadas}    Pendientes: ${pendientes}    Rechazadas: ${rechazadas}`, 14, 41);

  const columns = [18, 48, 88, 128, 178, 238, 270];
  const headers = ['ID', 'Estudiante', 'Matrícula', 'Carrera', 'Tipo', 'Estado', 'Fecha'];
  const rowHeight = 8;
  let y = 53;
  const drawHeader = () => {
    pdf.setFillColor(226, 232, 240);
    pdf.rect(12, y - 6, pageWidth - 24, rowHeight, 'F');
    pdf.setTextColor(15, 23, 42);
    pdf.setFont(undefined, 'bold');
    pdf.setFontSize(9);
    headers.forEach((header, index) => pdf.text(header, columns[index], y));
    pdf.setFont(undefined, 'normal');
    y += rowHeight;
  };
  drawHeader();

  solicitudes.forEach((solicitud, index) => {
    if (y > 190) { pdf.addPage(); y = 20; drawHeader(); }
    if (index % 2 === 0) { pdf.setFillColor(248, 250, 252); pdf.rect(12, y - 6, pageWidth - 24, rowHeight, 'F'); }
    pdf.setTextColor(71, 85, 105);
    pdf.setFontSize(8);
    const values = [solicitud.id, solicitud.nombre, solicitud.matricula, solicitud.carrera, solicitud.tipo, solicitud.estado, solicitud.fecha];
    values.forEach((value, valueIndex) => {
      const text = pdf.splitTextToSize(String(value || ''), valueIndex === 1 ? 48 : 38)[0];
      pdf.text(text, columns[valueIndex], y);
    });
    y += rowHeight;
  });

  const pageCount = pdf.internal.getNumberOfPages();
  for (let page = 1; page <= pageCount; page += 1) {
    pdf.setPage(page);
    pdf.setFontSize(8);
    pdf.setTextColor(148, 163, 184);
    pdf.text(`INCREEDU | Página ${page} de ${pageCount}`, pageWidth - 14, 202, {align: 'right'});
  }
  pdf.save(`reporte-solicitudes-${new Date().toISOString().slice(0, 10)}.pdf`);
}

function showMessage(message) { alert(message); }

function initializeGoogleLogin() {
  if (!window.google || !google.accounts) {
    setTimeout(initializeGoogleLogin, 300);
    return;
  }
  if (GOOGLE_CLIENT_ID.startsWith('TU_CLIENT_ID')) return;
  google.accounts.id.initialize({client_id: GOOGLE_CLIENT_ID, callback: handleGoogleLogin});
  google.accounts.id.renderButton(document.getElementById('googleButton'), {theme: 'outline', size: 'large', width: 320, text: 'signin_with'});
  document.getElementById('googleStatus').textContent = 'Acceso seguro con Google.';
}

function handleGoogleLogin(response) {
  const payload = JSON.parse(atob(response.credential.split('.')[1].replace(/-/g, '+').replace(/_/g, '/')));
  localStorage.setItem('edurequest-user', JSON.stringify({name: payload.name, email: payload.email, picture: payload.picture}));
  showApp(payload);
}

function showApp(user) {
  document.getElementById('loginScreen').style.display = 'none';
  document.getElementById('appShell').style.display = 'flex';
  const userName = user && user.name ? user.name : 'Admin User';
  const userImage = document.querySelector('.user-img');
  userImage.onerror = () => {
    userImage.onerror = null;
    userImage.src = fallbackUserAvatar(userName);
  };
  userImage.src = user && user.picture ? user.picture : fallbackUserAvatar(userName);
  document.querySelector('.user-menu strong').textContent = userName;
}

function logout(event) {
  event.preventDefault();
  localStorage.removeItem('edurequest-user');
  document.getElementById('userMenu').classList.remove('open');
  document.getElementById('appShell').style.display = 'none';
  document.getElementById('loginScreen').style.display = 'grid';
}

document.addEventListener('DOMContentLoaded', () => {

  // Fecha dinámica en el header
  const hoy = new Date();
  const fin = new Date(hoy.getFullYear(), hoy.getMonth()+1, 0);
  const opts = { day:'numeric', month:'short', year:'numeric' };
  document.getElementById('dateRange').textContent =
    `${hoy.toLocaleDateString('es-ES',opts)} — ${fin.toLocaleDateString('es-ES',opts)}`;

  const savedAccent = localStorage.getItem('edurequest-accent');
  const savedDark = localStorage.getItem('edurequest-dark') === '1';
  if (savedAccent) setAccent(savedAccent);
  document.getElementById('darkModeToggle').checked = savedDark;
  toggleDarkMode(savedDark);

  const savedUser = localStorage.getItem('edurequest-user');
  if (savedUser) showApp(JSON.parse(savedUser));
  else initializeGoogleLogin();
  cargarSolicitudes();
});

// Cerrar con Escape
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') closeModal();
  if (e.key === 'Escape') { closeQuickPanel(); closeSettings(); document.getElementById('userMenu').classList.remove('open'); }
});
