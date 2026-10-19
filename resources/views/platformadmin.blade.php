<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEST.PH Platform · Owner Requests</title>
    <link rel="icon" href="{{ asset('images/nestphgreen.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root { --green: #197335; --green-dark: #567357; --ink: #292420; --muted: #5b6168; --border: #c1c7cd; --bg: #f2f4f8; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Roboto', system-ui, sans-serif; background: var(--bg); color: var(--ink); line-height: 1.5; }
        a { color: inherit; }
        a:focus-visible, button:focus-visible, select:focus-visible, textarea:focus-visible { outline: 2px solid var(--green); outline-offset: 2px; }
        .wrap { width: min(1120px, 100% - 32px); margin-inline: auto; }
        header { background: linear-gradient(90deg, var(--green), var(--green-dark)); color: #fff; }
        header .wrap { display: flex; align-items: center; gap: 16px; padding: 14px 0; flex-wrap: wrap; }
        .brand { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 18px; text-decoration: none; }
        .brand img { height: 30px; }
        .tag { font-size: 12px; background: rgba(255,255,255,.2); padding: 2px 8px; border-radius: 999px; font-weight: 500; }
        header .right { margin-left: auto; display: flex; align-items: center; gap: 14px; font-size: 14px; }
        header button { background: #fff; color: var(--green); border: 0; border-radius: 6px; padding: 7px 14px; font: inherit; font-weight: 700; cursor: pointer; }
        main { padding: 28px 0 48px; }
        h1 { font-size: 26px; }
        .lead { color: var(--muted); margin: 4px 0 20px; }
        .flash { background: #eaf6eb; color: var(--green); padding: 10px 14px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
        .filters { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; }
        .filters a { text-decoration: none; background: #fff; border: 1px solid var(--border); border-radius: 999px; padding: 6px 14px; font-size: 14px; }
        .filters a[aria-current] { background: var(--green); border-color: var(--green); color: #fff; }
        .filters .n { font-weight: 700; margin-left: 4px; }
        .req { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin-bottom: 14px; display: grid; grid-template-columns: 1.3fr 1fr; gap: 20px; }
        .req h2 { font-size: 18px; }
        .meta { color: var(--muted); font-size: 14px; }
        .details { list-style: none; font-size: 14px; margin-top: 10px; display: grid; gap: 2px; }
        .details span { color: var(--muted); display: inline-block; min-width: 90px; }
        .message { margin-top: 10px; font-size: 14px; background: var(--bg); padding: 10px 12px; border-radius: 8px; white-space: pre-line; }
        .pill { display: inline-block; font-size: 12px; font-weight: 700; padding: 3px 10px; border-radius: 999px; background: #eef0f3; margin-left: 6px; vertical-align: middle; }
        .pill.new { background: #fff4d6; color: #8a5a00; }
        .pill.contacted, .pill.demo_scheduled { background: #e3eefc; color: #1d4f91; }
        .pill.onboarded { background: #eaf6eb; color: var(--green); }
        .pill.declined { background: #fdecea; color: #b42318; }
        form.update { display: flex; flex-direction: column; gap: 8px; }
        form.update label { font-size: 13px; font-weight: 500; }
        select, textarea { font: inherit; font-size: 14px; padding: 8px 10px; border: 1px solid var(--border); border-radius: 8px; width: 100%; }
        textarea { min-height: 80px; resize: vertical; }
        form.update button { align-self: flex-start; background: var(--green); color: #fff; border: 0; border-radius: 8px; padding: 8px 16px; font: inherit; font-weight: 700; cursor: pointer; }
        .empty { background: #fff; border: 1px dashed var(--border); border-radius: 12px; padding: 40px; text-align: center; color: var(--muted); }
        .pager { display: flex; gap: 12px; justify-content: center; margin-top: 20px; font-size: 14px; }
        @media (max-width: 720px) { .req { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <div class="wrap">
            <a class="brand" href="{{ route('platform.dashboard') }}"><img src="{{ asset('images/nestph.png') }}" alt=""> NEST.PH <span class="tag">Platform team</span></a>
            <div class="right">
                <span>{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Log out</button>
                </form>
            </div>
        </div>
    </header>

    <main class="wrap">
        <h1>Dormitory owner requests</h1>
        <p class="lead">Owners who asked to use NEST.PH for their dormitory, from the "Request NEST.PH" form on the landing page.</p>

        @if (session('status'))
            <div class="flash" role="status">{{ session('status') }}</div>
        @endif

        <nav class="filters" aria-label="Filter by status">
            <a href="{{ route('platform.dashboard') }}" @if(! $activeStatus) aria-current="page" @endif>All<span class="n">{{ $total }}</span></a>
            @foreach ($statuses as $key => $label)
                <a href="{{ route('platform.dashboard', ['status' => $key]) }}" @if($activeStatus === $key) aria-current="page" @endif>{{ $label }}<span class="n">{{ $counts[$key] ?? 0 }}</span></a>
            @endforeach
        </nav>

        @forelse ($requests as $req)
            <article class="req">
                <div>
                    <h2>{{ $req->dorm_name }} <span class="pill {{ $req->status }}">{{ $statuses[$req->status] ?? $req->status }}</span></h2>
                    <p class="meta">Sent {{ $req->created_at->format('M j, Y g:i A') }} ({{ $req->created_at->diffForHumans() }})</p>
                    <ul class="details">
                        <li><span>Owner</span>{{ $req->owner_name }}</li>
                        <li><span>Email</span><a href="mailto:{{ $req->email }}">{{ $req->email }}</a></li>
                        <li><span>Contact no.</span><a href="tel:{{ $req->contact_number }}">{{ $req->contact_number }}</a></li>
                        @if ($req->city)<li><span>City</span>{{ $req->city }}</li>@endif
                        @if ($req->bed_count)<li><span>Beds</span>~{{ $req->bed_count }}</li>@endif
                    </ul>
                    @if ($req->message)
                        <p class="message">{{ $req->message }}</p>
                    @endif
                </div>
                <form class="update" method="POST" action="{{ route('platform.requests.update', $req) }}">
                    @csrf
                    @method('PATCH')
                    <label for="status-{{ $req->id }}">Status</label>
                    <select id="status-{{ $req->id }}" name="status">
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" @selected($req->status === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <label for="notes-{{ $req->id }}">Team notes</label>
                    <textarea id="notes-{{ $req->id }}" name="notes" maxlength="2000" placeholder="e.g. Called on Oct 20, demo set for Oct 25">{{ $req->notes }}</textarea>
                    <button type="submit">Save</button>
                </form>
            </article>
        @empty
            <div class="empty">No requests {{ $activeStatus ? 'with this status' : 'yet' }}.</div>
        @endforelse

        @if ($requests->hasPages())
            <div class="pager">
                @if ($requests->previousPageUrl())<a href="{{ $requests->previousPageUrl() }}">&larr; Newer</a>@endif
                <span>Page {{ $requests->currentPage() }} of {{ $requests->lastPage() }}</span>
                @if ($requests->nextPageUrl())<a href="{{ $requests->nextPageUrl() }}">Older &rarr;</a>@endif
            </div>
        @endif
    </main>
</body>
</html>
