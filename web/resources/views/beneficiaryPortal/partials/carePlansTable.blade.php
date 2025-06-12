@if(isset($carePlans) && $carePlans->count() > 0)
    <div class="table-responsive"
         data-first-item="{{ $carePlans->firstItem() }}"
         data-last-item="{{ $carePlans->lastItem() }}"
         data-total-items="{{ $carePlans->total() }}">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($carePlans as $plan)
                    <tr>
                        <td>{{ $plan->author->first_name }} {{ $plan->author->last_name }}</td>
                        <td>
                            @if($plan->acknowledged_by_beneficiary || $plan->acknowledged_by_family)
                                <span class="status-badge status-acknowledged">Acknowledged</span>
                            @else
                                <span class="status-badge status-pending">Pending Review</span>
                            @endif
                        </td>
                        <td>{{ $plan->created_at->format('M d, Y') }}</td>
                        <td class="actions-cell">
                            @if(!$plan->acknowledged_by_beneficiary && !$plan->acknowledged_by_family)
                                <button class="btn btn-sm btn-primary acknowledge-btn" 
                                    data-id="{{ $plan->weekly_care_plan_id }}"
                                    title="Acknowledge">
                                    Acknowledge
                                </button>
                            @endif
                            <a href="{{ secure_url(route($userType === 'beneficiary' ? 'beneficiary.care.plan.view' : 'family.care.plan.view', $plan->weekly_care_plan_id)) }}" 
                                class="btn btn-sm btn-info" title="View Details">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="empty-state p-4 text-center">
        <i class="bi bi-file-earmark-text display-4 text-muted"></i>
        <h4 class="mt-3">No Care Plans Found</h4>
        <p class="text-muted">There are currently no care plan records available{{ $userType === 'family' ? ' for your family member' : '' }}.</p>
    </div>
@endif