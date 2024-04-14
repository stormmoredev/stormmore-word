@layout @backend-layout

<div class="flex justify-between items-center">
    <h3 class="text-4xl tracking-tight">Users</h3>
    <a class="btn" href="/admin/users/add">Add User</a>
</div>

<div class="mt-5">
    <table class="main-table">
        <thead>
            <tr>
                <th>{{ _ Usermame}}</th>
                <th>{{ _ Email }}</th>
                <th>{{ _ Name}}</th>
                <th>{{ _ Role}}</th>
                <th>{{ _ Status }}</th>
                <th>{{ _ Last activity at}}</th>
                <th>{{ _ Registered at}}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
        <tr>
            <td class="py-1 px-1 flex flex-row items-center">
                {{ ucfirst($user->name) }}
                @if($user->last_activity_at)
                <div title="Last updated at:  {{ $user->last_activity_at | datetime }}"
                     class="cursor-pointer ml-2 mt-[3px] rounded-full bg-green-400 w-1 h-1"></div>
                @end
            </td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
            <td>{{ $user->role }}</td>
            <td>
                @if (!$user->is_activated)
                    <span class="inline-flex items-center gap-x-1.5 rounded-md px-2 py-1 text-xs font-medium
                        text-gray-900 ring-1 ring-inset ring-gray-200">
                      <svg class="h-1.5 w-1.5 fill-yellow-500" viewBox="0 0 6 6" aria-hidden="true">
                        <circle cx="3" cy="3" r="3" />
                      </svg>
                        {{ _ Inactive }}
                    </span>
                @end
            </td>
            <td>{{ $user->last_activity_at | datetime }}</td>
            <td>{{ $user->created_at | datetime }}</td>
            <td>
                <!--
                <a href="#" class="group text-blue-600 hover:text-blue-500
                gap-x-1 text-sm font-semibold leading-6
                inline-flex items-center relative">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                    <div class="hidden absolute group-hover:block">
                        <a href="{{ url('/admin/users/edit', ['user-id' => $user->id]) }}" >
                            {{ _ Edit }}
                        </a>
                    </div>
                </a>
                -->
                <a href="{{ url('/admin/users/edit', ['user-id' => $user->id]) }}"
                   class="text-blue-600 hover:text-blue-500 text-sm font-semibold leading-6">
                    {{ _ Edit }}
                </a>
            </td>
        </tr>
        @end
        </tbody>
    </table>
</div>
