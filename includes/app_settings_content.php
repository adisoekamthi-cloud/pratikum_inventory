<?php
$assetPrefix = $assetPrefix ?? '../';
?>
<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0 fw-bold">Setting Aplikasi</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Setting</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-header bg-white border-bottom-0 pt-3">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-eye text-primary me-2"></i>Preview Brand</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <img src="<?= htmlspecialchars(app_settings_asset_url($settings, 'company_logo', $assetPrefix)); ?>" class="rounded shadow-sm mb-3 border" style="width: 112px; height: 112px; object-fit: contain; background: #fafafa;" alt="Logo Perusahaan">
                                <h5 class="mb-1 fw-bold"><?= htmlspecialchars($settings['app_name']); ?></h5>
                                <div class="text-muted small">Logo login dan register</div>
                            </div>
                            <hr class="text-muted opacity-25">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <img src="<?= htmlspecialchars(app_settings_asset_url($settings, 'favicon', $assetPrefix)); ?>" class="border rounded p-1.5 shadow-sm" style="width: 46px; height: 46px; object-fit: contain; background: #fff;" alt="Icon Tab Browser">
                                <div>
                                    <strong class="text-dark small d-block">Icon Tab Browser</strong>
                                    <div class="text-muted" style="font-size: 0.75rem;">Favicon halaman</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <img src="<?= htmlspecialchars(app_settings_asset_url($settings, 'sidebar_header_image', $assetPrefix)); ?>" class="border rounded p-1.5 shadow-sm" style="width: 46px; height: 46px; object-fit: contain; background: #fff;" alt="Gambar Header Sidebar">
                                <div>
                                    <strong class="text-dark small d-block">Header Sidebar</strong>
                                    <div class="text-muted" style="font-size: 0.75rem;">Gambar di samping nama aplikasi</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-header bg-white border-bottom-0 pt-3">
                            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-gear-fill text-primary me-2"></i>Ubah Tampilan Aplikasi</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="mb-4">
                                    <label for="app_name" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Nama Aplikasi</label>
                                    <input type="text" name="app_name" id="app_name" class="form-control" value="<?= htmlspecialchars($settings['app_name']); ?>" maxlength="80" required style="border-radius: 8px;">
                                    <div class="form-text text-muted" style="font-size: 0.75rem;">Nama ini menggantikan teks brand pada sidebar dan judul halaman.</div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label for="company_logo" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Logo Perusahaan</label>
                                        <input type="file" name="company_logo" id="company_logo" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp,image/x-icon" style="border-radius: 8px;">
                                        <div class="form-text text-muted" style="font-size: 0.75rem;">Untuk halaman login dan register. Maksimal 2MB.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="favicon" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Icon Tab Browser</label>
                                        <input type="file" name="favicon" id="favicon" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp,image/x-icon" style="border-radius: 8px;">
                                        <div class="form-text text-muted" style="font-size: 0.75rem;">Disarankan ukuran persegi seperti 32x32 atau 64x64.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="sidebar_header_image" class="form-label fw-semibold text-secondary" style="font-size: 0.9rem;">Gambar Header Sidebar</label>
                                        <input type="file" name="sidebar_header_image" id="sidebar_header_image" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp,image/x-icon" style="border-radius: 8px;">
                                        <div class="form-text text-muted" style="font-size: 0.75rem;">Ditampilkan di sidebar bagian atas.</div>
                                    </div>
                                </div>

                                <div class="mt-4 pt-2">
                                    <button type="submit" name="update_app_settings" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm" style="border-radius: 8px; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); border: none;">
                                        <i class="bi bi-save me-1"></i> Simpan Setting
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
