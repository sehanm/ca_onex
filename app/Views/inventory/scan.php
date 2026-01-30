<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>QR Scanner<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header">
    <div style="display:flex; align-items:center; gap:15px;">
        <a href="<?= base_url('inventory') ?>" class="btn-icon-lite"><i class="fa-solid fa-arrow-left"></i></a>
        <h2>Asset Scanner</h2>
    </div>
</div>

<div class="card scan-card">
    <div id="reader" style="width: 100%; max-width: 600px; margin: 0 auto; border-radius: 16px; overflow: hidden;"></div>
    
    <div class="scan-instructions">
        <h3>Position QR Code inside the frame</h3>
        <p>The system will automatically recognize the asset and redirect you to its management page.</p>
    </div>
    
    <div style="text-align:center; margin-top: 20px;">
        <button id="toggleCamera" class="btn-secondary">Switch Camera</button>
    </div>
</div>

<style>
    .scan-card { padding: 30px; text-align: center; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .scan-instructions { margin-top: 25px; }
    .scan-instructions h3 { font-size: 1.2rem; color: #1e293b; margin-bottom: 8px; }
    .scan-instructions p { color: #64748b; font-size: 0.95rem; }
    
    #reader { border: 2px solid #f1f5f9 !important; background: #fafafa; }
    #reader__dashboard_section_csr button { background: var(--primary-color) !important; color: white !important; border: none !important; padding: 8px 16px !important; border-radius: 8px !important; cursor: pointer !important; }
</style>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    function onScanSuccess(decodedText, decodedResult) {
        // decodedText will be the URL like http://.../inventory/view/ID
        // We can directly redirect if it looks like our URL
        if (decodedText.includes('inventory/view/')) {
            showToast("Asset Recognized!", "success");
            window.location.href = decodedText;
        } else {
            Swal.fire({
                title: 'Invalid Code',
                text: 'The scanned code is not a recognized asset label.',
                icon: 'warning'
            });
        }
    }

    function onScanFailure(error) {
        // handle scan failure, usually better to ignore and keep scanning
    }

    let html5QrcodeScanner = new Html5QrcodeScanner(
        "reader", 
        { fps: 10, qrbox: {width: 250, height: 250} },
        /* verbose= */ false
    );
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>
<?= $this->endSection() ?>
