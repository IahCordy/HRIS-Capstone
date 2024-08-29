document.addEventListener('DOMContentLoaded', function() {
    fetchLeaveRequests();
});

function fetchLeaveRequests() {
    fetch('fetch_hr_leave.php')
        .then(response => response.json())
        .then(data => {
            const leaveRequestsTable = document.getElementById('leave-requests');
            leaveRequestsTable.innerHTML = '';
            data.forEach(request => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${request.Emp_name}</td>
                    <td>${request.leave_type}</td>
                    <td>${request.from_when}</td>
                    <td>${request.to_when}</td>
                    <td>${request.reason}</td>
                    <td>${request.leave_datelog}</td>
                    <td>
                        <button class="approve" onclick="handleAction(${request.leave_id}, 'approve')">Approve</button>
                        <button class="reject" onclick="handleAction(${request.leave_id}, 'reject')">Reject</button>
                    </td>
                `;
                leaveRequestsTable.appendChild(row);
            });
        });
}

function handleAction(leave_id, action) {
    fetch('hr_leave.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ leave_id, action }),
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
        fetchLeaveRequests();
    });
}
