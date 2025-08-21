<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Dependency Table</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #d2e3ff, #e9f0ff);
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 30px;
        }
        h1 {
            color: #2b6cb0;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #2b6cb0;
            color: white;
            font-weight: bold;
            position: sticky;
            top: 0;
        }
        tr:nth-child(even) {
            background-color: #f2f7ff;
        }
        tbody tr:hover {
            background-color: #e6f0ff;
            transition: background-color 0.2s;
        }
        .alert-empty {
            text-align: center;
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeaa7;
            padding: 12px;
            border-radius: 5px;
        }
        .table-wrapper {
            overflow-x: auto;
            max-height: 70vh;
            overflow-y: auto;
        }
        .debug-info {
            font-size: 12px;
            color: #6c757d;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
            display: none; /* Sembunyikan di production */
        }
        .btn-primary {
            background-color: #2b6cb0;
            border-color: #2b6cb0;
        }
        .btn-primary:hover {
            background-color: #1a56a0;
            border-color: #1a56a0;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Activity Dependency Table</h1>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>State</th>
                    <th>Activity Name</th>
                    <th>ID</th>
                    <th>Dependency</th>
                </tr>
            </thead>
            <tbody>
        <?php
        // Debug information - helpful for troubleshooting
        $debugInfo = "";
        
        if(empty($data)) {
            $debugInfo = "Data is empty or cannot be parsed.";
            echo '<tr>
                <td colspan="5" class="alert-empty">Tidak ada data yang tersedia atau file tidak dapat diparse.</td>
            </tr>';
        } else {
            // Check the structure of the first item for debugging
            $firstItem = reset($data);
            if (!isset($firstItem['no'])) {
                $debugInfo = "Key 'no' is missing in data structure.";
            }
            
            foreach($data as $index => $row) {
                // Pastikan semua key yang diperlukan ada
                $no = isset($row['no']) ? $row['no'] : 'N/A';
                $state = isset($row['state']) ? $row['state'] : 'N/A';
                $activity_name = isset($row['activity_name']) ? $row['activity_name'] : 'N/A';
                $id = isset($row['id']) ? $row['id'] : 'N/A';
                
                // Handle dependency field
                $dependency = 'N/A';
                if (isset($row['dependency'])) {
                    if (is_array($row['dependency'])) {
                        $dependency = !empty($row['dependency']) ? implode(', ', $row['dependency']) : 'None';
                    } else {
                        $dependency = $row['dependency'];
                    }
                }
                
                echo '<tr>
                    <td>' . htmlspecialchars($no) . '</td>
                    <td>' . htmlspecialchars($state) . '</td>
                    <td>' . htmlspecialchars($activity_name) . '</td>
                    <td>' . htmlspecialchars($id) . '</td>
                    <td>' . htmlspecialchars($dependency) . '</td>
                </tr>';
            }
        }
        ?>
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between mt-3">
        <button onclick="toggleDebug()" class="btn btn-info btn-sm">Toggle Debug Info</button>
        <a href="{{ route('tableadg') }}" class="btn btn-primary">Lihat Graph</a>
    </div>

    <div id="debugSection" class="debug-info">
        <strong>Debug Information:</strong><br>
        <?php echo $debugInfo; ?><br>
        Data Structure: <?php echo isset($firstItem) ? json_encode($firstItem) : 'No data'; ?>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleDebug() {
        const debugSection = document.getElementById('debugSection');
        debugSection.style.display = debugSection.style.display === 'none' ? 'block' : 'none';
    }
</script>
</body>
</html>