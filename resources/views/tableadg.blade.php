<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Dependency Graph</title>
    <script type="text/javascript" src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
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

        table {
            border-collapse: collapse;
            width: auto;
            min-width: 400px;
            margin-bottom: 30px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        table th, table td {
            border: 1px solid #90caf9;
            padding: 12px 15px;
            text-align: center;
            font-size: 14px;
        }

        table th { 
            background-color: #2b6cb0; 
            color: white; 
            text-transform: uppercase;
        }

        #graph {
            width: 100%;
            height: 500px;
            border: 2px solid #2b6cb0;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .button-container {
            margin-top: 20px;
        }

        .button-container button {
            background-color: #2b6cb0;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        
        .button-container button:hover {
            background-color: #1a4f8a;
        }
    </style>
</head>
<body>

    <h2>Activity Dependency Graph</h2>

    <table>
        <thead>
            <tr>
                <th>Activity Name</th>
                <th>Dependency</th>
                <th>ID</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
            <tr>
                <td>{{ $row['activity_name'] }}</td>
                <td>
                    @if (is_array($row['dependency']))
                        {{ implode(',', $row['dependency']) }}
                    @else
                        {{ $row['dependency'] }}
                    @endif
                </td>
                <td>{{ $row['id'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Activity Graph</h2>
    <div id="graph"></div>
    
    <div class="button-container">
        <button onclick="window.location.href='hasil_kasus_uji';">Generate Test Case</button>
    </div>

    <script>
        const rawData = @json($data);
        let nodes = [];
        let edges = [];

        rawData.forEach(item => {
            nodes.push({
                id: item.id,
                label: String(item.id), 
                shape: "circle",
                size: 30,
                color: {
                    background: "#0b3d91",
                    border: "#05255d",
                    highlight: { background: "#1e5bb8", border: "#021a40" }
                },
                font: { color: "#fff", size: 14, face: "Arial", bold: true }
            });

            if (Array.isArray(item.dependency)) {
                item.dependency.forEach(dep => {
                    edges.push({ from: dep, to: item.id });
                });
            } else if (item.dependency) {
                edges.push({ from: item.dependency, to: item.id });
            }
        });

        const container = document.getElementById('graph');
        const data = { nodes: new vis.DataSet(nodes), edges: new vis.DataSet(edges) };

        const options = {
            physics: { enabled: false },
            interaction: { zoomView: false, dragView: true },
            edges: {
                arrows: { to: { enabled: true } },
                color: { color: "#2b6cb0" },
                smooth: { enabled: true, type: "dynamic" }
            }
        };

        const network = new vis.Network(container, data, options);
    </script>

</body>
</html>