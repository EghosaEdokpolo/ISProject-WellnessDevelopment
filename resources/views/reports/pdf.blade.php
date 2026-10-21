<!DOCTYPE html>
<html>
<head>
    <title>WPMIS Departmental Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #02338d; padding-bottom: 10px; }
        .header h1 { color: #02338d; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #02338d; color: white; }
        .status-engaged { color: green; font-weight: bold; }
        .status-developing { color: orange; font-weight: bold; }
        .status-disengaged { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Strathmore University</h1>
        <p>WPMIS — Departmental Engagement Report</p>
        <p>Generated on: {{ date('d M Y, H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Department</th>
                <th>Code</th>
                <th>Total Staff</th>
                <th>Unique Attendees</th>
                <th>Engagement Rate</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ranking['all_departments'] as $row)
                <tr>
                    <td>{{ $row['department'] }}</td>
                    <td>{{ $row['code'] }}</td>
                    <td>{{ $row['total_staff'] }}</td>
                    <td>{{ $row['unique_attendees'] }}</td>
                    <td>{{ $row['engagement_rate'] }}%</td>
                    <td class="status-{{ $row['status'] }}">{{ strtoupper($row['status']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>