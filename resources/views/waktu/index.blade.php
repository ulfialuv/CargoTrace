@extends('layouts.app')

@section('title', 'Waktu Dunia - CargoTrace')

@section('content')

<style>
    .waktu-page {
        color: #172033;
        padding: 24px;
        background: #f4f6fb;
        min-height: 100vh;
    }

    .waktu-page * {
        box-sizing: border-box;
    }

    .waktu-hero {
        background: linear-gradient(135deg, #07152f, #102c59, #1b437c);
        color: white;
        padding: 30px;
        border-radius: 24px;
        position: relative;
        overflow: hidden;
        margin-bottom: 25px;
    }

    .waktu-hero::after {
        content: "";
        position: absolute;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: rgba(255,255,255,.05);
        right: -70px;
        top: -100px;
    }

    .waktu-hero h1 {
        font-size: 30px;
        font-weight: 800;
        margin: 10px 0;
        color: white;
    }

    .waktu-hero p {
        color: #d5e1f5;
        margin: 0;
    }

    .waktu-tag {
        display: inline-block;
        background: rgba(255,255,255,.12);
        padding: 7px 13px;
        border-radius: 30px;
        font-size: 12px;
        letter-spacing: .5px;
    }

    .waktu-main-clock {
        margin-top: 25px;
        padding: 22px;
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.15);
        border-radius: 18px;
        max-width: 500px;
    }

    .waktu-main-clock .clock-time {
        font-size: clamp(35px, 6vw, 62px);
        font-weight: 800;
        letter-spacing: 2px;
        line-height: 1.2;
        color: white;
    }

    .waktu-main-clock .clock-date {
        margin-top: 10px;
        color: #d5e1f5;
    }

    .waktu-section {
        margin-bottom: 28px;
    }

    .waktu-section-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 17px;
    }

    .waktu-section-heading h2 {
        font-size: 21px;
        font-weight: 800;
        color: #101c33;
        margin: 0;
    }

    .waktu-section-heading p {
        color: #718096;
        font-size: 13px;
        margin: 5px 0 0;
    }

    .waktu-btn {
        border: 0;
        border-radius: 11px;
        padding: 11px 16px;
        font-weight: 700;
        cursor: pointer;
        transition: .2s;
    }

    .waktu-btn-primary {
        color: white;
        background: #102c59;
    }

    .waktu-btn-primary:hover {
        background: #1b437c;
    }

    .waktu-btn-light {
        background: white;
        color: #102c59;
        border: 1px solid #dce3ef;
    }

    .waktu-btn-light:hover {
        background: #edf2fa;
    }

    .waktu-search {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .waktu-search input {
        flex: 1;
        min-width: 220px;
        padding: 13px 15px;
        border-radius: 12px;
        border: 1px solid #dce3ef;
        background: white;
        outline: none;
        color: #172033;
    }

    .waktu-search input:focus {
        border-color: #315c9a;
        box-shadow: 0 0 0 3px rgba(49,92,154,.1);
    }

    .waktu-city-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .waktu-city-card {
        background: white;
        border: 1px solid #e5eaf2;
        border-radius: 18px;
        padding: 20px;
        transition: .2s;
        min-width: 0;
    }

    .waktu-city-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(12,35,70,.08);
    }

    .waktu-city-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
    }

    .waktu-city-name {
        font-size: 17px;
        font-weight: 800;
        color: #14213a;
    }

    .waktu-city-country {
        font-size: 12px;
        color: #718096;
        margin-top: 4px;
    }

    .waktu-favorite {
        border: 0;
        background: #f2f5fa;
        color: #8390a5;
        border-radius: 10px;
        width: 35px;
        height: 35px;
        cursor: pointer;
        font-size: 18px;
    }

    .waktu-favorite.active {
        color: #f2a900;
        background: #fff7df;
    }

    .waktu-city-time {
        font-size: 30px;
        font-weight: 800;
        color: #102c59;
        margin-top: 23px;
        letter-spacing: 1px;
    }

    .waktu-city-date {
        color: #718096;
        font-size: 12px;
        margin-top: 7px;
    }

    .waktu-city-zone {
        display: inline-block;
        background: #edf3fc;
        color: #315c9a;
        border-radius: 8px;
        padding: 6px 9px;
        font-size: 11px;
        margin-top: 14px;
        word-break: break-word;
    }

    .waktu-offset {
        font-size: 12px;
        color: #718096;
        margin-top: 8px;
    }

    .waktu-panel {
        background: white;
        border: 1px solid #e5eaf2;
        border-radius: 20px;
        padding: 24px;
    }

    .waktu-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .waktu-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .waktu-field label {
        font-size: 13px;
        font-weight: 700;
        color: #344054;
    }

    .waktu-field input,
    .waktu-field select {
        width: 100%;
        padding: 12px;
        border: 1px solid #dce3ef;
        border-radius: 11px;
        background: white;
        color: #172033;
        outline: none;
    }

    .waktu-field input:focus,
    .waktu-field select:focus {
        border-color: #315c9a;
    }

    .waktu-result {
        background: linear-gradient(135deg, #edf3fc, #f8faff);
        border: 1px solid #dce7f7;
        border-radius: 16px;
        padding: 20px;
        margin-top: 18px;
        display: none;
    }

    .waktu-result.show {
        display: block;
    }

    .waktu-result-time {
        font-size: 30px;
        font-weight: 800;
        color: #102c59;
        margin: 8px 0;
    }

    .waktu-info-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 15px;
    }

    .waktu-info-card {
        background: white;
        border: 1px solid #e5eaf2;
        border-radius: 16px;
        padding: 20px;
    }

    .waktu-info-card .info-label {
        color: #718096;
        font-size: 12px;
    }

    .waktu-info-card .info-value {
        font-size: 22px;
        font-weight: 800;
        color: #102c59;
        margin-top: 10px;
    }

    .waktu-info-card .info-desc {
        color: #718096;
        font-size: 12px;
        margin-top: 5px;
    }

    .waktu-calendar {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
    }

    .waktu-calendar th {
        color: #718096;
        font-size: 12px;
        padding: 12px 5px;
    }

    .waktu-calendar td {
        padding: 5px;
    }

    .waktu-calendar td span {
        display: block;
        padding: 12px 5px;
        border-radius: 10px;
        font-size: 13px;
    }

    .waktu-calendar td span.today {
        background: #102c59;
        color: white;
        font-weight: 800;
    }

    .waktu-calendar td span.empty {
        color: #cbd5e1;
    }

    .waktu-calendar td span.weekend {
        color: #dc2626;
    }

    .waktu-calendar td span.today.weekend {
        color: white;
    }

    .waktu-calendar-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
    }

    .waktu-calendar-nav h3 {
        margin: 0;
        color: #14213a;
        font-size: 17px;
    }

    .waktu-calendar-nav button {
        border: 1px solid #dce3ef;
        background: white;
        color: #102c59;
        border-radius: 9px;
        padding: 8px 12px;
        cursor: pointer;
    }

    .waktu-status {
        color: #718096;
        font-size: 12px;
        margin-top: 18px;
    }

    .waktu-error {
        background: #fff1f2;
        color: #b42318;
        border: 1px solid #ffd5d8;
        padding: 13px;
        border-radius: 12px;
        margin-top: 15px;
        display: none;
    }

    .waktu-error.show {
        display: block;
    }

    .waktu-loading {
        color: #718096;
        padding: 20px;
        text-align: center;
    }

    @media(max-width: 1000px) {
        .waktu-city-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .waktu-info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media(max-width: 650px) {
        .waktu-page {
            padding: 14px;
        }

        .waktu-hero {
            padding: 22px;
        }

        .waktu-city-grid,
        .waktu-info-grid,
        .waktu-form-grid {
            grid-template-columns: 1fr;
        }

        .waktu-main-clock .clock-time {
            font-size: 38px;
        }

        .waktu-panel {
            padding: 17px;
        }
    }
</style>

<div class="waktu-page">

    {{-- HEADER --}}
    <section class="waktu-hero">

        <span class="waktu-tag">
            CARGOTRACE • WORLD TIME
        </span>

        <h1>Waktu Dunia</h1>

        <p>
            Pantau waktu global, zona waktu, dan jadwal pengiriman
            dari berbagai lokasi secara real-time.
        </p>

        <div class="waktu-main-clock">

            <div style="font-size:12px;color:#d5e1f5;">
                WAKTU INDONESIA BARAT
            </div>

            <div class="clock-time" id="mainClock">
                --:--:--
            </div>

            <div class="clock-date" id="mainDate">
                Memuat tanggal...
            </div>

            <div style="margin-top:15px;font-size:12px;color:#d5e1f5;">
                Zona waktu: Asia/Jakarta (WIB)
            </div>

        </div>

    </section>


    {{-- INFORMASI WAKTU --}}
    <section class="waktu-section">

        <div class="waktu-section-heading">

            <div>
                <h2>Informasi Waktu</h2>
                <p>Ringkasan waktu lokal dan kalender hari ini.</p>
            </div>

            <button class="waktu-btn waktu-btn-light" id="refreshTime">
                ↻ Perbarui
            </button>

        </div>

        <div class="waktu-info-grid">

            <div class="waktu-info-card">

                <div class="info-label">
                    Waktu Indonesia
                </div>

                <div class="info-value" id="infoWib">
                    --
                </div>

                <div class="info-desc">
                    WIB · UTC+07:00
                </div>

            </div>

            <div class="waktu-info-card">

                <div class="info-label">
                    Hari
                </div>

                <div class="info-value" id="infoDay">
                    --
                </div>

                <div class="info-desc">
                    Hari dalam kalender
                </div>

            </div>

            <div class="waktu-info-card">

                <div class="info-label">
                    Tanggal
                </div>

                <div class="info-value" id="infoDate">
                    --
                </div>

                <div class="info-desc">
                    Tanggal lokal Indonesia
                </div>

            </div>

        </div>

    </section>


    {{-- JAM DUNIA --}}
    <section class="waktu-section">

        <div class="waktu-section-heading">

            <div>
                <h2>Jam Dunia</h2>
                <p>Jam kota dan zona waktu internasional.</p>
            </div>

            <button class="waktu-btn waktu-btn-light" id="showFavorites">
                ★ Favorit
            </button>

        </div>

        <div class="waktu-search">

            <input
                type="text"
                id="citySearch"
                placeholder="Cari kota, negara, atau zona waktu..."
            >

            <button class="waktu-btn waktu-btn-primary" id="resetSearch">
                Reset
            </button>

        </div>

        <div id="cityGrid" class="waktu-city-grid">

            <div class="waktu-loading">
                Memuat daftar kota...
            </div>

        </div>

        <div class="waktu-status" id="lastUpdated">
            Terakhir diperbarui: --
        </div>

        <div class="waktu-error" id="cityError"></div>

    </section>


    {{-- KONVERSI WAKTU --}}
    <section class="waktu-section">

        <div class="waktu-section-heading">

            <div>
                <h2>Konversi Waktu</h2>
                <p>
                    Ubah waktu dari satu lokasi ke lokasi lainnya.
                </p>
            </div>

        </div>

        <div class="waktu-panel">

            <form id="convertForm">

                <div class="waktu-form-grid">

                    <div class="waktu-field">

                        <label for="fromTimezone">
                            Dari lokasi
                        </label>

                        <select id="fromTimezone" required>
                            <option value="Asia/Jakarta">
                                Jakarta - Indonesia
                            </option>
                        </select>

                    </div>

                    <div class="waktu-field">

                        <label for="toTimezone">
                            Ke lokasi
                        </label>

                        <select id="toTimezone" required>
                            <option value="Asia/Tokyo">
                                Tokyo - Jepang
                            </option>
                        </select>

                    </div>

                    <div class="waktu-field">

                        <label for="convertDate">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            id="convertDate"
                            required
                        >

                    </div>

                    <div class="waktu-field">

                        <label for="convertTime">
                            Jam
                        </label>

                        <input
                            type="time"
                            id="convertTime"
                            value="12:00"
                            required
                        >

                    </div>

                </div>

                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:20px;">

                    <button
                        type="submit"
                        class="waktu-btn waktu-btn-primary"
                    >
                        Konversi Waktu
                    </button>

                    <button
                        type="button"
                        class="waktu-btn waktu-btn-light"
                        id="swapTimezone"
                    >
                        ⇄ Tukar Lokasi
                    </button>

                </div>

            </form>

            <div class="waktu-result" id="convertResult">

                <div style="font-size:12px;color:#718096;">
                    HASIL KONVERSI
                </div>

                <div class="waktu-result-time" id="convertedTime">
                    --
                </div>

                <div id="convertedDate"></div>

                <div
                    style="margin-top:10px;color:#315c9a;font-size:13px;"
                    id="convertedZone"
                ></div>

                <div
                    style="margin-top:10px;font-size:13px;color:#344054;"
                    id="convertedDifference"
                ></div>

            </div>

            <div class="waktu-error" id="convertError"></div>

        </div>

    </section>


    {{-- KALENDER --}}
    <section class="waktu-section">

        <div class="waktu-section-heading">

            <div>
                <h2>Kalender</h2>
                <p>Kalender berdasarkan tanggal Indonesia.</p>
            </div>

        </div>

        <div class="waktu-panel">

            <div class="waktu-calendar-nav">

                <button type="button" id="prevMonth">
                    ←
                </button>

                <h3 id="calendarTitle">
                    --
                </h3>

                <button type="button" id="nextMonth">
                    →
                </button>

            </div>

            <table class="waktu-calendar">

                <thead>
                    <tr>
                        <th>Min</th>
                        <th>Sen</th>
                        <th>Sel</th>
                        <th>Rab</th>
                        <th>Kam</th>
                        <th>Jum</th>
                        <th>Sab</th>
                    </tr>
                </thead>

                <tbody id="calendarBody"></tbody>

            </table>

        </div>

    </section>


    {{-- DETAIL ZONA WAKTU --}}
    <section class="waktu-section">

        <div class="waktu-section-heading">

            <div>
                <h2>Detail Zona Waktu</h2>
                <p>Informasi zona waktu yang digunakan CargoTrace.</p>
            </div>

        </div>

        <div class="waktu-panel">

            <div class="waktu-info-grid">

                <div class="waktu-info-card">

                    <div class="info-label">
                        Zona Utama
                    </div>

                    <div class="info-value" style="font-size:17px;">
                        Asia/Jakarta
                    </div>

                    <div class="info-desc">
                        Indonesia Barat
                    </div>

                </div>

                <div class="waktu-info-card">

                    <div class="info-label">
                        Format
                    </div>

                    <div class="info-value" style="font-size:17px;">
                        24 Jam
                    </div>

                    <div class="info-desc">
                        HH:mm:ss
                    </div>

                </div>

                <div class="waktu-info-card">

                    <div class="info-label">
                        Standar
                    </div>

                    <div class="info-value" style="font-size:17px;">
                        IANA
                    </div>

                    <div class="info-desc">
                        Identifikasi zona waktu
                    </div>

                </div>

            </div>

            <div class="waktu-status">
                Waktu kota menggunakan zona waktu IANA melalui browser.
                Perubahan waktu musim panas mengikuti aturan zona waktu
                yang tersedia pada perangkat.
            </div>

        </div>

    </section>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const locationsUrl = @json(route('waktu.locations'));
    const convertUrl = @json(route('waktu.convert'));

    const cityGrid = document.getElementById('cityGrid');
    const citySearch = document.getElementById('citySearch');
    const cityError = document.getElementById('cityError');

    const fromTimezone = document.getElementById('fromTimezone');
    const toTimezone = document.getElementById('toTimezone');

    const convertForm = document.getElementById('convertForm');
    const convertResult = document.getElementById('convertResult');
    const convertError = document.getElementById('convertError');

    let locations = [];
    let favorites = [];
    let favoritesOnly = false;

    let calendarDate = new Date();

    const indonesiaTimezone = 'Asia/Jakarta';


    // =========================
    // FORMAT WAKTU
    // =========================

    function getTime(timezone) {

        return new Intl.DateTimeFormat('id-ID', {
            timeZone: timezone,
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        }).format(new Date());

    }


    function getDate(timezone) {

        return new Intl.DateTimeFormat('id-ID', {
            timeZone: timezone,
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }).format(new Date());

    }


    function getOffset(timezone) {

        const parts = new Intl.DateTimeFormat('en-US', {
            timeZone: timezone,
            timeZoneName: 'longOffset'
        }).formatToParts(new Date());

        const part = parts.find(item => item.type === 'timeZoneName');

        return part ? part.value.replace('GMT', 'UTC') : 'UTC';

    }


    function escapeHtml(value) {

        return String(value ?? '').replace(/[&<>"']/g, function (char) {

            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char];

        });

    }


    // =========================
    // JAM UTAMA
    // =========================

    function updateMainClock() {

        const now = new Date();

        document.getElementById('mainClock').textContent =
            getTime(indonesiaTimezone);

        document.getElementById('mainDate').textContent =
            getDate(indonesiaTimezone);

        document.getElementById('infoWib').textContent =
            getTime(indonesiaTimezone);

        document.getElementById('infoDay').textContent =
            new Intl.DateTimeFormat('id-ID', {
                timeZone: indonesiaTimezone,
                weekday: 'long'
            }).format(now);

        document.getElementById('infoDate').textContent =
            new Intl.DateTimeFormat('id-ID', {
                timeZone: indonesiaTimezone,
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            }).format(now);

    }


    // =========================
    // LOAD LOKASI
    // =========================

    async function loadLocations() {

        cityGrid.innerHTML = `
            <div class="waktu-loading">
                Memuat daftar kota...
            </div>
        `;

        cityError.classList.remove('show');

        try {

            const response = await fetch(locationsUrl);

            if (!response.ok) {
                throw new Error('Gagal mengambil daftar kota.');
            }

            const data = await response.json();

            locations = Array.isArray(data)
                ? data
                : (data.locations || []);

            renderLocations();
            populateTimezoneSelects();

            document.getElementById('lastUpdated').textContent =
                'Terakhir diperbarui: ' +
                new Intl.DateTimeFormat('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit',
                            second: '2-digit'
                }).format(new Date());

        } catch (error) {

            cityGrid.innerHTML = `
                <div class="waktu-loading">
                    Daftar kota belum dapat dimuat.
                </div>
            `;

            cityError.textContent =
                error.message || 'Terjadi kesalahan saat memuat kota.';

            cityError.classList.add('show');

        }

    }


    // =========================
    // KARTU JAM DUNIA
    // =========================

    function renderLocations() {

        const keyword = citySearch.value
            .trim()
            .toLowerCase();

        let filtered = locations.filter(function (location) {

            const searchable = [
                location.city,
                location.country,
                location.timezone
            ].join(' ').toLowerCase();

            const matchesSearch = searchable.includes(keyword);

            const matchesFavorite =
                !favoritesOnly ||
                favorites.includes(location.timezone);

            return matchesSearch && matchesFavorite;

        });

        if (!filtered.length) {

            cityGrid.innerHTML = `
                <div class="waktu-loading">
                    Tidak ada kota yang ditemukan.
                </div>
            `;

            return;

        }

        cityGrid.innerHTML = filtered.map(function (location) {

            const city = escapeHtml(location.city);
            const country = escapeHtml(location.country);
            const timezone = escapeHtml(location.timezone);

            const isFavorite =
                favorites.includes(location.timezone);

            return `
                <div class="waktu-city-card">

                    <div class="waktu-city-top">

                        <div>
                            <div class="waktu-city-name">
                                ${city}
                            </div>

                            <div class="waktu-city-country">
                                ${country}
                            </div>
                        </div>

                        <button
                            type="button"
                            class="waktu-favorite ${isFavorite ? 'active' : ''}"
                            data-favorite="${timezone}"
                            title="Tambah atau hapus favorit"
                        >
                            ${isFavorite ? '★' : '☆'}
                        </button>

                    </div>

                    <div
                        class="waktu-city-time"
                        data-clock="${timezone}"
            >
                ${getTime(location.timezone)}
            </div>

                    <div
                        class="waktu-city-date"
                        data-date="${timezone}"
                    >
                        ${getDate(location.timezone)}
                    </div>

                    <div class="waktu-city-zone">
                        ${timezone}
                    </div>

                    <div
                        class="waktu-offset"
                        data-offset="${timezone}"
                    >
                        ${getOffset(location.timezone)}
                    </div>

                </div>
            `;

        }).join('');

        cityGrid.querySelectorAll('[data-favorite]').forEach(function (button) {

            button.addEventListener('click', function () {

                const timezone = button.dataset.favorite;

                if (favorites.includes(timezone)) {

                    favorites = favorites.filter(function (item) {
                        return item !== timezone;
                    });

                } else {

                    favorites.push(timezone);

                }

                renderLocations();

            });

        });

    }


    // =========================
    // UPDATE JAM SEMUA KOTA
    // =========================

    function updateWorldClocks() {

        document.querySelectorAll('[data-clock]').forEach(function (element) {

            const timezone = element.dataset.clock;

            try {
                element.textContent = getTime(timezone);
            } catch (error) {
                element.textContent = '--:--:--';
            }

        });

        document.querySelectorAll('[data-date]').forEach(function (element) {

            const timezone = element.dataset.date;

            try {
                element.textContent = getDate(timezone);
            } catch (error) {
                element.textContent = 'Tanggal tidak tersedia';
            }

        });

        document.querySelectorAll('[data-offset]').forEach(function (element) {

            const timezone = element.dataset.offset;

            try {
                element.textContent = getOffset(timezone);
            } catch (error) {
                element.textContent = 'UTC';
            }

        });

    }


    // =========================
    // PILIH ZONA WAKTU
    // =========================

    function populateTimezoneSelects() {

        const currentFrom = fromTimezone.value || indonesiaTimezone;
        const currentTo = toTimezone.value || 'Asia/Tokyo';

        const options = locations.map(function (location) {

            return `
                <option value="${escapeHtml(location.timezone)}">
                    ${escapeHtml(location.city)} - ${escapeHtml(location.country)}
                    (${escapeHtml(location.timezone)})
                </option>
            `;

        }).join('');

        fromTimezone.innerHTML = options;
        toTimezone.innerHTML = options;

        if (locations.some(item => item.timezone === currentFrom)) {
            fromTimezone.value = currentFrom;
        } else {
            fromTimezone.value = indonesiaTimezone;
        }

        if (locations.some(item => item.timezone === currentTo)) {
            toTimezone.value = currentTo;
        } else {
            toTimezone.value = 'Asia/Tokyo';
        }

    }


    // =========================
    // KONVERSI WAKTU
    // =========================

    convertForm.addEventListener('submit', async function (event) {

        event.preventDefault();

        convertError.classList.remove('show');
        convertResult.classList.remove('show');

        const from = fromTimezone.value;
        const to = toTimezone.value;

        const date = document.getElementById('convertDate').value;
        const time = document.getElementById('convertTime').value;

        if (!from || !to || !date || !time) {

            convertError.textContent =
                'Lengkapi lokasi, tanggal, dan jam terlebih dahulu.';

            convertError.classList.add('show');

            return;

        }

        const params = new URLSearchParams({
            from: from,
            to: to,
            date: date,
            time: time
        });

        try {

            const response = await fetch(
                convertUrl + '?' + params.toString()
            );

            const data = await response.json();

            if (!response.ok) {

                throw new Error(
                    data.message || 'Konversi waktu gagal.'
                );

            }

            document.getElementById('convertedTime').textContent =
                data.time || '--';

            document.getElementById('convertedDate').textContent =
                data.date || '--';

            document.getElementById('convertedZone').textContent =
                'Zona waktu: ' + (data.timezone || to);

            document.getElementById('convertedDifference').textContent =
                data.difference_text || '';

            convertResult.classList.add('show');

        } catch (error) {

            convertError.textContent =
                error.message || 'Terjadi kesalahan saat konversi.';

            convertError.classList.add('show');

        }

    });


    // =========================
    // TUKAR LOKASI
    // =========================

    document.getElementById('swapTimezone').addEventListener('click', function () {

        const from = fromTimezone.value;
        const to = toTimezone.value;

        fromTimezone.value = to;
        toTimezone.value = from;

    });


    // =========================
    // PENCARIAN
    // =========================

    citySearch.addEventListener('input', function () {

        favoritesOnly = false;

        document.getElementById('showFavorites').textContent =
            '★ Favorit';

        renderLocations();

    });


    document.getElementById('resetSearch').addEventListener('click', function () {

        citySearch.value = '';
        favoritesOnly = false;

        document.getElementById('showFavorites').textContent =
            '★ Favorit';

        renderLocations();

    });


    // =========================
    // FILTER FAVORIT
    // =========================

    document.getElementById('showFavorites').addEventListener('click', function () {

        favoritesOnly = !favoritesOnly;

        this.textContent = favoritesOnly
            ? '★ Semua Kota'
            : '★ Favorit';

        renderLocations();

    });


    // =========================
    // REFRESH
    // =========================

    document.getElementById('refreshTime').addEventListener('click', function () {

        updateMainClock();
        updateWorldClocks();
        loadLocations();

    });


    // =========================
    // KALENDER
    // =========================

    function renderCalendar() {

        const year = calendarDate.getFullYear();
        const month = calendarDate.getMonth();

        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        const today = new Date();

        const todayYear = today.getFullYear();
        const todayMonth = today.getMonth();
        const todayDate = today.getDate();

        const monthName = new Intl.DateTimeFormat('id-ID', {
            month: 'long',
            year: 'numeric'
        }).format(calendarDate);

        document.getElementById('calendarTitle').textContent =
            monthName;

        let html = '';
        let day = 1;

        for (let week = 0; week < 6; week++) {

            html += '<tr>';

            for (let weekday = 0; weekday < 7; weekday++) {

                const cellIndex = week * 7 + weekday;

                if (cellIndex < firstDay || day > daysInMonth) {

                    html += `
                        <td>
                            <span class="empty">—</span>
                        </td>
                    `;

                } else {

                    const isToday =
                        year === todayYear &&
                        month === todayMonth &&
                        day === todayDate;

                    const isWeekend =
                        weekday === 0 || weekday === 6;

                    let classes = [];

                    if (isToday) classes.push('today');
                    if (isWeekend) classes.push('weekend');

                    html += `
                        <td>
                            <span class="${classes.join(' ')}">
                                ${day}
                            </span>
                        </td>
                    `;

                    day++;

                }

            }

            html += '</tr>';

            if (day > daysInMonth) {
                break;
            }

        }

        document.getElementById('calendarBody').innerHTML = html;

    }


    document.getElementById('prevMonth').addEventListener('click', function () {

        calendarDate.setMonth(calendarDate.getMonth() - 1);

        renderCalendar();

    });


    document.getElementById('nextMonth').addEventListener('click', function () {

        calendarDate.setMonth(calendarDate.getMonth() + 1);

        renderCalendar();

    });


    // =========================
    // INISIALISASI
    // =========================

    const now = new Date();

    const localDate = new Intl.DateTimeFormat('en-CA', {
        timeZone: indonesiaTimezone,
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    }).format(now);

    document.getElementById('convertDate').value = localDate;

    updateMainClock();
    renderCalendar();
    loadLocations();

    setInterval(function () {

        updateMainClock();
        updateWorldClocks();

    }, 1000);

});
</script>

@endsection
