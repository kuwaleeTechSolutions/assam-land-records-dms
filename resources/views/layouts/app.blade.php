<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        {{ $title ?? 'Assam Land Records DMS' }}
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body>

<header class="gov-header">
    <div>
        <strong>GOVERNMENT OF ASSAM</strong>

        <div class="small">
            Land Records Digitization & Document Management System
        </div>
    </div>

    <div>
        {{ auth()->user()?->name }}
    </div>
</header>

<div class="d-flex">

    <aside class="gov-sidebar">

        <a href="{{ route('dashboard') }}">
            Dashboard
        </a>

        <a href="{{ route('documents.search') }}">
            Search Records
        </a>

        <a href="{{ route('documents.register') }}">
            Register Document
        </a>

        <a href="{{ route('verification.queue') }}">
            Verification
        </a>

        <a href="{{ route('reports.dashboard') }}">
            Reports
        </a>

        <a href="{{ route('bulk.import') }}">
            Bulk Import
        </a>

        @can('audit.view')
            <a href="{{ route('admin.audit') }}">
                Audit Trail
            </a>
        @endcan

        @can('system.configure')
            <a href="{{ route('admin.system-health') }}">
                System Health
            </a>

            <a href="{{ route('admin.retention') }}">
                Retention
            </a>
        @endcan

    </aside>

    <main class="flex-grow-1 p-4">
        {{ $slot }}
    </main>

</div>

@livewireScripts

</body>
</html>
