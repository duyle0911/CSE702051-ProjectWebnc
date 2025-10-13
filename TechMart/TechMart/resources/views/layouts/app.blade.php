<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TechMart')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    @stack('styles')

    <style>
        body {
            overflow: hidden;
        }

        .sidebar {
            position: fixed;
            top: 56px; /* Dưới navbar */
            left: 0;
            width: 250px;
            height: calc(100vh - 56px);
            background-color: #fff;
            border-right: 1px solid #dee2e6;
            transition: transform 0.3s ease;
            z-index: 1000;
            padding: 1rem;
            overflow-y: auto;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .content-area {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
            height: 100vh;
            padding-top: 56px;
            overflow-y: auto;
        }

        .content-area.expanded {
            margin-left: 0;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .content-area {
                margin-left: 0;
            }
        }
    </style>
</head>
<body class="bg-light">

    <!-- Navbar cố định -->
    @include('partials.navbar')

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <h5 class="text-danger mb-3 d-flex justify-content-between align-items-center">
            <span><i class="fas fa-layer-group me-2"></i>Danh mục</span>
            <button class="btn btn-sm btn-outline-secondary" id="toggleSidebarBtn">
                <i class="fas fa-bars"></i>
            </button>
        </h5>
        <div class="collapse show" id="categoryList">
            <ul class="nav flex-column">
                @foreach ($categories as $category)
                    <li class="nav-item mb-1">
                        <a href="{{ route('products.category', $category) }}" class="nav-link text-dark">
                            {{ $category->category_name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </aside>

    <!-- Nội dung chính -->
    <main class="content-area py-4" id="mainContent">
        @yield('content')
        @include('partials.footer')
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    <!-- Script toggle sidebar -->
    <script>
        const toggleBtn = document.getElementById('toggleSidebarBtn');
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('mainContent');

        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('expanded');
        });
    </script>
</body>
</html>
