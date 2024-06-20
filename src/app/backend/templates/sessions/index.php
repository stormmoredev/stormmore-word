@layout @backend-layout

<div class="mt-5">
    <table class="main-table">
        <thead>
        <tr>
            <th>{{ _ Usermame}}</th>
            <th>{{ _ Key }}</th>
            <th>{{ _ Valid to}}</th>
            <th>{{ _ Last activity at}}</th>
            <th>{{ _ Created at}}</th>
        </tr>
        </thead>
        <tbody>
            @foreach($sessions as $session)
            <tr>
                <td>{{ $session->name }}</td>
                <td>{{ $session->id }}</td>
                <td title="{{ $session->valid_to | datetime }}">{{ $session->valid_to | datetime }}</td>
                <td>{{ $session->last_activity_at | datetime }}</td>
                <td>{{ $session->created_at | datetime }}</td>
            </tr>
            @end
        </tbody>
    </table>

    {{ pagination('/admin/sessions', $count) }}
</div>
