<?php $__env->startSection('content'); ?>
<div class="p-6 min-h-screen bg-white">
    <div class="max-w-6xl mx-auto">
        
        
        <div class="bg-yellow-100 border border-yellow-400 p-4 rounded mb-6" style="display: none;">
            <h4 class="font-bold">Debug Data:</h4>
            <p>Label: <?php echo e(json_encode($labelPendapatan)); ?></p>
            <p>Data Pendapatan: <?php echo e(json_encode($dataPendapatan)); ?></p>
            <p>Data Reservasi: <?php echo e(json_encode($dataReservasi)); ?></p>
        </div>

        
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-800">Dashboard Admin - Aretha Beauty</h2>

            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button 
                    type="submit"
                    onclick="return confirm('Apakah Anda yakin ingin logout?')"
                    class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition"
                >
                    Logout
                </button>
            </form>
        </div> 

        
        <div class="overflow-x-auto mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 min-w-[640px]">
                <div class="bg-white border border-purple-100 rounded-2xl p-4 shadow">
                    <p class="text-sm text-gray-500">Reservasi Hari Ini</p>
                    <p class="text-2xl font-bold text-purple-800 mt-1"><?php echo e($jumlahReservasiHariIni); ?></p>
                </div>
                <div class="bg-white border border-purple-100 rounded-2xl p-4 shadow">
                    <p class="text-sm text-gray-500">Pendapatan Hari Ini</p>
                    <p class="text-2xl font-bold text-purple-800 mt-1">Rp <?php echo e(number_format($pendapatanHariIni, 0, ',', '.')); ?></p>
                </div>
                <div class="bg-white border border-purple-100 rounded-2xl p-4 shadow">
                    <p class="text-sm text-gray-500">Total Pelanggan Hari Ini</p>
                    <p class="text-2xl font-bold text-purple-800 mt-1"><?php echo e($totalPelangganHariIni); ?></p>
                </div>
            </div>
        </div>

        
        <div class="flex justify-end mb-6">
            <form method="GET">
                <label class="mr-2 text-gray-600 font-medium">Periode:</label>
                <select name="periode" onchange="this.form.submit()" class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:ring-purple-400 focus:border-purple-400">
                    <option value="mingguan" <?php echo e($periode == 'mingguan' ? 'selected' : ''); ?>>Mingguan</option>
                    <option value="bulanan" <?php echo e($periode == 'bulanan' ? 'selected' : ''); ?>>Bulanan</option>
                    <option value="tahunan" <?php echo e($periode == 'tahunan' ? 'selected' : ''); ?>>Tahunan</option>
                </select>
            </form>
        </div>

        
        

        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            
            <div class="bg-white border border-purple-100 rounded-2xl shadow p-6">
                <h3 class="text-lg font-semibold text-purple-800 mb-4">Grafik Reservasi</h3>
                <div class="relative" style="height: 300px;">
                    <canvas id="reservasiBarChart"></canvas>
                </div>
            </div>

            
            <div class="bg-white border border-purple-100 rounded-2xl shadow p-6">
                <h3 class="text-lg font-semibold text-purple-800 mb-4">Grafik Pendapatan</h3>
                <div class="relative" style="height: 300px;">
                    <canvas id="pendapatanBarChart"></canvas>
                </div>
            </div>

            
            <div class="bg-white border border-purple-100 rounded-2xl shadow p-6">
                <h3 class="text-lg font-semibold text-purple-800 mb-4">Grafik Pelanggan</h3>
                <div class="relative" style="height: 300px;">
                    <canvas id="pelangganBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Fungsi untuk inisialisasi bar charts
    function initializeBarCharts() {
        console.log('Initializing bar charts...');
        
        // Data dari controller
        const labels = <?php echo json_encode($labelPendapatan ?? [], 15, 512) ?>;
        const dataPendapatan = <?php echo json_encode($dataPendapatan ?? [], 15, 512) ?>;
        const dataReservasi = <?php echo json_encode($dataReservasi ?? [], 15, 512) ?>;
        const dataPelanggan = <?php echo json_encode($dataPelanggan ?? [], 15, 512) ?>;

        console.log('Bar chart data:', {
            labels: labels,
            pendapatan: dataPendapatan,
            reservasi: dataReservasi,
            pelanggan: dataPelanggan
        });

        // Konfigurasi umum untuk bar charts
        const commonOptions = {
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
                        color: '#f3f4f6'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        };

        // Bar Chart Reservasi
        const reservasiBarChart = document.getElementById('reservasiBarChart');
        if (reservasiBarChart && dataReservasi.length > 0) {
            try {
                new Chart(reservasiBarChart, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: dataReservasi,
                            backgroundColor: '#a855f7',
                            borderColor: '#7c3aed',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            ...commonOptions.plugins,
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.raw} reservasi`;
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Reservasi bar chart created successfully');
            } catch (error) {
                console.error('Error creating reservasi bar chart:', error);
            }
        }

        // Bar Chart Pendapatan
        const pendapatanBarChart = document.getElementById('pendapatanBarChart');
        if (pendapatanBarChart && dataPendapatan.length > 0) {
            try {
                new Chart(pendapatanBarChart, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: dataPendapatan,
                            backgroundColor: '#10b981',
                            borderColor: '#059669',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            ...commonOptions.plugins,
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `Rp ${context.raw.toLocaleString('id-ID')}`;
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Pendapatan bar chart created successfully');
            } catch (error) {
                console.error('Error creating pendapatan bar chart:', error);
            }
        }

        // Bar Chart Pelanggan
        const pelangganBarChart = document.getElementById('pelangganBarChart');
        if (pelangganBarChart && dataPelanggan.length > 0) {
            try {
                new Chart(pelangganBarChart, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: dataPelanggan,
                            backgroundColor: '#3b82f6',
                            borderColor: '#2563eb',
                            borderWidth: 1,
                            borderRadius: 4
                        }]
                    },
                    options: {
                        ...commonOptions,
                        plugins: {
                            ...commonOptions.plugins,
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.raw} pelanggan`;
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('Pelanggan bar chart created successfully');
            } catch (error) {
                console.error('Error creating pelanggan bar chart:', error);
            }
        }
    }

    // Tunggu sampai DOM siap dan Chart.js terload
    if (typeof Chart !== 'undefined') {
        document.addEventListener('DOMContentLoaded', initializeBarCharts);
    } else {
        console.error('Chart.js not loaded');
        
        // Coba load ulang Chart.js jika gagal
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
        script.onload = initializeBarCharts;
        document.head.appendChild(script);
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\ASVS\Documents\PBL S5\fix\abee\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>