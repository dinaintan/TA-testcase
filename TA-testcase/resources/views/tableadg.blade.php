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

        /* Styling untuk kotak-kotak nama aktivitas */
        .activity-box {
            display: inline-block;
            border: 1px solid #2b6cb0;
            padding: 5px 10px;
            margin: 0 5px;
            border-radius: 5px;
            background-color: #e9f0ff;
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

<div class="test-case-flow-container">
    <h2 style="text-align: center;">Kasus Uji</h2>
    <ol id="test-case-list"></ol>
</div>

<div class="test-case-flow-container">
    <h2 style="text-align: center;">Keterangan Kasus Uji</h2>
    <ol id="test-case-description-list"></ol>
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

    function generateTestCaseFlow() {
        const graph = new Map();
        const idMap = new Map();
        const allIds = new Set(rawData.map(item => item.id));
        const dependentIds = new Set();

        rawData.forEach(item => {
            idMap.set(item.id, item);
            if (Array.isArray(item.dependency)) {
                item.dependency.forEach(dep => {
                    if (!graph.has(dep)) graph.set(dep, []);
                    graph.get(dep).push(item.id);
                    dependentIds.add(item.id);
                });
            } else if (item.dependency) {
                if (!graph.has(item.dependency)) graph.set(item.dependency, []);
                graph.get(item.dependency).push(item.id);
                dependentIds.add(item.id);
            }
        });

        const rootNodes = [];
        allIds.forEach(id => {
            if (!dependentIds.has(id)) rootNodes.push(id);
        });

        const allPaths = [];
        
        function dfs(nodeId, path) {
            path.push(nodeId);
            
            const neighbors = graph.get(nodeId) || [];

            if (neighbors.length === 0) {
                allPaths.push([...path]);
            } else {
                neighbors.forEach(nextId => {
                    if (!path.includes(nextId)) {
                        dfs(nextId, path);
                    }
                });
            }
            
            path.pop();
        }

        rootNodes.forEach(root => dfs(root, []));

        const flowList = document.getElementById('test-case-list');
        flowList.innerHTML = '';
        const descriptionList = document.getElementById('test-case-description-list');
        descriptionList.innerHTML = '';

        if (allPaths.length === 0) {
            flowList.innerHTML = "<li>Tidak ada jalur kasus uji yang ditemukan</li>";
            descriptionList.innerHTML = "<li>Tidak ada keterangan kasus uji yang ditemukan</li>";
            return;
        }

        // Jalur ID
        allPaths.forEach(path => {
            const li = document.createElement('li');
            const activities = path.map(id => `${id}`);
            li.innerHTML =`${activities.join(" &#10140; ")}`;
            flowList.appendChild(li);
        });

        // Keterangan Kasus Uji (Nama Aktivitas)
        allPaths.forEach(path => {
            const li = document.createElement('li');
            const activityNames = path.map(id => {
                const item = idMap.get(id);
                return item.activity_name;
            });
            
            const activityBoxes = activityNames.map(name => {
                return `${name}`;
            });

            li.innerHTML = activityBoxes.join(" &#10140; ");
            descriptionList.appendChild(li);
        });
    }
    const exportPdfBtn = document.getElementById('exportPdfBtn');
    if (exportPdfBtn) {
        exportPdfBtn.addEventListener('click', () => {
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'pt', 'a4');
            const table = document.querySelector('table');
            const descriptionList = document.getElementById('test-case-description-list');
            
            pdf.html(table, {
                callback: function (pdf) {
                    pdf.addPage();
                    pdf.html(descriptionList, {
                        callback: function (pdf) {
                            pdf.save('activity_data.pdf');
                        },
                        x: 15,
                        y: 15
                    });
                },
                x: 15,
                y: 15
            });
        });
    }

    const exportExcelBtn = document.getElementById('exportExcelBtn');
    if (exportExcelBtn) {
        exportExcelBtn.addEventListener('click', () => {
            const data = @json($data);
            let csvContent = "Activity Name,ID,Dependency\n";
            data.forEach(item => {
                const dependency = Array.isArray(item.dependency) ? item.dependency.join(',') : item.dependency;
                csvContent += `${item.activity_name},${item.id},"${dependency}"\n`;
            });

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download", "activity_data.csv");
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', generateTestCaseFlow);
</script>

</body>
</html>