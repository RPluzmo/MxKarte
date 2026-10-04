<x-layout>
    <div class="page-shell">
        <section class="panel">
        <h1>Administratora panelis</h1>
        @include('admin.partials.nav')

        <dl class="stat-grid">
            <div class="stat-card"><dt>Lietotāji</dt><dd>{{ $usersCount }}</dd></div>
            <div class="stat-card"><dt>Trases</dt><dd>{{ $tracksCount }}</dd></div>
            <div class="stat-card"><dt>Trases ziņojumi</dt><dd>{{ $announcementsCount }}</dd></div>
            <div class="stat-card"><dt>Admin ziņojumi</dt><dd>{{ $siteAnnouncementsCount }}</dd></div>
        </dl>
        </section>
    </div>
</x-layout>
