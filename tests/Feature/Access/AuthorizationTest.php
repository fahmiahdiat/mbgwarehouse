<?php

use App\Models\User;

/*
|--------------------------------------------------------------------------
| Guest — Tidak Boleh Akses Apapun
|--------------------------------------------------------------------------
| Semua halaman harus redirect ke /login kalau belum authenticated.
*/

test('guest di-redirect dari dashboard', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('guest di-redirect dari master data', function () {
    $this->get('/products')->assertRedirect('/login');
    $this->get('/categories')->assertRedirect('/login');
    $this->get('/units')->assertRedirect('/login');
    $this->get('/suppliers')->assertRedirect('/login');
    $this->get('/locations')->assertRedirect('/login');
});

test('guest di-redirect dari transaksi', function () {
    $this->get('/transactions/in')->assertRedirect('/login');
    $this->get('/transactions/out')->assertRedirect('/login');
    $this->get('/transactions/return_supplier')->assertRedirect('/login');
    $this->get('/transactions/return_customer')->assertRedirect('/login');
});

test('guest di-redirect dari opname & adjustment', function () {
    $this->get('/opnames')->assertRedirect('/login');
    $this->get('/adjustments')->assertRedirect('/login');
});

test('guest di-redirect dari monitoring', function () {
    $this->get('/stocks')->assertRedirect('/login');
    $this->get('/stocks/mutation')->assertRedirect('/login');
});

test('guest di-redirect dari laporan', function () {
    $this->get('/reports/stock')->assertRedirect('/login');
    $this->get('/reports/in')->assertRedirect('/login');
    $this->get('/reports/out')->assertRedirect('/login');
    $this->get('/reports/opname')->assertRedirect('/login');
    $this->get('/reports/adjustment')->assertRedirect('/login');
    $this->get('/reports/expired')->assertRedirect('/login');
    $this->get('/reports/batch')->assertRedirect('/login');
});

test('guest di-redirect dari activity log', function () {
    $this->get('/activity-logs')->assertRedirect('/login');
});

/*
|--------------------------------------------------------------------------
| Authenticated — Boleh Akses
|--------------------------------------------------------------------------
*/

test('user login bisa akses dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->get('/dashboard')->assertOk();
});

test('user login bisa akses master data', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/products')->assertOk();
    $this->actingAs($user)->get('/categories')->assertOk();
    $this->actingAs($user)->get('/units')->assertOk();
    $this->actingAs($user)->get('/suppliers')->assertOk();
    $this->actingAs($user)->get('/locations')->assertOk();
});

test('user login bisa akses form barang masuk', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->get('/transactions/in/create')->assertOk();
});

test('user login bisa akses monitoring & laporan', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/stocks')->assertOk();
    $this->actingAs($user)->get('/reports/stock')->assertOk();
    $this->actingAs($user)->get('/reports/expired')->assertOk();
});

/*
|--------------------------------------------------------------------------
| Route Type Validation
|--------------------------------------------------------------------------
| Route /transactions/{type} harus reject type yang tidak dikenal.
*/

test('route transaksi menolak type tidak valid', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/transactions/unknown')->assertNotFound();
    $this->actingAs($user)->get('/transactions/hacked')->assertNotFound();
});

test('route transaksi menerima type valid', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/transactions/in')->assertOk();
    $this->actingAs($user)->get('/transactions/out')->assertOk();
    $this->actingAs($user)->get('/transactions/return_supplier')->assertOk();
    $this->actingAs($user)->get('/transactions/return_customer')->assertOk();
});