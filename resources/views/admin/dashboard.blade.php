<x-layout>
    <main style="max-width: 1200px; margin: 0 auto; padding: 20px 16px;">
        <h1>Administratora panelis</h1>
        @include('admin.partials.nav')

        <p>Lietotāji: {{ $usersCount }}</p>
        <p>Trases: {{ $tracksCount }}</p>
        <p>Trases ziņojumi: {{ $announcementsCount }}</p>
        <p>Admin ziņojumi: {{ $siteAnnouncementsCount }}</p>
    </main>
</x-layout>
