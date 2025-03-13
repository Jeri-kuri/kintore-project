document.addEventListener("DOMContentLoaded",function(){
    
    document.getElementById('logoutButton').addEventListener('click', function() {
        if (confirm("Are you sure you want to logout?")) {
            window.location.href = "<?php echo Uri::create('login/logout'); ?>";
        }
    });
    
});