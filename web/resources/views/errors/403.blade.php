@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">403 - Access Denied</div>

                <div class="card-body">
                    <div class="alert alert-danger">
                        <h4>Permission Denied</h4>
                        <p>{{ $message ?? 'You do not have permission to access this page.' }}</p>
                    </div>
                    
                    @if(config('app.debug'))
                    <div class="mt-4">
                        <h5>Debug Information:</h5>
                        <pre>User ID: {{ Auth::id() ?? 'Not logged in' }}
                            Role ID: {{ Auth::check() ? Auth::user()->role_id : 'N/A' }}
                            Organization Role ID: {{ Auth::check() ? Auth::user()->organization_role_id : 'N/A' }}
                            URL: {{ request()->fullUrl() }}</pre>
                    </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ url('/') }}" class="btn btn-primary">Return to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection