@extends('layouts.app')

@section('title', __('New Recurring Ticket Template'))

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-10">
      <h2>{{ __('New Recurring Ticket Template') }}</h2>
      <p class="text-muted">{{ __('Define what ticket should be created and how often it should repeat.') }}</p>

      <form class="form-horizontal" method="POST" action="{{ route('recurringtickets.store') }}">
        @csrf

        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
          <label class="col-sm-2 control-label">{{ __('Name') }}</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Quarterly Server Patch">
            @include('partials/field_error', ['field' => 'name'])
          </div>
        </div>

        <div class="form-group {{ $errors->has('mailbox_id') ? 'has-error' : '' }}">
          <label class="col-sm-2 control-label">{{ __('Mailbox ID') }}</label>
          <div class="col-sm-3">
            <input type="number" class="form-control" name="mailbox_id" value="{{ old('mailbox_id') }}" placeholder="1">
            @include('partials/field_error', ['field' => 'mailbox_id'])
          </div>
          <div class="col-sm-5 text-muted" style="padding-top: 7px;">{{ __('Use the numeric mailbox id.') }}</div>
        </div>

        <div class="form-group {{ $errors->has('subject') ? 'has-error' : '' }}">
          <label class="col-sm-2 control-label">{{ __('Subject') }}</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="subject" value="{{ old('subject') }}" placeholder="Patch server SRV-01 (Quarterly)">
            @include('partials/field_error', ['field' => 'subject'])
          </div>
        </div>

        <div class="form-group {{ $errors->has('body') ? 'has-error' : '' }}">
          <label class="col-sm-2 control-label">{{ __('Body') }}</label>
          <div class="col-sm-8">
            <textarea class="form-control" rows="6" name="body" placeholder="Steps / checklist...">{{ old('body') }}</textarea>
            @include('partials/field_error', ['field' => 'body'])
          </div>
        </div>

        <div class="form-group {{ $errors->has('rrule') ? 'has-error' : '' }}">
          <label class="col-sm-2 control-label">{{ __('RRULE') }}</label>
          <div class="col-sm-8">
            <input type="text" class="form-control" name="rrule" value="{{ old('rrule', 'FREQ=MONTHLY;INTERVAL=3') }}" placeholder="FREQ=MONTHLY;INTERVAL=3">
            <span class="help-block">{{ __('Use an iCalendar RRULE string. Example: FREQ=MONTHLY;INTERVAL=3 for quarterly.') }}</span>
            @include('partials/field_error', ['field' => 'rrule'])
          </div>
        </div>

        <div class="form-group {{ $errors->has('starts_at') ? 'has-error' : '' }}">
          <label class="col-sm-2 control-label">{{ __('Starts at') }}</label>
          <div class="col-sm-4">
            <input type="datetime-local" class="form-control" name="starts_at" value="{{ old('starts_at') }}">
            @include('partials/field_error', ['field' => 'starts_at'])
          </div>
        </div>

        <div class="form-group {{ $errors->has('timezone') ? 'has-error' : '' }}">
          <label class="col-sm-2 control-label">{{ __('Timezone') }}</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="timezone" value="{{ old('timezone', config('app.timezone')) }}" placeholder="America/Toronto">
            @include('partials/field_error', ['field' => 'timezone'])
          </div>
        </div>

        <div class="form-group {{ $errors->has('requester_email') ? 'has-error' : '' }}">
          <label class="col-sm-2 control-label">{{ __('Requester Email') }}</label>
          <div class="col-sm-6">
            <input type="email" class="form-control" name="requester_email" value="{{ old('requester_email') }}" placeholder="noreply@example.com">
            <span class="help-block">{{ __('Optional. If omitted, the system requester setting may be used.') }}</span>
            @include('partials/field_error', ['field' => 'requester_email'])
          </div>
        </div>

        <div class="form-group {{ $errors->has('assignee_id') ? 'has-error' : '' }}">
          <label class="col-sm-2 control-label">{{ __('Assignee User ID') }}</label>
          <div class="col-sm-3">
            <input type="number" class="form-control" name="assignee_id" value="{{ old('assignee_id') }}" placeholder="">
            <span class="help-block">{{ __('Optional numeric user id to assign ticket to.') }}</span>
            @include('partials/field_error', ['field' => 'assignee_id'])
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-offset-2 col-sm-8">
            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            <a class="btn btn-default" href="{{ route('recurringtickets.index') }}">{{ __('Cancel') }}</a>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection
