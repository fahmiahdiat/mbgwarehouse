# Contributing Guide

Panduan kontribusi untuk **Sistem Informasi Manajemen Gudang (SIMG) — SPPG Kuningan Jalaksana Maniskidul 3**, Badan Gizi Nasional.

Dokumen ini menjelaskan standar pengembangan, workflow Git, konvensi commit, dan proses review yang digunakan dalam proyek ini.

---

## 📋 Daftar Isi

1. [Persiapan Development](#1-persiapan-development)
2. [Branch Naming Convention](#2-branch-naming-convention)
3. [Commit Message Convention](#3-commit-message-convention)
4. [Workflow Git](#4-workflow-git)
5. [Pull Request Process](#5-pull-request-process)
6. [Code Style & Standards](#6-code-style--standards)
7. [Testing Requirements](#7-testing-requirements)
8. [Code Review Checklist](#8-code-review-checklist)

---

## 1. Persiapan Development

### 1.1 Requirement
- PHP 8.3+
- Composer 2.x
- Node.js 20+
- MySQL 8.0+
- Git 2.40+

### 1.2 Setup Lokal
```bash
# Clone repository
git clone https://github.com/username/gudang.git
cd gudang

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate:fresh --seed

# Build assets
npm run dev

# Jalankan server
php artisan serve
```

### 1.3 Verifikasi Setup
```bash
# Cek semua test hijau
./vendor/bin/pest

# Cek route
php artisan route:list
```

Akses `http://127.0.0.1:8000`, login dengan:
- Email: `admin@admin.com`
- Password: `password`

---

## 2. Branch Naming Convention

Gunakan prefix yang jelas agar mudah di-identifikasi.

| Prefix | Kegunaan | Contoh |
|---|---|---|
| `feature/` | Fitur baru | `feature/batch-expired-tracking` |
| `bugfix/` | Perbaikan bug non-critical | `bugfix/column-shift-on-nonbatch` |
| `hotfix/` | Perbaikan urgent production | `hotfix/login-session-error` |
| `test/` | Menambah/perbaiki test | `test/transaction-flow-coverage` |
| `docs/` | Dokumentasi | `docs/qa-test-plan` |
| `refactor/` | Refactor kode tanpa ubah behavior | `refactor/service-layer` |
| `chore/` | Maintenance (deps, config, tooling) | `chore/update-pest-3` |
| `perf/` | Peningkatan performa | `perf/query-optimization-stock` |

### Aturan Penamaan

✅ **Benar:**
```
feature/batch-expired-tracking
bugfix/validation-qty-zero
docs/qa-test-plan
```

❌ **Salah:**
```
Feature/BatchExpired       # camelCase
feature_batch_expired      # underscore
new-feature                # tanpa prefix
fix1                       # tidak deskriptif
```

### Format

```
<prefix>/<deskripsi-singkat-kebab-case>
```

- Gunakan **kebab-case** (huruf kecil, pisahkan dengan `-`)
- Deskriptif tapi singkat (max 5 kata)
- Bahasa Inggris lebih disarankan untuk konsistensi

---

## 3. Commit Message Convention

Proyek ini menggunakan **[Conventional Commits](https://www.conventionalcommits.org/)**.

### 3.1 Format

```
<type>(<scope>): <subject>

[optional body]

[optional footer]
```

### 3.2 Type

| Type | Deskripsi | Contoh |
|---|---|---|
| `feat` | Fitur baru | `feat(transaction): add batch tracking` |
| `fix` | Perbaikan bug | `fix(form): reset expired value on product change` |
| `docs` | Dokumentasi | `docs(readme): add installation guide` |
| `test` | Menambah/perbaiki test | `test(batch): add FEFO allocation test` |
| `refactor` | Refactor tanpa ubah behavior | `refactor(service): extract validation logic` |
| `style` | Formatting (spasi, titik koma) | `style: fix indentation` |
| `chore` | Maintenance | `chore(deps): update laravel to 13.x` |
| `perf` | Peningkatan performa | `perf(query): add composite index` |
| `ci` | Perubahan CI/CD | `ci: add codecov integration` |
| `revert` | Revert commit sebelumnya | `revert: feat(transaction): add batch` |

### 3.3 Scope (Opsional tapi Disarankan)

Scope menjelaskan **bagian mana** yang terpengaruh:

- `auth` — Autentikasi
- `product` — Data barang
- `transaction` — Transaksi
- `batch` — Batch & expired
- `opname` — Stock opname
- `report` — Laporan
- `dashboard` — Dashboard
- `form` — Form input
- `ui` — Tampilan
- `db` — Database/migration
- `deps` — Dependencies

### 3.4 Subject

- Mulai dengan **lowercase**
- Tidak pakai titik di akhir
- Max 72 karakter
- Imperative mood (perintah): "add", bukan "added" atau "adds"

### 3.5 Body (Opsional)

Jelaskan **apa** dan **kenapa**, bukan **bagaimana**. Max 72 karakter per baris.

### 3.6 Footer (Opsional)

- `Closes #123` — menutup issue
- `Fixes #123` — memperbaiki issue
- `BREAKING CHANGE: xxx` — perubahan breaking

### 3.7 Contoh Lengkap

#### Contoh sederhana (single line)
```
feat(transaction): add batch & expired tracking
```

#### Contoh dengan body
```
feat(transaction): add batch & expired tracking

Menambahkan kolom expired_date dan harga beli pada form barang masuk.
Batch otomatis dibuat untuk produk dengan flag has_batch = true.

Implementasi FEFO otomatis saat barang keluar.

Closes #12
```

#### Contoh bug fix
```
fix(form): reset expired value on product change

Kolom Tgl Expired sebelumnya menggunakan display:none yang menyebabkan
kolom berikutnya bergeser. Diganti dengan disabled attribute + reset
value saat transisi batch → non-batch.

Fixes #15
```

#### Contoh breaking change
```
feat(api): change product endpoint response format

BREAKING CHANGE: endpoint /api/products sekarang mengembalikan
array di dalam key 'data' untuk konsistensi dengan endpoint lain.
```

### 3.8 Yang Harus Dihindari

❌ **Commit message buruk:**
```
update
fix bug
commit
wip
asdfasdf
update file
fix
```

❌ **Terlalu panjang:**
```
feat: add batch tracking feature and also fix the bug on transaction form and update the UI color from blue to navy
```

✅ **Benar:**
```
feat(batch): add batch tracking feature
fix(form): reset expired value on product change
style(ui): change primary color to navy
```

---

## 4. Workflow Git

### 4.1 Branch Structure

```
main        ← stable, production-ready (protected)
  │
  └── develop          ← integration branch (protected)
        │
        ├── feature/xxx
        ├── bugfix/xxx
        └── docs/xxx
```

### 4.2 Alur Kerja

#### Step 1 — Update `develop` Lokal
```bash
git checkout develop
git pull origin develop
```

#### Step 2 — Buat Branch Baru
```bash
git checkout -b feature/nama-fitur
```

#### Step 3 — Kerjakan & Commit
```bash
# Kerja di kode...

git add .
git commit -m "feat(scope): deskripsi singkat"

# Atau commit per bagian
git add app/Models/Product.php
git commit -m "feat(product): add has_batch flag"

git add app/Models/ProductBatch.php
git commit -m "feat(batch): add ProductBatch model"
```

#### Step 4 — Push ke Remote
```bash
git push -u origin feature/nama-fitur
```

#### Step 5 — Buat Pull Request
Buka GitHub → klik **Compare & pull request** → isi template PR → submit.

#### Step 6 — Tunggu Review & CI
- ✅ CI (GitHub Actions) harus PASS
- ✅ Reviewer approve (kalau ada tim)
- ✅ Conflict resolved

#### Step 7 — Merge
Klik **Squash and merge** (rekomendasi) atau **Merge commit**.

#### Step 8 — Cleanup
```bash
git checkout develop
git pull origin develop
git branch -d feature/nama-fitur
```

Remote branch akan otomatis terhapus (auto-delete setting).

### 4.3 Merge Strategy

| Skenario | Strategy |
|---|---|
| Feature branch → develop | **Squash and merge** (rapikan history) |
| Bugfix → develop | **Squash and merge** |
| develop → main (release) | **Merge commit** (simpan history) |
| Hotfix → main | **Merge commit** |

### 4.4 Release Process

```bash
# 1. Buat PR dari develop → main
# 2. Setelah merge, update lokal
git checkout main
git pull origin main

# 3. Tag version
git tag -a v1.0.0 -m "Release v1.0.0 — Batch & Expired Feature"
git push origin v1.0.0

# 4. Update develop dari main (kalau ada hotfix di main)
git checkout develop
git merge main
git push origin develop
```

### 4.5 Versioning

Format: `v{MAJOR}.{MINOR}.{PATCH}`

- **MAJOR** — breaking change (misal API berubah)
- **MINOR** — fitur baru backward-compatible
- **PATCH** — bug fix backward-compatible

Contoh:
- `v1.0.0` — release pertama
- `v1.1.0` — tambah fitur laporan PDF
- `v1.0.1` — fix bug kecil
- `v2.0.0` — ganti struktur API

### 4.6 Aturan Penting

- ❌ **JANGAN** push langsung ke `main`
- ❌ **JANGAN** push langsung ke `develop`
- ❌ **JANGAN** force push ke branch yang sudah di-push (`main`, `develop`)
- ✅ **SELALU** pull dari `develop` sebelum bikin branch
- ✅ **SELALU** buka PR untuk merge
- ✅ **SELALU** pastikan CI PASS sebelum merge
- ✅ **SELALU** hapus branch setelah merge

---

## 5. Pull Request Process

### 5.1 PR Title

Format sama dengan commit message:

```
<type>(<scope>): <subject>
```

Contoh:
```
feat(batch): add FEFO auto allocation
fix(form): reset expired value on product change
docs(qa): add test plan document
```

### 5.2 PR Description Template

Buat file `.github/pull_request_template.md`:

```markdown
## 📝 Deskripsi
<!-- Jelaskan apa yang diubah dan kenapa -->

## 🔗 Issue Terkait
<!-- Contoh: Closes #12 -->
Closes #

## 🧪 Jenis Perubahan
<!-- Centang yang sesuai -->
- [ ] Bug fix (non-breaking change)
- [ ] Fitur baru (non-breaking change)
- [ ] Breaking change (perubahan yang mempengaruhi existing functionality)
- [ ] Dokumentasi
- [ ] Refactoring
- [ ] Test

## ✅ Checklist
- [ ] Code sudah di-review sendiri
- [ ] Sudah ada test (unit/feature)
- [ ] Semua test PASS (`./vendor/bin/pest`)
- [ ] Coverage ≥ 80%
- [ ] Sudah update dokumentasi (kalau perlu)
- [ ] Tidak ada conflict dengan `develop`
- [ ] Tidak ada `console.log` / `dd()` yang tertinggal

## 📸 Screenshot (kalau UI berubah)
<!-- Tambahkan screenshot before/after -->

## 🧪 Cara Test
<!-- Step by step cara verifikasi perubahan ini -->
1. Buka halaman X
2. Klik Y
3. Harus muncul Z

## 📌 Catatan Tambahan
<!-- Info lain yang perlu diketahui reviewer -->
```

### 5.3 PR Size

Usahakan PR **kecil & fokus**:

| Size | Lines | Review Time |
|---|---|---|
| 🟢 Ideal | < 200 | 15 menit |
| 🟡 OK | 200-500 | 30 menit |
| 🟠 Besar | 500-1000 | 1 jam |
| 🔴 Terlalu besar | > 1000 | Sulit direview |

Kalau PR besar, **pecah jadi beberapa PR**.

### 5.4 Definition of Done

PR bisa di-merge kalau:

- ✅ CI (GitHub Actions) hijau semua
- ✅ Minimal 1 approval (kalau tim > 1)
- ✅ Tidak ada conflict
- ✅ Semua komentar reviewer resolved
- ✅ Branch up-to-date dengan `develop`
- ✅ Test coverage tidak turun

---

## 6. Code Style & Standards

### 6.1 PHP

Menggunakan **Laravel Pint** (PSR-12 + Laravel preset).

```bash
# Cek style tanpa ubah
./vendor/bin/pint --test

# Fix style otomatis
./vendor/bin/pint
```

**Aturan penting:**
- 4 spasi indentasi (bukan tab)
- Nama class: `PascalCase`
- Nama method/variabel: `camelCase`
- Nama konstanta: `SCREAMING_SNAKE_CASE`
- Nama tabel: `snake_case` plural
- Nama kolom: `snake_case`

### 6.2 Blade

- Indentasi 4 spasi
- Selalu pakai `{{ }}` untuk output (escape otomatis)
- `{!! !!}` hanya untuk HTML yang sudah di-sanitize
- Directive `@if`, `@foreach`, dst. tanpa kurung kurawal
- Komentar pakai `{{-- --}}`

### 6.3 JavaScript

- Gunakan `const` / `let`, hindari `var`
- Semicolon di akhir statement
- Nama fungsi: `camelCase`
- Nama konstanta: `SCREAMING_SNAKE_CASE`
- Pakai `===` bukan `==`

### 6.4 Database

- Nama tabel **plural**: `products`, `stock_transactions`
- Foreign key: `{singular}_id` → `product_id`
- Pivot table: alphabetical singular → `product_supplier`
- Selalu pakai migration, **jangan** edit database manual

### 6.5 Commit Hook (Opsional)

Install husky untuk pre-commit check:

```bash
composer require --dev brainmaestro/composer-git-hooks
```

Tambah di `composer.json`:

```json
"extra": {
    "hooks": {
        "pre-commit": [
            "echo 'Running Pint...'",
            "./vendor/bin/pint --dirty",
            "echo 'Running Tests...'",
            "./vendor/bin/pest --compact"
        ]
    }
}
```

---

## 7. Testing Requirements

### 7.1 Coverage Minimum

- **Setiap PR harus** menyertakan test
- **Coverage minimum:** 80%
- **Coverage baru** tidak boleh menurunkan coverage existing

### 7.2 Test untuk Setiap Perubahan

| Jenis Perubahan | Test Wajib |
|---|---|
| Fitur baru | Feature test + Unit test |
| Bug fix | Regression test |
| Refactor | Existing test harus tetap pass |
| Model baru | Unit test untuk accessor & relasi |
| Service baru | Unit test untuk setiap method |

### 7.3 Jenis Test

**Unit Test** — test logic kecil (service, model):
```php
test('stock status aman ketika stok > min_stock', function () {
    $product = Product::factory()->create(['stock' => 100, 'min_stock' => 50]);
    expect($product->stock_status)->toBe('aman');
});
```

**Feature Test** — test HTTP endpoint:
```php
test('user bisa login dengan kredensial valid', function () {
    $user = User::factory()->create();
    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect('/dashboard');
});
```

### 7.4 Menjalankan Test

```bash
# Semua test
./vendor/bin/pest

# Dengan coverage
./vendor/bin/pest --coverage

# Hanya feature
./vendor/bin/pest tests/Feature

# Filter by nama
./vendor/bin/pest --filter=login

# Parallel (lebih cepat)
./vendor/bin/pest --parallel
```

### 7.5 Test Naming

- Nama test deskriptif: `test('user bisa login dengan kredensial valid')`
- **Bukan** `test('test login')` atau `test('it works')`
- Struktur: `[subject] [behavior] [expected result]`

---

## 8. Code Review Checklist

### Untuk Author (sebelum minta review)

- [ ] Code sudah di-review sendiri (baca ulang diff)
- [ ] Tidak ada `dd()`, `dump()`, `console.log()`, `var_dump()`
- [ ] Tidak ada komentar `TODO` yang belum di-handle
- [ ] Semua test pass
- [ ] Coverage tidak turun
- [ ] Sudah update dokumentasi (kalau perlu)
- [ ] PR description jelas & lengkap
- [ ] Screenshot dilampirkan (kalau UI berubah)

### Untuk Reviewer

**Fungsionalitas:**
- [ ] Apakah kode memenuhi requirement?
- [ ] Edge case sudah di-handle?
- [ ] Ada potensi bug?

**Kualitas Kode:**
- [ ] Penamaan jelas?
- [ ] DRY (Don't Repeat Yourself)?
- [ ] Single Responsibility Principle?
- [ ] Ada dead code / commented code?

**Keamanan:**
- [ ] Input sudah di-validasi?
- [ ] Tidak ada SQL injection?
- [ ] Tidak ada XSS?
- [ ] Authorization sudah benar?

**Performance:**
- [ ] Tidak ada N+1 query?
- [ ] Index sudah tepat?
- [ ] Tidak ada query di loop?

**Testing:**
- [ ] Ada test untuk perubahan?
- [ ] Test cover edge case?
- [ ] Test readable?

---

## 📞 Kontak

Untuk pertanyaan terkait kontribusi:

- **Project Owner:** Muhammad Fahmi Ahdiat
- **Email:** muhammadfahmiahdiat@gmail.com
- **Issue Tracker:** [GitHub Issues](https://github.com/fahmiahdiat/mbgwarehouse/issues)

---

**Terima kasih sudah berkontribusi!** 🎉

Dokumen ini dibuat untuk menjaga kualitas dan konsistensi code base. Kalau ada saran perbaikan, silakan buat PR ke `docs/` branch.