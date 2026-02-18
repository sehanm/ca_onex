<?= $this->extend('layouts/main') ?>
<?= $this->section('title') ?>Request Fulfillment Console<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="scanner-console-wrapper">
    <!-- Header Area -->
    <div class="console-header">
        <div class="console-brand">
            <div class="brand-glyph">
                <i class="fa-solid fa-qrcode"></i>
            </div>
            <div class="brand-label">
                <h2>Fulfillment <span>Console</span></h2>
                <p>Unified Assign & Return System</p>
            </div>
        </div>
        <div class="console-stats">
            <div class="stat-pill pending">
                <span class="pill-label">PENDING</span>
                <span class="pill-value" id="countPending">0</span>
            </div>
            <div class="stat-pill active">
                <span class="pill-label">ACTIVE</span>
                <span class="pill-value" id="countActive">0</span>
            </div>
            <a href="<?= base_url('hardware-requests/manage') ?>" class="btn-exit-console">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </a>
        </div>
    </div>

    <div class="console-main-grid">
        <!-- LEFT: Request Hub -->
        <div class="console-hub shadow-premium">
            <div class="hub-header">
                <div class="hub-tabs">
                    <button class="hub-tab active" data-target="pendingRequests" onclick="switchHubTab('pending')">
                        <i class="fa-solid fa-clock-rotate-left"></i> To Assign
                    </button>
                    <button class="hub-tab" data-target="assignedRequests" onclick="switchHubTab('assigned')">
                        <i class="fa-solid fa-user-check"></i> To Return
                    </button>
                </div>
                <div class="hub-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="hubSearchInput" placeholder="Search requests..."
                        oninput="filterHubRequests()">
                </div>
            </div>

            <div class="hub-content-scroll" id="requestListContainer">
                <!-- Content loaded via AJAX -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary opacity-50"></div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Action Center -->
        <div class="console-actions">
            <!-- Scan Input Bar (Crucial) -->
            <div class="action-input-bar card shadow-premium">
                <div class="scan-glow-border"></div>
                <div class="input-wrapper">
                    <i class="fa-solid fa-barcode"></i>
                    <input type="text" id="scanInput" placeholder="SCAN HARDWARE OR ENTER CODE..." autofocus
                        autocomplete="off">
                    <div class="keyboard-indicator">
                        <i class="fa-solid fa-keyboard"></i>
                    </div>
                </div>
                <div id="scanStatus" class="scan-status-indicator">
                    <span class="dot"></span>
                    <span class="text">Listening</span>
                </div>
            </div>

            <!-- Contextual Result Area -->
            <div id="resultArea" class="action-result-area">
                <div class="empty-state">
                    <div class="pulsing-circle">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <h4>Awaiting Hardware Scan</h4>
                    <p>Identify an item to begin fulfillment or return process</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --c-primary: #6366f1;
        --c-success: #10b981;
        --c-danger: #ef4444;
        --c-warning: #f59e0b;
        --c-dark: #0f172a;
        --c-slate: #64748b;
        --c-border: #e2e8f0;
        --c-bg-light: #f8fafc;
    }

    .scanner-console-wrapper {
        height: calc(100vh - 120px);
        display: flex;
        flex-direction: column;
        gap: 20px;
        color: var(--c-dark);
    }

    /* Header */
    .console-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 5px;
    }

    .console-brand {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .brand-glyph {
        width: 48px;
        height: 48px;
        background: var(--c-primary);
        color: white;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
    }

    .brand-label h2 {
        margin: 0;
        font-size: 1.3rem;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .brand-label h2 span {
        color: var(--c-primary);
    }

    .brand-label p {
        margin: 0;
        font-size: 0.75rem;
        color: var(--c-slate);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .console-stats {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .stat-pill {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 16px;
        background: white;
        border-radius: 100px;
        border: 1px solid var(--c-border);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.02);
    }

    .pill-label {
        font-size: 0.65rem;
        font-weight: 800;
        color: var(--c-slate);
    }

    .pill-value {
        font-size: 0.9rem;
        font-weight: 800;
    }

    .stat-pill.pending .pill-value {
        color: var(--c-warning);
    }

    .stat-pill.active .pill-value {
        color: var(--c-success);
    }

    .btn-exit-console {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--c-slate);
        transition: 0.3s;
        text-decoration: none;
    }

    .btn-exit-console:hover {
        background: var(--c-danger);
        color: white;
        transform: rotate(-90deg);
    }

    /* Main Grid */
    .console-main-grid {
        display: grid;
        grid-template-columns: 400px 1fr;
        gap: 20px;
        flex-grow: 1;
        min-height: 0;
        /* Important for flex-grow with inner scrolling */
    }

    /* Hub (Left Column) */
    .console-hub {
        background: white;
        border-radius: 24px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .hub-header {
        padding: 20px;
        background: var(--c-bg-light);
        border-bottom: 1px solid var(--c-border);
    }

    .hub-tabs {
        display: flex;
        background: #e2e8f0;
        padding: 4px;
        border-radius: 12px;
        margin-bottom: 15px;
    }

    .hub-tab {
        flex: 1;
        padding: 10px;
        border: none;
        background: transparent;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--c-slate);
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .hub-tab.active {
        background: white;
        color: var(--c-primary);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .hub-search {
        position: relative;
    }

    .hub-search i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--c-slate);
        font-size: 0.9rem;
    }

    .hub-search input {
        width: 100%;
        padding: 12px 15px 12px 42px;
        border-radius: 12px;
        border: 1px solid var(--c-border);
        background: white;
        font-size: 0.85rem;
        outline: none;
        transition: 0.2s;
    }

    .hub-search input:focus {
        border-color: var(--c-primary);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .hub-content-scroll {
        flex-grow: 1;
        overflow-y: auto;
        padding: 15px;
    }

    .hub-content-scroll::-webkit-scrollbar {
        width: 5px;
    }

    .hub-content-scroll::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }

    /* Request Cards */
    .request-item {
        padding: 15px;
        border-radius: 18px;
        border: 1px solid var(--c-border);
        margin-bottom: 12px;
        cursor: pointer;
        transition: 0.2s;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        background: white;
    }

    .request-item:hover {
        border-color: var(--c-primary);
        background: #f8fafc;
        transform: translateX(5px);
    }

    .request-item.selected {
        border-color: var(--c-primary);
        background: #f5f3ff;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
    }

    .req-avatar {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #f1f5f9;
        color: var(--c-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .request-item.selected .req-avatar {
        background: var(--c-primary);
        color: white;
    }

    .req-meta {
        flex-grow: 1;
    }

    .req-user {
        display: block;
        font-weight: 800;
        font-size: 0.9rem;
        margin-bottom: 2px;
    }

    .req-type {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.7rem;
        font-weight: 700;
        color: var(--c-slate);
        text-transform: uppercase;
    }

    .req-category {
        color: var(--c-primary);
    }

    .req-badges {
        display: flex;
        gap: 5px;
        margin-top: 8px;
    }

    .req-badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.6rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .badge-info {
        background: #eef2ff;
        color: #4f46e5;
    }

    .badge-time {
        background: #f1f5f9;
        color: #64748b;
    }

    /* Right Column (Actions) */
    .console-actions {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .action-input-bar {
        padding: 10px;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        border: none;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .scan-glow-border {
        position: absolute;
        inset: -2px;
        background: linear-gradient(90deg, #6366f1, #10b981, #6366f1);
        background-size: 200% 100%;
        animation: gradient-flow 3s linear infinite;
        opacity: 0.3;
        z-index: 1;
    }

    @keyframes gradient-flow {
        0% {
            background-position: 0% 50%;
        }

        100% {
            background-position: 200% 50%;
        }
    }

    .input-wrapper {
        flex-grow: 1;
        background: white;
        border-radius: 15px;
        z-index: 2;
        padding: 5px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .input-wrapper i {
        color: var(--c-primary);
        font-size: 1.2rem;
    }

    .input-wrapper input {
        border: none;
        background: transparent;
        padding: 15px 0;
        width: 100%;
        font-weight: 800;
        font-size: 1.1rem;
        color: var(--c-dark);
        letter-spacing: 1px;
        outline: none;
    }

    .keyboard-indicator {
        color: var(--c-border);
        font-size: 0.9rem;
    }

    .scan-status-indicator {
        z-index: 2;
        padding-right: 20px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .scan-status-indicator .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--c-success);
        box-shadow: 0 0 10px var(--c-success);
        animation: blink 1.5s infinite;
    }

    .scan-status-indicator .text {
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--c-slate);
        letter-spacing: 0.5px;
    }

    @keyframes blink {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.3;
        }
    }

    /* Result Area */
    .action-result-area {
        flex-grow: 1;
        background: white;
        border-radius: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed var(--c-border);
        position: relative;
    }

    .empty-state {
        text-align: center;
    }

    .pulsing-circle {
        width: 100px;
        height: 100px;
        background: var(--c-bg-light);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        color: var(--c-slate);
        font-size: 2.5rem;
        position: relative;
    }

    .pulsing-circle::after {
        content: '';
        position: absolute;
        inset: -10px;
        border: 2px solid var(--c-primary);
        border-radius: 50%;
        animation: circle-pulse 2s infinite;
        opacity: 0;
    }

    @keyframes circle-pulse {
        0% {
            transform: scale(0.8);
            opacity: 0.5;
        }

        100% {
            transform: scale(1.4);
            opacity: 0;
        }
    }

    .empty-state h4 {
        font-weight: 800;
        margin-bottom: 8px;
        color: var(--c-dark);
    }

    .empty-state p {
        font-size: 0.9rem;
        color: var(--c-slate);
        max-width: 300px;
    }

    /* Result Card Premium */
    .result-card-premium {
        width: 100%;
        height: 100%;
        padding: 40px;
        display: flex;
        flex-direction: column;
        animation: slide-up-fade 0.4s ease-out;
    }

    @keyframes slide-up-fade {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .res-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 35px;
    }

    .res-identity {
        display: flex;
        gap: 20px;
    }

    .res-icon-box {
        width: 70px;
        height: 70px;
        background: var(--c-bg-light);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: var(--c-primary);
    }

    .res-title h3 {
        margin: 0;
        font-weight: 800;
        font-size: 1.5rem;
    }

    .res-title p {
        margin: 5px 0 0;
        color: var(--c-slate);
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.9rem;
    }

    .res-body {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        flex-grow: 1;
    }

    .res-data-group label {
        display: block;
        font-size: 0.7rem;
        font-weight: 800;
        color: var(--c-slate);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .res-data-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--c-dark);
        background: var(--c-bg-light);
        padding: 12px 20px;
        border-radius: 12px;
    }

    .res-footer {
        margin-top: 40px;
    }

    .btn-action-large {
        width: 100%;
        padding: 22px;
        border-radius: 18px;
        border: none;
        font-weight: 800;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-assign {
        background: var(--c-dark);
        color: white;
    }

    .btn-assign:hover {
        background: var(--c-primary);
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(99, 102, 241, 0.2);
    }

    .btn-return {
        background: #fee2e2;
        color: var(--c-danger);
        border: 2px solid #fecaca;
    }

    .btn-return:hover {
        background: var(--c-danger);
        color: white;
        transform: translateY(-3px);
    }

    .d-none {
        display: none !important;
    }

    /* Selection Highlighting */
    .matching-category {
        border-color: var(--c-success) !important;
        background: #ecfdf5 !important;
    }
</style>

<script>
    let currentHubTab = 'pending';
    let allRequests = { pending: [], assigned: [] };
    let selectedRequestId = null;
    let scannedItem = null;
    let scannedType = null;

    document.addEventListener('DOMContentLoaded', () => {
        fetchScannerData();

        const input = document.getElementById('scanInput');
        input.focus();

        // Keep focus on scan input unless typing in search
        document.addEventListener('click', (e) => {
            if (!e.target.closest('input, button, .request-item')) {
                input.focus();
            }
        });

        let scanTimeout;
        input.addEventListener('input', (e) => {
            clearTimeout(scanTimeout);
            const val = e.target.value.trim();
            if (val.length > 2) {
                scanTimeout = setTimeout(() => handleHardwareScan(val), 400);
            }
        });
    });

    function fetchScannerData() {
        fetch('<?= base_url('hardware-requests/scanner-data') ?>')
            .then(res => res.json())
            .then(data => {
                allRequests = data;
                document.getElementById('countPending').innerText = data.pending.length;
                document.getElementById('countActive').innerText = data.assigned.length;
                renderHub();
            });
    }

    function renderHub() {
        const container = document.getElementById('requestListContainer');
        const list = allRequests[currentHubTab];
        const search = document.getElementById('hubSearchInput').value.toLowerCase();

        container.innerHTML = '';

        const filtered = list.filter(r =>
            r.requester_name.toLowerCase().includes(search) ||
            r.category.toLowerCase().includes(search) ||
            (r.item && r.item.asset_code && r.item.asset_code.toLowerCase().includes(search))
        );

        if (filtered.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 opacity-50">
                    <i class="fa-solid fa-inbox fa-3x mb-3"></i>
                    <p class="small fw-bold">No ${currentHubTab} requests found</p>
                </div>`;
            return;
        }

        filtered.forEach(req => {
            const isSelected = selectedRequestId == req.id;
            container.innerHTML += `
                <div class="request-item ${isSelected ? 'selected' : ''}" 
                     id="req-${req.id}" 
                     onclick="selectRequest('${req.id}')"
                     data-category="${req.category.toLowerCase()}">
                    <div class="req-avatar">${req.requester_name.charAt(0)}</div>
                    <div class="req-meta">
                        <span class="req-user">${req.requester_name}</span>
                        <div class="req-type">
                            <i class="fa-solid ${req.item_type === 'asset' ? 'fa-laptop' : 'fa-plug'}"></i>
                            <span class="req-category">${req.category}</span>
                        </div>
                        <div class="req-badges">
                            <span class="req-badge badge-info">${req.requirement_type}</span>
                            ${req.requirement_type === 'temporary' && req.due_date ?
                    `<span class="req-badge" style="background:#fff7ed; color:#c2410c;">
                                    <i class="fa-solid fa-calendar-day me-1"></i>Due: ${new Date(req.due_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}
                                </span>` : ''
                }
                            <span class="req-badge badge-time">${req.time_ago}</span>
                        </div>
                        ${req.item ? `
                        <div class="req-badges mt-1">
                            <span class="req-badge" style="background:#e0f2fe; color:#0369a1; font-family:monospace">
                                <i class="fa-solid fa-barcode me-1"></i>${req.item.asset_code}
                            </span>
                        </div>` : ''}
                    </div>
                </div>
            `;
        });
    }

    function switchHubTab(tab) {
        currentHubTab = tab;
        document.querySelectorAll('.hub-tab').forEach(b => b.classList.toggle('active', b.dataset.target === tab + 'Requests'));
        renderHub();
    }

    function filterHubRequests() {
        renderHub();
    }

    function selectRequest(id) {
        selectedRequestId = id;
        renderHub();
        // If we already have a scanned item, maybe we can highlight if they match
        evaluateMatch();
    }

    function handleHardwareScan(code) {
        updateScanStatus('Processing', 'primary');
        fetch(`<?= base_url('hardware-requests/item-details') ?>?code=${encodeURIComponent(code)}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('scanInput').value = '';
                if (data.status === 'success') {
                    scannedItem = data.item;
                    scannedType = data.itemType;
                    displayScanResult(data.item, data.itemType);
                    updateScanStatus('Matched', 'success');
                    setTimeout(() => updateScanStatus('Listening', 'success'), 2000);
                } else {
                    updateScanStatus('Not Found', 'danger');
                    setTimeout(() => updateScanStatus('Listening', 'success'), 2000);
                }
            })
            .catch(err => {
                updateScanStatus('Error', 'danger');
            });
    }

    function updateScanStatus(text, type) {
        const span = document.getElementById('scanStatus');
        const dot = span.querySelector('.dot');
        const label = span.querySelector('.text');

        label.innerText = text;
        if (type === 'danger') {
            dot.style.background = 'var(--c-danger)';
            dot.style.boxShadow = '0 0 10px var(--c-danger)';
            label.style.color = 'var(--c-danger)';
        } else if (type === 'primary') {
            dot.style.background = 'var(--c-primary)';
            dot.style.boxShadow = '0 0 10px var(--c-primary)';
            label.style.color = 'var(--c-primary)';
        } else {
            dot.style.background = 'var(--c-success)';
            dot.style.boxShadow = '0 0 10px var(--c-success)';
            label.style.color = 'var(--c-slate)';
        }
    }

    function displayScanResult(item, type) {
        const area = document.getElementById('resultArea');
        area.innerHTML = `
            <div class="result-card-premium">
                <div class="res-header">
                    <div class="res-identity">
                        <div class="res-icon-box">
                            <i class="fa-solid ${type === 'asset' ? 'fa-laptop' : 'fa-plug'}"></i>
                        </div>
                        <div class="res-title">
                            <h3>${(item.brand || '')} ${item.model || item.category}</h3>
                            <p>${item.asset_code}</p>
                        </div>
                    </div>
                    <button class="btn-exit-console" style="background:#f1f5f9; width:36px; height:36px;" onclick="resetConsole()">
                        <i class="fa-solid fa-rotate-left"></i>
                    </button>
                </div>

                <div class="res-body">
                    <div class="res-data-group">
                        <label>Item Status</label>
                        <div class="res-data-value" style="color:${item.status === 'Assigned' ? 'var(--c-danger)' : 'var(--c-success)'}">
                            ${item.status}
                        </div>
                    </div>
                    <div class="res-data-group">
                        <label>Serial / Reference</label>
                        <div class="res-data-value">${item.serial_number || 'N/A'}</div>
                    </div>
                    <div class="res-data-group">
                        <label>Custodian / Location</label>
                        <div class="res-data-value">${item.assigned_to_name || 'In Store'}</div>
                    </div>
                    <div class="res-data-group">
                        <label>Hardware Class</label>
                        <div class="res-data-value" style="text-transform:capitalize">${type}</div>
                    </div>
                </div>

                <div class="res-footer" id="actionButtonContainer">
                    <!-- Action button injected depending on status -->
                </div>
            </div>
        `;

        const btnContainer = document.getElementById('actionButtonContainer');
        if (item.status.toLowerCase() === 'assigned') {
            // Suggest Return
            btnContainer.innerHTML = `
                <button class="btn-action-large btn-return" onclick="executeAction('return')">
                    <i class="fa-solid fa-rotate-left"></i> Confirm Return to Stock
                </button>
                <p class="text-center small text-muted mt-3">Scanning an assigned item defaults to Return mode.</p>
            `;
            // Auto switch tab to assigned to show matching request if possible
            switchHubTab('assigned');
        } else {
            // Suggest Assign
            btnContainer.innerHTML = `
                <button class="btn-action-large btn-assign" id="btnFulfill" onclick="executeAction('assign')">
                    <i class="fa-solid fa-check-circle"></i> Fulfill Selected Request
                </button>
                <div id="selectionAlert" class="text-center small mt-3 ${selectedRequestId ? 'd-none' : ''}">
                    <i class="fa-solid fa-circle-info text-warning"></i> Please select a request from the left list.
                </div>
            `;
            switchHubTab('pending');
        }

        evaluateMatch();
    }

    function evaluateMatch() {
        if (!scannedItem) return;

        const items = document.querySelectorAll('.request-item');
        items.forEach(el => {
            el.classList.remove('matching-category');
            if (el.dataset.category === scannedItem.category.toLowerCase()) {
                el.classList.add('matching-category');
            }
        });

        // Toggle alert if fulfill button exists
        const alert = document.getElementById('selectionAlert');
        if (alert) {
            alert.classList.toggle('d-none', selectedRequestId !== null);
        }
    }

    function executeAction(action) {
        const payload = { qr_data: scannedItem.asset_code, action: action };

        if (action === 'assign') {
            if (!selectedRequestId) {
                showToast('Please select a request to fulfill.', 'warning');
                return;
            }
            // Verify if selected request matches item type? (Optional)
            payload.request_id = selectedRequestId;
        }

        // Add loading state to button
        const btn = document.querySelector('.btn-action-large');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div> Processing...';

        fetch('<?= base_url('hardware-requests/process-scan') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
            body: new URLSearchParams(payload)
        })
            .then(res => res.json())
            .then(res => {
                if (res.status === 'success') {
                    resetConsole();
                    fetchScannerData();
                    showToast(res.message, 'success');
                } else {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                    showToast(res.message, 'error');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                showToast('Network error occurred.', 'error');
            });
    }

    function resetConsole() {
        scannedItem = null;
        scannedType = null;
        selectedRequestId = null;
        document.getElementById('resultArea').innerHTML = `
            <div class="empty-state">
                <div class="pulsing-circle">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <h4>Awaiting Hardware Scan</h4>
                <p>Identify an item to begin fulfillment or return process</p>
            </div>
        `;
        renderHub();
        document.getElementById('scanInput').focus();
    }
</script>
<?= $this->endSection() ?>