@extends('layouts.app')

@section('title', __('Recurring Tickets'))

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <h2>{{ __('Recurring Tickets') }}</h2>
      <p class="text-muted">{{ __('Templates that generate new tickets on a schedule.') }}</p>

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <div style="margin: 10px 0;">
        <a class="btn btn-primary" href="{{ route('recurringtickets.create') }}">{{ __('New template') }}</a>
      </div>

      <table class="table table-striped">
        <thead>
          <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Mailbox') }}</th>
            <th>{{ __('RRULE') }}</th>
            <th>{{ __('Next run') }}</th>
            <th>{{ __('Active') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($templates as $t)
            <tr>
              <td>{{ $t->name }}</td>
              <td>{{ $t->mailbox_id }}</td>
              <td><code>{{ $t->rrule }}</code></td>
              <td>{{ $t->next_run_at }}</td>
              <td>{{ $t->active ? __('Yes') : __('No') }}</td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-muted">{{ __('No templates yet.') }}</td></tr>
          @endforelse
        </tbody>
      </table>

    </div>
  </div>
</div>
@endsection
