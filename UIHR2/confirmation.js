document.addEventListener('DOMContentLoaded', function () {
    fetch('confirmation.php')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('ConfirmationTableBody');
            tableBody.innerHTML = ''; // Clear the loading message

            if (data.length > 0) {
                data.forEach(user => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${user.id}</td>
                        <td>${user.username}</td>
                        <td>${user.email}</td>
                        <td>${user.role}</td>
                        <td><button onclick="approveUser(${user.id})">Approve</button></td>
                    `;
                    tableBody.appendChild(row);
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No pending approvals</td></tr>';
            }
        })
        .catch(error => console.error('Error fetching data:', error));
});

function approveUser(userId) {
    if (confirm("Are you sure you want to approve this user?")) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "confirmationapproval.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert("User approved successfully!");
                    location.reload(); // Reload the page to update the list
                } else {
                    alert("Error: " + response.message);
                }
            }
        };
        xhr.send("id=" + userId);
    }
}
