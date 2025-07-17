<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<header class="dashboard-header">
   <div class="menu-toggle"><i class="fas fa-bars"></i></div>
   <div class="user-info">
       <div class="user-avatar">
           <?php
               $initials = '';
               if (isset($_SESSION['first_name'])) {
                   $initials = strtoupper(substr($_SESSION['first_name'], 0, 1));
                   if (isset($_SESSION['last_name'])) {
                       $initials .= strtoupper(substr($_SESSION['last_name'], 0, 1));
                   }
               }
               echo htmlspecialchars($initials);
           ?>
       </div>
       <div class="user-details">
           <h2>
               <?php 
               if (isset($_SESSION['first_name'])) {
                   echo htmlspecialchars($_SESSION['first_name']);
                   if (isset($_SESSION['last_name'])) {
                       echo ' ' . htmlspecialchars($_SESSION['last_name']);
                   }
               } else {
                   echo 'Guest';
               }
               ?>
           </h2>
           <p>Welcome back to your dashboard</p>
       </div>
   </div>
   <div class="header-actions">
       <div class="search-bar">
           <i class="fas fa-search"></i>
           <input type="text" placeholder="Search..." id="searchInput">
       </div>
       <div class="notification-icon">
           <i class="far fa-bell"></i>
           <span class="notification-badge">3</span>
       </div>
   </div>
</header>