<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Monitoring Gas ESP32</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .card { border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .chart-wrapper { position: relative; height: 420px; width: 100%; }
        .table-responsive { max-height: 400px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Header & Tombol Export CSV -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold text-dark m-0">Real-time Gas Leak Detection System</h2>
            <a href="export.php" class="btn btn-success fw-bold shadow-sm">
                📥 Unduh Log (CSV / Excel)
            </a>
        </div>

        <!-- Ringkasan Angka Sensor & Status -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card p-3 text-center">
                    <h5 class="text-muted mb-2">Nilai Sensor Saat Ini</h5>
                    <h1 class="display-3 fw-bold text-primary m-0" id="latest-gas">--</h1>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3 text-center">
                    <h5 class="text-muted mb-2">Status Keamanan</h5>
                    <h1 class="display-3 fw-bold m-0" id="latest-status">--</h1>
                </div>
            </div>
        </div>

        <!-- Grafik Utama (Ukuran Tinggi 420px) -->
        <div class="card p-4 mb-4">
            <h5 class="card-title mb-3 fw-bold">Grafik Kebocoran Gas (Real-Time Dynamic)</h5>
            <div class="chart-wrapper">
                <canvas id="gasChart"></canvas>
            </div>
        </div>

        <!-- Tabel Riwayat Log Data -->
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0 fw-bold">Riwayat Log Terakhir</h5>
                <span class="badge bg-secondary" id="total-logs">0 Data Ditampilkan</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Waktu</th>
                            <th>Nilai Gas</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="log-table-body">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('gasChart').getContext('2d');
        const gasChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Kadar Gas (ADC)',
                    data: [],
                    borderColor: '#20c997',
                    backgroundColor: 'rgba(32, 201, 151, 0.15)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, grid: { color: '#e9ecef' } },
                    x: { grid: { display: false } }
                }
            }
        });

        function fetchData() {
            fetch('api.php?limit=50')
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        const latest = data[0];
                        document.getElementById('latest-gas').innerText = latest.gas_value;
                        const statusElem = document.getElementById('latest-status');
                        statusElem.innerText = latest.status;

                        if (latest.status === 'BAHAYA' || parseInt(latest.gas_value) > 1000) {
                            statusElem.className = 'display-3 fw-bold text-danger m-0';
                        } else {
                            statusElem.className = 'display-3 fw-bold text-success m-0';
                        }

                        const reversedData = [...data].reverse();
                        gasChart.data.labels = reversedData.map(item => item.created_at);
                        gasChart.data.datasets[0].data = reversedData.map(item => item.gas_value);
                        gasChart.update();

                        let tableHTML = '';
                        data.forEach(item => {
                            const statusBadge = item.status === 'BAHAYA' 
                                ? '<span class="badge bg-danger">BAHAYA</span>' 
                                : '<span class="badge bg-success">NORMAL</span>';
                            tableHTML += `
                                <tr>
                                    <td>${item.created_at}</td>
                                    <td>${item.gas_value}</td>
                                    <td>${statusBadge}</td>
                                </tr>
                            `;
                        });
                        document.getElementById('log-table-body').innerHTML = tableHTML;
                        document.getElementById('total-logs').innerText = data.length + " Data Ditampilkan";
                    }
                })
                .catch(err => console.error("Error fetching data:", err));
        }

        setInterval(fetchData, 2000);
        fetchData();
    </script>
</body>
</html>
