<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Architecture Benchmark</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-gray-100 p-8 font-sans">

    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">System Performance Benchmark</h1>
            <p class="text-gray-500 mt-2">Komparasi: Single Table (100k Rows) vs Split Tables (50k Rows)</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- STAGE 1 --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col">
                <h2 class="text-lg font-bold text-gray-800 mb-1"><span
                        class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-sm mr-2">Stage 1</span>Basic Fetch
                </h2>
                <p class="text-sm text-gray-500 mb-6">Mengambil 100 data rumah terbaru tanpa filter kompleks.</p>

                <div class="flex-grow space-y-4">
                    <div class="p-3 bg-red-50 rounded-lg border border-red-100">
                        <div class="text-xs text-red-600 font-semibold mb-1">Old Architecture (Single Table)</div>
                        <div class="flex justify-between items-end">
                            <div class="text-2xl font-bold text-gray-800" id="s1-old-time">0 <span
                                    class="text-sm font-normal text-gray-500">ms</span></div>
                            <div class="text-xs text-gray-500" id="s1-old-rows">0 rows</div>
                        </div>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-100">
                        <div class="text-xs text-emerald-700 font-semibold mb-1">New Architecture (Split Tables)</div>
                        <div class="flex justify-between items-end">
                            <div class="text-2xl font-bold text-gray-800" id="s1-new-time">0 <span
                                    class="text-sm font-normal text-gray-500">ms</span></div>
                            <div class="text-xs text-gray-500" id="s1-new-rows">0 rows</div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex gap-2">
                    <button onclick="runTest(1, 1)"
                        class="flex-1 bg-gray-800 hover:bg-gray-900 text-white text-sm py-2 rounded-lg transition-colors">1
                        User</button>
                    <button onclick="runTest(1, 10)"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm py-2 rounded-lg transition-colors flex justify-center items-center"><i
                            class="bi bi-people-fill mr-1.5"></i> 10 Users</button>
                </div>
            </div>

            {{-- STAGE 2 --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col">
                <h2 class="text-lg font-bold text-gray-800 mb-1"><span
                        class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-sm mr-2">Stage 2</span>Medium Filter
                </h2>
                <p class="text-sm text-gray-500 mb-6">Kueri dengan parameter harga minimum dan batasan luas tanah.</p>

                <div class="flex-grow space-y-4">
                    <div class="p-3 bg-red-50 rounded-lg border border-red-100">
                        <div class="text-xs text-red-600 font-semibold mb-1">Old Architecture</div>
                        <div class="flex justify-between items-end">
                            <div class="text-2xl font-bold text-gray-800" id="s2-old-time">0 <span
                                    class="text-sm font-normal text-gray-500">ms</span></div>
                            <div class="text-xs text-gray-500" id="s2-old-rows">0 rows</div>
                        </div>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-100">
                        <div class="text-xs text-emerald-700 font-semibold mb-1">New Architecture</div>
                        <div class="flex justify-between items-end">
                            <div class="text-2xl font-bold text-gray-800" id="s2-new-time">0 <span
                                    class="text-sm font-normal text-gray-500">ms</span></div>
                            <div class="text-xs text-gray-500" id="s2-new-rows">0 rows</div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex gap-2">
                    <button onclick="runTest(2, 1)"
                        class="flex-1 bg-gray-800 hover:bg-gray-900 text-white text-sm py-2 rounded-lg transition-colors">1
                        User</button>
                    <button onclick="runTest(2, 10)"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm py-2 rounded-lg transition-colors flex justify-center items-center"><i
                            class="bi bi-people-fill mr-1.5"></i> 10 Users</button>
                </div>
            </div>

            {{-- STAGE 3 --}}
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex flex-col border-t-4 border-t-orange-500">
                <h2 class="text-lg font-bold text-gray-800 mb-1"><span
                        class="bg-orange-100 text-orange-700 px-2 py-0.5 rounded text-sm mr-2">Stage 3</span>Heavy Query
                </h2>
                <p class="text-sm text-gray-500 mb-6">Fulltext search, multiple filters, dan reverse sorting.</p>

                <div class="flex-grow space-y-4">
                    <div class="p-3 bg-red-50 rounded-lg border border-red-100">
                        <div class="text-xs text-red-600 font-semibold mb-1">Old Architecture</div>
                        <div class="flex justify-between items-end">
                            <div class="text-2xl font-bold text-gray-800" id="s3-old-time">0 <span
                                    class="text-sm font-normal text-gray-500">ms</span></div>
                            <div class="text-xs text-gray-500" id="s3-old-rows">0 rows</div>
                        </div>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-100">
                        <div class="text-xs text-emerald-700 font-semibold mb-1">New Architecture</div>
                        <div class="flex justify-between items-end">
                            <div class="text-2xl font-bold text-gray-800" id="s3-new-time">0 <span
                                    class="text-sm font-normal text-gray-500">ms</span></div>
                            <div class="text-xs text-gray-500" id="s3-new-rows">0 rows</div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex gap-2">
                    <button onclick="runTest(3, 1)"
                        class="flex-1 bg-gray-800 hover:bg-gray-900 text-white text-sm py-2 rounded-lg transition-colors">1
                        User</button>
                    <button onclick="runTest(3, 10)"
                        class="flex-1 bg-orange-600 hover:bg-orange-700 text-white text-sm py-2 rounded-lg transition-colors flex justify-center items-center"><i
                            class="bi bi-people-fill mr-1.5"></i> 10 Users</button>
                </div>
            </div>

        </div>
    </div>

    <script>
        async function runTest(stage, concurrentUsers) {
            // Set Loading UI
            document.getElementById(`s${stage}-old-time`).innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i>';
            document.getElementById(`s${stage}-new-time`).innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i>';

            let totalOldTime = 0;
            let totalNewTime = 0;
            let rowsOld = 0;
            let rowsNew = 0;

            try {
                // Front-end timer untuk mengukur beban server secara keseluruhan saat concurrent
                const clientStartTime = performance.now();

                // Buat array of promises (1 request jika 1 user, 10 request jika concurrent)
                const requests = Array(concurrentUsers).fill().map(() => fetch(`/api/benchmark/${stage}`));
                const responses = await Promise.all(requests);

                for (const response of responses) {
                    const data = await response.json();
                    // Akumulasi waktu database murni dari server
                    totalOldTime += data.old.time;
                    totalNewTime += data.new.time;
                    rowsOld = data.old.rows; // Asumsi jumlah row selalu sama per request
                    rowsNew = data.new.rows;
                }

                const clientTotalTime = performance.now() - clientStartTime;

                // Hitung Rata-rata waktu eksekusi database per user
                const avgOldTime = (totalOldTime / concurrentUsers).toFixed(2);
                const avgNewTime = (totalNewTime / concurrentUsers).toFixed(2);

                // Update UI
                document.getElementById(`s${stage}-old-time`).innerHTML =
                    `${avgOldTime} <span class="text-sm font-normal text-gray-500">ms</span>`;
                document.getElementById(`s${stage}-old-rows`).innerText = `${rowsOld} rows`;

                document.getElementById(`s${stage}-new-time`).innerHTML =
                    `${avgNewTime} <span class="text-sm font-normal text-gray-500">ms</span>`;
                document.getElementById(`s${stage}-new-rows`).innerText = `${rowsNew} rows`;

                if (concurrentUsers > 1) {
                    console.log(
                        `[Stage ${stage}] Total Client Response Time for 10 users: ${clientTotalTime.toFixed(2)} ms`
                    );
                }

            } catch (error) {
                console.error("Benchmark failed:", error);
                alert("Gagal menjalankan benchmark. Cek console log.");
            }
        }
    </script>
</body>

</html>
