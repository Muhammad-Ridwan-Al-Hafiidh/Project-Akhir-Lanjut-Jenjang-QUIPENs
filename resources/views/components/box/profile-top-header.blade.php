<div class="row">
    {{-- Profile Box - Full Width --}}
    <div class="col-12 mb-3">
        <x-box.profile-box :user="$user"/>
    </div>

    {{-- Messages & Alerts - Full Width --}}
    <div class="col-12">
        <div class="card shadow-sm border-left-danger">
            <div class="card-header bg-light py-2 d-flex align-items-center justify-content-between">
                <h6 class="m-0 text-muted">
                    <i class="fa fa-comment me-2"></i>
                    {{ __('Messages & Alerts') }}
                </h6>
            </div>
            <div class="card-body">
                @livewire('services.mentors.comments',[
                    'activable_id' => $activable_id,
                    'activable_type' => $activable_type,
                    'userId' => $user->id
                ])
            </div>
        </div>
    </div>
</div>