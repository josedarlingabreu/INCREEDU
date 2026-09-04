<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>INCREEDU — Registro de Solicitudes Estudiantiles</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/components.css">
  <script src="https://accounts.google.com/gsi/client" async defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>
  <section class="login-screen" id="loginScreen">
    <div class="login-card">
      <div class="logo-mark login-logo">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.7 1.3 3 3 3h6c1.7 0 3-1.3 3-3v-5"/></svg>
      </div>
      <h1>Bienvenido a INCREEDU</h1>
      <p>Inicia sesión para administrar las solicitudes estudiantiles.</p>
      <div id="googleButton"></div>
      <small id="googleStatus">Configura tu Client ID de Google para activar el acceso.</small>
    </div>
  </section>

  <div class="app" id="appShell" style="display:none">
    <aside class="sidebar">
      <div class="sidebar-top">
        <div class="logo-mark">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.7 1.3 3 3 3h6c1.7 0 3-1.3 3-3v-5"/></svg>
        </div>
        <nav class="side-nav">
          <a href="#" class="side-link active" title="Dashboard">
            <span class="link-bg"></span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/></svg>
          </a>
          <a href="#" class="side-link" title="Solicitudes" onclick="showSection('solicitudes', event)">
            <span class="link-bg"></span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
          </a>
          <a href="#" class="side-link" title="Estudiantes" onclick="showSection('estudiantes', event)">
            <span class="link-bg"></span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </a>
          <a href="#" class="side-link" title="Reportes" onclick="exportPDF(); event.preventDefault()">
            <span class="link-bg"></span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
          </a>
          <a href="#" class="side-link" title="Mensajes" onclick="showNotifications(event)">
            <span class="link-bg"></span>
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
          </a>
        </nav>
      </div>
      <div class="sidebar-bottom">
        <a href="#" class="side-link" title="Configuración" onclick="openSettings(event)">
          <span class="link-bg"></span>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 5 15.4a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
        </a>
        <a href="#" class="side-link" title="Salir" onclick="logout(event)">
          <span class="link-bg"></span>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        </a>
      </div>
    </aside>

    <main class="main">
      <header class="topbar">
        <div class="topbar-left">
          <div class="brand">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 1.7 1.3 3 3 3h6c1.7 0 3-1.3 3-3v-5"/></svg>
            <span>INCREEDU</span>
          </div>
          <nav class="top-nav">
            <a href="#" class="top-link active" onclick="showSection('dashboard', event)">Dashboard</a>
            <a href="#" class="top-link" onclick="showSection('solicitudes', event)">Solicitudes</a>
            <a href="#" class="top-link" onclick="exportPDF(); event.preventDefault()">Reportes</a>
          </nav>
        </div>
        <div class="topbar-right">
          <button class="notif-btn" onclick="showNotifications(event)" title="Notificaciones">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <span class="notif-dot"></span>
          </button>
          <button class="user-pill" onclick="toggleUserMenu(event)" title="Abrir menú de usuario">
            <img src="https://ui-avatars.com/api/?name=Admin+User&background=16a34a&color=fff&size=64" alt="Admin" class="user-img">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-muted)"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
        </div>
      </header>

      <div class="content">
        <div class="title-row">
          <h1 class="page-title">Bienvenido de nuevo, <span>Admin</span></h1>
          <div class="title-actions">
            <div class="date-pill">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              <span id="dateRange">1 Sep, 2026 — 30 Sep, 2026</span>
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-muted)"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <button class="btn-primary" onclick="openModal()">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
              Nueva solicitud
            </button>
          </div>
        </div>

        <div class="cards-grid">
          <div class="card card-highlight">
            <div class="card-header">
              <div>
                <div class="card-title">Solicitudes del mes</div>
                <div class="card-sub">Total acumulado</div>
              </div>
              <button class="icon-btn-soft" onclick="exportPDF()" title="Exportar reporte PDF">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
              </button>
            </div>
            <div class="big-number" id="stat-total">0</div>
            <div class="trend-pill up">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
              +12.8%
            </div>
          </div>

          <div class="card card-chart">
            <div class="card-header">
              <div class="card-title-row">
                <div class="icon-circle">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 1 1 8 2.83"/><path d="M22 12A10 10 0 0 0 12 2v10z"/></svg>
                </div>
                <div class="card-title">Tasa de resolución</div>
              </div>
              <div class="chart-tabs">
                <button class="chart-tab" onclick="setChartMode(this, 'mensual')">Mensual</button>
                <button class="chart-tab active" onclick="setChartMode(this, 'anual')">Anual</button>
              </div>
              <button class="icon-btn-soft" onclick="exportPDF()" title="Exportar reporte PDF">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
              </button>
            </div>
            <div class="bar-chart">
              <div id="resolutionChart" class="bar-chart-data"></div>
            </div>
          </div>

          <div class="card card-balance">
            <div class="card-header">
              <div>
                <div class="card-title">Balance de solicitudes</div>
                <div class="card-sub">Total procesadas</div>
              </div>
              <button class="icon-btn-soft" onclick="exportPDF()" title="Exportar reporte PDF">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
              </button>
            </div>
            <div class="balance-label">Total resueltas</div>
            <div class="balance-value" id="stat-resueltas">0</div>
            <div class="area-chart">
              <svg viewBox="0 0 200 60" preserveAspectRatio="none">
                <defs>
                  <linearGradient id="areaGrad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#16a34a" stop-opacity="0.25"/>
                    <stop offset="100%" stop-color="#16a34a" stop-opacity="0"/>
                  </linearGradient>
                </defs>
                <path d="M0,45 Q20,35 40,38 T80,30 T120,25 T160,32 T200,15 V60 H0 Z" fill="url(#areaGrad)"/>
                <path d="M0,45 Q20,35 40,38 T80,30 T120,25 T160,32 T200,15" fill="none" stroke="#16a34a" stroke-width="2"/>
              </svg>
            </div>
            <div class="balance-actions">
              <button class="btn-soft" onclick="showMessage('El resumen de solicitudes está listo para compartir.')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                Enviar
              </button>
              <button class="btn-soft" style="color:var(--text-muted)" onclick="showMessage('No hay solicitudes pendientes de recepción.')">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/></svg>
                Recibir
              </button>
            </div>
          </div>

        </div>

        <div class="bottom-grid">
          <div class="card card-table">
            <div class="table-header">
              <div>
                <div class="card-title">Historial de solicitudes</div>
                <div class="card-sub">Solicitudes recientes de estudiantes</div>
              </div>
              <div class="table-filters">
                <div class="search-box">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                  <input type="text" id="searchInput" placeholder="Buscar estudiante..." oninput="renderTable()">
                </div>
                <div class="filter-chips">
                  <button class="filter-chip active" data-filter="todos" onclick="setFilter(this)">Todos</button>
                  <button class="filter-chip" data-filter="pendiente" onclick="setFilter(this)">Pendientes</button>
                  <button class="filter-chip" data-filter="aprobada" onclick="setFilter(this)">Aprobadas</button>
                  <button class="filter-chip" data-filter="rechazada" onclick="setFilter(this)">Rechazadas</button>
                </div>
              </div>
            </div>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>Estudiante</th>
                    <th>Matrícula</th>
                    <th>Tipo de solicitud</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th style="width:90px">Acciones</th>
                  </tr>
                </thead>
                <tbody id="tableBody"></tbody>
              </table>
              <div id="emptyState" class="empty-state" style="display:none;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-muted);margin-bottom:12px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                <p>No se encontraron solicitudes.</p>
              </div>
            </div>
          </div>

          <div class="card card-side">
            <div class="card-header" style="margin-bottom:16px">
              <div>
                <div class="card-title">Solicitudes recientes</div>
                <div class="card-sub">Últimas 5 solicitudes</div>
              </div>
              <button class="icon-btn-soft" onclick="exportPDF()" title="Exportar reporte PDF">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
              </button>
            </div>
            <div class="recent-list" id="recentList"></div>
            <div class="avatar-stack" style="margin-top:20px">
              <div class="avatar-stack-label">Coordinadores activos</div>
              <div class="avatar-group">
                <img src="https://ui-avatars.com/api/?name=Carlos+R&background=f59e0b&color=fff&size=64" class="stack-avatar">
                <img src="https://ui-avatars.com/api/?name=Ana+L&background=3b82f6&color=fff&size=64" class="stack-avatar">
                <img src="https://ui-avatars.com/api/?name=Luis+H&background=ef4444&color=fff&size=64" class="stack-avatar">
                <img src="https://ui-avatars.com/api/?name=Sofia+M&background=8b5cf6&color=fff&size=64" class="stack-avatar">
                <div class="stack-more">+2</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <div class="quick-panel" id="quickPanel" onclick="if(event.target===this)closeQuickPanel()">
    <div class="quick-panel-card">
      <div class="quick-panel-header"><h2 id="quickPanelTitle">Notificaciones</h2><button class="modal-close" onclick="closeQuickPanel()">&times;</button></div>
      <div id="quickPanelContent"></div>
    </div>
  </div>

  <div class="settings-panel" id="settingsPanel" onclick="if(event.target===this)closeSettings()">
    <div class="settings-card">
      <div class="quick-panel-header"><h2>Configuración</h2><button class="modal-close" onclick="closeSettings()">&times;</button></div>
      <p class="settings-note">Personaliza la apariencia de tu panel.</p>
      <label class="settings-label" for="accentColor">Color principal</label>
      <div class="color-options" id="accentColor">
        <button class="color-swatch green" data-color="#16a34a" onclick="setAccent(this.dataset.color)" title="Verde"></button>
        <button class="color-swatch blue" data-color="#2563eb" onclick="setAccent(this.dataset.color)" title="Azul"></button>
        <button class="color-swatch orange" data-color="#ea580c" onclick="setAccent(this.dataset.color)" title="Naranja"></button>
        <button class="color-swatch rose" data-color="#e11d48" onclick="setAccent(this.dataset.color)" title="Rosa"></button>
      </div>
      <label class="settings-toggle"><span>Modo oscuro</span><input type="checkbox" id="darkModeToggle" onchange="toggleDarkMode(this.checked)"><span class="toggle-track"></span></label>
      <button class="btn-primary settings-save" onclick="closeSettings()">Listo</button>
    </div>
  </div>

  <div class="user-menu" id="userMenu">
    <strong>Admin User</strong><span>Administrador</span><button onclick="openSettings(event)">Configuración</button><button onclick="logout(event)">Cerrar sesión</button>
  </div>

  <div class="modal-overlay" id="modalOverlay" onclick="if(event.target===this)closeModal()">
    <div class="modal">
      <div class="modal-header">
        <h2 id="modalTitle">Nueva solicitud</h2>
        <button class="modal-close" onclick="closeModal()">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <form id="solicitudForm" onsubmit="saveSolicitud(event)">
        <input type="hidden" id="editId">
        <div class="form-group">
          <label>Nombre completo del estudiante</label>
          <input type="text" id="nombre" required placeholder="Ej: María González Pérez">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Matrícula</label>
            <input type="text" id="matricula" required placeholder="Ej: 2024-00123">
          </div>
          <div class="form-group">
            <label>Carrera</label>
            <input type="text" id="carrera" required placeholder="Ej: Ingeniería en Sistemas">
          </div>
        </div>
        <div class="form-group">
          <label>Correo electrónico</label>
          <input type="email" id="email" required placeholder="Ej: maria.gonzalez@universidad.edu">
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Tipo de solicitud</label>
            <select id="tipo" required>
              <option value="">Seleccionar...</option>
              <option value="Constancia de estudios">Constancia de estudios</option>
              <option value="Cambio de carrera">Cambio de carrera</option>
              <option value="Baja temporal">Baja temporal</option>
              <option value="Reinscripción">Reinscripción</option>
              <option value="Examen extraordinario">Examen extraordinario</option>
              <option value="Equivalencia de materias">Equivalencia de materias</option>
              <option value="Constancia de servicio social">Constancia de servicio social</option>
              <option value="Otro">Otro</option>
            </select>
          </div>
          <div class="form-group">
            <label>Estado</label>
            <select id="estado" required>
              <option value="pendiente">Pendiente</option>
              <option value="aprobada">Aprobada</option>
              <option value="rechazada">Rechazada</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Descripción / Motivo</label>
          <textarea id="descripcion" placeholder="Describe brevemente el motivo de la solicitud..."></textarea>
        </div>
        <div class="form-actions">
          <button type="button" class="btn" onclick="closeModal()">Cancelar</button>
          <button type="submit" class="btn-primary">Guardar solicitud</button>
        </div>
      </form>
    </div>
  </div>

  <script src="js/app.js"></script>
</body>
</html>