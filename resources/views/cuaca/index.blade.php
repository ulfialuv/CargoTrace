<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuaca | CargoTrace</title>

    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3f6fb;
            color: #17243b;
        }

        .sidebar {
            position: fixed;
            width: 245px;
            height: 100vh;
            background: #101d35;
            color: white;
            padding: 28px 18px;
        }

        .logo {
            font-size: 25px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .logo span {
            color: #48a8ff;
        }

        .subtitle {
            color: #9baac2;
            font-size: 12px;
            margin-bottom: 35px;
        }

        .menu-title {
            color: #8292ad;
            font-size: 11px;
            margin: 22px 12px 12px;
            text-transform: uppercase;
        }

        .menu a {
            display: block;
            text-decoration: none;
            color: #cbd5e5;
            padding: 13px 14px;
            border-radius: 10px;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .menu a:hover,
        .menu a.active {
            background: #243b60;
            color: white;
        }

        .menu a.active {
            border-left: 4px solid #42a5ff;
        }

        .main {
            margin-left: 245px;
            padding: 30px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            gap: 15px;
        }

        .topbar h1 {
            font-size: 27px;
            color: #142541;
        }

        .topbar p {
            color: #77849a;
            margin-top: 7px;
            font-size: 14px;
        }

        .date {
            background: white;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            box-shadow: 0 4px 15px #152b4b08;
        }

        .search-box {
            display: flex;
            gap: 10px;
            background: white;
            padding: 16px;
            border-radius: 15px;
            margin-bottom: 22px;
            box-shadow: 0 5px 20px #152b4b08;
        }

        .search-box input {
            flex: 1;
            min-width: 0;
            border: 1px solid #e1e7f0;
            padding: 13px;
            border-radius: 9px;
            outline: none;
        }

        .search-box button {
            border: none;
            background: #18345b;
            color: white;
            padding: 0 22px;
            border-radius: 9px;
            cursor: pointer;
        }

        .weather-main {
            background: linear-gradient(120deg, #142b4d, #245b91);
            color: white;
            padding: 28px;
            border-radius: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 22px;
        }

        .weather-main h2 {
            font-size: 20px;
            margin-bottom: 10px;
        }

        .weather-main .location {
            color: #c6d9ef;
            font-size: 14px;
        }

        .temperature {
            font-size: 54px;
            font-weight: bold;
            margin: 15px 0;
        }

        .weather-condition {
            font-size: 16px;
        }

        .weather-icon {
            font-size: 75px;
        }

        .updated {
            font-size: 12px;
            color: #c6d9ef;
            margin-top: 20px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 22px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 20px #152b4b08;
        }

        .card-label {
            color: #7b879b;
            font-size: 13px;
            margin-bottom: 14px;
        }

        .card-value {
            font-size: 23px;
            font-weight: bold;
            color: #172b49;
        }

        .card-icon {
            font-size: 24px;
            margin-bottom: 12px;
        }

        .section {
            background: white;
            border-radius: 17px;
            padding: 22px;
            margin-bottom: 22px;
            box-shadow: 0 5px 20px #152b4b08;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .section-header h3 {
            font-size: 17px;
        }

        .section-header span {
            color: #8190a5;
            font-size: 12px;
        }

        #weatherMap {
            height: 350px;
            border-radius: 12px;
            z-index: 1;
        }

        .forecast {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 12px;
        }

        .forecast-item {
            text-align: center;
            background: #f5f8fc;
            padding: 18px 8px;
            border-radius: 12px;
        }

        .forecast-item .day {
            font-size: 12px;
            color: #738199;
        }

        .forecast-item .icon {
            font-size: 27px;
            margin: 12px 0;
        }

        .forecast-item .temp {
            font-weight: bold;
            font-size: 14px;
        }

        .warning {
            background: #fff8e8;
            border: 1px solid #f4dfae;
            color: #8a5b12;
            padding: 16px;
            border-radius: 12px;
            font-size: 13px;
            margin-bottom: 22px;
        }

        .status {
            color: #728198;
            font-size: 13px;
            margin-top: 12px;
        }

        .error {
            color: #c0392b;
            background: #fff0ee;
            padding: 12px;
            border-radius: 10px;
            margin-top: 12px;
            display: none;
        }

        @media (max-width: 1100px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 750px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;
                padding: 18px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .weather-main {
                align-items: flex-start;
            }

            .temperature {
                font-size: 42px;
            }

            .grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .forecast {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>

<body>

<aside class="sidebar">
    <div class="logo">Cargo<span>Trace</span></div>
    <div class="subtitle">Logistics Management System</div>

    <div class="menu-title">Menu Utama</div>

    <nav class="menu">
        <a href="{{ route('dashboard') }}">📊 Dashboard</a>
        <a href="{{ route('pengiriman.index') }}">📦 Pengiriman</a>
        <a href="{{ route('pelacakan.index') }}">📍 Pelacakan</a>
        <a href="{{ route('fasilitas.index') }}">🏢 Fasilitas</a>
        <a href="{{ url('/cuaca') }}" class="active">🌦️ Cuaca</a>
        <a href="{{ url('/berita') }}">📰 Berita</a>
        <a href="{{ url('/mata-uang') }}">💱 Mata Uang</a>
        <a href="{{ url('/waktu') }}">🕒 Waktu</a>
    </nav>
</aside>

<main class="main">

    <header class="topbar">
        <div>
            <h1>Informasi Cuaca</h1>
            <p>Pantau kondisi cuaca lokasi operasional dan pengiriman.</p>
        </div>

        <div class="date" id="currentDate">Memuat tanggal...</div>
    </header>

    <form class="search-box" id="weatherForm">
        <input
            type="text"
            id="locationInput"
            placeholder="Cari kota, contoh: Kabanjahe"
            value="Kabanjahe"
            required
        >
        <button type="submit">Cari Cuaca</button>
    </form>

    <section class="weather-main">
        <div>
            <h2 id="locationName">Kabanjahe</h2>
            <div class="location">Kondisi cuaca saat ini</div>

            <div class="temperature" id="temperature">--°C</div>
            <div class="weather-condition" id="condition">
                Mengambil data cuaca...
            </div>

            <div class="updated" id="updated">
                Menunggu data API
            </div>
        </div>

        <div class="weather-icon" id="weatherIcon">🌦️</div>
    </section>

    <div class="grid">

        <div class="card">
            <div class="card-icon">💧</div>
            <div class="card-label">Kelembapan</div>
            <div class="card-value" id="humidity">--%</div>
        </div>

        <div class="card">
            <div class="card-icon">🌬️</div>
            <div class="card-label">Kecepatan Angin</div>
            <div class="card-value" id="windSpeed">-- km/j</div>
        </div>

        <div class="card">
            <div class="card-icon">🧭</div>
            <div class="card-label">Arah Angin</div>
            <div class="card-value" id="windDirection">--</div>
        </div>

        <div class="card">
            <div class="card-icon">🌧️</div>
            <div class="card-label">Curah Hujan</div>
            <div class="card-value" id="rain">-- mm</div>
        </div>

    </div>

    <div class="warning" id="weatherWarning">
        ⚠️ Peringatan cuaca akan ditampilkan berdasarkan data aktual.
    </div>

    <section class="section">
        <div class="section-header">
            <h3>📅 Prakiraan Cuaca</h3>
            <span>Prakiraan harian</span>
        </div>

        <div class="forecast" id="forecast">
            <div class="forecast-item">Memuat prakiraan...</div>
        </div>
    </section>

    <section class="section">
        <div class="section-header">
            <h3>🗺️ Peta Lokasi Cuaca</h3>
            <span>OpenStreetMap</span>
        </div>

        <div id="weatherMap"></div>
        <div class="status" id="mapStatus">
            Peta menampilkan lokasi pencarian cuaca.
        </div>
    </section>

    <section class="section">
        <div class="section-header">
            <h3>🕒 Riwayat Pembaruan</h3>
            <span>Data cuaca terbaru</span>
        </div>

        <div id="history">
            Belum ada pembaruan.
        </div>

        <div class="error" id="errorMessage"></div>
    </section>

</main>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    const API_URL = 'https://api.open-meteo.com/v1/forecast';

    const dateElement = document.getElementById('currentDate');

    dateElement.textContent = new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });

    let map = L.map('weatherMap').setView([3.1, 98.5], 10);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let marker = L.marker([3.1, 98.5]).addTo(map);

    function weatherDescription(code) {
        const descriptions = {
            0: ['Cerah', '☀️'],
            1: ['Cerah berawan', '🌤️'],
            2: ['Berawan sebagian', '⛅'],
            3: ['Berawan', '☁️'],
            45: ['Kabut', '🌫️'],
            48: ['Kabut tebal', '🌫️'],
            51: ['Gerimis ringan', '🌦️'],
            53: ['Gerimis sedang', '🌦️'],
            55: ['Gerimis lebat', '🌧️'],
            61: ['Hujan ringan', '🌧️'],
            63: ['Hujan sedang', '🌧️'],
            65: ['Hujan lebat', '🌧️'],
            71: ['Salju ringan', '❄️'],
            80: ['Hujan lokal', '🌦️'],
            81: ['Hujan lokal sedang', '🌧️'],
            82: ['Hujan lokal lebat', '⛈️'],
            95: ['Badai petir', '⛈️']
        };

        return descriptions[code] || ['Kondisi tidak diketahui', '🌦️'];
    }

    function windDirection(degrees) {
        const directions = [
            'Utara', 'Timur Laut', 'Timur', 'Tenggara',
            'Selatan', 'Barat Daya', 'Barat', 'Barat Laut'
        ];

        return directions[Math.round(degrees / 45) % 8];
    }

    async function searchLocation(city) {
        const response = await fetch(
            'https://geocoding-api.open-meteo.com/v1/search?name=' +
            encodeURIComponent(city) +
            '&count=1&language=id&format=json'
        );

        if (!response.ok) {
            throw new Error('Tidak dapat mencari lokasi.');
        }

        const result = await response.json();

        if (!result.results || result.results.length === 0) {
            throw new Error('Lokasi tidak ditemukan. Coba nama kota lain.');
        }

        return result.results[0];
    }

    async function loadWeather(city) {
        const errorElement = document.getElementById('errorMessage');
        errorElement.style.display = 'none';

        document.getElementById('condition').textContent =
            'Mengambil data cuaca aktual...';

        try {
            const location = await searchLocation(city);

            const url = API_URL +
                '?latitude=' + location.latitude +
                '&longitude=' + location.longitude +
                '&current=temperature_2m,relative_humidity_2m,apparent_temperature,precipitation,rain,weather_code,wind_speed_10m,wind_direction_10m' +
                '&daily=weather_code,temperature_2m_max,temperature_2m_min,precipitation_probability_max' +
                '&timezone=auto&forecast_days=5';

            const response = await fetch(url);

            if (!response.ok) {
                throw new Error('API cuaca sedang tidak tersedia.');
            }

            const data = await response.json();

            if (!data.current) {
                throw new Error('Data cuaca kosong.');
            }

            const current = data.current;
            const weather = weatherDescription(current.weather_code);

            document.getElementById('locationName').textContent =
                location.name + ', ' + (location.country || '');

            document.getElementById('temperature').textContent =
                Math.round(current.temperature_2m) + '°C';

            document.getElementById('condition').textContent = weather[0];

            document.getElementById('weatherIcon').textContent = weather[1];

            document.getElementById('humidity').textContent =
                current.relative_humidity_2m + '%';

            document.getElementById('windSpeed').textContent =
                current.wind_speed_10m + ' km/j';

            document.getElementById('windDirection').textContent =
                windDirection(current.wind_direction_10m);

            document.getElementById('rain').textContent =
                current.rain + ' mm';

            document.getElementById('updated').textContent =
                'Diperbarui: ' +
                new Date(current.time).toLocaleString('id-ID') +
                ' • Sumber: Open-Meteo';

            marker.setLatLng([location.latitude, location.longitude]);

            marker.bindPopup(
                '<b>' + location.name + '</b><br>' +
                weather[0] + '<br>' +
                current.temperature_2m + '°C'
            );

            map.setView([location.latitude, location.longitude], 10);

            renderForecast(data.daily);

            renderWarning(current);

            document.getElementById('mapStatus').textContent =
                'Lokasi: ' + location.name +
                ' | Koordinat: ' +
                location.latitude + ', ' + location.longitude;

            document.getElementById('history').textContent =
                location.name + ' — ' +
                weather[0] + ' — ' +
                new Date(current.time).toLocaleString('id-ID');

        } catch (error) {
            errorElement.textContent = error.message;
            errorElement.style.display = 'block';

            document.getElementById('condition').textContent =
                'Data cuaca tidak dapat dimuat';

            document.getElementById('updated').textContent =
                'Periksa koneksi internet lalu coba lagi.';
        }
    }

    function renderForecast(daily) {
        const container = document.getElementById('forecast');
        container.innerHTML = '';

        daily.time.forEach((date, index) => {
            const weather = weatherDescription(daily.weather_code[index]);

            const item = document.createElement('div');
            item.className = 'forecast-item';

            const day = new Date(date + 'T00:00:00')
                .toLocaleDateString('id-ID', {
                    weekday: 'short',
                    day: 'numeric'
                });

            item.innerHTML = `
                <div class="day">${day}</div>
                <div class="icon">${weather[1]}</div>
                <div class="temp">
                    ${Math.round(daily.temperature_2m_max[index])}° /
                    ${Math.round(daily.temperature_2m_min[index])}°
                </div>
                <div class="day">${weather[0]}</div>
                <div class="day">
                    Hujan:
                    ${daily.precipitation_probability_max[index] ?? '--'}%
                </div>
            `;

            container.appendChild(item);
        });
    }

    function renderWarning(current) {
        const warning = document.getElementById('weatherWarning');

        if (current.rain >= 5) {
            warning.textContent =
                '⚠️ Hujan terdeteksi. Perhatikan kondisi perjalanan dan pengiriman.';
        } else if (current.wind_speed_10m >= 40) {
            warning.textContent =
                '⚠️ Angin cukup kencang. Periksa kondisi operasional pengiriman.';
        } else {
            warning.textContent =
                '✅ Tidak ada peringatan otomatis berdasarkan ambang sederhana halaman ini. Tetap periksa informasi resmi.';
        }
    }

    document.getElementById('weatherForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const city = document.getElementById('locationInput').value.trim();

        if (city) {
            loadWeather(city);
        }
    });

    loadWeather('Kabanjahe');
</script>

</body>
</html>
