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
            width: 80%;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 50px;
        }
        h1 {
            color: #2b6cb0;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
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
        }
        tr:nth-child(even) {
            background-color: #f2f7ff;
        }
        tbody tr:hover {
            background-color: #e6f0ff;
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
        @if(empty($data))
            <tr>
                <td colspan="5" style="text-align: center; color: gray;">Tidak ada data yang tersedia atau file tidak dapat diparse.</td>
            </tr>
        @else
            @foreach($data as $row)
                <tr>
                    <td>{{ $row['no'] }}</td>
                    <td>{{ $row['state'] }}</td>
                    <td>{{ $row['activity_name'] }}</td>
                    <td>{{ $row['id'] }}</td>
                    <td>
                        @if(is_array($row['dependency']))
                            {{ implode(', ', $row['dependency']) }}
                        @else
                            {{ $row['dependency'] }}
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
        </table>
    </div>

 <div class="d-flex justify-content-end mt-3">
        <a href="{{ route('tableadg') }}" class="btn btn-primary">Lihat Graph</a>
    </div>
</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
