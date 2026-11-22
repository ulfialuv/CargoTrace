<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mata Uang - CargoTrace</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f4f6fb;
            color: #172033;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #071b3b, #102d59, #07152e);
            color: white;
            padding: 28px 18px;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            overflow-y: auto;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 10px 32px;
        }

        .brand-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #e6c76a, #f5df9b);
            color: #071b3b;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
            font-weight: 800;
        }

        .brand h2 {
            font-size: 19px;
            letter-spacing: 1px;
        }

        .brand small {
            color: #a8bad7;
            font-size: 10px;
            display: block;
            margin-top: 4px;
        }

        .menu-title {
            font-size: 10px;
            font-weight: 700;
            color: #8198bb;
            letter-spacing: 1.5px;
            margin: 18px 12px 12px;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .menu a {
            text-decoration: none;
            color: #c3d0e5;
            padding: 13px 14px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            transition: .2s;
        }

        .menu a:hover {
            background: rgba(255,255,255,.08);
            color: white;
        }

        .menu a.active {
            background: linear-gradient(90deg, #d7b85c, #f0d98d);
            color: #102344;
            font-weight: 800;
        }

        .menu-icon {
            width: 20px;
            text-align: center;
            font-size: 17px;
        }

        /* MAIN */

        .main {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 30px 35px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .topbar h1 {
            font-size: 27px;
            color: #10284d;
            font-weight: 800;
        }

        .topbar p {
            color: #7b879b;
            font-size: 13px;
            margin-top: 8px;
        }

        .date-label {
            background: white;
            border: 1px solid #e5eaf2;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 12px;
            color: #53647d;
            white-space: nowrap;
        }

        /* CARDS */

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border: 1px solid #e8edf5;
            border-radius: 18px;
            padding: 23px;
            box-shadow: 0 5px 20px rgba(16,40,77,.035);
        }

        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-icon {
            width: 43px;
            height: 43px;
            border-radius: 13px;
            background: #edf3ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-card h3 {
            font-size: 12px;
            color: #8490a4;
            font-weight: 600;
            margin-top: 17px;
        }

        .stat-card strong {
            font-size: 25px;
            display: block;
            margin-top: 8px;
            color: #10284d;
        }

        .stat-card small {
            display: block;
            font-size: 11px;
            color: #9ba6b7;
            margin-top: 8px;
        }

        /* CONTENT */

        .content-grid {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 22px;
            margin-bottom: 25px;
        }

        .panel {
            background: white;
            border: 1px solid #e8edf5;
            border-radius: 18px;
            padding: 24px;
            box-shadow: 0 5px 20px rgba(16,40,77,.035);
            min-width: 0;
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 23px;
        }

        .panel-header h2 {
            color: #10284d;
            font-size: 16px;
            font-weight: 800;
        }

        .panel-header p {
            font-size: 11px;
            color: #96a1b2;
            margin-top: 6px;
        }

        .badge {
            background: #edf3ff;
            color: #315e9c;
            border-radius: 8px;
            padding: 7px 10px;
            font-size: 10px;
            font-weight: 700;
        }

        /* CONVERTER */

        .converter {
            background: linear-gradient(135deg, #0b2347, #153d70);
            color: white;
            border-radius: 17px;
            padding: 24px;
        }

        .converter h2 {
            font-size: 17px;
        }

        .converter p {
            color: #b7c8e2;
            font-size: 11px;
            margin-top: 7px;
        }

        .field {
            margin-top: 20px;
        }

        .field label {
            display: block;
            font-size: 11px;
            color: #c1d0e5;
            margin-bottom: 9px;
        }

        .field input,
        .field select {
            width: 100%;
            border: 1px solid rgba(255,255,255,.14);
            background: rgba(255,255,255,.09);
            color: white;
            padding: 13px;
            border-radius: 10px;
            outline: none;
            font-family: inherit;
            font-size: 13px;
        }

        .field select option {
            background: white;
            color: #172033;
        }

        .field input:focus,
        .field select:focus {
            border-color: #e5c56c;
        }

        .swap-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 18px;
        }

        .swap-btn {
            width: 40px;
            height: 40px;
            border: 1px solid rgba(255,255,255,.2);
            background: rgba(255,255,255,.1);
            border-radius: 11px;
            color: #f1d98f;
            cursor: pointer;
            font-size: 19px;
        }

        .convert-btn {
            width: 100%;
            margin-top: 23px;
            padding: 14px;
            border: none;
            border-radius: 11px;
            background: linear-gradient(90deg, #d6b354, #f2d98b);
            color: #102344;
            font-weight: 800;
            cursor: pointer;
            font-family: inherit;
        }

        .convert-btn:hover {
            filter: brightness(1.05);
        }

        .result-box {
            display: none;
            margin-top: 20px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.13);
            border-radius: 12px;
            padding: 17px;
        }

        .result-box.show {
            display: block;
        }

        .result-label {
            color: #b7c8e2;
            font-size: 11px;
        }

        .result-value {
            color: #f2d98b;
            font-size: 24px;
            font-weight: 800;
            margin-top: 8px;
            overflow-wrap: anywhere;
        }

        .result-detail {
            color: #c5d3e7;
            font-size: 11px;
            margin-top: 10px;
            line-height: 1.7;
        }

        /* SEARCH */

        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-box input {
            width: 100%;
            padding: 13px 15px;
            border: 1px solid #e4eaf3;
            border-radius: 11px;
            outline: none;
            font-family: inherit;
            font-size: 12px;
            background: #fbfcfe;
        }

        .search-box input:focus {
            border-color: #315e9c;
        }

        .search-box button {
            border: none;
            background: #102d55;
            color: white;
            padding: 0 17px;
            border-radius: 11px;
            cursor: pointer;
        }

        /* TABLE */

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 460px;
        }

        th {
            text-align: left;
            font-size: 10px;
            color: #8c99ac;
            font-weight: 700;
            padding: 13px 10px;
            border-bottom: 1px solid #edf0f5;
        }

        td {
            padding: 15px 10px;
            border-bottom: 1px solid #f0f3f7;
            font-size: 12px;
        }

        tbody tr:hover {
            background: #f8faff;
        }

        .currency-code {
            color: #142d50;
            font-weight: 800;
        }

        .currency-name {
            color: #8b97a9;
            font-size: 10px;
            margin-top: 4px;
        }

        .rate {
            font-weight: 700;
            color: #315e9c;
        }

        .use-btn {
            border: 1px solid #dce5f2;
            background: #f3f7ff;
            color: #315e9c;
            border-radius: 7px;
            padding: 7px 10px;
            cursor: pointer;
            font-size: 10px;
            font-weight: 700;
        }

        .use-btn:hover {
            background: #e4edff;
        }

        .empty {
            text-align: center;
            padding: 35px 15px;
            color: #8c99ac;
            font-size: 12px;
        }

        .loading {
            text-align: center;
            padding: 25px;
            color: #7b879b;
            font-size: 12px;
        }

        .error {
            display: none;
            margin-bottom: 18px;
            padding: 13px;
            background: #fff0f0;
            color: #b42323;
            border-radius: 10px;
            font-size: 12px;
        }

        .error.show {
            display: block;
        }

        .source {
            color: #98a3b4;
            font-size: 10px;
            margin-top: 18px;
            line-height: 1.7;
        }

        /* RESPONSIVE */

        @media (max-width: 1100px) {
            .main {
                padding: 25px;
            }

            .content-grid {
                grid-template-columns: 1fr;
            }

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 760px) {
            .sidebar {
                width: 72px;
                padding: 20px 10px;
            }

            .brand {
                justify-content: center;
                padding: 0 0 25px;
            }

            .brand-text,
            .menu-title,
            .menu a span:not(.menu-icon) {
                display: none;
            }

            .menu a {
                justify-content: center;
                padding: 14px 5px;
            }

            .menu-icon {
                font-size: 20px;
            }

            .main {
                margin-left: 72px;
                width: calc(100% - 72px);
                padding: 20px 14px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .topbar h1 {
                font-size: 23px;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .panel {
                padding: 17px;
            }

            .converter {
                padding: 18px;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="brand">
            <div class="brand-icon">C</div>

            <div class="brand-text">
                <h2>CARGOTRACE</h2>
                <small>LOGISTICS MANAGEMENT</small>
            </div>
        </div>

        <div class="menu-title">MENU UTAMA</div>

        <nav class="menu">

            <a href="{{ url('/dashboard') }}">
                <span class="menu-icon">▦</span>
                <span>Dashboard</span>
            </a>

            <a href="{{ url('/pengiriman') }}">
                <span class="menu-icon">▣</span>
                <span>Pengiriman</span>
            </a>

            <a href="{{ url('/pelacakan') }}">
                <span class="menu-icon">⌖</span>
                <span>Pelacakan</span>
            </a>

            <a href="{{ url('/fasilitas') }}">
                <span class="menu-icon">▤</span>
                <span>Fasilitas</span>
            </a>

            <a href="{{ url('/cuaca') }}">
                <span class="menu-icon">☁</span>
                <span>Cuaca</span>
            </a>

            <a href="{{ url('/berita') }}">
                <span class="menu-icon">▧</span>
                <span>Berita</span>
            </a>

            <a href="{{ url('/mata-uang') }}" class="active">
                <span class="menu-icon">＄</span>
                <span>Mata Uang</span>
            </a>

            <a href="{{ url('/waktu') }}">
                <span class="menu-icon">◷</span>
                <span>Waktu Dunia</span>
            </a>

        </nav>

        <div class="menu-title">SISTEM</div>

        <nav class="menu">
            <a href="{{ url('/login') }}">
                <span class="menu-icon">⇥</span>
                <span>Keluar</span>
            </a>
        </nav>

    </aside>

    <!-- MAIN -->

    <main class="main">

        <header class="topbar">

            <div>
                <h1>Mata Uang Dunia</h1>

                <p>
                    Informasi kurs internasional dan konversi mata uang
                    untuk kebutuhan logistik CargoTrace.
                </p>
            </div>

            <div class="date-label">
                📅 <span id="today"></span>
            </div>

        </header>

        <!-- ERROR -->

        <div class="error" id="errorMessage"></div>

        <!-- STATISTICS -->

        <section class="stats">

            <div class="stat-card">

                <div class="stat-top">
                    <span style="color:#8490a4;font-size:12px">
                        Mata Uang Tersedia
                    </span>

                    <div class="stat-icon">🌐</div>
                </div>

                <h3>DATA INTERNASIONAL</h3>

                <strong id="totalCurrencies">-</strong>

                <small>Jumlah mata uang dari API</small>

            </div>

            <div class="stat-card">

                <div class="stat-top">
                    <span style="color:#8490a4;font-size:12px">
                        Mata Uang Dasar
                    </span>

                    <div class="stat-icon">💱</div>
                </div>

                <h3>BASE CURRENCY</h3>

                <strong id="baseCurrency">IDR</strong>

                <small>Mata uang untuk daftar kurs</small>

            </div>

            <div class="stat-card">

                <div class="stat-top">
                    <span style="color:#8490a4;font-size:12px">
                        Tanggal Kurs
                    </span>

                    <div class="stat-icon">◷</div>
                </div>

                <h3>DATA TERBARU</h3>

                <strong id="rateDate" style="font-size:20px">-</strong>

                <small>Tanggal referensi kurs API</small>

            </div>

        </section>

        <!-- CONTENT GRID -->

        <section class="content-grid">

            <!-- CONVERTER -->

            <div class="converter">

                <h2>Konversi Mata Uang</h2>

                <p>
                    Hitung nilai mata uang berdasarkan kurs referensi API.
                </p>

                <div class="field">

                    <label>Nominal</label>

                    <input
                        type="number"
                        id="amount"
                        value="1"
                        min="0"
                        step="any"
                        placeholder="Masukkan nominal"
                    >

                </div>

                <div class="field">

                    <label>Mata Uang Asal</label>

                    <select id="fromCurrency">
                        <option value="IDR">IDR - Rupiah Indonesia</option>
                    </select>

                </div>

                <div class="swap-row">

                    <span style="font-size:11px;color:#b7c8e2">
                        Mata uang tujuan
                    </span>

                    <button
                        type="button"
                        class="swap-btn"
                        id="swapButton"
                        title="Tukar mata uang"
                    >
                        ⇅
                    </button>

                </div>

                <div class="field" style="margin-top:12px">

                    <select id="toCurrency">
                        <option value="USD">USD - US Dollar</option>
                    </select>

                </div>

                <button
                    type="button"
                    class="convert-btn"
                    id="convertButton"
                >
                    KONVERSI SEKARANG →
                </button>

                <div class="result-box" id="resultBox">

                    <div class="result-label">
                        HASIL KONVERSI
                    </div>

                    <div class="result-value" id="resultValue">
                        -
                    </div>

                    <div class="result-detail" id="resultDetail">
                    </div>

                </div>

            </div>

            <!-- CHART -->

            <div class="panel">

                <div class="panel-header">

                    <div>
                        <h2>Grafik Kurs</h2>

                        <p>
                            Perbandingan beberapa mata uang
                        </p>
                    </div>

                    <span class="badge">API DATA</span>

                </div>

                <div style="height:270px">
                    <canvas id="currencyChart"></canvas>
                </div>

                <div class="source">
                    Grafik menampilkan kurs mata uang terhadap
                    mata uang dasar yang dipilih.
                </div>

            </div>

        </section>

        <!-- CURRENCY TABLE -->

        <section class="panel">

            <div class="panel-header">

                <div>
                    <h2>Daftar Mata Uang Internasional</h2>

                    <p>
                        Cari kode, nama mata uang, atau negara.
                    </p>
                </div>

                <span class="badge" id="tableCount">
                    Memuat...
                </span>

            </div>

            <div class="search-box">

                <input
                    type="text"
                    id="searchCurrency"
                    placeholder="Cari negara atau mata uang, contoh: Germany, Jerman, USD..."
                >

                <button type="button" id="searchButton">
                    Cari
                </button>

            </div>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>MATA UANG</th>
                            <th>KODE</th>
                            <th>KURS</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>

                    <tbody id="currencyTable">

                        <tr>
                            <td colspan="5" class="loading">
                                Memuat data mata uang...
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

            <div class="source">
                Sumber data kurs:
                <a
                    href="https://frankfurter.dev/"
                    target="_blank"
                    rel="noopener"
                    style="color:#315e9c"
                >
                    Frankfurter API
                </a>.
                Kurs merupakan kurs referensi dan dapat berbeda
                dari harga transaksi bank atau pasar.
            </div>

        </section>

    </main>

</div>

<script>
    const API_CURRENCIES = "{{ route('mata-uang.currencies') }}";
    const API_RATES = "{{ route('mata-uang.api') }}";
    const API_CONVERT = "{{ route('mata-uang.convert') }}";

    const currencyTable = document.getElementById('currencyTable');
    const searchInput = document.getElementById('searchCurrency');
    const fromSelect = document.getElementById('fromCurrency');
    const toSelect = document.getElementById('toCurrency');

    const totalCurrencies = document.getElementById('totalCurrencies');
    const baseCurrencyLabel = document.getElementById('baseCurrency');
    const rateDateLabel = document.getElementById('rateDate');
    const tableCount = document.getElementById('tableCount');

    const errorMessage = document.getElementById('errorMessage');
    const resultBox = document.getElementById('resultBox');
    const resultValue = document.getElementById('resultValue');
    const resultDetail = document.getElementById('resultDetail');

    let currencies = [];
    let rates = {};
    let chart = null;

    let currentBase = 'IDR';

    const countryCurrencyMap = {
        'indonesia': 'IDR',
        'indonesian': 'IDR',
        'jerman': 'EUR',
        'germany': 'EUR',
        'german': 'EUR',
        'france': 'EUR',
        'perancis': 'EUR',
        'italy': 'EUR',
        'italia': 'EUR',
        'spain': 'EUR',
        'spanyol': 'EUR',
        'netherlands': 'EUR',
        'belanda': 'EUR',
        'belgium': 'EUR',
        'belgia': 'EUR',
        'portugal': 'EUR',
        'austria': 'EUR',
        'finland': 'EUR',
        'ireland': 'EUR',
        'yunani': 'EUR',
        'greece': 'EUR',
        'united states': 'USD',
        'amerika': 'USD',
        'amerika serikat': 'USD',
        'usa': 'USD',
        'us': 'USD',
        'united kingdom': 'GBP',
        'inggris': 'GBP',
        'britain': 'GBP',
        'japan': 'JPY',
        'jepang': 'JPY',
        'china': 'CNY',
        'tiongkok': 'CNY',
        'malaysia': 'MYR',
        'singapore': 'SGD',
        'singapura': 'SGD',
        'thailand': 'THB',
        'india': 'INR',
        'south korea': 'KRW',
        'korea selatan': 'KRW',
        'australia': 'AUD',
        'canada': 'CAD',
        'kanada': 'CAD',
        'switzerland': 'CHF',
        'swiss': 'CHF',
        'swedia': 'SEK',
        'sweden': 'SEK',
        'norway': 'NOK',
        'norwegia': 'NOK',
        'denmark': 'DKK',
        'russia': 'RUB',
        'rusia': 'RUB',
        'turkey': 'TRY',
        'turki': 'TRY',
        'saudi arabia': 'SAR',
        'arab saudi': 'SAR',
        'united arab emirates': 'AED',
        'uae': 'AED',
        'dubai': 'AED',
        'qatar': 'QAR',
        'brazil': 'BRL',
        'brasil': 'BRL',
        'mexico': 'MXN',
        'afrika selatan': 'ZAR',
        'south africa': 'ZAR',
        'new zealand': 'NZD',
        'selandia baru': 'NZD',
        'philippines': 'PHP',
        'filipina': 'PHP',
        'vietnam': 'VND',
        'pakistan': 'PKR',
        'bangladesh': 'BDT',
        'sri lanka': 'LKR',
        'nepal': 'NPR',
        'egypt': 'EGP',
        'mesir': 'EGP',
        'israel': 'ILS',
        'ukraine': 'UAH',
        'poland': 'PLN',
        'polandia': 'PLN',
        'czech republic': 'CZK',
        'hungary': 'HUF',
        'romania': 'RON',
        'bulgaria': 'BGN',
        'croatia': 'EUR',
        'iceland': 'ISK',
        'argentina': 'ARS',
        'chile': 'CLP',
        'colombia': 'COP',
        'peru': 'PEN',
        'nigeria': 'NGN',
        'kenya': 'KES',
        'ghana': 'GHS',
        'morocco': 'MAD',
        'maroko': 'MAD'
    };

    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.classList.add('show');
    }

    function hideError() {
        errorMessage.textContent = '';
        errorMessage.classList.remove('show');
    }

    function formatNumber(value) {
        return new Intl.NumberFormat('id-ID', {
            maximumFractionDigits: 8
        }).format(value);
    }

    function formatMoney(value, currency) {
        try {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: currency,
                maximumFractionDigits: 8
            }).format(value);
        } catch (error) {
            return formatNumber(value) + ' ' + currency;
        }
    }

    function normalizeText(value) {
        return String(value || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();
    }

    function getCurrencyName(code) {
        const found = currencies.find(item => item.code === code);

        return found ? found.name : code;
    }

    function renderSelects() {
        const options = currencies
            .map(item => {
                return `<option value="${item.code}">
                    ${item.code} - ${item.name}
                </option>`;
            })
            .join('');

        fromSelect.innerHTML = options;
        toSelect.innerHTML = options;

        fromSelect.value = currencies.some(
            item => item.code === 'IDR'
        ) ? 'IDR' : currencies[0]?.code;

        toSelect.value = currencies.some(
            item => item.code === 'USD'
        ) ? 'USD' : currencies[1]?.code;

        currentBase = fromSelect.value;

        baseCurrencyLabel.textContent = currentBase;
    }

    async function loadCurrencies() {
        hideError();

        try {
            const response = await fetch(API_CURRENCIES);

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(
                    data.message || 'Gagal mengambil daftar mata uang.'
                );
            }

            currencies = data.currencies || [];

            totalCurrencies.textContent = data.total ?? currencies.length;

            renderSelects();

            await loadRates(currentBase);

        } catch (error) {
            showError(
                'Tidak dapat memuat mata uang. Periksa koneksi internet dan route API.'
            );

            currencyTable.innerHTML = `
                <tr>
                    <td colspan="5" class="empty">
                        Gagal memuat data mata uang.
                    </td>
                </tr>
            `;
        }
    }

    async function loadRates(base) {
        hideError();

        currentBase = base;

        baseCurrencyLabel.textContent = base;

        try {
            const response = await fetch(
                `${API_RATES}?base=${encodeURIComponent(base)}`
            );

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(
                    data.message || 'Gagal mengambil kurs.'
                );
            }

            rates = {};

            (data.rates || []).forEach(item => {
                rates[item.quote] = Number(item.rate);
            });

            rates[base] = 1;

            const dates = (data.rates || [])
                .map(item => item.date)
                .filter(Boolean)
                .sort();

            rateDateLabel.textContent = dates.length
                ? dates[dates.length - 1]
                : '-';

            renderTable(searchInput.value);

            renderChart();

        } catch (error) {
            showError(
                'Kurs untuk ' + base + ' tidak tersedia dari API.'
            );

            currencyTable.innerHTML = `
                <tr>
                    <td colspan="5" class="empty">
                        Kurs tidak tersedia untuk mata uang ${base}.
                    </td>
                </tr>
            `;

            tableCount.textContent = '0 data';
        }
    }

    function searchCurrency() {
        const query = normalizeText(searchInput.value);

        const mappedCurrency = countryCurrencyMap[query];

        if (mappedCurrency) {
            const found = currencies.find(
                item => item.code === mappedCurrency
            );

            if (found) {
                fromSelect.value = mappedCurrency;

                currentBase = mappedCurrency;

                loadRates(mappedCurrency);
            }
        }

        renderTable(query);
    }

    function renderTable(query = '') {
        const normalizedQuery = normalizeText(query);

        const filtered = currencies.filter(item => {
            const code = normalizeText(item.code);
            const name = normalizeText(item.name);

            const mappedCode = countryCurrencyMap[normalizedQuery];

            return code.includes(normalizedQuery)
                || name.includes(normalizedQuery)
                || (
                    mappedCode
                    && item.code === mappedCode
                );
        });

        tableCount.textContent = filtered.length + ' mata uang';

        if (!filtered.length) {
            currencyTable.innerHTML = `
                <tr>
                    <td colspan="5" class="empty">
                        Mata uang tidak ditemukan.
                        <br><br>
                        Coba cari kode mata uang seperti EUR atau USD.
                    </td>
                </tr>
            `;

            return;
        }

        currencyTable.innerHTML = filtered
            .map((item, index) => {
                const rate = rates[item.code];

                const rateText = rate !== undefined
                    ? formatNumber(rate)
                    : '-';

                return `
                    <tr>
                        <td>${index + 1}</td>

                        <td>
                            <div class="currency-code">
                                ${item.code}
                            </div>

                            <div class="currency-name">
                                ${item.name}
                            </div>
                        </td>

                        <td>
                            <span class="currency-code">
                                ${item.code}
                            </span>
                        </td>

                        <td>
                            <span class="rate">
                                ${rateText}
                            </span>
                        </td>

                        <td>
                            <button
                                class="use-btn"
                                data-code="${item.code}"
                                type="button"
                            >
                                Gunakan
                            </button>
                        </td>
                    </tr>
                `;
            })
            .join('');
    }

    function renderChart() {
        const canvas = document.getElementById('currencyChart');

        if (!canvas) return;

        const preferred = [
            'USD',
            'EUR',
            'GBP',
            'JPY',
            'SGD',
            'MYR',
            'AUD',
            'CNY'
        ];

        const available = preferred.filter(
            code => rates[code] !== undefined
        );

        const labels = available;

        const values = available.map(
            code => rates[code]
        );

        if (chart) {
            chart.destroy();
        }

        chart = new Chart(canvas, {
            type: 'bar',

            data: {
                labels: labels,

                datasets: [{
                    label: 'Kurs terhadap ' + currentBase,
                    data: values,
                    borderRadius: 6,
                    backgroundColor: '#315e9c'
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                plugins: {
                    legend: {
                        display: false
                    }
                },

                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#edf0f5'
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },

                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    }

    async function convertCurrency() {
        hideError();

        const amount = Number(
            document.getElementById('amount').value
        );

        const from = fromSelect.value;
        const to = toSelect.value;

        if (!Number.isFinite(amount) || amount < 0) {
            showError('Masukkan nominal yang valid.');
            return;
        }

        const button = document.getElementById('convertButton');

        button.disabled = true;
        button.textContent = 'Menghitung...';

        try {
            const params = new URLSearchParams({
                amount: amount,
                from: from,
                to: to
            });

            const response = await fetch(
                `${API_CONVERT}?${params.toString()}`
            );

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(
                    data.message || 'Konversi gagal.'
                );
            }

            resultValue.textContent = formatMoney(
                Number(data.result),
                to
            );

            resultDetail.innerHTML = `
                ${formatNumber(amount)} ${from}
                = ${formatNumber(data.result)} ${to}
                <br>
                1 ${from} = ${formatNumber(data.rate)} ${to}
                <br>
                Tanggal kurs: ${data.date || '-'}
            `;

            resultBox.classList.add('show');

        } catch (error) {
            showError(
                error.message || 'Gagal melakukan konversi.'
            );

            resultBox.classList.remove('show');

        } finally {
            button.disabled = false;
            button.textContent = 'KONVERSI SEKARANG →';
        }
    }

    function swapCurrencies() {
        const oldFrom = fromSelect.value;
        const oldTo = toSelect.value;

        fromSelect.value = oldTo;
        toSelect.value = oldFrom;

        currentBase = fromSelect.value;

        loadRates(currentBase);

        resultBox.classList.remove('show');
    }

    // PILIH MATA UANG DARI TABEL

    currencyTable.addEventListener('click', function(event) {
        const button = event.target.closest('.use-btn');

        if (!button) return;

        const code = button.dataset.code;

        if (!code) return;

        toSelect.value = code;

        document.getElementById('amount').focus();
    });

    // EVENT SEARCH

    searchInput.addEventListener('input', function() {
        renderTable(searchInput.value);
    });

    document.getElementById('searchButton')
        .addEventListener('click', searchCurrency);

    searchInput.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            searchCurrency();
        }
    });

    // EVENT CONVERTER

    document.getElementById('convertButton')
        .addEventListener('click', convertCurrency);

    document.getElementById('swapButton')
        .addEventListener('click', swapCurrencies);

    fromSelect.addEventListener('change', function() {
        loadRates(fromSelect.value);
        resultBox.classList.remove('show');
    });

    toSelect.addEventListener('change', function() {
        resultBox.classList.remove('show');
    });

    // TANGGAL

    document.getElementById('today').textContent =
        new Intl.DateTimeFormat('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        }).format(new Date());

    // LOAD DATA

    loadCurrencies();
</script>

</body>
</html>
