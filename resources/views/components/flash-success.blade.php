@if(session('success'))
    <div class="flash-message success">
        {{ session('success') }}
    </div>
@endif
