<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kasus Uji</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #d2e3ff, #e9f0ff);
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 { 
            color: #2b6cb0; 
            margin-bottom: 20px; 
            text-align: center;
        }

        .test-case-flow-container {
            margin-top: 30px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
        }
        
        .test-case-flow-container ol {
            padding-left: 20px;
            margin: 0;
        }

        .test-case-flow-container li {
            margin-bottom: 10px;
            font-size: 16px;
            color: #4a5568;
            line-height: 1.5;
        }
    </style>
</head>
<body>

    <div class="test-case-flow-container">
        <h2>Kasus Uji</h2>
        <ol id="test-case-list"></ol>
    </div>

    <div class="test-case-flow-container">
        <h2>Keterangan Kasus Uji</h2>
        <ol id="test-case-description-list"></ol>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetch('process_puml.php') // Ganti dengan nama skrip PHP Anda
                .then(response => response.json())
                .then(data => {
                    displayTestCases(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    const flowList = document.getElementById('test-case-list');
                    const descriptionList = document.getElementById('test-case-description-list');
                    flowList.innerHTML = "<li>Gagal memuat data.</li>";
                    descriptionList.innerHTML = "<li>Gagal memuat data.</li>";
                });
        });

        function displayTestCases(testCaseFlows) {
            const flowList = document.getElementById('test-case-list');
            const descriptionList = document.getElementById('test-case-description-list');

            if (testCaseFlows.length === 0) {
                flowList.innerHTML = "<li>Tidak ada jalur kasus uji yang ditemukan.</li>";
                descriptionList.innerHTML = "<li>Tidak ada keterangan kasus uji yang ditemukan.</li>";
                return;
            }

            // Menampilkan Jalur ID
            testCaseFlows.forEach(path => {
                const li = document.createElement('li');
                const ids = path.map(item => item.id);
                li.innerHTML = ids.join(" &#10140; ");
                flowList.appendChild(li);
            });

            // Menampilkan Keterangan Kasus Uji (Nama Aktivitas)
            testCaseFlows.forEach(path => {
                const li = document.createElement('li');
                const names = path.map(item => item.activity_name);
                li.innerHTML = names.join(" &#10140; ");
                descriptionList.appendChild(li);
            });
        }
    </script>
</body>
</html>