@layout @backend-layout

<div class="flex justify-between items-center">
    <h3 class="text-4xl tracking-tight">Articles</h3>
    <a class="btn" href="/admin/articles/edit">Add article</a>
</div>

<div class="mt-5">
    <table class="main-table">
        <thead>
            <tr>
                <th>{{ _ Title }}</th>
                <th>{{ _ Author }}</th>
                <th>{{ _ Language }}</th>
                <th>{{ _ Published at }}</th>
                <th>{{ _ Updated at }}</th>
                <th>{{ _ Created at }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($articles as $article)
            <tr class="hover:bg-slate-100">
                <td class="py-1 px-1">
                    <a href="{{ url('/admin/articles/edit', ['article-id' => $article->id]) }}">
                        {{ $article->title }}
                    </a>
                </td>
                <td>{{ $article->author_name }}</td>
                <td>{{ $article->language }}</td>
                <td title="{{ $article->published_at | datetime }}">{{ $article->published_at | date }}</td>
                <td title="{{ $article->updated_at | datetime }}">{{ $article->updated_at | date }}</td>
                <td title="{{ $article->created_at | datetime }}">
                    {{ $article->created_at | date }}
                </td>
            </tr>
            @end
        </tbody>
    </table>
</div>