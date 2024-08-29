document.addEventListener('DOMContentLoaded', function() {
    fetch('uniqui.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('username').value = data.username;
            } else {
                console.error('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error fetching user:', error));
});
