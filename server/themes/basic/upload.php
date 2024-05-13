@layout @frontend/layout.php

<div>
    <div>Name</div>
    <form action="/upload" method="post"  enctype="multipart/form-data">
        <input type="file" name="file" />
        <input type="submit" />

        {{ html::text('test', 'przykladlowy text') }}
    </form>
</div>