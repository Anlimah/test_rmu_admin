<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<meta name="author" content="Francis A. Anlimah">
<meta name="email" content="francis.ano.anlimah@gmail.com">
<meta name="website" content="https://linkedin.com/in/francis-anlimah">

<!-- Favicons -->
<link href="../assets/img/rmu-logo.png" rel="icon">
<link href="../assets/img/rmu-logo.png" rel="apple-touch-icon">

<!-- Google Fonts -->
<!--<link href="https://fonts.gstatic.com" rel="preconnect">-->
<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

<!-- Vendor CSS Files -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<!-- <link href="../assets/vendor/simple-datatables/style.css" rel="stylesheet"> -->

<!-- Template Main CSS File -->
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    :root {
        --primary-color: #2c3e50;
        --secondary-color: #34495e;
        --accent-color: #3498db;
        --text-color: #ecf0f1;
        --danger-color: #e74c3c;
        --success-color: #2ecc71;
    }

    body {
        display: flex;
        min-height: 100vh;
        background-color: #f5f6fa;
        overflow: hidden;
    }

    /* Sidebar Styles */
    .sidebar {
        width: 250px;
        background-color: var(--primary-color);
        color: var(--text-color);
        padding: 20px;
        transition: all 0.3s ease;
        position: fixed;
        height: 100vh;
        overflow-y: auto;
        top: 0;
        left: 0;
    }

    .sidebar.collapsed {
        width: 60px;
    }

    .sidebar.collapsed+.main-content {
        margin-left: 60px;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 30px;
    }

    .logo h2 {
        font-size: 1.5rem;
        transition: opacity 0.3s;
    }

    .sidebar.collapsed .logo h2 {
        opacity: 0;
        width: 0;
    }

    .menu-groups {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .menu-group {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 15px;
    }

    .menu-group h3 {
        font-size: 0.8rem;
        text-transform: uppercase;
        margin-bottom: 10px;
        color: rgba(255, 255, 255, 0.6);
        transition: opacity 0.3s;
    }

    .sidebar.collapsed .menu-group h3 {
        opacity: 0;
    }

    .menu-items {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .menu-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px;
        text-decoration: none;
        color: var(--text-color);
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .menu-item:hover {
        background-color: var(--secondary-color);
    }

    .menu-item i {
        width: 20px;
        text-align: center;
    }

    .menu-item span {
        transition: opacity 0.3s;
    }

    .sidebar.collapsed .menu-item span {
        opacity: 0;
        width: 0;
    }

    /* Main Content Styles */
    .main-content {
        flex: 1;
        padding: 20px;
        transition: all 0.3s ease;
        margin-left: 250px;
        height: 100vh;
        overflow-y: auto;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .toggle-sidebar {
        background: none;
        border: none;
        color: var(--primary-color);
        cursor: pointer;
        font-size: 1.5rem;
    }

    .search-bar {
        display: flex;
        gap: 10px;
    }

    .search-bar input {
        padding: 8px 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
        width: 300px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .stat-info h3 {
        font-size: 1.8rem;
        margin-bottom: 5px;
    }

    .stat-info p {
        color: #666;
        font-size: 0.9rem;
    }

    .recent-activity,
    .upcoming-deadlines,
    .academic-actions {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .recent-activity h2,
    .upcoming-deadlines h2,
    .academic-actions h2 {
        margin-bottom: 20px;
        color: var(--primary-color);
    }

    .activity-list,
    .deadline-list,
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .activity-item,
    .deadline-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 10px;
        border-radius: 5px;
        background-color: #f8f9fa;
    }

    .activity-icon,
    .deadline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .activity-details h4,
    .deadline-details h4 {
        margin-bottom: 5px;
    }

    .activity-details p,
    .deadline-details p {
        font-size: 0.9rem;
        color: #666;
    }

    .deadline-status {
        margin-left: auto;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .deadline-status.urgent {
        background-color: #ffebee;
        color: var(--danger-color);
    }

    .deadline-status.pending {
        background-color: #fff3e0;
        color: var(--warning-color);
    }

    .deadline-status.normal {
        background-color: #e8f5e9;
        color: var(--success-color);
    }

    .action-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }

    .action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px;
        background-color: var(--accent-color);
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .action-btn:hover {
        background-color: var(--primary-color);
    }

    .action-btn i {
        font-size: 1.2rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {

        .search-bar input {
            width: 200px;
        }

        .sidebar {
            position: fixed;
            left: -250px;
            height: 100vh;
            z-index: 1000;
        }

        .sidebar.active {
            left: 0;
        }

        .main-content {
            margin-left: 0;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .search-bar input {
            width: 150px;
        }
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 20px;
    }

    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
    }

    .modal.active {
        display: flex;
    }

    .modal-content {
        background-color: white;
        border-radius: 10px;
        width: 100%;
        max-width: 500px;
        padding: 30px;
        position: relative;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .modal-content h2 {
        margin-bottom: 20px;
        color: var(--primary-color);
        text-align: center;
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: var(--danger-color);
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        color: var(--primary-color);
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .radio-group {
        display: flex;
        gap: 15px;
    }

    .radio-group label {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .modal-footer {
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
    }

    .cancel-btn,
    .submit-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .cancel-btn {
        background-color: #f8f9fa;
        color: var(--primary-color);
    }

    .submit-btn {
        background-color: var(--accent-color);
        color: white;
    }

    .cancel-btn:hover {
        background-color: #e9ecef;
    }

    .submit-btn:hover {
        background-color: var(--primary-color);
    }

    #customDateRange {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .btn-group-xs>.btn,
    .btn-xs {
        padding: 1px 5px;
        font-size: 12px;
        line-height: 1.5;
        border-radius: 3px;
    }

    input.transform-text,
    select.transform-text,
    textarea.transform-text {
        text-transform: uppercase !important;
    }
</style>
<link href="../assets/css/style.css" rel="stylesheet">
<script src="../js/jquery-3.6.0.min.js"></script>