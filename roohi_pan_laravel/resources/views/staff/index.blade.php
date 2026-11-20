@extends('layouts.business')

@section('title', 'Staff - Roohi Pan')

@section('content')
<header class="page-header">
    <div><p class="eyebrow">Owner tools</p><h1>Staff Management</h1></div>
</header>
<p class="page-intro">New team members can register from the sign-in page. Promote team members or remove accounts without order history.</p>
<section class="panel table-panel">
    <div class="table-scroll">
        <table>
            <thead><tr><th>Name</th><th>Username</th><th>Role</th><th>Actions</th></tr></thead>
            <tbody>
            @foreach ($staff as $member)
                <tr>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->username ?: '—' }}</td>
                    <td><span class="role-badge role-{{ $member->role }}">{{ strtoupper($member->role) }}</span></td>
                    <td>
                        @if ($member->is(auth()->user()))
                            <span class="muted">Current user</span>
                        @else
                            @if ($member->role !== 'owner')
                                <form class="inline-action" method="POST" action="{{ route('staff.promote', $member) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="button button-small button-primary" type="submit">Promote to Owner</button>
                                </form>
                            @endif
                            <form class="inline-action" method="POST" action="{{ route('staff.destroy', $member) }}" onsubmit="return confirm('Remove this account? Accounts with order history cannot be removed.')">
                                @csrf
                                @method('DELETE')
                                <button class="button button-small button-danger" type="submit">Remove</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
